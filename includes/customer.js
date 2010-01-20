function customerOrderTypeToggle(ordertypeField, form) {
	for (i=0;i<ordertypeField.length;i++){
		option=ordertypeField[i]
  		if (option.checked && option.value=='Quote'){
			setCC_DivVisibility(form, false);
		}
		if (option.checked && option.value=='Order'){
			setCC_DivVisibility(form, true);
		}
	}
}

function setCC_DivVisibility(form, vis){
//	alert("Set CC vis:" + vis);
	newVisibility='hidden'; newState='none';
	if(vis) {newVisibility='visible'; newState='block';}
	
	if (document.getElementById) { // DOM3 = IE5, NS6 
		document.getElementById('ccdata').style.visibility = newVisibility;
		} 
		else { 
		if (document.layers) { // Netscape 4 
		document.ccdata.visibility = newVisibility; 
		} 
		else { // IE 4 
		document.all.ccdata.style.visibility = newVisibility; 
		} 
		}
	if (document.all) { //IS IE 4 or 5 (or 6 beta) 
		eval( "document.all.ccdata.style.display = '"+newState+"'"); 
		} 
		if (document.layers) { //IS NETSCAPE 4 or below 
		document.layers['ccdata'].display = newState; 
		} 
		if (document.getElementById &&!document.all) { 
		hza = document.getElementById('ccdata'); 
		hza.style.display = newState; 
		} 
}
	
function test(){
	alert("Test");
}