<?php 
//////////////////////////
//Specify default values//
//////////////////////////

//Your E-mail
$your_email = 'tjc.schipper@gmail.com';

//Default Subject if 'subject' field not specified
$default_subject = 'Nieuw bericht van contactformulier';

//Message if 'name' field not specified
$name_not_specified = '[Anoniempje]';

//Message if 'message' field not specified
$message_not_specified = '[Geen tekst ingevuld]';

//Message if e-mail sent successfully
$email_was_sent = 'Bedankt, uw bericht is verzonden. Wij zullen spoedig contact met u opnemen.';

//Message if e-mail not sent (server not configured)
$server_not_configured = 'Er is een probleem met de mailservice. Email naar [placeholder] of bel ons om contact op te nemen. Excuses voor het ongemak.';

// Message if captcha not verified
$invalid_captcha = "Het anti-spamfilter kon uw browser niet valideren. Email naar [placeholder] of bel ons om contact op te nemen. Excuses voor het ongemak.";

// No name or email entered
$no_name_or_email = 'Dit bericht kan niet worden verzonden zonder naam of email adres. Vul deze in alvorens u op verzenden klikt.';


///////////////////////////
//Contact Form Processing//
///////////////////////////
$errors = array();
if(isset($_POST['message']) and isset($_POST['name']) and isset($_POST['captcha'])) {
	if(!empty($_POST['name']) and !empty($_POST['lastname']))
		$sender_name  = stripslashes(strip_tags(trim($_POST['name']))).' '.stripslashes(strip_tags(trim($_POST['lastname'])));
	
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
			echo responseObject(true, $email_was_sent);
			//echo $email_was_sent;
		} else {
			$errors[] = $server_not_configured;
			echo responseObject(false, $errors);
			//echo implode('<br>', $errors );
		}
	} else {
		echo implode('<br>', $errors );
		echo responseObject(false, $errors);
	}
} else {
	// if "name" or "message" vars not send ('name' attribute of contact form input fields was changed)
	echo responseObject(false, $no_name_or_email);
	//echo '"name" and "message" variables were not received by server. Please check "name" attributes for your input fields';
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

function responseObject($success, $errors) {
	$res = array('success' => $success, 'errors' => $errors);
	return json_encode($res);
}
?>