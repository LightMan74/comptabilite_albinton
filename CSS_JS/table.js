
function searchtable() {
	var index ;
	index = document.getElementById("columid").value;
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("searchtable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[index];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }

table = document.getElementsByName("nosearchtable");
for (j = 0; j < table.length; j++) { 

tr = table[j].getElementsByTagName("tr");
for (i = 0; i < tr.length; i++) { 
        tr[i].style.display = "" ;
  }

  }
}
