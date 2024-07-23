function toogleForm(obj) {
	document.getElementById(obj).classList.toggle(obj + "_show");  
}

function closeForm(obj) {
  document.getElementById(obj).classList.remove(obj + "_show");
  window.location.href = "liste.php";
}


// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
} 
