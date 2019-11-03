<?php

require("./aaa-gas-price.php");
require("./mex-gas-stations.php");

try {
	$dsn = "mysql:dbname=" . $_ENV["MYSQL_DATABASE"] . ";host=mysql";
	$options = [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"];
	$pdo = new PDO($dsn, $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"]);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	downloadAaaGasPrice($pdo);
	importMexGasStations($pdo);
} catch (Exception $exception) {
	echo "Exception: " . $exception->getMessage() . PHP_EOL;
}