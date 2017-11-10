<?php 
//////////////////////////
//Specify default values//
//////////////////////////




//Your E-mail
$to_email = 'info@mooibijtgoed.nl';

//Internal contactform email 'sender'
$contact_form_email = 'contactformulier@mooibijtgoed.nl';	// WERKT DIT? Dit email adres bestaat niet. Anders komt er altijd "From:me" te staan, verwarrend.

//Default Subject if 'subject' field not specified
$default_subject = 'Nieuw bericht van contactformulier';

//Message if 'name' field not specified
$name_not_specified = 'Om een bericht te versturen hebben wij in elk geval uw naam en email adres nodig. Vul deze in alvorens te verzenden.';

//Message if 'message' field not specified
$message_not_specified = 'Zonder berichttekst weten wij niet waarmee we u van dienst kunnen zijn. Vul deze in alvorens te verzenden.';

//Message if no contact information supplied
$no_contact_info = 'Zonder contactinformatie (telefoonnummer of email adres) kunnen wij u niet meer bereiken. Vul deze in alvorens te verzenden.';

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
	
	if (!empty($_POST['birthdate']))
		$birthdate		= stripslashes(strip_tags(trim($_POST['birthdate'])));

	if (!empty($_POST['address']))
		$address		= stripslashes(strip_tags(trim($_POST['address'])));

	if (!empty($_POST['housenr']))
		$housenr		= stripslashes(strip_tags(trim($_POST['housenr'])));

	if (!empty($_POST['addition']))
		$addition		= stripslashes(strip_tags(trim($_POST['addition'])));

	if (!empty($_POST['postcode']))
		$postcode		= stripslashes(strip_tags(trim($_POST['postcode'])));

	if (!empty($_POST['city']))
		$city		= stripslashes(strip_tags(trim($_POST['city'])));

	if (!empty($_POST['captcha']))
		$captcha 		= stripslashes(strip_tags(trim($_POST['captcha'])));

	
	// Verify captcha response
	$verification = json_decode(verifyCaptcha($captcha), true);
	if ($verification['success'] === FALSE) {
		array_push($errors, $invalid_captcha);
	}

	// Compose contact name, accounting for empty fields
	$contact_name = ((isset($firstname)) ? $firstname : '').' '.((isset($lastname)) ? $lastname : '');
	$full_address_line = (isset($address) ? $address : '')
		.(isset($housenr) ? " $housenr" : '')
		.(isset($addition) ? " $addition" : '')
		.','
		.(isset($postcode) ? " $postcode": '')
		.(isset($city) ? " $city" : '');


	// Error if no sender name was specified
	if(empty($contact_name)) {
		array_push($errors, $name_not_specified);
	}

	//Error if no message was specified
	if(empty($message)) {
		array_push($errors, $message_not_specified);
	}

	// Error if no contact information was supplied
	if(empty($contact_email ) && empty($phone)) {
		array_push($errors, $no_contact_info);
	}

	

	// Compose message body
	$body = '<html><body>';
	$body .= '<h2>Nieuw bericht van <i>'.$contact_name.'</i>.</h2>';
	$body .= '<h4>Verstuurd op '.date('d-m-Y').' om '.date('g:iA').'</h4>';

	if (!empty($message))
		$body .= wordwrap($message, 70);
	else
		$body .= '[GEEN BERICHT INGEVOERD]';

	$body .= '<br><br><h2>Contactinformatie</h2><ul>';
	$body .= '<li>Telefoon: '.issetor($phone).'</li>';
	$body .= '<li>Email: '.issetor($contact_email).'</li>';
	$body .= '<li>Adres: '.issetor($full_address_line).'</li>';
	$body .= '<li>Geboortedatum: '.issetor($birthdate).'</li>';
	$body .= '</ul></body></html>';

	// Configure headers
	$headers = "From: ".$contact_form_email."\r\n";	//From MUST be from own domain! (spam protection) Use reply-to instead.
	$headers .=	((!empty($contact_email)) ? 'Reply-to: '.$contact_email : '')."\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	//sending message if no errors
	if(empty($errors)) {
		if (mail($to_email, $default_subject, $body, $headers)) {
			echo responseObject(true, $email_was_sent);
		} else {
			array_push($errors, $server_not_configured);
			echo responseObject(false, $errors);
		}
	} else {
		echo responseObject(false, $errors);
	}
} else {
	echo responseObject(false, $no_name_or_email);
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

function issetor(&$ref, $def="Onbekend") {
	return (isset($ref) && !empty($ref)) ? $ref : $def;
}

?>