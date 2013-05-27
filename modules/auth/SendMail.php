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
	function __construct( $smtp=array(), $aa_inst_id=0, $customer=array(), $user=array() ) {
		
		if (array_key_exists('host', $smtp))
			$this->smtp_host = $smtp['host'];
		
		if (array_key_exists('port', $smtp))
			$this->smtp_port = $smtp['port'];
		
		if (array_key_exists('user', $smtp))
			$this->smtp_user = $smtp['user'];
		
		if (array_key_exists('host', $smtp))
			$this->smtp_pass = $smtp['pass'];
		
		if ($aa_inst_id != 0)
			$this->aa_inst_id = intval( $aa_inst_id );
		
		$sender = array(
			'mail' => $customer['email'],
			'name' => $customer['name']
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
	function send_email($email=array(), $link='') {
		
		if ( strlen( $link ) <= 0 ) {
			
			return 'missing link! need a link for the user to be forwarded (like: "https://www.app-arena.com/myapp/modules/auth/recover_password.php/recovery_redirect.php?aa_inst_id=4656&activationkey=fc43607492acab2a42ff51312e189179")';
			
		}
		
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
		
		// generate new password
		$password = $this->generatePassword();
		
		// generate a key for activation
		$key = $this->generatePassword();
		$key = md5( $key );
		
		// Replace variables in Email-text
		$email_body = str_replace( "{{link}}", $link, $email_body );
		$email_body = str_replace( "{{password}}", $password, $email_body );

		// Setup Zend SMTP server
		$smtp_config = array(
			'ssl'	   =>'tls',
			'username' => $this->smtp_user, 
			'password' => $this->smtp_pass,
			'port'	   => $this->smtp_port,
			'auth'	   => 'login'
		);
		$transport = new Zend_Mail_Transport_Smtp($this->smtp_host, $smtp_config);
		
		// setup email
		$mail = new Zend_Mail('UTF-8');
		$mail->setBodyHtml($email_body);
		$mail->setFrom($this->customer['email'], $this->customer['name']);
		$mail->addTo($this->user['email'], $this->user['firstname'] . ' ' . $this->user['lastname']);
		$mail->setSubject($email_subject);
		
		try{
			
			// if the mail is sent successfully we need to save the link and password to the db...
			$_GET[ 'aa_inst_id' ] = $this->aa_inst_id;
			include_once '../../config.php';
			include_once '../../init.php';
			include_once 'check_database.php';
			
			$user_id = 0;
			
			// get the users id by email
			$query = "SELECT * FROM `user_data` WHERE `aa_inst_id` = " . $this->aa_inst_id . " AND `email` = '" . mysql_real_escape_string( $this->user[ 'email' ] ) . "'";
			$result = mysql_query( $query );
			if ( $result ) {
				if ( mysql_num_rows( $result ) > 0 ) {
					$row = mysql_fetch_assoc( $result );
					$user_id = $row[ 'id' ];
				}
				mysql_free_result( $result );
			}
			
			if ( $user_id <= 0 ) {
				return 'email not found in db, please try again...';
			}
			
			$return = $mail->send($transport);
			
			$data = array(
				'password' => md5( $password ),
				'key'      => $key,
				'email'    => $this->user[ 'email' ]
			);
			
			// very unlikely to happen but check if this password has already been created once before...
			$query = "SELECT * FROM `user_log` WHERE `aa_inst_id` = " . $this->aa_inst_id . " AND `action` = 'user_password_recover' AND `data` = '" . json_encode( $data ) . "' AND `user_id` = " . $user_id;
			$result = mysql_query( $query );
			if ( $result ) {
				if ( mysql_num_rows( $result ) > 0 ) {
					return 'the password was already used by the password recovery, please try again...';
				}
				mysql_free_result( $result );
			}
			
			// insert the recovery action and the new password
			$query = "INSERT INTO `user_log` SET `aa_inst_id` = " . $this->aa_inst_id . ", `action` = 'user_password_recover', `data` = '" . json_encode( $data ) . "', `user_id` = " . $user_id;
			mysql_query( $query );
			
			return $return;
			
		} catch(Exception $e) {
			//send mail failed
			$return_msg  = "<strong>Receiver: </strong>" . var_dump($this->user);
			$return_msg .= "<strong>Email: </strong>" . var_dump($email);
			$return_msg .= "<strong>SMT-Settings: </strong>" . var_dump($smtp_config) . "Smtp-Host: " . $this->smtp_host;
			return '' . $return_msg . $e->getMessage();
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
	 * A function for generating a random password of whatever length you need.
	 * You don't get super-secure passwords from this...
	 * To avoid generating passwords containing offensive words,
	 * vowels are excluded from the list of possible characters.
	 * To avoid confusing users, pairs of characters which look similar
	 * (letter O and number 0, letter S and number 5, lower-case letter L and number 1)
	 * have also been left out.
	 * 
	 * (credits go to http://www.laughing-buddha.net/php/password)
	 * 
	 * @param number $length The desired length of the generated password (default is 6).
	 * @return string A password only containing characters from the $possible var.
	 */
	function generatePassword ($length = 6) {
	
		// start with a blank password
		$password = "";
	
		// define possible characters - any character in this string can be
		// picked for use in the password, so if you want to put vowels back in
		// or add special characters such as exclamation marks, this is where
		// you should do it
		$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	
		// we refer to the length of $possible a few times, so let's grab it now
		$maxlength = strlen($possible);
	
		// check for length overflow and truncate if necessary
		if ($length > $maxlength) {
			$length = $maxlength;
		}
	
		// set up a counter for how many characters are in the password so far
		$i = 0;
	
		// add random characters to $password until $length is reached
		while ($i < $length) {
	
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
	
			// have we already used this character in $password?
			if (!strstr($password, $char)) {
				// no, so it's OK to add it onto the end of whatever we've already got...
				$password .= $char;
				// ... and increase the counter by one
				$i++;
			}
	
		}
	
		// done!
		return $password;
	
	}
	
}