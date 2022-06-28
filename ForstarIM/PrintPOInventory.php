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
	//echo "The factory is $factory";
	$bearer=$pORec[16];
	$emailid=$companyRec[16];
	$state=$companyRec[17];
	$phoneno2=$companyRec[18];
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
<title>RAW MATERIAL PURCHASE MEMO</title>
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
<table width="75%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<table width='92%' cellspacing='1' cellpadding='1' class="boarder" align='center' border=0>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
 <!-- <tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="5"><?=$companyArr["Name"];?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=$addr["ADR1"]?>,<?=$addr["ADR2"];?></td>
  </tr>
  <tr bgcolor=white>
  
    <td colspan="17" class="listing-item" align="center" ><?=$addr["ADR9"];?><?=$addr["ADR10"];?><?=$addr["ADR3"];?></td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="16" class="listing-item" align="center" ><table width="1003" border="0">
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
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="16" align="RIGHT" class="listing-head" ></td>
  </tr>

  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='100%' cellpadding='0' cellspacing='0' class="print" align="center">
         <tr>
           <td nowrap="nowrap" align='left' valign='top' style=" padding-left:8px;">
		   <table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
             <tr>
               <td nowrap="nowrap" class="listing-head" colspan="2" height="25"><font size="2">ISSUED TO:</font> </td>
             </tr>
             <tr>
               <td height="25" colspan="2" align="center" nowrap="nowrap" class="listing-item"><font size="3">
                 <?=$supplierName?>
               </font></td>
             </tr>
             <tr>
               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?//=$supplierAddress?>
               </font></td>
             </tr>
		<tr>

               <td class="listing-item" width='200' height="55" colspan="2"><font size="3">
                 <?//=$supplierPhone?>
               </font></td>
             </tr>
           </table></td>		
		   <td class="listing-head" nowrap="nowrap" align='right' valign='top'>			
			<table width="100%" cellpadding="0" cellspacing="0"  class="tdBoarder">
				  <tr>
					<td width="37%" height="35" class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">PO ID:					  </td>
					<td width="63%" nowrap="nowrap" class="listing-item"><?=$pOGenerateId?></td>
				  </tr>
				  <tr>
					<td class="listing-head" height="35" style="padding-left:5px; padding-right:5px; font-size:8pt;" ><span class="listing-item">PO DATE:</span></td>
					<td class="listing-item"><?=$poDate;?>&nbsp;</td>
				  </tr>
				  <tr>
				    <td class="listing-head" height="35" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span class="listing-item">QUOT REF:</span></td>
				    <td class="listing-item">&nbsp;</td>
				    </tr>
				  <tr>
				    <td class="listing-head" height="35" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span class="listing-item">QUOT DATE:</span></td>
				    <td class="listing-item">&nbsp;</td>
				    </tr>
				</table></td>
		 </tr>
	</table>	</td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="16" align="LEFT" class="listing-head" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="16" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="16" align="center" class="listing-head"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="16" align="center" height="5"></td>
  </tr>
  <!--<tr bgcolor=white> 
    <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS</td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="16" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="16" align="center" class="fieldName">
<table width="100%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if (sizeof($purchaseOrderRecs)) {

	?>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=10%><span class="listing-thead">Sr.NO</span></th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=40%><span class="listing-thead">Description</span></th>
		<!--<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Description
		
		</th>-->
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=15%><span class="listing-thead">Qty</span></th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=15%><span class="listing-thead">Rate</span></th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=20%><span class="listing-thead">&nbsp;</span></th>
      </tr>
      <?
	  	
		$numRows	=	14; // Setting No.of rows
		//$numRows=3;
		$j = 0;
		$purchaseOrderRecSize = sizeof($purchaseOrderRecs);
		
		$totalPage = ceil($purchaseOrderRecSize/$numRows);

		$totalAmount = "";
		foreach ($purchaseOrderRecs as $por) {
			$i++;
			$editStockId	=	$por[2];
			$stockRec	=	$stockObj->find($editStockId);
			$stockName	=	stripSlash($stockRec[2]);
			$unitRate	=	$por[3];
			$editQuantity	=	$por[4];
			$total		=	$por[5];
			$totalAmount = $totalAmount + $total;
			$description=$por[6];
			$printdescStatus=$por[7];
			
		
		?>
      <tr bgcolor="#FFFFFF">
        <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?php echo $i;?>&nbsp;</td>
        <!--<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?></td>-->
		 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;"><?=$stockName?><br/><?php if ($printdescStatus=="Yes"){?><?=$description?>
		 <?php }?> </td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><span class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
          <?=$editQuantity?>
        </span></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap" align="right"><span class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">
          <?=number_format($unitRate,2);?>
        </span></td>
	<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?//=$total?></td>
      </tr>
	  	<?
		if ($i%$numRows==0 && $purchaseOrderRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td>
		<table width="98%" cellpadding="3">
        <tr>
        <td colspan="6" height="10"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
	  <tr>
		<td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><?=$date?></td>
        </tr>
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
    </table></td></tr>
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
		<table width="100%">
		<!--<tr bgcolor=white>
    <td colspan="17" class="listing-head" align="center" ><font size="3"><?=$companyArr["Name"];?></font></td>
  </tr>-->
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-head" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="listing-head"><table width='99%' bgcolor="#f2f2f2">
         <!--<tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2'>
			<font size="2"></font>Purchase Order - Cont.</td>
		   <td class="listing-head" nowrap="nowrap" align='right'>
		<div id="printMsg">No:<?=$pOGenerateId?></div></td>		 
		 </tr>-->
	</table></td>
	    </tr>
	  <!--<tr bgcolor=white>
	    <td colspan="17" align="center">
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">-->
		<!--<tr><td>
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
          </tr>-->
        <!--<tr> 
          <td class="fieldName"> </td>
          </tr>
      </table>
	  </td></tr>-->
      
      </table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="27"></td>
	    </tr>
	 <!-- <tr bgcolor=white> 
   		 <td colspan="17" align="center" class="listing-head">SUMMARY OF ITEMS
                                        </td>
  		</tr>-->
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr><td colspan="17" align="center" class="fieldName">
	  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 		<tr bgcolor="#f2f2f2" align="center">

		  <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=10%><span class="listing-thead">Sr.NO</span></th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=40%>Description</th>
		<!--<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Description
		
		</th>-->
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=15%><span class="listing-thead">Qty</span></th>
        <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=15%><span class="listing-thead">Rate</span></th>
	<th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt" width=20%>&nbsp;</th>
 		 <!-- <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt"><span class="listing-thead">Sr.NO</span></th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="246">Item</th>
		<th  class="listing-head" style="padding-left:5px; padding-right:5px; font-size:8pt">Description
		
		</th>
        <th  class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt"><span class="listing-thead">Qty</span></th>
        <th  class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt"><span class="listing-thead">Rate</span></th>
	<th  class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">&nbsp;</th>-->
      </tr>
   <?
	#Main Loop ending section 
			
	       }
	}
   ?>
      <tr bgcolor="#FFFFFF">
        <!--<td height='30' colspan="5" nowrap="nowrap" class="listing-head" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? //echo number_format($totalAmount,2);?></strong></td>-->
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="5" nowrap="nowrap" class="listing-item" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;">Remarks:<?php echo $remarks;?></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="5" nowrap="nowrap" class="listing-head" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span class="listing-item" >Above rates are inclusive of
            <!--<input type="checkbox" name="transport" id="transport" value=1 <?php if ($transport==1){?> checked="true" <?php }?>/>
		    Transport <input type="checkbox" name="excise" id="excise" value=1 <?php if ($excise==1){?> checked="true" <?php }?>/>
		    Excise<input type="checkbox" name="vat" id="vat" value=1  <?php if ($vat==1){?> checked="true" <?php }?>/>
		    Vat</span>-->Transport<?php if ($transport==1){?> <img src="images/y.png" /> <?php } else {?> 
			<img src="images/x.png" />
			<?php }?>
			Excise <?php if ($excise==1){?> <img src="images/y.png" /> <?php } else {?><img src="images/x.png" /> <?php }?> Vat<?php if ($vat==1){?> <img src="images/y.png" /> <?php } else {?><img src="images/x.png" /> <?php }?></td>
       
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="4" nowrap="nowrap" class="listing-item" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;">
		
		Delivery&nbsp;<?php //echo $factory;?>At our factory at M-53,MIDC,Taloja<?php if ($factory==1){?> <img src="images/y.png" /> <?php } else {?>
<img src="images/x.png" />

		<?php }?>To Bearer of this PO <?php if ($bearer==1){?> <img src="images/y.png" /> <?php } else {?>
<img src="images/x.png" />

		<?php }?>
         <!--<input type="checkbox" name="factory" id="factory" value=1 <?php if ($factory==1){?> checked="true" <?php }?>/>-->
<!--At our factory at M-53,MIDC,Taloja (OR)
<input type="checkbox" name="bearer" id="bearer" value=1 <?php if ($bearer==1){?> checked="true" <?php }?>/>To Bearer of this PO--></td>
        <td height='30' nowrap="nowrap" class="listing-item" align="left">Delivered to:<?php 
		  
	  $deliveredtoRec = $plantandunitObj->find($deliveredto);
					$deliveredtoName = $deliveredtoRec[2];
	  echo $deliveredtoName;?>&nbsp;Delivery Date:<?php echo dateformat($delivarydate);?>&nbsp;</td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="5" nowrap="nowrap" class="listing-head" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span class="listing-item">Payment Terms:<?php echo $paymenttermsInv;?></span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="5" nowrap="nowrap" class="listing-head" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;"><span class="listing-item">THIS PURCHASE ORDER IS SUBJECT TO THE FOLLOWING TERMS &amp; CONDITIONS:</span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="5" nowrap="nowrap" class="listing-head" align="left" style="padding-left:5px; padding-right:5px; font-size:8pt;"><p><span class="listing-item"><?php echo $termsConditionsInv;?><!--1.It is important that the SUPPLIER sign.And returns a copy of this PURCHASE ORDER within three(3) days of </span></p>
          <p><span class="listing-item">receipt as no other form of acceptance will be accepted.Failure to return the acceptance of this PO </span></p>
          <p><span class="listing-item">may result in a delay to any payments that may be due and may be cause for termination of this PURCHASE ORDER.</span></p>--></td>
      </tr>
      <!--<tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><p><span class="listing-item">2.Supplier should mention the P.O.No in the Delivery Challan (or Packing List) and Invoice.</span></p>
          <p><span class="listing-item">Copy of this Purchase Order should be attached along with Supplier's Delivery Chellan.Supplier should obtain Forstar's Store Entryand Gate Entry</span></p>
          <p><span class="listing-item"> Stamps with No &amp; Date on their Delivary Challan.Supplier should obtain their copy of Forstar's GRN as</span></p>
          <p><span class="listing-item"> proof of delivary of goods.Failure to do so and / or submission of incomplete documents may be the reason</span></p>
          <p><span class="listing-item"> for rejection of goods or delay in release of payment(s)</span></p></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><span class="listing-item">3.Goods found defective during /after delivary(during usage) will be rejected and returned to the supplier.</span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><span class="listing-item">4.Delivary accepted between 10.00 AM and 5.00 PM on all working days only.</span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><span class="listing-item">5.Forstar reserve the right to reject or charge penalty on the supplier for delayed delivary of goods.</span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><span class="listing-item">For FORSTAR FROZEN FOODS PVT. LTD</span></td>
      </tr>
      <tr bgcolor="#FFFFFF">
        <td height='30' colspan="6" nowrap="nowrap" class="listing-head" align="left"><span class="listing-item">Director/General Manager</span></td>
      </tr>-->
        </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName"><table width="98%" cellpadding="3">
      
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      
      <!--tr>
        <td class="listing-item" nowrap="nowrap" valign="bottom" style="line-height:8px;"><?=$date?><strong>Page <?=$totalPage?> of <?=$totalPage?></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
        <td class="fieldName" nowrap="nowrap" valign="bottom">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="bottom"></td>
      </tr-->
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
	  <tr>
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:5px;"><?=date("d/m/Y");?></td>
        </tr>
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" >(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
