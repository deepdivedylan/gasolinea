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
	// handle GET request
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();

		$dsn = "mysql:dbname=" . $_ENV["MYSQL_DATABASE"] . ";host=mysql";
		$options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
		$pdo = new PDO($dsn, $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"]);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$reply->data = new stdClass();
		$reply->data->prices = [];
		$query = "CALL getCurrentPrices(@currExchangeRate, @currDate)";
		$statement = $pdo->prepare($query);
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$statement->execute();

		while (($row = $statement->fetch()) !== false) {
			$price = (float)$row["price"];
			$municipio = $row["municipio"];
			$gasType = $row["gasType"];
			$result = (object)["gasType" => $gasType, "municipio" => $municipio, "price" => $price];
			$reply->data->prices[] = $result;
		}

		$query = "SELECT @currExchangeRate, @currDate";
		$statement = $pdo->prepare($query);
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		$statement->execute();
		$row = $statement->fetch();
		$exchangeRate = (float)$row["@currExchangeRate"];
		$timestamp = 1000 * (int)(DateTime::createFromFormat("Y-m-d H:i:s", $row["@currDate"] . " 00:00:00", new DateTimeZone("Etc/UTC"))->format("U"));
		$reply->data->exchangeRate = $exchangeRate;
		$reply->data->timestamp = $timestamp;
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
