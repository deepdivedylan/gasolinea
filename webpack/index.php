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
			<h1><?php echo $translator->getTranslatedString("title"); ?></h1>
		</main>
	</body>
</html>