<?php 
//////////////////////////
//Specify default values//
//////////////////////////

//Your E-mail
$to_email = 'tjc.schipper@gmail.com';

//Internal contactform email 'sender'
$contact_form_email = 'info@mooibijtgoed.nl';

//Default Subject if 'subject' field not specified
$default_subject = 'Nieuw bericht van contactformulier';

//Message if 'name' field not specified
$name_not_specified = 'Om een bericht te versturen hebben wij in elk geval uw naam en email adres nodig. Vul deze in alvorens te verzenden.';

//Message if 'message' field not specified
$message_not_specified = 'Zonder berichttekst weten wij niet waarmee we u van dienst kunnen zijn. Vul deze in alvorens te verzenden.';

//Message if e-mail sent successfully
$email_was_sent = 'Bedankt, uw bericht is verzonden. Wij zullen spoedig contact met u opnemen.';

//Message if e-mail not sent (server not configured)
$server_not_configured = 'Er is een probleem met de mailservice. Email naar info@mooibijtgoed.nl of bel ons om contact op te nemen. Onze excuses voor het ongemak!';

// Message if captcha not verified
$invalid_captcha = "Het anti-spamfilter kon uw browser niet valideren. Email naar info@mooibijtgoed.nl of bel ons om contact op te nemen. Onze excuses voor het ongemak!";

// No name or email entered
$no_name_or_email = 'Dit bericht kan niet worden verzonden zonder naam of email adres. Vul deze in alvorens u op verzenden klikt.';


///////////////////////////
//Contact Form Processing//
///////////////////////////
$errors = array();
if(isset($_POST['message']) and isset($_POST['name']) and isset($_POST['captcha'])) {
	if(!empty($_POST['name']) and !empty($_POST['lastname']))
		$firstname		= stripslashes(strip_tags(trim($_POST['name'])));
	
	if (!empty($_POST['lastname']))
		$lastname		= stripslashes(strip_tags(trim($_POST['lastname'])));

	if (!empty($_POST['phone']))
		$phone			= stripslashes(strip_tags(trim($_POST['phone'])));

	if(!empty($_POST['message']))
		$message 		= stripslashes(strip_tags(trim($_POST['message'])));
	
	if(!empty($_POST['email']))
		$contact_email 	= stripslashes(strip_tags(trim($_POST['email'])));
	
	if (!empty($_POST['captcha']))
		$captcha 		= stripslashes(strip_tags(trim($_POST['captcha'])));


	// Verify captcha response
	$verification = json_decode(verifyCaptcha($captcha), true);
	if ($verification['success'] === FALSE) {
		$errors[] = $invalid_captcha;
	}

	// Compose contact name, accounting for empty fields
	$contact_name = ((isset($firstname)) ? $firstname : '').' '.((isset($lastname)) ? $lastname : '');

	//Error if no sender name was specified
	if(empty($contact_name)) {
		$errors[] = $name_not_specified;
	}

	//Error if no message was specified
	if(empty($message)) {
		$errors[] = $message_not_specified;
	}

	// Compose message body
	$body = '<html><body>';
	$body .= '<h1>Nieuw bericht van <i>'.$contact_name.'</i>.</h1>';
	if (!empty($message))
		$body .= wordwrap($message, 70);
	else
		$body .= '[GEEN BERICHT INGEVOERD]';
	$body .= '<br><br><h2>Contactinformatie</h2><ul>';
	$body .= '<li>Telefoon: '.((!empty($phone)) ? $phone : '[niet bekend]').'</li>';
	$body .= '<li>Email: '.((!empty($contact_email)) ? $contact_email : '[niet bekend]').'</li>';
	$body .= '</ul></body></html>';

	// Configure headers
	$headers = "From: ".$contact_form_email."\r\n";	//From MUST be from own domain! (spam protection) Use reply-to instead.
	$headers .=	((!empty($contact_email)) ? 'Reply-to: '.$contact_email : '')."\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	//sending message if no errors
	if(empty($errors)) {
		if (mail($to_email, $default_subject, $body, $headers)) {
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