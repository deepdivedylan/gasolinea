<?php
require("./aaa-gas-price.php");
require("./exchange-rate.php");
require("./mex-gas-stations.php");

try {
	if ($argc !== 2) {
		throw(new RuntimeException("usage: " . $argv[0] . " <IP Address>"));
	}
	$ipAddress = $argv[1];
	if (@inet_pton($ipAddress) === false) {
		throw(new RuntimeException("invalid IP address"));
	}

	$dsn = "mysql:dbname=" . $_ENV["MYSQL_DATABASE"] . ";host=$ipAddress";
	$options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
	$pdo = new PDO($dsn, $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"]);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	downloadAaaGasPrice($pdo);
	importMexGasStations($pdo);
	getLatestExchangeRate($pdo);
} catch (Exception $exception) {
	echo "Exception: " . $exception->getMessage() . PHP_EOL;
}