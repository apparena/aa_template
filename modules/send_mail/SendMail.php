<?php
require_once( 'Zend/Mail.php' );
require_once( 'Zend/Mail/Transport/Smtp.php' );

class SendMail {

	private $smtp_host = "localhost";
	private $smtp_port = 587;
	private $smtp_user = "none";
	private $smtp_pass = "none";
	private $customer_name = "";
	private $customer_email = "";
	private $aa_inst_id;
	private $customer;
	private $user;
	private $template = '';
	
	/**
	 * Initializes a Mail object to send out a mail.
	 * @param array $smtp Smtp access data as an array: (host, port, user, pass)
	 * @param int $aa_inst_id App Arena Instance Id
	 * @param array $sender Email sender data: (name, email) of the user who sent the form
	 */
	function __construct( $smtp=array(), $aa_inst_id=0, $customer=array(), $user=array(), $template='' ) {
		
		if (array_key_exists('host', $smtp))
			$this->smtp_host = $smtp['host'];
		
		if (array_key_exists('port', $smtp))
			$this->smtp_port = $smtp['port'];
		
		if (array_key_exists('user', $smtp))
			$this->smtp_user = $smtp['user'];
		
		if (array_key_exists('host', $smtp))
			$this->smtp_pass = $smtp['pass'];
		
		if ($aa_inst_id != 0)
			$this->aa_inst_id = $aa_inst_id;
		
		$this->template = $template;
		
		$sender = array(
			'mail' => $user['email'],
			'name' => $user['firstname'] . ' ' . $user['lastname']
		);
		
		$this->user = $user;
		$this->customer = $customer;
		
		$this->set_sender($sender);
	}

	/**
	 * Send a mail to the customer. This email contains user data from the contact form.
	 * @param array $receiver (name, email) Array with all receiver information.
	 * @param array $email (subject, body) The email content templates.
	 * @return boolean Returns if email could be sent out or not.
	 */
	function send_email($email=array()) {
		
		// Get email content
		if (array_key_exists('body', $email))
			$email_body = $email['body'];
		else $email_body = "";
		if (array_key_exists('subject', $email))
			$email_subject = $email['subject'];
		else $email_subject = "";
		
		// Get receiver data (the customer gets the mail from the contact form, the user is the sender)
		if (array_key_exists('name', $this->customer))
			$receiver_name = $this->customer['name'];
		else $receiver_name = "";
		if (array_key_exists('email', $this->customer))
			$receiver_email = $this->customer['email'];
		else $receiver_email = "";
		
		// Replace variables in Email-text
		$email_body = str_replace("{{name}}", $this->user['firstname'] . ' ' . $this->user['lastname'], $email_body);
		
		$header = '';
		
		if ( isset( $this->user['mailcontent'] ) ) {
			
			// we have some email content to show
			$header = $this->user['mailcontent'] . '<br /><br />';
			
		}
		// a flag if we shall not show the user data in the mailbody
		if ( !isset( $this->user['dontshowdata'] ) ) {
			
			// take the keys and replace the keynames with their translations
			$data = array();
			
			foreach( $this->user as $key => $value ) {
				// firstname and lastname are shown above...
				if ( $key != 'firstname' &&
					 $key != 'lastname' &&
					 $key != 'dontshowdata' &&
					 $key != 'sendtouser' &&
					 $key != 'mailcontent' &&
					 $key != 'usertouser' &&
					 $key != 'toemail' &&
					 $key != 'toname' ) {
					$data[] = __t( $key ) . ': ' . $value;
				}
			}
				
			$data = implode( '<br />', $data );
			
		}
		
		if ( !isset( $data ) ) {
			$data = '';
		}
		
		$email_body = str_replace("{{userdata}}", $header . $data, $email_body);

		// Setup Zend SMTP server
		$smtp_config = array(
			'ssl'	   =>'tls',
			'username' => $this->smtp_user, 
			'password' => $this->smtp_pass,
			'port'	   => $this->smtp_port,
			'auth'	   => 'login'
		);
		$transport = new Zend_Mail_Transport_Smtp($this->smtp_host, $smtp_config);

		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($email_body);
		if ( isset( $this->user['usertouser'] ) ) {
			// a mail from the user to another user
			$mail->setFrom($this->user['email'], $this->user['firstname'] . ' ' . $this->user['lastname']);
			if ( !isset( $this->user['toemail'] ) || !isset( $this->user['toname'] ) ) {
				return 'missing toemail or toname in usertousermode. provide these data in the user_data array.';
			}
			$mail->addTo($this->user['toemail'], $this->user['toname']);
		} else {
			if ( !isset( $this->user['sendtouser'] ) ) {
				// send the email to the customer
				$mail->setFrom($this->user['email'], $this->user['firstname'] . ' ' . $this->user['lastname']);
				$mail->addTo($this->customer['email'], $this->customer['name']);
			} else {
				// send the email to the user
				$mail->setFrom($this->customer['email'], $this->customer['name']);
				$mail->addTo($this->user['email'], $this->user['firstname'] . ' ' . $this->user['lastname']);
			}
		}
		
		$mail->setSubject($email_subject);

		try{
			$return = $mail->send($transport);
			//return true;
			return print_r( $mail );
			//return '666';
		} catch(Exception $e) {
			//send mail failed
			$return_msg = "<strong>Receiver: </strong>" . var_dump($this->user);
			$return_msg .= "<strong>Email: </strong>" . var_dump($email);
			$return_msg .= "<strong>SMT-Settings: </strong>" . var_dump($smtp_config) . "Smtp-Host: " . $this->smtp_host;
			return '666' . $return_msg . $e->getMessage();
		}
	}
	
	/**
	 * Sets the sender for all sent out emails
	 * @param array $sender
	 */
	function set_sender($sender=array()) {
		if (array_key_exists('name', $sender))
			$this->sender_name = $sender['name'];
		
		if (array_key_exists('email', $sender))
			$this->sender_email = $sender['email'];
	}
	
	
	/**
	 * Returns the IP of the client
	 * @return String client ip
	 */
	private function get_client_ip(){
		// Get client ip address
		if ( isset($_SERVER["REMOTE_ADDR"]))
			$client_ip = $_SERVER["REMOTE_ADDR"];
		else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
			$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
			$client_ip = $_SERVER["HTTP_CLIENT_IP"];
	
		return $client_ip;
	}

}
