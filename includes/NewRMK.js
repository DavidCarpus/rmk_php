// Ajax script stuff
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

function defaultField(formName, fieldName)
{
	var elem = document.getElementById(formName).elements;
	for(var i=0; i < elem.length; i++){
		if(elem != null && elem[i].name == fieldName){
			elem[i].focus();
			elem[i].select();
		}
	}
//	document.form.field1.focus();
//	document.form.field1.select();
}

function recomputeTotalRetail(form, newBaseRetail){
//	alert("Recompute Total" + form.name);
	var featureTotal=0;
	var qty=0;
	var elem = form.elements;
	for(var i=0; i < elem.length; i++){
		var elemName = ""+elem[i].name; 
		if(elemName == 'BaseRetail'){
			if(newBaseRetail >= 0)
				elem[i].value = newBaseRetail;
			else
				newBaseRetail = elem[i].value;
		} else if(elemName == 'Quantity'){
			qty = parseFloat(elem[i].value);
		} else if(elemName.substring(0, 15) == 'Addition_Price_'){
			val = parseFloat(elem[i].value);
			if(val > 0){
				featureTotal += val;
			}
		}
	}
	for(var i=0; i < elem.length; i++){
//		if(elem.name == 'TotalRetail'){
		if(elem[i].name == 'InvoiceEntry_TotalRetail'){			
//			alert("New total?:" + qty + "\n Base: " + newBaseRetail + "\n +Features:" + featureTotal + "\n=" + (qty * (parseFloat(newBaseRetail) + featureTotal)).toFixed(2))
			elem[i].value = (qty * (parseFloat(newBaseRetail) + featureTotal)).toFixed(2);
		}
	}
}
function updateRetail(formName){
	var form = document.getElementById(formName);
	var baseRetailField = document.getElementById('BaseRetail');
	recomputeTotalRetail(form, baseRetailField.value); 
//	alert(baseRetailField.name);

}

function updatedFeature(featureName, formName, newValues){
	var form = document.getElementById(formName);
	var featurePriceName = "Addition_Price_" + featureName.substring(9, 10);
//	alert(featurePriceName);
	var baseRetail=0;
	var elem = form.elements;
	for(var i=0; i < elem.length; i++){
		var elemName = ""+elem[i].name; 
		if(elemName == 'BaseRetail'){
			baseRetail = elem[i].value;
		} else if(elemName == featurePriceName){
			elem[i].value = newValues['BaseRetail']
		}
	}
	recomputeTotalRetail(form, baseRetail);

//	alert(featureName + ":" + formName + ":" + newValues['BaseRetail']);
}

function validPartCodeKey(KeyID){
   switch(KeyID){
      case 9: return false; // Tab
      case 16: return false; // SHIFT
      case 17:return false; // Ctrl
      case 18:return false; // Alt
      case 19:return false; // Pause
      case 37:return false; // Left
      case 38:return false; // Up
      case 39:return false; // Right
      case 40:return false; // Down
   }
//   alert(KeyID);
   return true;
}

function featureFieldEdit(formName, field, keyEvent){
	var KeyID = (window.event) ? event.keyCode : keyEvent.keyCode;
	if(!validPartCodeKey(KeyID)) return false;
	
	fetchURL="partInfo.php?partCode="+field.value;
	
	xmlHttp=getXmlHttpObject()
	if(!xmlHttp) alert("No xmlHttp object??");
	xmlHttp.open("GET",fetchURL,true);
	xmlHttp.send(null);
	
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4)
		{
			var local=new Function("return "+xmlHttp.responseText)();
			var form = document.getElementById(formName);
			updatedFeature(field.name, formName, local);
//			alert(local);
			recomputeTotalRetail(form, -1);
		}
	}
}

function newPart(formName, field){
	fetchURL="partInfo.php?partCode="+escape(field.value);
//	alert(fetchURL);

	xmlHttp=getXmlHttpObject()
	if(!xmlHttp) alert("No xmlHttp object??");
	xmlHttp.open("GET",fetchURL,true);
	xmlHttp.send(null);
	
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4)
		{
			var local=new Function("return "+xmlHttp.responseText)();
//			alert(xmlHttp.responseText);
			var form = document.getElementById(formName);
			recomputeTotalRetail(form, local['BaseRetail']);
		}
	}
}

function newInvoiceEntrySubmit(form){
	for ( var _elem_num in form.elements){
		var _elem = form.elements[_elem_num];
		if(_elem.type == 'submit' ){
			if( _elem.value == "New Item"){
				var theDiv = document.getElementById('NewInvoiceEntryHide');
				_elem.value = "Submit";
				theDiv.style.display = "block";
			} else {
				return true;
			}
		}
	}
	return false;
}

function newPaymentSubmit(form){
//for(i=0; i<form.elements.length; i++){alert("Field is:" + form.elements[i].name);}		return false;
	
	for(i=0; i<form.elements.length; i++){
//		alert("Test" + _elem_num);
		var _elem = form.elements[i];
		if(_elem.type == 'submit' ){
//			dumpProps(_elem);
			if( _elem.value == "Add Payment"){
				var theDiv = document.getElementById('NewInvoicePaymentDiv');
//				alert(theDiv.className);
				theDiv.className="NewInvoicePayment_UnHide";
				theDiv.style.display = "block";
				theDiv.style.visibility = "visible";
//				alert(theDiv.className);
//				alert(theDiv.style.display+" : "+theDiv.style.visibility);
//				alert(theDiv.style.display+" : "+theDiv.style.visibility);
				_elem.value = "Submit";
				break;
			} else {
				return true;
			}
		}
	}

	for(i=0; i<form.elements.length; i++){
		var _elem = form.elements[i];
		if(_elem.name == 'Payment' ){
			_elem.focus();
			_elem.select();
		}
	}
	return false;
}

function showHint(id){
alert(id);
}

function updateForm(formName, updateInfo)
{
	if(document.getElementById(formName) != null){
		for ( var _name in updateInfo){
			if(document.forms[formName].elements[_name]){
				var val=updateInfo[_name];
				if(val == 'null') val="";
				document.forms[formName].elements[_name].value = val;
			}
		}
	}
}
function updateCustomerFullName(fullName)
{
	var theDiv = document.getElementById('CustomerFullName'); 
	if( theDiv != null){
		theDiv.innerHTML = fullName;
//		alert(theDiv);
	}

}
function updateInvoiceNumInHref(link, invNum){
	var newHref = link.href;
	newHref = newHref.substring(0, newHref.indexOf("Invoice") ) + "Invoice=" + invNum;
	link.href = newHref;
}

function updateViewInvoiceLink(newInvNum){
	theLink = document.getElementById("viewInvoiceLink");
	if(theLink != null){
		updateInvoiceNumInHref(theLink, newInvNum );	
	}
}

function updateLinks(divName, updateInfo)
{
	theDiv = document.getElementById(divName);
	if(theDiv != null){
	  	var x=theDiv.getElementsByTagName("a");
		for( i=0; i< x.length; i++ ){
			var link = x[i];
//			alert(link.innerHTML);
			if( link.innerHTML.indexOf("Comment") > 0 ){
				if( updateInfo['Comment'] ){
					 link.innerHTML = "Edit Comment<span>" + updateInfo["Comment"] + "</span>";
				} else {
					 link.innerHTML = "Add Comment";
				}
				updateInvoiceNumInHref(link, updateInfo['invoice_num'] );
			}
			else if( link.innerHTML.indexOf("Payments") > 0 ){
				updateInvoiceNumInHref(link, updateInfo['invoice_num'] );
			}
		}
	}
}

function invoiceNumber(selfForm)
{
	xmlHttp=getXmlHttpObject()
	if(!xmlHttp) alert("No xmlHttp object??");
	
	xmlHttp.open("GET","invoiceData.php?Invoice="+selfForm.Invoice.value,true);
//	alert(selfForm.invoice_num.value);
  	xmlHttp.send(null);
  	
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4)
		{
			var local=new Function("return "+xmlHttp.responseText)();
//			alert("Updating");
			updateForm("CustomerSummary",local['CustomerSummary'] );
			updateForm("InvoiceDetails",local['InvoiceDetails'] );

			if(document.getElementById("InvoiceKnifeList") != null)
				 document.getElementById("InvoiceKnifeList").innerHTML = local['InvoiceKnifeList'];
			if(document.getElementById("InvoicePayments") != null)
				 document.getElementById("InvoicePayments").innerHTML = local['InvoicePayments'];
			if(document.getElementById("InvoiceFinanceSummary") != null)
				 document.getElementById("InvoiceFinanceSummary").innerHTML = local['InvoiceFinanceSummary'];
			if(document.getElementById("NewInvoiceEntry") != null)
				 document.getElementById("NewInvoiceEntry").innerHTML = local['NewInvoiceEntry'];

			if(document.getElementById("CustomerSummaryDisp") != null)
				 document.getElementById("CustomerSummaryDisp").innerHTML = local['CustomerSummaryDisp'];
			if(document.getElementById("CustomerFlags") != null)
				 document.getElementById("CustomerFlags").innerHTML = local['CustomerFlags'];
			if(document.getElementById("invoiceAckLink") != null)
				 document.getElementById("invoiceAckLink").innerHTML = local['invoiceAckLink'];


			updateViewInvoiceLink(selfForm.invoice_num.value);
			updateLinks("InvoiceDetailButtonLinks",local['InvoiceDetails'] );
			updateCustomerFullName(local['CustomerSummary']['FullName'])
		}
	}
}
