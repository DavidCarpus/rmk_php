<?php 
session_start(); 
/* * Created on Dec 27, 2006 */
include_once "../includes/db/db.php";
include_once "../includes/htmlHead.php";
include_once "../includes/adminFunctions.php";
?>
<LINK href="../Style.css" rel="stylesheet" type="text/css">

<?php echo logo_header("admin", ".."); ?>
<div class="mainbody">
	<div class="centerblock">
		<?php echo adminToolbar(); ?>
		<div class="content">
			<?php  
				if(loggedIn()){
				?>
Web priority list <I>Last Updated Mar 1, 2007</I><BR>
<OL>

<LI><strike>apostrophes as \&rsquo; in emails instead of &rsquo;</strike><B>2007-03-01</B></LI>

<LI> &rsquo; in name and address fields uses  &rsquo;</LI>

<LI><strike>Create margins on nonCatII text</strike><B>2007-03-01</B></LI>

<LI><strike>Do not make phone number manditory for catalog requests. (Gary 2006-12-28)
</strike><B>2007-01-29</B></LI>


<LI><strike>Reply  - save  Reply   to a file where we can review</strike><B>2007-02-06</B></LI> 

<LI> Time submitted &#150; when an order,quote,cat,  is sent to processed, the time will vary. We need one permanent time and date stamp. No changes.</LI>

<LI><strike> Processed orders &#150; remove  option to &lsquo;unprocess&rsquo; a processed item.  In the screen of actual processed item, (cat,quote,order, - once item is processed, we have no need to &lsquo;unprocess&rsquo; **************review  screens----look at list and look at individual item—where unprocess item appears
</strike><B>-01-24</B></LI>

<LI><strike>Processed orders, quote, cat.   page forward &#150; page backward  need to be able to page 
forward and page backward when viewing list or view individual.</strike> Replaced with search option</LI>

<LI><strike>One step print button &#150; create for cat, quote, orders, create one option to send complete list of unprocessed requests to printer, 
then automatic move the list of unprocessed requests that were printed to the processed list.  
Print catalogs on evelopes, print quote and orders on 8&frac12; x 11 paper, one/quote/order per page,   
(((KEEP the option to print one at a time as we now do.)))</strike><B>2007-02-14</B></LI>

<LI><strike>Name and address fields &#150; allow  an apostrophe for names such a O&rsquo;Reily.
</strike><B>-01-25</B></LI>

<LI><strike>Process orders, quote, cat  &#150;   we would like to be able to enter the processed  
items  to mark for whatever reason,   rejected, pending, etc.</strike><B>2007-02-14</B></LI>

<LI>Processed order &#150; purge option.</LI>

<LI>Examples of Combinations -  list model, desc, and price.</LI>

<LI><strike>Non Catalog II &#150; list model, desc, and price.</strike><B>2007-02-08</B></LI>

<LI>Set up so that all catalogue requests are deleted every week. (Gary 2006-10-22)</LI>


</OL>
			<?php  
				} else{
		 			echo loginProcessing();
				}
	 		?>
		</div>
	</div>
	<?php echo footer(); ?>
</div>
