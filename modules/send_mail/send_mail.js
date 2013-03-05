/**
 * All functionality to send out an email.
 * No db required. 
 */

/**
 * This function sends out the email.
 * @param {Number} aa_inst_id The current App-Arena instance id.
 * @param {Object} user_data An object containing the user data.
 *                           Required keys:
 *                           	user_data.email     -> This will be set as the "from" mailaddress if sent to the customer (e.g. for sending form data) or as "to" if sent to the user (use "sendtouser" key!).
 *                           	user_data.firstname -> The users name will be taking the place of the {{name}} tag in the App-Arena mailcontent var and it is used for the email from or to.
 *                           	user_data.lastname
 *                           Optional keys (will not be shown in the users data area):
 *                           	user_data.dontshowdata -> If this is set, the user's data will not be shown in the mail!
 *                           	user_data.mailcontent  -> If this is set it will be shown in the mail (above the user's data if dontshowdata is not set). Can be HTML content string or plain text.
 *                           	user_data.sendtouser   -> If this is set, the email will be sent from the customer to the user.
 *														  If this key is missing, the email will be sent from the user to the customer.
 *								user_data.usertouser   -> If this is set, the email will be sent from the user to another user (e.g. a friend).
 *														  You also need to provide a receiver email and name for using this mode.
 *								user_data.toemail      -> These two are only needed if you use the usertouser mode.
 *								user_data.toname
 *							 Optional keys (will be shown in the users data area):
 *                           	user_data.mykewlvar    -> Use some keys to store data you want to show in the mail (if dontshowdata is not set).
 *                           							  The keys will be TRANSLATED before they are put into the mail!!
 *                           							  So if you need a translation for the keys you'd have to enter a translation named mykewlvar in this case...
 * @param {Function} callback Gets executed after the ajax call to send the mail returns.
 * @param {String} template The App-Arena template to use when sending the email.
 *                          The template should contain a {{name}} and a {{userdata}} key to put in the contents.
 * @return The callback function will contain the response from the mail-script passed in as a parameter. Should contain a success or an error key...
 */
function send_mail( aa_inst_id, user_data, callback, template ) {

	// pack the template
	user_data.template = template;
	var url = "modules/send_mail/send_mail.php?aa_inst_id=" + aa_inst_id;
	jQuery.post(url, {user_data: user_data}, function(response) {
		if ( typeof( response ) != 'undefined' ) {
			if ( typeof( response.error ) != 'undefined' ) {
				//callback;
			}
		} else {
			//error
			//alert(response.error_msg);
		}
		if ( typeof( callback ) == 'function' ) {
			callback( response );
		}
	}, 'json').fail(function() {
		if ( typeof( callback ) == 'function' ) {
			callback( {error: 'mail not sent'} );
		}
	});
}