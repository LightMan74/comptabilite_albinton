// vanilla JavaScript
document.addEventListener("DOMContentLoaded", function() {

	document.querySelector('.hamburger').addEventListener("click", function() {
		this.classList.toggle("hamburger--active");
		document.querySelector(".nav-fullscreen").classList.toggle("nav-fullscreen--open");
	});
	
});

// jQuery
/*$(document).ready(function() {

    $('.nav-toggle').on('click', function(){
			$(this).toggleClass('nav-toggle--open');
			$(".nav-fullscreen").toggleClass("nav-fullscreen--open");
		});

});
*/