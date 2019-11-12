<?php
require_once(dirname(__DIR__) . "/php/intl/Translator.php");

if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

if (empty($_SESSION["locale"]) === true) {
	$locale = Translator::getLocale();
	$_SESSION["locale"] = $locale;
	setcookie("locale", $locale, time() + 2592000); // 30 day cookie
} else {
	$locale = $_SESSION["locale"];
}
$translator = new Translator($locale);
?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo $translator->getTranslatedString("title"); ?></title>
	</head>
	<body>
		<main class="container">
			<h1><i class="fas fa-gas-pump"></i> <?php echo $translator->getTranslatedString("title"); ?></h1>
			<main class="container">
				<div>
					<h2 id="loading">Loading</h2>
					<div id="gasData">
						<div class="row">
							<div class="col-md-6">
								<form name="currencyForm">
									<label for="currency"><i class="fas fa-money-bill-wave"></i> Currency</label><br />
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="currency" value="dollar" />
										<label class="form-check-label" for="dollar">Dollars</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input" type="radio" name="currency" value="peso" checked />
										<label class="form-check-label" for="peso">Pesos</label>
									</div>
								</form>
							</div>
							<div class="col-md-6">
								<p>
									<em>Last updated: <span id="timestamp"></span></em><br />
									<em>Exchange Rate: 1 USD = <span id="exchangeRate"></span> MXN</em>
								</p>
							</div>
							<table class="table table-fluid table-striped">
								<thead>
									<tr>
										<th data-field-name="municipio"><i class="fas fa-sort-amount-down-alt"></i> Municipio</th>
										<th data-field-name="price"><i class="fas fa-sort-amount-down-alt"></i> Price</th>
										<th data-field-name="gasType"><i class="fas fa-sort-amount-down-alt"></i> Gas Type</th>
									</tr>
								</thead>
								<tbody id="tableData"></tbody>
							</table>
						</div>
					</div>
			</main>
		</main>
	</body>
</html>