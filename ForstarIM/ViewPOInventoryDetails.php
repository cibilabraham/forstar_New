<?php	
	require("include/include.php");

	$selPOId	=	$g["selPOId"];

	#Find PO Records
	$pORec	=	$purchaseOrderInventoryObj->find($selPOId);
	$pOGenerateId		=	$pORec[1];
	$poNumber		=	$pORec[2];
	$supplierId		=	$pORec[3];
	$poDate=dateformat($pORec[4]);
	$delivarydate=$pORec[10];
	$remarks=$pORec[8];
	$deliveredto=$pORec[11];
		$vat=$pORec[14];
		$transport=$pORec[12];
		$excise=$pORec[13];
		$factory=$pORec[15];
		$bearer=$pORec[16];
	#Supplier Rec
	$supplierRec		=	$supplierMasterObj->find($supplierId);
	$code			=	$supplierRec[1];
	$supplierName		=	stripSlash($supplierRec[2]);
	$supplierAddress	=	stripSlash($supplierRec[3]);
	$supplierPhone		=	stripSlash($supplierRec[4]);
	$vatNo			=	stripSlash($supplierRec[5]);
	$cstNo			=	stripSlash($supplierRec[6]);

	#Get all PO Items
	$purchaseOrderRecs = $purchaseOrderInventoryObj->fetchAllStockItem($selPOId, $poItem);
	$displaySettingsRecInv	=	$displayrecordObj->findInvPurchaseOrder();
	$editDisplayRecIdInv		=	$displaySettingsRecInv[0];
	$termsConditionsInv		=	$displaySettingsRecInv[1];
	$paymenttermsInv		=	$displaySettingsRecInv[2];
	$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
?>
<html>
<head>
<title>PURCHASE ORDER MEMO</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintDailyCatchReportMemo">
<!--table width="75%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table-->
<table width='75%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="right" >&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="right" >&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" >&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td colspan='2' rowspan="3" align='left' nowrap="nowrap" bgcolor="#FFFFFF" class="listing-head" ><img width="325" height="36" border="0" alt="" src="images/forstarfoods.gif"></td>
           <td width="34%" align='left' nowrap="nowrap" bgcolor="#FFFFFF" class="listing-head" ><font size="5">
            <?=$exportAddrArr[1]?>
           </font></td>
         </tr>
         <tr>
           <td align='left' nowrap="nowrap" bgcolor="#FFFFFF" class="listing-head" ><span class="listing-item">
           <?=$exportAddrArr[2]?>,<?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?>
           </span>&nbsp;</td>
           </tr>
         <tr>
           <td align='left' nowrap="nowrap" bgcolor="#FFFFFF" class="listing-head" ><span class="listing-item">
            Phone:<?php 
			foreach($exportAddrContact as $expt1)
			 {
				if($expt1[1]!='') echo $expt1[1].',';
			}
			?> &nbsp;Mobile:
			<?php 
			foreach($exportAddrContact as $expt2)
			 {
				if($expt2[2]!='')  echo $expt2[2].',';
			 }
			 ?>&nbsp;Fax:
			 <?php 
			foreach($exportAddrContact as $expt3)
			 {
				 if($expt3[3]!='') echo  $expt3[3].',';
			 }
			?>&nbsp;E-mail:
			<?php 
			 foreach($exportAddrContact as $expt4)
			 {
				if($expt4[4]!='') echo $expt4[4].',';
			 }
			 ?>
           </span></td>
           </tr>
         
         <tr>
           <!--<td class="listing-head" nowrap="nowrap" align='left' colspan='3' >-->
		   <td class="listing-head" nowrap="nowrap" align='center' colspan='3' bgcolor="#fff">
			<font size="3">PURCHASE ORDER </font>		   </td>
		   <!--<td class="listing-head" nowrap="nowrap" align='right'>
		   <div id="printMsg">PO ID:&nbsp;<?=$pOGenerateId?></div></td>-->
		 </tr></table></td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" >
             <tr>
               <td colspan="4" rowspan="4" nowrap="nowrap" class="listing-head"><font size="2">ISSUED To :<!--Name, Address & Phone of Supplier--></font> <font size="3">
                 <?=$supplierName?>
               </font><font size="3">
                 <?//=$supplierAddress?>
               </font><font size="3">
                 <?//=$supplierPhone?>
               </font></td>
               <td nowrap="nowrap" class="listing-item" height="25">PO:<?=$pOGenerateId?></td>
             </tr>
             <tr>
               <td class="listing-item" nowrap="nowrap" height="25">PO DATE:<?=$poDate;?></td>
             </tr>
             <tr>
               <td class="listing-item" width='159' height="35">QUOT REF:</td>
             </tr>
		<tr>
               <td class="listing-item" width='159' height="35">QUOT DATE:</td>
             </tr>
		<tr>
		  <td width="120" height="35" class="listing-item">Sr.NO</td>
		 <!-- <td width="128" height="35" class="listing-item">Item</td>-->
		  <td width="167" height="35" class="listing-item">Description</td>
		  <td width="172" height="35" class="listing-item">Qty</td>
		  <td width="126" height="35" class="listing-item">Rate</td>
		 <td class="listing-item" height="35">&nbsp;</td>
		  </tr>
          <?php  $totalAmount = "";
		foreach ($purchaseOrderRecs as $por) {
			$i++;
			$editStockId	=	$por[2];
			$stockRec	=	$stockObj->find($editStockId);
			$stockName	=	stripSlash($stockRec[2]);
			$unitRate	=	$por[3];
			$editQuantity	=	$por[4];
			$total		=	$por[5];
			$productDesc=$por[6];
			//$delivarydate=$por[7];
			$totalAmount = $totalAmount + $total;
			?>
		<tr>
		  <td height="35" class="listing-item"><?php echo $i;?>&nbsp;</td>
		  <!--<td height="35" class="listing-item"><span class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
		    <?//=$stockName?>
		  </span></td>-->
		  <td height="35" class="listing-item"><?=$stockName?><br/><?php echo $productDesc;?>&nbsp;</td>
		  <td height="35" class="listing-item"><span class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
		    <?=$editQuantity?>
		  </span></td>
		  <td height="35" class="listing-item"><span class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
		    <?=number_format($unitRate,2);?>
		  </span></td>
		  <td class="listing-item" height="35"><?//=$total?>&nbsp;</td>
		  </tr>
          <?php }?>
		<!--<tr>
		  <td height="35" class="listing-item">&nbsp;</td>
		  <td height="35" class="listing-item">&nbsp;</td>
		  <td height="35" class="listing-item">&nbsp;</td>
		  <td height="35" class="listing-item">&nbsp;</td>
		 <td height="35" align="right" class="listing-item">Net Total</td>
		  <td class="listing-item" height="35"><? echo number_format($totalAmount,2);?>&nbsp;</td>
		  </tr>-->
		<tr>
		  <td height="35" colspan="3" class="listing-item">Remarks:<?php echo $remarks;?></td>
		  <td height="35" align="right" class="listing-item">&nbsp;</td>
		  <td class="listing-item" height="35">&nbsp;</td>
		  </tr>
		<tr>
		  <!--<td height="35" class="listing-item">&nbsp;</td>-->
		  <td height="35" colspan="3" class="listing-item">
		  Above rates are inclusive of
            <!--<input type="checkbox" name="transport" id="transport" value=1 <?php if ($transport==1){?> checked="true" <?php }?>/>
		    Transport <input type="checkbox" name="excise" id="excise" value=1 <?php if ($excise==1){?> checked="true" <?php }?>/>
		    Excise<input type="checkbox" name="vat" id="vat" value=1  <?php if ($vat==1){?> checked="true" <?php }?>/>
		    Vat</span>-->Transport<?php if ($transport==1){?> <img src="images/y.png" /> <?php } else {?> 
			<img src="images/x.png" />
			<?php }?>
			Excise <?php if ($excise==1){?> <img src="images/y.png" /> <?php } else {?><img src="images/x.png" /> <?php }?> Vat<?php if ($vat==1){?> <img src="images/y.png" /> <?php } else {?><img src="images/x.png" /> <?php }?>
		  
		  <!--Above rates are inclusive of 
		     <input type="checkbox" name="transport" id="transport" value=1 <?php if ($transport==1){?> checked="true" <?php }?>/>
		    Transport <input type="checkbox" name="excise" id="excise" value=1 <?php if ($excise==1){?> checked="true" <?php }?>/>
		    Excise<input type="checkbox" name="vat" id="vat" value=1  <?php if ($vat==1){?> checked="true" <?php }?>/>
		    Vat--></td>
		  <td height="35" align="right" class="listing-item">&nbsp;</td>
		  <td class="listing-item" height="35">&nbsp;</td>
		  </tr>
          
           </table>
		   <p>&nbsp;</p></td>	
		   
		 </tr>
	</table>
	<table width="932" border="0">
	  <tr>
	    <td width="650">Delivery&nbsp;<?php //echo $factory;?>At our factory at<!-- M-53,MIDC,Taloja--><?php if ($factory==1){?> <img src="images/y.png" /> <?php } else {?>
<img src="images/x.png" />

		<?php }?>To Bearer of this PO <?php if ($bearer==1){?> <img src="images/y.png" /> <?php } else {?>
<img src="images/x.png" />

		<?php }?></td>
	    <td width="135">Delivered to-<?php
		
		$deliveredtoRec = $plantandunitObj->find($deliveredto);
					$deliveredtoName = $deliveredtoRec[2];
		
		echo $deliveredtoName;?></td>
	    <td width="135">Delivery Date:<?php echo dateformat($delivarydate);?></td>
	    </tr>
	  <tr>
	    <td colspan="3">Payment Terms:<?php echo $paymenttermsInv;?></td>
	    </tr>
	  <tr>
	    <td colspan="3">THIS PURCHASE ORDER IS SUBJECT TO THE FOLLOWING TERMS &amp; CONDITIONS:</td>
	    </tr>
	  <tr>
	    <td colspan="3"><p><?php echo $termsConditionsInv;?></p></td>
	    </tr>
	 <!-- <tr>
	    <td colspan="2"><p>2.Supplier should mention the P.O.No in the Delivery Challan (or Packing List) and Invoice.Copy of this Purchase Order should be attached along with Supplier's Delivery Chellan.Supplier should obtain Forstar's Store Entry and Gate Entry Stamps with No &amp; Date on their Delivary Challan.Supplier should obtain their copy of Forstar's GRN as proof of delivary of goods.Failure to do so and / or submission of incomplete documents may be the reason for rejection of goods or delay in release of payment(s)</p></td>
	    </tr>
	  <tr>
	    <td colspan="2">3.Goods found defective during /after delivary(during usage) will be rejected and returned to the supplier.</td>
	    </tr>
	  <tr>
	    <td colspan="2">4.Delivary accepted between 10.00 AM and 5.00 PM on all working days only.</td>
	    </tr>
	  <tr>
	    <td colspan="2">5.Forstar reserve the right to reject or charge penalty on the supplier for delayed delivary of goods.</td>
	    </tr>-->
	  <tr>
	    <td colspan="3">For FORSTAR FROZEN FOODS PVT. LTD</td>
	    </tr>
	  </table>
	<p>Director/General Manager</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p></td>
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
  <!--<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName">
<!--<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	//if (sizeof($purchaseOrderRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Item</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Unit Price</th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Quantity</th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Total</th>
      </tr>
      <?
	  	
		//$numRows	=	25; // Setting No.of rows
		//$j = 0;
		//$purchaseOrderRecSize = sizeof($purchaseOrderRecs);
		
		//$totalPage = ceil($purchaseOrderRecSize/$numRows);

		/*$totalAmount = "";
		foreach ($purchaseOrderRecs as $por) {
			$i++;
			$editStockId	=	$por[2];
			$stockRec	=	$stockObj->find($editStockId);
			$stockName	=	stripSlash($stockRec[2]);
			$unitRate	=	$por[3];
			$editQuantity	=	$por[4];
			$total		=	$por[5];
			$totalAmount = $totalAmount + $total;*/
		
		?>
      <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=number_format($unitRate,2);?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><?=$editQuantity?></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$total?></td>
      </tr>
	  	<?
		//if ($i%$numRows==0 && $purchaseOrderRecSize!=$numRows) {
			//$j++;
		?>
	    </table>--></td></tr>
		<tr bgcolor="#FFFFFF">
		<td>
		</td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
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
	    <td colspan="17" align="center" class="listing-head"><!--<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="2"></font>Purchase Order - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?//=$pOGenerateId?></div></td>		 
		 </tr>
	</table>--></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		<!--<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr><td>
		<table width="100%" cellpadding="0" cellspacing="3" class="tdBoarder">
        <tr> 
          <td valign="top"><table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="listing-head" valign="top">Supplier:</td>
                <td class="listing-item" style="line-height:normal;" valign="top"><?=$supplierName?>&nbsp;</td>
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
   		 <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS
                                        </td>
  		</tr>
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr><td colspan="17" align="center" class="fieldName">
	  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">Item</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Unit Price</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Quantity</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Total</th>
      </tr>
   <?
	#Main Loop ending section 
			
	       //}
	//}
   ?>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr>
    </table>--></td>
  </tr>
  <? //} else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?//=$msgNoRecords;?>
    </span></td>
  </tr><? //}?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName"></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>