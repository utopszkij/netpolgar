  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function adatkezelesFun() {
	  $('#scope').show();
	  return 'adatkezeles';
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  adatkezelesFun();
  
