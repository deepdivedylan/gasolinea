<?php
require_once(dirname(__DIR__) . "/vendor/autoload.php");

function importMexGasStations(PDO $pdo) : void {
	try {
		$placesUrl = "https://publicacionexterna.azurewebsites.net/publicaciones/places";
		$pricesUrl = "https://publicacionexterna.azurewebsites.net/publicaciones/prices";

		$placeClient = new GuzzleHttp\Client();
		$priceClient = new GuzzleHttp\Client();
		$placesResult = $placeClient->get($placesUrl);
		$pricesResult = $priceClient->get($pricesUrl);
		if ($placesResult->getStatusCode() < 200 || $placesResult->getStatusCode() >= 400) {
			throw(new RuntimeException("unable to download places", $placesResult->getStatusCode()));
		}
		if ($pricesResult->getStatusCode() < 200 || $pricesResult->getStatusCode() >= 400) {
			throw(new RuntimeException("unable to download prices", $pricesResult->getStatusCode()));
		}

		$placesData = $placesResult->getBody();
		$pricesData = $pricesResult->getBody();
		$placesXml = new SimpleXMLElement($placesData);
		$pricesXml = new SimpleXMLElement($pricesData);

		$pdo->query("SET FOREIGN_KEY_CHECKS = 0;");
		$pdo->query("TRUNCATE mexGasPrice");
		$pdo->query("TRUNCATE mexGasStation");
		$pdo->query("SET FOREIGN_KEY_CHECKS = 1;");

		$gasTypes = [];
		$query = "SELECT gasTypeId, name FROM gasType";
		$statement = $pdo->prepare($query);
		$statement->setFetchMode(PDO::FETCH_ASSOC);
		$statement->execute();
		while (($row = $statement->fetch()) !== false) {
			$gasTypes[$row["name"]] = $row["gasTypeId"];
		}

		$query = "CALL createGasolinera(:gasPlaceId, :gasName, POINT(:gasLocationX, :gasLocationY))";
		$statement = $pdo->prepare($query);
		foreach ($placesXml as $place) {
			$argv = ["gasPlaceId" => (int)$place->attributes()[0], "gasName" => (string)$place->name, "gasLocationX" => (float)$place->location->x, "gasLocationY" => (float)$place->location->y];
			$statement->execute($argv);
		}

		$query = "CALL addPrice(:gasTypeId, :placeId, :price)";
		$statement = $pdo->prepare($query);
		foreach ($pricesXml as $place) {
			$placeId = (int)$place->attributes()[0];
			foreach ($place->gas_price as $gasPrice) {
				$gasType = (string)$gasPrice->attributes()[0];
				$price = (float)$gasPrice;
				$argv = ["gasTypeId" => (int)$gasTypes[ucfirst($gasType)], "placeId" => $placeId, "price" => $price];
				$statement->execute($argv);
			}
		}

	} catch (Exception $exception) {
		echo "Exception: " . $exception->getMessage() . PHP_EOL;
	}
}
