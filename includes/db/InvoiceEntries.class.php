<?php
include_once "db.php";

class InvoicesEntries
{
	function details( $entryID )
	{
		return getBasicSingleDbRecord("InvoiceEntries", "InvoiceEntryID", $entryID);
	}
	
}

?>