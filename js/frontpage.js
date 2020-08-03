  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function frontpageFun() {
	  $('#scope').show();
	  
	  $( window ).scroll(function() {
		  if ($(document).scrollTop() > 10) {
			  $( "nav" ).css( "background-color", "red" );
		  } else {
			  $( "nav" ).css( "background-color", "transparent" );
		  }
		});
	  return '';

  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  frontpageFun();
  
  // slider automatikus inditása
  window.setInterval("$('.carousel-control-next').click();",7000);
