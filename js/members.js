  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function membersFun() {
	  $('#scope').show();
	  return 'members';
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  membersFun();
  
