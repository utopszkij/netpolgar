<?php

/*
if(!isset($_SERVER['HTTPS'])) {
    header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit();
}
*/

function clean_input($input) {
	$input=preg_replace('/[^-a-zA-Z0-9()@:%_\+.~#?&\/=]*/', '', strip_tags(trim($input)));
	
	return $input;
}



// get page

if(isset($_GET['pg'])) {
	$page=clean_input($_GET['pg']);
}
else {
	$page='frontpage';
}

// get header templates

include('templates/modern/templates/header.php');
?>
<body>
<div ng-app="myApp" ng-controller="myCtrl">
<?php
include('templates/modern/templates/menu.php');

// get site templates

if($page=='frontpage') {
	include('templates/modern/templates/'.$page.'/frontpage.php');
}

if($page=='csoportok') {
	include('templates/modern/templates/'.$page.'/header.php');
	include('templates/modern/templates/'.$page.'/csoportlista.php');
}

if($page=='csoport') {
	include('templates/modern/templates/'.$page.'/header.php');
	include('templates/modern/templates/'.$page.'/menu.php');
	include('templates/modern/templates/'.$page.'/tulajdonsagok.php');
	include('templates/modern/templates/'.$page.'/gombok.php');
}

// get footer template
include('templates/modern/templates/footer.php');

?>
