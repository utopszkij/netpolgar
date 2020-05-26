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

$('#scope').append('<div id="formGroupForm"></div>'); // <form -al nem jó!!!!
$('#formGroupForm').append('<input id="id" />');
$('#formGroupForm').append('<input id="parent" />');
$('#formGroupForm').append('<input id="name" />');
$('#formGroupForm').append('<input id="description" />');
$('#formGroupForm').append('<input id="group_to_active" />');
$('#formGroupForm').append('<input id="group_to_close" />');
$('#formGroupForm').append('<input id="member_to_active" />');
$('#formGroupForm').append('<input id="member_to_exclude" />');
$('#formGroupForm').append('<input id="reg_mode" />');
$('#formGroupForm').append('<input id="state" />');
$('#formGroupForm').append('<input id="avatarUrl" />');
$('#formGroupForm').append('<button id="btnOK" type="button"></button>');
$('#formGroupForm').append('<button id="btnCancel" type="button"></button>');
$('#formGroupForm').append('<button id="btnLogin" type="button"></button>');
$('#formGroupForm').append('<button id="btnCandidate" type="button"></button>');
$('#formGroupForm').append('<button id="btnExit" type="button"></button>');
$('#formGroupForm').append('<button id="btnAdd" type="button"></button>');
$('#formGroupForm').append('<button id="btnRemove" type="button"></button>');
$('#formGroupForm').append('<button id="btnBack" type="button"></button>');

$('#scope').append('<div id="groupsList"></div>'); // <form -al nem jó!!!!

//define angularjs test enviroment
var $scope = {};
$scope.id = 0;
$scope.item = {};
$scope.item.id = 0;
$scope.item.avatarUrl = '';
$scope.item.reg_mode = '';
$scope.item.state = '';
$scope.item.cancelUrl = '';
$scope.userGroupAdmin = false;
$scope.userMember = false;
$scope.user = {};
$scope.user.id = 0;

//define multylanguage system test enviroment
$scope.txt = function(token) {return token};

//include js file for test (must pageOnLoad() root level function)
var fs = require('fs');
eval(fs.readFileSync('./js/groups.js')+'');


//run jquery pageOnload 
if (pageOnLoad != undefined) {
	pageOnLoad();
}	
// angular init
if (groupsFun != undefined) {
	groupsFun();
}	


// test cases
describe('groupsTest.js', function() {
	
	it('btnOkClick_allError', function() {
		global.alertTxt = '';
		$('#id').val('0');
		$('#name').val('');
		$('#description').val('');
		$('#group_to_active').val('x');
		$('#group_to_close').val('x');
		$('#member_to_active').val('x');
		$('#member_to_exclude').val('x');
		$('#reg_mode').val('self');
		$('#state').val('activr');
		$('#parent').val('0');
		$('#btnOK').click();
		assert.equal(global.alertTxt,
		'NAME_REQUED<br />DESCRIPTION_REQUED<br />'+
		'INVALID_NUMBER<br />INVALID_NUMBER<br />INVALID_NUMBER<br />INVALID_NUMBER<br />');
	});
	
	it('btnOkClick_ok', function() {
		global.alertTxt = '';
		$('#id').val('0');
		$('#name').val('name1');
		$('#description').val('desc1');
		$('#group_to_active').val('0');
		$('#group_to_close').val('0');
		$('#member_to_active').val('0');
		$('#member_to_exclude').val('0');
		$('#reg_mode').val('self');
		$('#state').val('activr');
		$('#parent').val('0');
		$('#btnOK').click();
		assert.equal(global.alertTxt,'');
	});
	
	it('btnCancelClick', function() {
		global.alertTxt = '';
		$('#btnCancel').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});

	it('btnBackClick', function() {
		global.alertTxt = '';
		$('#btnBack').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});

	it('btnLoginClick', function() {
		global.alertTxt = '';
		$('#btnLogin').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});

	it('btnCandidateClick', function() {
		global.alertTxt = '';
		$('#btnCandidate').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});
	
	it('btnExitClick', function() {
		global.alertTxt = '';
		$('#btnExit').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});
	
	it('btnRemoveClick', function() {
		global.alertTxt = '';
		$('#btnRemove').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});
	
	it('btnAddClick', function() {
		global.alertTxt = '';
		$('#btnAdd').click();
		assert.ok(true); // csak szintaktikai ellenörzés
	});
	
	it('groupAdmin', function() {
		$scope.userGroupAdmin = true;
		$scope.userMember = true;
		$scope.item.id = -1;
		$scope.item.state = 'closed';
		groupsFun();
	});
});
