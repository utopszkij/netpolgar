  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function readmeFun() {
	  $('#scope').show();
	  return 'readme';
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  readmeFun();
  
