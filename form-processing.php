<?php

/**
 * Application Name: Custom PHP Form
 * Application URI: http://github.com/amirolzolkifli/customphpform
 * Description: Custom PHP Form.
 * Version: 1.0.0
 * Author: Amirol Zolkifli
 * Author URI: http://www.amirolzolkifli.com
 * License: MIT
 */

require 'form-config.php';
require 'form-helpers.php';
require 'sb-includes/vendor/wixel/gump/gump.class.php';
require 'sb-includes/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';


// Process submitted form post
if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	// Form Validation
	$gump = new GUMP();

	$_POST = $gump->sanitize( $_POST );

	$gump->validation_rules(array(
	    'name'    => 'required|min_len,3',
	    'email'   => 'required|valid_email',
	    'phone'   => 'required|min_len,3',
	    'address' => 'required|min_len,3'
	));

	$validated_data = $gump->run( array_merge( $_POST, $_FILES ) );

	if( $validated_data === false )
	{
	    $errors = $gump->get_readable_errors(true);
	    include('form-errors.php');
	    exit();
	} 

	// Process and send emails
	$mail = new PHPMailer;

	// $mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                    // Set mailer to use SMTP
	$mail->Host = MAIL_HOST;  							// Specify main and backup SMTP servers
	$mail->SMTPAuth = MAIL_SMTP_AUTH;                  	// Enable SMTP authentication
	$mail->Username = MAIL_USERNAME;         			// SMTP username
	$mail->Password = MAIL_PASSWORD;                    // SMTP password
	$mail->SMTPSecure = MAIL_SMTP_SECURE;				// Enable TLS encryption, `ssl` also accepted
	$mail->Port = MAIL_PORT;							// TCP port to connect to

	$mail->setFrom( EMAIL_FROM, SENDER_NAME );
	$mail->addAddress( EMAIL_REPLY_TO, SENDER_NAME );     // Add a recipient
	$mail->addReplyTo( $_POST['email'], $_POST['name'] );

	// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

	// Check for attachments
	if( isset( $_FILES['attachments'] ) AND ! empty( $_FILES['attachments'] ))
	{
		$name_array = $_FILES['attachments']['name'];
		$tmp_name_array = $_FILES['attachments']['tmp_name'];
		$type_array = $_FILES['attachments']['type'];
		$size_array = $_FILES['attachments']['size'];
		$error_array = $_FILES['attachments']['error'];

		for( $i = 0; $i < count($tmp_name_array); $i++ )
		{
			$mail->AddAttachment( $tmp_name_array[$i], $name_array[$i]);
		}
	}
	
	$mail->isHTML(false);								// Set email format to HTML

	$mail->Subject = emailSubject( $_POST['name'], EMAIL_SUBJECT );
	$mail->Body    = emailContent( $_POST );
	$mail->AltBody = 'Please enable HTML to view the email content';


	if( ! $mail->send() )
	{
	    echo 'Message could not be sent.';
	    echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
	else
	{
		header( 'location: ' . THANK_YOU_PAGE );
		exit();
	}	

}

echo '<meta http-equiv="refresh" content="2;url=./">';