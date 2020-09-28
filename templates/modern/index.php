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
	$page='index';
}

// echot html header, replace {{..}} variables
$lines = file('htmlhead.html');
foreach ($lines as $s) {
    $s = str_replace('{{MYDOMAIN}}','.',$s);
    $s = str_replace('{{TEMPLATEURL}}','.',$s);
    echo $s;
}

?>
<body>
<div ng-app="myApp" ng-controller="myCtrl" id="scope">
<?php
include('templates/menu.php');

// get site templates

if($page=='index') {
    include "templates/frontpage/header_slider.php";
    include "templates/frontpage/kiemelt_projektek.php";
    include "templates/frontpage/kiemelt_cikk.php";
    include "templates/frontpage/legujabb_csoportok.php";
    include "templates/frontpage/ujdonsagok_a_piacteren.php";
    include "templates/frontpage/lejaro_szavazas.php";
    include "templates/frontpage/kozelgo_esemenyek.php";
    include "templates/frontpage/legujabb_tagjaink.php";
    include "templates/frontpage/ajanlott_oldalak.php";
    ?>
   <script>
	function pageInit($scope) {
			// AngularJS page init , mindegyik képernyöhöz kell egy ilyen teszt adatok a képernyőre
			
			// be van jelentkezve:
			$scope.loggedUser = {"id":1, "nick":"Test Elek", "avatar":"//gravatar.com/avatar/123456"};
			
			// nincs bejelentkezve
			// $scope.loggedUser = {"id":0, "nick":"Guest", "avatar":""};
			
		}   
   </script>
   <?php 
}

if($page=='csoportok') {
	include('templates/formtitle.php');
	include('templates/'.$page.'/csoportlista.php');
	?>
	<script>
		function pageInit($scope) {
			$scope.formTitle = 'Csoportok';
		}
	</script>
	<?php 
}

if($page=='csoport') {
	include('templates/formtitle.php');
	include('templates/'.$page.'/menu.php');
	include('templates/'.$page.'/tulajdonsagok.php');
	include('templates/'.$page.'/gombok.php');
	?>
	<script>
		function pageInit($scope) {
			$scope.loggedUser = {"id":1, "nick":"test elek", "avatar":"https://gravatar.com/avatar/123456"};
			           // $scope.loggedUser.id = 0 ha nincs bejelentkezve
			$scope.msgs = ['hiabüzenet'];
			$scope.msgClass = 'danger';
			$scope.parents = [{"id":2,"name":"csoport2"},{"id":3,"name":"csoport3"}]; 
			$scope.item = {};
			$scope.item.id = 1;
			$scope.item.name = 'próba csoport';
			$scope.item.state='active';
			$scope.item.description='próba csoport leírása\nmásodik sor';
			$scope.item.group_to_active = 1;
			$scope.item.group_to_close = 2;
			$scope.item.member_to_active = 3;
			$scope.item.member_to_exclude = 4;
			$scope.item.avatar='http://valami/valami.png';
			$scope.userState = 'active'; // 'none' ha not legged, vagy a logged user nem tag
			$scope.formTitle = $scope.item.name;
			$scope.commentCount = {"total":10,"new":5};
			$scope.pollCount = {"total":11,"new":6};
			$scope.eventCount = {"total":12,"new":7};
			$scope.messageCount = {"total":7,"new":3};
		}
	</script>
	<?php 
}

// get footer template
include('templates/footer.php');

?>
</div><!--  #scope -->
<script src="https://code.angularjs.org/1.7.8/angular.js"></script>
<script>
	angular.module('myApp',[]).controller('myCtrl', function($scope) {
		  $scope.MYDOMAIN= './';
		  $scope.TEMPLATEURL= './';
		  $scope.txt = function(s) {return s};
		  pageInit($scope);
	});
</script>
</body>
</html>