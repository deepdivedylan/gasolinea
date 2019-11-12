<?php
require(dirname(__DIR__, 2) . "/vendor/autoload.php");

function getLatestExchangeRate(\PDO $pdo) {
	$url = "http://data.fixer.io/api/latest?access_key=" . $_ENV["FIXER_API_KEY"] . "&symbols=MXN,USD";
	$client = new GuzzleHttp\Client();
	$result = $client->get($url);
	$status = $result->getStatusCode();

	if ($status >= 200 && $status < 400) {
		$json = $result->getBody();
		$reply = json_decode($json);
		if ($reply->success) {
			$eurToMxn = (float)$reply->rates->MXN;
			$eurToUsd = (float)$reply->rates->USD;
			$usdToMxn = $eurToMxn / $eurToUsd;
			$date = DateTime::createFromFormat("U", $reply->timestamp);
			$formattedDate = $date->format("Y-m-d H:i:s");

			try {
				$query = "INSERT INTO exchangeRate(exchangeDate, exchangeRate) VALUES(:exchangeDate, :exchangeRate)";
				$statement = $pdo->prepare($query);
				$statement->execute(["exchangeDate" => $formattedDate, "exchangeRate" => $usdToMxn]);
			} catch (Exception $exception) {
				echo "Exception: " . $exception->getMessage() . PHP_EOL;
			}
		}
	}
}
