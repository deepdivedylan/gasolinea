<?php
require_once(dirname(__DIR__, 2) . "/vendor/autoload.php");
require_once(dirname(__DIR__, 2) . "/php/lib/xsrf.php");
require_once(dirname(__DIR__, 2) . "/php/intl/Translator.php");

//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
try {
	//determine which HTTP method was used
	$method = $_SERVER["HTTP_X_HTTP_METHOD"] ?? $_SERVER["REQUEST_METHOD"];
	// handle GET request - just kick them out
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();
		header("Location: ..", true, 301);
	} else if($method === "POST") {
		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);
		$newLocale = $requestObject->locale;
		if(in_array($newLocale, Translator::ACCEPTED_LOCALES) === true) {
			$_SESSION["locale"] = $locale;
			setcookie("locale", $locale, time() + 2592000); // 30 day cookie
			$reply->message = "Locale updated OK";
		} else {
			throw(new InvalidArgumentException("locale not supported", 405));
		}
	} else {
		throw(new InvalidArgumentException("Invalid HTTP method request", 405));
	}
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}
// encode and return reply to front end caller
header("Content-type: application/json");
echo json_encode($reply);