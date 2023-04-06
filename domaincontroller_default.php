<?php

use DD\Exceptions\ValidationException;
use DD\SystemType;

$host      = $_SERVER['HTTP_HOST'];
$hostFound = false;

define ("HOST", strtolower ($host));
const DOMAIN = "your-prod-domain";

if (str_contains (HOST, "test.".DOMAIN)) {

	ini_set ('display_errors', 1);
	ini_set ('display_startup_errors', 1);

	$hostFound = true;

	$systemType     = SystemType::TEST;
	$systemTypeName = "TEST";

	$dbHost = "";
	$dbName = "";
	$dbUser = "";
	$dbPass = "";

} else if (HOST == DOMAIN) {

	ini_set ('display_errors', 0);
	ini_set ('display_startup_errors', 0);

	$hostFound = true;

	$systemType     = SystemType::TEST;
	$systemTypeName = "TEST";

	$dbHost = "";
	$dbName = "";
	$dbUser = "";
	$dbPass = "";

} else {
	throw new ValidationException("No Host could be found for the given host: ".HOST);
}

define ("SYSTEMTYPE", $systemType);
define ("SYSTEMTYPE_NAME", $systemTypeName);

//DB
define ("DB_NAME", $dbName);
define ("DB_USER", $dbUser);
define ("DB_PASS", $dbPass);
define ("DB_HOST", $dbHost);

//SMTP
const SMTP_SERVER    = "your-smtp-server";
const SMTP_USER      = "your-smtp-user-name";
const SMTP_PASS      = "your-smtp-password";
const SMTP_AUTH      = true; //Change if needed but it is not recommended to use false
const SMTP_USE_TLS   = true; //Change if needed but it is not recommended to use false
const SMTP_DEBUG     = false; //Change if needed but it is not recommended to use true in PROD environments
const SMTP_PORT      = 587; //Change if needed but it is the standard port for SMTP using encryption
const SMTP_FROM_NAME = "your-name"; //that will be displayed in the from part as name
const SMTP_FROM      = "your-from-email-address"; //that will be displayed in the from part as email address
