<?php
require_once(dirname(__DIR__) . "/vendor/autoload.php");

function parseMunicipios(string $kmlFile, PDO $pdo) : void {
	try {
		$kml = file_get_contents($kmlFile);
		$geometry = geoPHP::load($kml, "kml");
		$components = $geometry->getComponents();
		$pdo->query("SET FOREIGN_KEY_CHECKS = 0;");
		$pdo->query("TRUNCATE municipio");
		$pdo->query("SET FOREIGN_KEY_CHECKS = 1;");

		$xml = new SimpleXMLElement($kml);
		$names = [];
		foreach ($xml->Folder->Document->Folder->Placemark as $placemark) {
			$betterName = ucwords(strtolower(substr($placemark->name, 0, -6)));
			$names[] = $betterName;
		}

		$i = 0;
		$query = "INSERT INTO municipio(name, shape) VALUES(:name, ST_GeomFromText(:shape))";
		$statement = $pdo->prepare($query);
		foreach ($components as $component) {
			$type = get_class($component) . PHP_EOL;
			if (trim($type) === "Polygon") {
				$name = $names[$i++];
				$statement->execute(["name" => $name, "shape" => $component->out("wkt")]);

				// Ensenada is in three polygons: one huge and then two small
				// using the huge one isn't perfect but it's good enough for government work
				if ($name === "Ensenada") {
					break;
				}
			}
		}
	} catch (Exception $exception) {
		echo "Exception: " . $exception->getMessage() . PHP_EOL;
	}
}
