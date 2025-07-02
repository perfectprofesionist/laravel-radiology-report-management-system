
$(document).ready(function(){
	$(".faciliator-list-grid a").click(function(){
	  $(".faciliator-list-grid").removeClass("Isactive");
	  $(this).parent().addClass("Isactive");
	});
	  
	  //MOBILE-MENU	
	jQuery("button.navbar-toggle").click(function(){
		jQuery("body").toggleClass("nav-open");
		jQuery("html").toggleClass("overflow-hidden");
	  });
	  
	 //FILTER 
		jQuery(".faciliator-filter-grid a").click(function(e){
			 e.stopPropagation();
		  jQuery(".faciliator-filter-grid").removeClass("Isactive");
		  jQuery(this).parent().addClass("Isactive");

		});
		jQuery(".faciliator-filter-grid-view").click(function(e){
				 e.stopPropagation();
			});
			
			jQuery(window).click(function () {

			if(jQuery('.faciliator-filter-grid-view').is(':visible')){
			  jQuery('.faciliator-filter-grid').removeClass('Isactive');
			}
		  })
		
	//FILTER-2 
	
	//SCROLL	
		$(".content").mCustomScrollbar({
			theme:"minimal"
		});
	//DATEPIKER
		$( function() {
			$( ".datepicker" ).datepicker();
		} );

$('body').on('click','#unreadToggle', function () {
    $(this).toggleClass('active');
    if ($(this).hasClass('active')) {
      console.log('Show only unread messages');
      // Add your filter logic here
    } else {
      console.log('Show all messages');
      // Reset or show all messages here
    }
  });



	  
});

function showFileName(input) {
      const file = input.files[0];
      document.getElementById('fileName').textContent = file ? file.name : '';
    }


// password
function togglePassword() {
    const pwdInput = document.getElementById('password');
    pwdInput.type = pwdInput.type === 'password' ? 'text' : 'password';
}


	
	
	
