<?php
require_once(dirname(__DIR__) . "/vendor/autoload.php");

function downloadAaaGasPrice(\PDO $pdo) {
	$priceMap = [1, null, 2, 3];
	$prices = [1 => 0.0, 2 => 0.0, 3 => 0.0];
	$url = "https://gasprices.aaa.com/?state=CA";
	$client = new GuzzleHttp\Client();
	$result = $client->get($url);
	$status = $result->getStatusCode();

	if ($status >= 200 && $status < 400) {
		$html = $result->getBody();
		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$h3Tags = $dom->getElementsByTagName("h3");
		foreach ($h3Tags as $h3Tag) {
			if ($h3Tag->nodeValue === "San Diego") {
				$numPrices = 0;
				$nextDiv = $h3Tag->nextSibling->nextSibling;
				$table = $nextDiv->childNodes[1];
				$priceRow = $table->getElementsByTagName("tr")[1];
				foreach ($priceRow->childNodes as $childNode) {
					$tagName = $childNode->tagName ?? null;
					if ($tagName === "td") {
						preg_match("/\\\$(\d\.\d{3})/", $childNode->nodeValue, $matches);
						$price = $matches[1] ?? 0.0;
						$priceIndex = $priceMap[$numPrices];
						if ($price > 0.0) {
							if ($priceIndex !== null) {
								$prices[$priceIndex] = (float)$price;
							}
							$numPrices++;
						}
					}
				}
				break;
			}
		}
	}

	$hasValidPrices = array_reduce($prices, function ($previous, $price) {
		return $previous && is_numeric($price) === true && $price > 0.0;
	}, true);
	if ($hasValidPrices) {
		$today = gmdate("Y-m-d");
		$sql = "INSERT INTO usaGasPrice(priceDate, price, gasTypeId) VALUES(:priceDate, :price, :gasTypeId)";
		$statement = $pdo->prepare($sql);
		array_walk($prices, function ($price, $gasTypeId) use ($statement, $today) {
			$statement->execute(["priceDate" => $today, "price" => $price, "gasTypeId" => $gasTypeId]);
		});
	}
}
