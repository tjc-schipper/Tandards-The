<?php

echo json_encode(array('success' => '123'));

/*$errors = array();
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
		$errors[] = $invalid_captcha;
	}

	// Compose contact name, accounting for empty fields
	$contact_name = ((isset($firstname)) ? $firstname : '').' '.((isset($lastname)) ? $lastname : '');
	$full_address_line = (isset($address) ? $address : '')
	.(isset($housenr) ? " $housenr" : '')
	.(isset($addition) ? " $addition" : '')
	.','
	.(isset($postcode) ? " $postcode": '')
	.(isset($city) ? " $city" : '';


	// Error if no sender name was specified
		if(empty($contact_name)) {
			$errors[] = $name_not_specified;
		}

	//Error if no message was specified
		if(empty($message)) {
			$errors[] = $message_not_specified;
		}

	// Error if no contact information was supplied
		if(empty($email) && empty($phone)) {
			$errors[] = $no_contact_info;
		}

echo json_encode(array(
	"name" => $contact_name,
	"address" => $full_address_line,
	"message" => $message,
	"email" => $email,
	"phone" => $phone,
	"date" => $birthdate,
	"captcha" => $captcha
	));
*/
?>