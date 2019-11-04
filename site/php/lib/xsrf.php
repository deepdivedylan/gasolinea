<?php
/**
 * sets an XSRF cookie, generating one if necessary
 *
 * @param string $cookiePath path the cookie is relevant to, root by default
 * @throws RuntimeException if the session is not active
 * @throws Exception if random bytes cannot be generated
 **/
function setXsrfCookie($cookiePath = "/") {
	// enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}
	// if the token does not exist, create one and send it in a cookie
	if(empty($_SESSION["XSRF-TOKEN"]) === true) {
		$_SESSION["XSRF-TOKEN"] = hash("sha512", session_id() . bin2hex(random_bytes(16)));
	}
	setcookie("XSRF-TOKEN", $_SESSION["XSRF-TOKEN"], 0, $cookiePath);
}
/**
 * verifies the X-XSRF-TOKEN sent by Angular matches the XSRF-TOKEN saved in this session.
 * This function returns nothing, but will throw an exception when something does not match
 *
 * @see https://angular.io/guide/http Angular HttpClient documentation
 * @throws InvalidArgumentException when tokens do not match
 * @throws RuntimeException if the session is not active
 **/
function verifyXsrf() {
	// enforce that the session is active
	if(session_status() !== PHP_SESSION_ACTIVE) {
		throw(new RuntimeException("session not active"));
	}
	// grab the XSRF token sent by Angular, jQuery, or JavaScript in the header
	$headers = array_change_key_case(getallheaders(), CASE_UPPER);
	if(array_key_exists("X-XSRF-TOKEN", $headers) === false) {
		throw(new InvalidArgumentException("invalid XSRF token", 401));
	}
	$angularHeader = $headers["X-XSRF-TOKEN"];
	// compare the XSRF token from the header with the correct token in the session
	$correctHeader = $_SESSION["XSRF-TOKEN"];
	if($angularHeader !== $correctHeader) {
		throw(new InvalidArgumentException("invalid XSRF token", 401));
	}
}