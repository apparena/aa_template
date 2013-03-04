=============================
Usage instruction
=============================
Module: Send Mail


-----------------
- Requirements: -
-----------------

1. App-Manager:
---------------
In the App-Manager you need to add the following config-values to make this work:
- wizard_email              [text]: The email address the mail gets sent to.
- wizard_company_name       [text]: The name will be used for addressing the email ($mail->addTo).
- contactform_email_subject	[text]: The subject will be shown as the mail subject.
/*@deprecated
 * - contactform_email_body    [html]: The body is a template for the mail body containing up to two placeholders for inserting data.
 */
- 'your_key_here'			[html]: Use App-Arena HTML items to specify templates to use for mailing.
							        A template should contain two placeholders for user related data:
							        {{name}}, {{userdata}}

2. Email Content (contactform_email_body):
------------------------------------------
This html content element should contain these placeholders:
{{name}}     - This will be replaced with the users firstname and lastname
{{userdata}} - This will be replaced with the users data (if user_data.dontshowdata is not set!)
               and optionally shows user_data.mailcontent above the users data if it is set.

3. Translation:
---------------
The keys you use in user_data (except firstname and lastname) will be used as App-Arena translation variables.
So if you do not set them in App-Arena, the keys will just be printed out.
If you have a key "user_data.message" for example, the mail content will look like:

blabla...
message: The message the user entered in a form maybe...
blabla...

And if you set a translation variable named "message" in App-Arena (and maybe you set the german translation to "Nachricht")
it will look like:

blabla...
Nachricht: The message the user entered in a form maybe...
blabla...


----------
- Usage: -
----------

1a. Include the .js file:
<script type="text/javascript" src="modules/send_mail/send_mail.js"></script>

OR:

1b. Require the .js file with require.js in your require-main functions require.config and requires launch function:
>> file: js/main.js, called by <script data-main="js/main" src="libs/require/require.js"></script> in your index.php
require.config({
    paths:{
        jquery:   '//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.0/jquery.min',
        SendMail: '../modules/send_mail/send_mail'
    },
    shim:{
        jquery: {
            exports:'$'
        },
        SendMail: {
        	deps: [ 'jquery' ],
            exports: 'SendMail'
        }
    }
});
require([
    'jquery',
    'SendMail'
], function ($, SendMail) {
	send_mail(12345, {firstname:'paul',lastname:'meier',email:'paul@meier.de',mailcontent:'showmetoo'}, function(response){console.log(response);}, 'contactform_email_body')
});

2. Use the send_mail function like on the bottom of 1b.
Parameters:
aa_inst_id: The App-Arena instance id is needed to establish a session.
user_data:  This must be an object containing at least a firstname, lastname and email key.
    Optional keys (will not be shown in the users data area):
        user_data.dontshowdata: If this is set, the user's data will not be shown in the mail body.
        user_data.sendtouser:   If this is set, the email will be sent from the customer to the user.
                                If this key is missing, the email will be sent from the user to the customer.
        user_data.mailcontent:  If this is set, its content should be a string and it will be placed before the users data if this is set to be shown.
        user_data.usertouser   -> If this is set, the email will be sent from the user to another user (e.g. a friend).
								  You also need to provide a receiver email and name for using this mode.
		user_data.toemail      -> These two are only needed if you use the usertouser mode.
		user_data.toname
    Optional keys (will be shown in the users data area):
       	user_data.mykewlvar:    Use some keys to store data you want to show in the mail (if dontshowdata is not set).
       							The keys will be TRANSLATED before they are put into the mail!!
       							So if you need a translation for the keys you'd have to enter a translation named mykewlvar in this case...


-----------------
- Example Calls -
-----------------

call this from your javscript somewhere

1. send a mail to the user:
send_mail(
	aa.inst.aa_inst_id,
	{
		email:        _.user.key,
		firstname:    _.user.value.first_name,
		lastname:     _.user.value.last_name,
		mailcontent:  'thanx for registering for our lottery. you will receive extra tickets if your friends join too...',
		dontshowdata: true, // do not show user data in the email
		sendtouser:   true
	},
	null, // dont use the callback function
	'user_mail_body' // the module uses the App-Arena HTML template with the key 'user_mail_body' for sending the mail.
);

2. send a mail to the customer:
send_mail(
	aa.inst.aa_inst_id,
	{
		email:        _.user.key,
		firstname:    _.user.value.first_name,
		lastname:     _.user.value.last_name,
		mailcontent:  '<div style="color: red;">this is additional content for the email</div>',
		user_address: 'Teststreet 66, 12345 Testcity',
		user_request: 'call me back',
		user_phone:   '0123456789', // set an aa-translation-var with the key 'user_phone' and it will be translated in the email.
		newsletter:   true 
	},
	function( response ){
		console.log( response );
	},
	'my_mail_template'
);