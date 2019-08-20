<?php
require_once(dirname(__DIR__) . "/vendor/autoload.php");

function downloadAaaGasPrice() {
	$price = 0.0;
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
				foreach ($h3Tag->attributes as $attribute) {
					if ($attribute->name === "data-cost") {
						$price = floatval($attribute->value);
						break;
					}
				}
				break;
			}
		}
	}

	return $price;
}
