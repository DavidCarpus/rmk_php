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

function invoiceNumber(selfForm)
{
	xmlHttp=getXmlHttpObject()
	if(!xmlHttp) alert("No xmlHttp object??");
	
	xmlHttp.open("GET","invoiceData.php?invoice_num="+selfForm.invoice_num.value,true);
//	alert(selfForm.invoice_num.value);
  	xmlHttp.send(null);
  	
	xmlHttp.onreadystatechange=function(){
		if(xmlHttp.readyState==4)
		{
			var local=new Function("return "+xmlHttp.responseText)();
			updateForm("CustomerSummary",local['CustomerSummary'] );
			updateForm("InvoiceDetails",local['InvoiceDetails'] );
			if(document.getElementById("InvoiceKnifeList") != null)
				 document.getElementById("InvoiceKnifeList").innerHTML = knifeInfo;
		}
	}
}
