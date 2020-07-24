  
  // params for controller	  
  // param1  requed	
  // sid string requed
  
  // jquery page onload --- must this function !

  // here $scope is not valid. 
  function pageOnLoad() {
  }	

  function frontpageFun() {
	  $('.carousel').carousel();
	  $('#scope').show();
	  
	  $( window ).scroll(function() {
		  console.log($(document).scrollTop());
		  if ($(document).scrollTop() > 10) {
			  $( "nav" ).css( "background-color", "red" );
		  } else {
			  $( "nav" ).css( "background-color", "transparent" );
		  }
		});
	  
	  return 'frontpage';
	  
  }
  
  // jquery pageOnLoad
  $(function() {
  	pageOnLoad();
  });	

  // angular pageOnLoad
  frontpageFun();
  
