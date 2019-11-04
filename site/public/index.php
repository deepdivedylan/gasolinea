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
		<title><?php echo $translator->getTranslatedString("title"); ?></title>
	</head>
	<body>
		<h1><?php echo $translator->getTranslatedString("title"); ?></h1>
	</body>
</html>