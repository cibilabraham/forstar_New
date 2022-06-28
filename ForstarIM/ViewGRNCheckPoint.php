<?php
	require("include/include.php");

	$grnId	=	$g["grnId"];

	$goodsReceiptRec	=	$goodsreceiptObj->find($grnId);
	$poId			=	$goodsReceiptRec[1];		
	$storeEntry		=	$goodsReceiptRec[6];

	#Find PO Records
	$pORec				=	$purchaseOrderInventoryObj->find($poId);
	$pOGenerateId		=	$pORec[1];
	$poNumber			=	$pORec[2];
	$supplierId			=	$pORec[3];

	#Supplier Rec
	$supplierRec		=	$supplierMasterObj->find($supplierId);
	$code				=	$supplierRec[1];
	$supplierName		=	stripSlash($supplierRec[2]);
	$supplierAddress	=	stripSlash($supplierRec[3]);
	$supplierPhone		=	stripSlash($supplierRec[4]);
	$vatNo				=	stripSlash($supplierRec[5]);
	$cstNo				=	stripSlash($supplierRec[6]);
	
	#Get all GRN Items
	if ($grnId) $goodsReceiptRecs = $goodsreceiptObj->fetchAllStockItem($grnId);	
?>
<html>
<head>
<title>GRN DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function displayBtn()
	{
		document.getElementById("printButton").style.display="block";
	}
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";
		window.print();
		setTimeout("displayBtn()",2000);
		//document.getElementById("printButton").style.display="block";
	}
</script>
</head>
<body>
<form name="frmPrintDailyCatchReportMemo">
<!-- Print start -->
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<!-- Print End -->
<table width='80%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="3"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" height="5"></td>
  </tr>
<!--<tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='90%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			GRN DETAILS</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">GRN NO:&nbsp;<?=$storeEntry?></div></td>
		 </tr></table></td>
  </tr>
<tr bgcolor="White"><TD height="5"></TD></tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='90%' cellpadding='0' cellspacing='0' class="print" align="center">
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
             <tr>
               <td nowrap="nowrap" class="listing-head" colspan="2" height="25"><font size="2">Name, Address & Phone of Supplier</font> </td>
             </tr>
             <tr>
               <td class="listing-item" nowrap="nowrap" colspan="2" height="22"><font size="2">
                 <?=$supplierName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="22" colspan="2"><font size="2">
                 <?=$supplierAddress?>
               </font></td>
             </tr>
		<tr>
               <td class="listing-item" width='200' height="22" colspan="2"><font size="2">
                 <?=$supplierPhone?>
               </font></td>
             </tr>
           </table></td>	
		   
		 </tr>
	</table>	</td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="listing-head" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<table width="90%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" align="center" >
	<?
	if (sizeof($goodsReceiptRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>  
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Actual Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Accepted Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Rejected Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Remarks</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt"></th>
      </tr>
      <?php	
		$numRows	= 14; // Setting No.of rows 20
		$j = 0;
		$grnRecSize = sizeof($goodsReceiptRecs);
		
		$totalPage = ceil($grnRecSize/$numRows);

		$totalAmount = "";
		foreach ($goodsReceiptRecs as $grn) {
			$i++;
			$goodsReceiptEntryId = $grn[0];
			$editStockId	= $grn[2];
			$stockRec	= $stockObj->find($editStockId);
			$stockName	= stripSlash($stockRec[2]);
			$quantity	= $grn[3];
			$qtyReceived	= $grn[4];
			$qtyRejected	= $grn[5];
			$remarks	= $grn[6];
			$subCategoryId ="";
						# Check Subcategory has Check Point
						$subCategoryId	= $goodsreceiptObj->checkPointExist($editStockId);
						
						if ($subCategoryId)
						{
							# Get Selected Chk Point
							$chkPointRec = $goodsreceiptObj->getSelCheckPointRecs($goodsReceiptEntryId);
							$getCheckPointRecs = $goodsreceiptObj->getCheckPointRecs($subCategoryId);
						} else $getCheckPointRecs = array();
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$quantity;?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$qtyReceived?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$qtyRejected?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$remarks?></td>
	<td style="padding-left:5px; padding-right:5px;" align="center">
		<?php
			$k = 0;	
			if (sizeof($getCheckPointRecs)>0) {
				
		?>	
			<table cellspacing="1" bgcolor="#999999" cellpadding="2" class="tdBoarder">
				<tr bgcolor="#f2f2f2" align="center">
                     			<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;">Check Point</td>
					<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;"></td>
					<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;">Remark</td>
				</tr>
				<?php
					foreach ($getCheckPointRecs as $cpr) {
						$k++;	
						$checkPointId 	= $cpr[1];
						$checkPointName = $cpr[2];
						
						
							$cpRec =  $chkPointRec[$k-1];
							$grCPEntryId = $cpRec[0];
							$chkAnsChecked = ($cpRec[2]=='Y')?"Checked":"";
							$chkPointRemarks = $cpRec[3];
						
				?>
				<TR bgcolor="White">
					<TD class="fieldName" nowrap="true">
						<?=$checkPointName?>
						<input type="hidden" name="chkPointId_<?=$j?>_<?=$k?>" value="<?=$checkPointId?>">
						<input type="hidden" name="grCPEntryId_<?=$j?>_<?=$k?>" value="<?=$grCPEntryId?>">
					</TD>
					<td >
						<? if($chkAnsChecked){?><img src="images/y.gif" /><? } else {?><img src="images/x.gif" /><?}?>
			<input type="hidden" name="reEdit_<?=$i;?>" value="<?=$edited?>">
					</td>
					<td class="listing-item">
						<?=$chkPointRemarks?>
					</td>
				</TR>
				<?php
					}
				?>
			</table>
		<?php
			}
		?>
		<input type="hidden" name="chkPointRowCount_<?=$j?>" id="chkPointRowCount_<?=$j?>" value="<?=$k?>">
	</td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $grnRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>	
		<tr bgcolor="#FFFFFF">
		<td height="10">
		</td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='80%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head">
	<table width='90%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			GRN DETAILS - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$storeEntry?></div></td>		 
		 </tr>
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width='90%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="3" class="tdBoarder">
        <tr> 
          <td valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" valign="top">Supplier:</td>
                <td class="listing-item" style="line-height:normal;" valign="top">&nbsp;<?=$supplierName?></td>
              </tr>
            </table></td>
          <td valign="top">&nbsp;</td>
          <td valign="top"></td>
          </tr>
        <tr> 
          <td class="fieldName"> </td>
          </tr>
      </table>
	  </td></tr></table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>
	  <tr bgcolor=white> 
   		 <td colspan="17" align="center" class="listing-head">
                  SUMMARY OF ITEMS
                </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center" class="fieldName">
	  	  <table width="90%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>  
        	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Actual Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Accepted Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Rejected Qty</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Remarks</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt"></th>
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
    </table></td>
  </tr>
<!-- GRN -->
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" height="10"></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>