<?php 
//////////////////////////
//Specify default values//
//////////////////////////

//Your E-mail
$your_email = 'tjc.schipper@gmail.com';

//Default Subject if 'subject' field not specified
$default_subject = 'Test subject';

//Message if 'name' field not specified
$name_not_specified = '[ANONYMOUS]';

//Message if 'message' field not specified
$message_not_specified = '[NO MESSAGE]';

//Message if e-mail sent successfully
$email_was_sent = 'Thanks, your message successfully sent';

//Message if e-mail not sent (server not configured)
$server_not_configured = 'Sorry, mail server not configured';

// Message if captcha not verified
$invalid_captcha = "Sorry, your captcha attempt was not verified. Please try again.";


///////////////////////////
//Contact Form Processing//
///////////////////////////
$errors = array();
if(isset($_POST['message']) and isset($_POST['name']) and isset($_POST['captcha'])) {
	if(!empty($_POST['name']))
		$sender_name  = stripslashes(strip_tags(trim($_POST['name'])));
	
	if(!empty($_POST['message']))
		$message      = stripslashes(strip_tags(trim($_POST['message'])));
	
	if(!empty($_POST['email']))
		$sender_email = stripslashes(strip_tags(trim($_POST['email'])));
	
	if(!empty($_POST['subject']))
		$subject      = stripslashes(strip_tags(trim($_POST['subject'])));

	if (!empty($_POST['captcha']))
		$captcha      = stripslashes(strip_tags(trim($_POST['captcha'])));


	// Verify captcha response
	$verification = json_decode(verifyCaptcha($captcha), true);
	if ($verification['success'] === FALSE) {
		$errors[] = $invalid_captcha;
	}

	//Message if no sender name was specified
	if(empty($sender_name)) {
		$errors[] = $name_not_specified;
	}

	//Message if no message was specified
	if(empty($message)) {
		$errors[] = $message_not_specified;
	}

	$from = (!empty($sender_email)) ? 'From: '.$sender_email : '';

	$subject = (!empty($subject)) ? $subject : $default_subject;

	$message = (!empty($message)) ? wordwrap($message, 70) : '';

	//sending message if no errors
	if(empty($errors)) {
		if (mail($your_email, $subject, $message, $from)) {
			echo $email_was_sent;
		} else {
			$errors[] = $server_not_configured;
			echo implode('<br>', $errors );
		}
	} else {
		echo implode('<br>', $errors );
	}
} else {
	// if "name" or "message" vars not send ('name' attribute of contact form input fields was changed)
	echo '"name" and "message" variables were not received by server. Please check "name" attributes for your input fields';
}

function verifyCaptcha($response) {
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array('secret' => '6Lcp8goUAAAAAAxPxLSxXirkgYpiOvuD_8FihzUD', 'response' => $response);

	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($data)
			)
		);

	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) {
		// Handle errors
	} else {
		return $result;
	}
}
?>