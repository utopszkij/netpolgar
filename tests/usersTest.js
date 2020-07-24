var assert = require('assert');
const { JSDOM } = require('jsdom');
const jsdom = new JSDOM('<!doctype html><html><body></body></html>');
const { window } = jsdom;
const $ = global.jQuery = require('jquery')(window);

// mock document object and some browser function
var mock = require('./mock.js');
mock.init(window);

// params for controller	  
var param1  = 'TestParam';	

// create test html 
$('body').append('<div id="scope"></div>'); // <form -al nem jó!!!!
$('#scope').append('<div id="webRegForm"></div>'); // <form -al nem jó!!!!
$('#scope').append('<div id="ukloginRegForm"></div>'); // <form -al nem jó!!!!
$('#scope').append('<div id="registForm"></div>');
	$('#ukloginRegform').append('<iframe id="ifrmUklogin"></iframe>'); // <form -al nem jó!!!!
$('#webRegForm').append('<div id="formRegForm"></div>'); // <form -al nem jó!!!!
	$('#formRegForm').append('<img id="imgAvatar" />');
	$('#formRegForm').append('<select id="regmodSelect"></select>');
	$('#regmodSelect').append('<option value=""></option>');
	$('#regmodSelect').append('<option value="uklogin"></option>');
		$('#regmodSelect').append('<option value="web"></option>');
	$('#formRegForm').append('<input id="id" type="text" value="" />');
	$('#formRegForm').append('<input id="nick" type="text" value="" />');
	$('#formRegForm').append('<input id="psw" type="text" value="" />');
	$('#formRegForm').append('<input id="psw2" type="text" value="" />');
	$('#formRegForm').append('<input id="name" type="text" value="" />');
	$('#formRegForm').append('<input id="email" type="text" value="" />');
	$('#formRegForm').append('<input id="avatar" type="text" value="" />');
	$('#formRegForm').append('<input id="reg_mode" type="text" value="" />');
	$('#formRegForm').append('<button id="btnAvatarShow" type="button"></button>');
	$('#formRegForm').append('<button id="btnOk">OK</button>');
	$('#formRegForm').append('<button id="btnCancel">OK</button>');

//define angularjs test enviroment
var $scope = {};
$scope.avatarUrl = '';
$scope.id = 0;
$scope.reg_mode = '';
$scope.cancelUrl = '';

//define multylanguage system test enviroment
$scope.txt = function(token) {return token};

//include js file for test (must pageOnLoad() root level function)
var fs = require('fs');
eval(fs.readFileSync('./js/users.js')+'');


//run jquery pageOnload 
if (pageOnLoad != undefined) {
	pageOnLoad();
}	
// angular init
if (mainFun != undefined) {
	mainFun();
}	


// test cases
describe('usersTest.js', function() {
	
	it('regmodSelectChange_uklogin', function() {
		$('#regmodSelect').val('uklogin');
		$('#regmodSelect').change();
		assert.ok($scope.reg_mode == 'uklogin');
	});

	it('regmodSelectChange_web', function() {
		$('#regmodSelect').val('web');
		$('#regmodSelect').change();
		assert.ok($scope.reg_mode == 'web');
	});

	it('regmodSelectChange_empty', function() {
		$('#regmodSelect').val('');
		$('#regmodSelect').change();
		assert.ok(true);
	});
	
	it('avatarChange_empty', function() {
		$('#avatar').val('');
		$('#avatar').change();
		assert.ok($scope.avatarUrl == '');
	});
	
	it('avatarChange_notempty', function() {
		$('#avatar').val('123');
		$('#avatar').change();
		assert.ok($scope.avatarUrl == '123');
	});
	
	it('btnAvatarShow_click', function() {
		$('#btnAvatarShow').click();
		assert.ok(true);
	});

	it('btnCancel_click_emptyCancelUrl', function() {
		$('#btnCancel').click();
		assert.ok(true);
	});

	it('btnCancel_click_cancelUrl', function() {
		$scope.cancelUrl = '123';
		$('#btnCancel').click();
		assert.ok(true);
	});
	
	it('btnOk_click_ok', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('123456');
		$('#psw2').val('123456');
		$('#name').val('test user 1');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt,'');
	});
	
	it('btnOk_click_nickEmpty', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('');
		$('#psw').val('123456');
		$('#psw2').val('123456');
		$('#name').val('test user 1');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'NICK_REQUED<br />');
	});

	it('btnOk_click_name_empty', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('123456');
		$('#psw2').val('123456');
		$('#name').val('');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'NAME_REQUED<br />');
	});
	
	it('btnOk_click_psw_empty', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('');
		$('#psw2').val('');
		$('#name').val('test user 1');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'PSW_REQUED<br />');
	});
	
	it('btnOk_click_psw_sort', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('123');
		$('#psw2').val('123');
		$('#name').val('test user 1');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'PSWS_SORT<br />');
	});
	
	it('btnOk_click_psws_not_equals', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('123456');
		$('#psw2').val('123');
		$('#name').val('test user 1');
		$('#email').val('test1@gmail.hu');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'PSWS_NOT_EQUALS<br />');
	});
	
	it('btnOk_click_email_empty', function() {
		global.alertTxt = '';
		$('#id').val(0);
		$('#nick').val('test1');
		$('#psw').val('123456');
		$('#psw2').val('123456');
		$('#name').val('test user 1');
		$('#email').val('');
		$('#avatar').val('123');
		$('#reg_mode').val('web');
		$('#btnOk').click();
		assert.equal(global.alertTxt, 'EMAIL_REQUED<br />');
	});
		
});
