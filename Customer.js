function getXmlHttpObject()
{
var xmlHttp
try   {  xmlHttp=new XMLHttpRequest();  }  // Firefox, Opera 8.0+, Safari
catch (e){  // Internet Explorer
  try    {    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");    }
  catch (e) {
    try      {      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");      }
    catch (e)      {      alert("Your browser does not support AJAX!");      xmlHttp=null;      }
    }
}
return xmlHttp;
}


function dumpProps(obj, parent) {
   // Go through all the properties of the passed-in object
   for (var i in obj) {
      // if a parent (2nd parameter) was passed in, then use that to
      // build the message. Message includes i (the object's property name)
      // then the object's property value on a new line
      if (parent) { var msg = parent + "." + i + "\n" + obj[i]; } else { var msg = i + "\n" + obj[i]; }
      // Display the message. If the user clicks "OK", then continue. If they
      // click "CANCEL" then quit this level of recursion
      if (!confirm(msg)) { return; }
      // If this property (i) is an object, then recursively process the object
      if (typeof obj[i] == "object") {
         if (parent) { dumpProps(obj[i], parent + "." + i); } else { dumpProps(obj[i], i); }
      }
   }
}

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

function checkCountry(field){
//	if(document.customerCatalogRequest.zip.value == '') return false;
	
	fetchURL="checkCountry.php?country=" + escape(document.customerCatalogRequest.country.value) + "&zip="+ escape(document.customerCatalogRequest.zip.value);
//	alert(fetchURL);

	xmlHttp=getXmlHttpObject()
	if(!xmlHttp) alert("No xmlHttp object??");
	xmlHttp.open("GET",fetchURL,true);
	xmlHttp.send(null);
	
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4)
		{
			var local=new Function("return "+xmlHttp.responseText)();
			if(xmlHttp.responseText == 'false'){ // hide CCInfo
				setCC_DivVisibility(document.customerCatalogRequest,false);
			} else{ // show CC Info
				setCC_DivVisibility(document.customerCatalogRequest,true);
			}
			
//			alert(xmlHttp.responseText);
//			dumpProps(local);
			var form = document.getElementById(formName);
		}
	}
}