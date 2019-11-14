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
				<div id="loading" class="alert alert-primary d-block">
					<i class="fas fa-pulse fa-spinner"></i> <?php echo $translator->getTranslatedString("loading"); ?>&hellip;
				</div>
				<div class="alert alert-secondary">
					<span id="localeLink">
						<img class="flag-icon" src="<?php echo $translator->getTranslatedString("imgFlag"); ?>" alt="" /> <?php echo $translator->getTranslatedString("changeLocale"); ?>
					</span>
					<button id="localeDismiss" type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div id="gasData" class="d-none">
					<div class="row">
						<div class="col-md-6">
							<form name="currencyForm">
								<label for="currency"><i class="fas fa-money-bill-wave"></i> <?php echo $translator->getTranslatedString("currency"); ?></label><br />
								<div class="form-check form-check-inline">
									<input id="dollar" class="form-check-input" type="radio" name="currency" value="dollar" />
									<label class="form-check-label" for="dollar"><?php echo $translator->getTranslatedString("dollars"); ?></label>
								</div>
								<div class="form-check form-check-inline">
									<input id="peso" class="form-check-input" type="radio" name="currency" value="peso" checked />
									<label class="form-check-label" for="peso"><?php echo $translator->getTranslatedString("pesos"); ?></label>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<p>
								<em><?php echo $translator->getTranslatedString("lastUpdated"); ?> <span id="timestamp"></span></em><br />
								<em><?php echo $translator->getTranslatedString("exchangeRate"); ?> 1 USD = <span id="exchangeRate"></span> MXN</em>
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<form name="municipioForm">
								<label for="municipio"><i class="fas fa-city"></i> <?php echo $translator->getTranslatedString("municipio"); ?></label><br />
								<div class="form-check form-check-inline">
									<input id="ensenada" class="form-check-input" type="checkbox" name="municipio" value="Ensenada" checked>
									<label for="ensenada">Ensenada</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="mexicali" class="form-check-input" type="checkbox" name="municipio" value="Mexicali" checked>
									<label for="mexicali">Mexicali</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="rosarito" class="form-check-input" type="checkbox" name="municipio" value="Playas de Rosarito" checked>
									<label for="rosarito">Playas de Rosarito</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="sanDiego" class="form-check-input" type="checkbox" name="municipio" value="San Diego" checked>
									<label for="sanDiego">San Diego</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="tecate" class="form-check-input" type="checkbox" name="municipio" value="Tecate" checked>
									<label for="tecate">Tecate</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="tijuana" class="form-check-input" type="checkbox" name="municipio" value="Tijuana" checked>
									<label for="tijuana">Tijuana</label>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<form name="gasTypeForm">
								<label for="municipio"><i class="fas fa-oil-can"></i> <?php echo $translator->getTranslatedString("gasType"); ?></label><br />
								<div class="form-check form-check-inline">
									<input id="diesel" class="form-check-input" type="checkbox" name="municipio" value="Diesel" checked>
									<label for="diesel">Diesel</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="premium" class="form-check-input" type="checkbox" name="municipio" value="Premium" checked>
									<label for="premium">Premium</label>
								</div>
								<div class="form-check form-check-inline">
									<input id="regular" class="form-check-input" type="checkbox" name="municipio" value="Regular" checked>
									<label for="regular">Regular</label>
								</div>
							</form>
						</div>
					</div>
					<table class="table table-fluid table-striped">
						<thead>
							<tr>
								<th data-field-name="municipio"><i class="fas fa-sort"></i> <?php echo $translator->getTranslatedString("municipio"); ?></th>
								<th data-field-name="price"><i class="fas fa-sort"></i> <?php echo $translator->getTranslatedString("price"); ?></th>
								<th data-field-name="gasType"><i class="fas fa-sort"></i> <?php echo $translator->getTranslatedString("gasType"); ?></th>
							</tr>
						</thead>
						<tbody id="tableData"></tbody>
					</table>
				</div>
		</main>
	</body>
</html>