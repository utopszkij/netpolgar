<?php
   
error_reporting(E_ALL);
define('MYDOMAIN','http://robitc/netpolgar');
define('MYPATH','/var/www/html/netpolgar');
define('DEFLNG','hu');
function config(string $token) {
	$values = new stdClass();
	$values->TEMPLATE = 'default';
	$values->MYSQLHOST = 'localhost';
	$values->MYSQLUSER = 'root';
	$values->MYSQLPSW = '13Marika';
	$values->MYSQLDB = 'test';
	$values->MYSQLLOG = false;

	$values->falseLoginLimit = 10;  // hibás bejelentkezési kisérlet limit
	$values->blockExpired = 60000;  // user blokkolás lejárati ideje 10 óra
	
	$values->GITHUB_REPO = '';
	$values->GITHUB_USER = '';
	$values->GITHUB_PSW = '';
	
	$values->smtpHost = '';
	$values->smtpUser = '';
	$values->smtpPsw = '';
	$values->smtpSecure = 'tls';
	$values->smtpPort = '587';
	$values->smtpSender = '';
	
	$result = '';
	if (isset($values->$token)) {
		$result = $values->$token;	
	}
	return $result;
}

?>