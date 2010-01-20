<?php
include_once "Base.class.php";

class Invoice extends Base
{
	function __construct() {
//       print "In constructor\n";
       $this->name = "InvoiceForms";
   }
   
   public function entryFormMode($formValues)
   {
   		if(array_key_exists("ERROR", $formValues) && strlen($formValues['ERROR']) > 0){return "validate";}	
			
		if(array_key_exists("submit", $formValues) && !is_numeric($formValues["Invoice"])){return "validate";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Update"){return "validate";}
				
		if(array_key_exists("Invoice", $formValues) && is_numeric($formValues["Invoice"])){return "edit";}
		if(array_key_exists("submit", $formValues) && $formValues["submit"] == "Save"){return "save";}

		return "new";	
   }
   
	public function invNum( $invoice ){
		$formName="InvoiceNum";
		$this_page = basename($_SERVER['REQUEST_URI']);
		if(!array_key_exists('Invoice', $invoice)) $invoice['Invoice'] = "";
		$results="";
//		$results .=  "<legend>$formName</legend>";
		$results .=  "<div id='$formName'>";
//		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='$this->formMode'>" ;
		$results .=  "<form name='$formName' action='invoiceEdit.php' method='$this->formMode'>" ;

		$options=array();
//		if(substr($this_page,0,strlen("invoiceEdit.php")) != "invoiceEdit.php"){
			$options['jscript']=array("field"=>"onblur=\"invoiceNumber($formName);\"");
//		}
		$results .=  $this->textField('Invoice', $this->fieldDesc('Invoice'),  $invoice['Invoice'],$options, "", "", "", "");
//		$results .=  $this->textField('Invoice', $this->fieldDesc('Invoice'), false, $invoice['Invoice'],"",$JS) ;
		if(substr($this_page,0,strlen("invoiceEdit.php")) != "invoiceEdit.php"){
			$results .= " <a id='viewInvoiceLink' href='invoiceEdit.php?Invoice=" . $invoice['Invoice'] . "'>View Invoice</a>";
		}
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		return $results;
	}
	

	public function editComment($invoice){
		$formName="InvoiceCommentEdit";
		if(!array_key_exists('Comment', $invoice)) $invoice['Comment'] = "";
		if(!array_key_exists('Invoice', $invoice)) $invoice['Invoice'] = "";
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='$this->formMode'>" . "\n" ;
		$results .=  $this->textArea('Comment','Invoice Comment',  $invoice['Comment'], $options, "", "", "", "");
		$results .=  $this->hiddenField('Invoice', $invoice['Invoice']);
		$results .=  "<br />";
		$results .=  $this->button("submit", "Save_Update");
		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		
		return $results;
	}
	
	public function details($invoice, $mode){
		$formName="InvoiceDetails";
		if(!array_key_exists('invoice_num', $invoice)) $invoice['invoice_num'] = "";

		$errors = array();
		if(array_key_exists("ERROR", $invoice) && count($invoice['ERROR']) > 0){
			$errors=array_fill_keys(explode(",", $invoice['ERROR']), true);
		}
		$readOnly = !( ($mode == "edit") || ($mode == "new") || $mode == "err" );
		
		$results="";
		
		$results .=  "<div id='$formName'>" . "\n";
		$results .=  "<form name='$formName' action='invoiceEdit.php' method='$this->formMode'>" . "\n" ;
//		$results .=  "<form name='$formName' action='". $_SERVER['PHP_SELF']. "' method='$this->formMode'>" . "\n" ;
//		$results .=  "<legend>$formName</legend>" . "\n";
//		echo debugStatement(__FILE__ .":". __FUNCTION__.":" . dumpDBRecord($values));
		$fields = array('DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 'ShippingAmount', 
			"PONumber", "ShippingInstructions", "KnifeCount", "TaxPercentage");
		foreach($fields as $name)
		{
			if( strncmp($name, "Date", 4) == 0 && strlen($invoice[$name]) > 9) // Trim off timestamp
			{
				$invoice[$name] = substr($invoice[$name], 0, 10);
			}
			if(!array_key_exists($name, $invoice)) $invoice[$name] = "";

//			$results .=  "\n";
//			$JS['label']="onmouseover='showHint(this.htmlFor);'";
//			$JS['label']=$this->helpTextJS("ajax-tooltip.html");
			
//			if( $name=="KnifeCount" ) $JS['field'] = " disabled='true' " . $this->helpTextJS($formName, $name, "Test Help text<br />Line2");
//			if( $name=="KnifeCount" ) $JS['field'] = $this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']);
//			if( $name=="KnifeCount" ) $JS['label'] = $this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']);
			
			$value = $invoice[$name];
			if($name == "TotalRetail") $value = "$" . number_format($invoice['TotalRetail'] ,2);
			if($name == "ShippingAmount") $value = "$" . number_format($invoice['ShippingAmount'] ,2);
			
			$options=array();
//			if( $name=="KnifeCount" ){ $options['jscript']=array(
//					'field'=>$this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']),
//					'label'=>$this->helpTextJS("invKnivesHelp.php?invoice_num=" . $invoice['Invoice']),
//				);
//			}
			$options['readonly']=$readOnly;
			if(array_key_exists($name, $errors)) $options['error']=1;
			
			$results .=  $this->textField($name, $this->fieldDesc($name),  $value, $options, "", "", "", "");
				
//			$results .=  $this->textField($name, $this->fieldDesc($name), $err, $value,'',$JS, $readOnly) . "\n";
			if($this->isInternetExploder() && ( $name=="DateShipped"  || $name=="PONumber" ) )
					$results .=  "<br />";
		}

		if(array_key_exists('CustomerID', $invoice)) $results .=  $this->hiddenField('CustomerID', $invoice['CustomerID']);		
		if(array_key_exists('Invoice', $invoice)) $results .=  $this->hiddenField('Invoice', $invoice['Invoice']);		
		
		//		PO#, TotalRetail, Shipping$, ShippingInfo, ShippingLocation, discount totalPayments, totalknives
	
		// Add buttons to save/update	
		if($mode == "edit" || $mode == "err") 	$results .=  $this->button("submit", "Update");
		if($mode == "new")		$results .=  $this->button("submit", "Save");

		$results .= "</form>";
		$results .= "</div><!-- End $formName -->\n";
		
		return $results;
	}
	

		
	function buttonLinks($invoice){
		$formName="InvoiceDetailButtonLinks";
		if(!array_key_exists('Invoice', $invoice)) $invoice['Invoice'] = "";
		if(!array_key_exists('Comment', $invoice)) $invoice['Comment'] = "";
		
		$results="";
		$results .=  "<div id='$formName'>" . "\n";
		$label = ((strlen($invoice["Comment"]) > 0) ? "Edit" : "Add"). " Comment";
		$results .= "<span class='helptext'>";
		$results .= "<a href='invoiceCommentEntryEdit.php?Invoice=" . $invoice['Invoice'] . "'>";
		$results .= $label;
		if(strlen($invoice["Comment"]) > 0){
			$results .= "<span>" . $invoice["Comment"] . "</span>";
		}
		$results .= "</a>";
		
		$results .= "</span>";
		$results .= "&nbsp; &nbsp;";
		$label = "Edit Payments";
		$results .= "<a href='invoicePaymentsEntryEdit.php?Invoice=" . $invoice['Invoice'] . "'>$label</a>";
		$results .= "</div><!-- End $formName -->\n";
//		$results .= dumpDBRecord($invoice);
		return $results;
	}
	
	public function getCustomerInvoiceList($invoices){
		$formName="CustomerInvoiceList";
		$results =  "<div id='$formName'>" . "\n";
		$fields = array('Invoice', 'DateOrdered', 'DateEstimated', 'DateShipped', 'TotalRetail', 'Due');
//		, 'TCol1', 'TCol2'
		foreach($fields as $field)
		{
			$results .= "<span class='Header$field'>";
			if(substr($field,0,4) == 'Date'){
				$results .= substr($field,4);
			} else {
				$results .= $field;
			}
			$results .= "</span>\n";
		}
		$results .= "<br />\n";
//		$results .= "<span id='CustomerInvoices'>\n";
		$cnt=1;
		foreach($invoices as $invoice)
		{
			if($cnt%2)
				$results .= "<div class='CustomerInvoiceListItemHL'>\n";
				
//			$results .= debugStatement(dumpDBRecord($invoice));
			foreach($fields as $field)
			{
				$results .= "<span class='$field'>";
				if(!array_key_exists($field, $invoice)) $invoice[$field] = "";
				if(substr($field,0,4) == 'Date'){
					$results .= substr($invoice[$field],0,10) . " ";
				} else if($field == 'Invoice') {
					$results .= " <a href='invoiceEdit.php?Invoice=" . $invoice['Invoice'] . "'>$invoice[$field]</a>";
				} else if($field == 'TotalRetail' || $field == 'Due') {
					$results .= "$" . number_format($invoice[$field] ,2);				
				} else {
					$results .= $invoice[$field] . " ";
				}
				$results .= "&nbsp;</span>";
			}
			if($cnt%2)
				$results .= "</div>";
			$cnt++;			$results .= "<br />\n";
		}
//		$results .= "</span>";
		$results .= "</div><!-- End $formName -->\n";
		return $results;
	}
	
}
?>