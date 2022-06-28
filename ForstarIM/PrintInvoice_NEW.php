<?php
	require("include/include.php");
	
	$selInvoiceId = $g["selInvoiceNo"];
	
	$invoiceRec   = $invoiceObj->getInvoiceRecs($selInvoiceId);
	$invoiceNo	  = $invoiceRec[1];
	//$invoiceDate  = $invoiceRec[2];
	$eDate				=	explode("-", $invoiceRec[2]);
	$invoiceDate		=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
	
	$customerName	=	$invoiceRec[4];
	$customerCountry = 	$invoiceRec[5];

	//echo $containerId."=".$purchaseOrderId;
	#------------------
	$containerId 	=	$invoiceRec[6];
	$containerRec	=	$invoiceObj->getContainerRecs($containerId);
	$shippingLine 	=	$containerRec[2];
	$vessalDetails	=	$containerRec[5];
	//$sailingDate	=	$containerRec[6];
	$eDate				=	explode("-", $containerRec[6]);
	$sailingDate		=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
	#------------------
	
	$purchaseOrderId 	= 	$invoiceRec[7];	
	$poRec				=	$invoiceObj->getPORecs($purchaseOrderId);
	$dischargePort 		= 	$poRec[5];
	$paymentMode		=	$poRec[6];
	$paymentTerms		=	$poRec[7];
	
	#Get All Purchase Order Records
	$invoiceRecords		=	$invoiceObj->fetchAllInvoiceRecords($selInvoiceId);
?>
<html>
<head>
<title>INVOICE</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
<script language="JavaScript" type="text/JavaScript" src="libjs/ConvertString.js"></script>
</head>
<body>
<form name="frmPrintInvoice">
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<!--  Strat table here-->
<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="listing-head" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='center' colspan='2' >
			<font size="3">INVOICE</font></td>
		   </tr>
	</table>	</td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top">&nbsp;</td>
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
    <td colspan="17" align="center" class="listing-head">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="print">
          <tr>
            <td width="400" colspan="2" rowspan="3" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="fieldName">Exporter</td>
              </tr>
              <tr>
                <td class="listing-item" nowrap="nowrap"><strong><?=$companyArr["Name"];?><BR>
				<?=$companyArr["ADR6"];?> <br><?=$companyArr["ADR2"];?><br> <?=$companyArr["ADR7"];?></strong></td>
              </tr>
            </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Invoice No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:10px; padding-right:10px;"><table width="100%" border="0">
                      <tr>
                        <td><span class="listing-item" style="padding-left:10px; padding-right:10px;">
                          <?=$invoiceNo?>
                        </span></td>
                        <td><span class="listing-item" style="padding-left:10px; padding-right:10px;">
                          <?=$invoiceDate?>
                        </span></td>
                      </tr>
                    </table></td>
                  </tr>
                  
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Exporter's Ref  </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td colspan="2">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
              <tr>
                <td></td>
                <td>				</td>
              </tr>
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="fieldName" nowrap="nowrap">Buyer's Ref.No. &amp; Date </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="fieldName">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item" align="right" style="padding-right:10px">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
              <tr>
                <td></td>
                <td>				</td>
              </tr>
              
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td class="fieldName">Other Reference(s)  </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
                <td>&nbsp;</td>
              </tr>
              </table></td>
          </tr>
          <tr>
            <td width="400" colspan="2" rowspan="2" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="fieldName">Consignee</td>
              </tr>
              <tr>
                <td class="listing-item" nowrap="nowrap"><?=$customerName?><br><?=$customerCountry?>
                  </td>
              </tr>
            </table></td>
            <td width="500" colspan="2" style="padding-left:2px;" valign="top">
			<table width="200" border="0" class="tdBoarder">
              <tr>
                <td class="fieldName">Buyer (if other than Consignee) </td>
              </tr>
              <tr>
                <td class="listing-item" nowrap="nowrap">&nbsp;</td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Country of Origin of Goods </td>
                  </tr>
                  <tr>
                    <td class="listing-item">India</td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Country of Final Destination  </td>
                  </tr>
                  <tr>
                    <td class="listing-item"><?=$customerCountry?></td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Pre-Carriage by </td>
                  </tr>
                  <tr>
                    <td class="listing-item">By Sea </td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Place of Receipt by Pre-carrier </td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
            <td colspan="2" rowspan="3" valign="top">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Terms of Delivery and payment  </td>
                  </tr>
                  <tr>
                    <td class="listing-item"><?=$dischargePort?><br>
                    PAYMENT BY <?=$paymentMode?> &nbsp;<?=$paymentTerms?><br>VESSEL <?=$shippingLine?> ON DATE: <?=$sailingDate?> </td>
                  </tr>
                </table></td>
          </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Vessal/ Flight No. </td>
                  </tr>
                  <tr>
                    <td class="listing-item"><?=$vessalDetails?></td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Port of Loading </td>
                  </tr>
                  <tr>
                    <td class="listing-item">J.N.P.T</td>
                  </tr>
                </table></td>
            </tr>
          <tr>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Port of Discharge </td>
                  </tr>
                  <tr>
                    <td class="listing-item"><?=$dischargePort?></td>
                  </tr>
                </table></td>
            <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName">Final Destination </td>
                  </tr>
                  <tr>
                    <td class="listing-item"><?=$dischargePort?></td>
                  </tr>
                </table></td>
            </tr>
          <!--tr>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
          </tr-->
          <tr>
            <td colspan="4" style="line-height:normal">
			<table width="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-color:#FFFFFF">						
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt" width="100">Marks &amp; Nos/ Container No </th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">No. &amp; kind of PKgs </th>
	    <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Description of Goods </th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Quantity<div>In Kgs</div></th>
		<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Rate<div>US$</div><div>Per Kgs</div></th> 
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:8pt">Amount<div>CFR US$</div></th>
		</tr>
		<? 
		$totalAmount = 0;
		$prevContainerId = "";
		foreach($invoiceRecords as $ir)
		 {
		 	
			$numMC		=	$ir[16];
			$fishName 	= 	$ir[20];
			$gradeCode	=	$ir[21];
			
			$filledQty	=	$numMC * $ir[22];
			$pricePerKg = 	$ir[17];
			$amount		=	$filledQty * $pricePerKg;
			$totalAmount += $amount;
			
			$containerId	=	$ir[4];
			if($prevContainerId!=$containerId)
			{
				$containerNo	=	$ir[23];
				$sealNo			=	$ir[24];
			}
		?>
	  <tr bgcolor="#FFFFFF">
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;">Container No:<?=$containerNo?><br>Seal No:<?=$sealNo?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="center"><?=$numMC?>&nbsp;Cartons</td>
		 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" nowrap="nowrap"><?=$fishName?>&nbsp;COUNT/SIZE&nbsp;<?=$gradeCode?></td>
		 <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$filledQty?></td>
		<td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><?=$pricePerKg?></td>
        <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right"><? echo number_format($amount,2);?></td>
		</tr>
		<? 
		$prevContainerId = $containerId;
		}
		?>
	  <tr bgcolor="#FFFFFF">
	    <td height='30' colspan="5" align="right"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" nowrap>Amount in Words  </td>
                    <td class="listing-head" nowrap align="right">Total:</td>
                  </tr>
                  <tr>
                    <td class="listing-item" style="padding-left:5px;">CFR US$ 
					<? 
					$input = ceil($totalAmount);
					echo convert($input)?> Only</td>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                </table></td>
	    <td height='30' class="listing-item" style="padding-left:5px; padding-right:5px; font-size:8pt;" align="right" valign="top"><strong><? echo number_format($totalAmount,2);?></strong></td>
	    </tr>
	     </table></td>
            </tr>
          <tr>
            <td colspan="4"><table width="100%" align="center" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-color:#FFFFFF">						
      
	  <tr bgcolor="#FFFFFF">
	    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td colspan="2" nowrap class="listing-item">SEA CAUGHT PRODUCT. CAUGHT IN INDIAN OCEAN AREA FAO NO.51<BR>MANUFACTURER/PROCESSOR/PACKER:<BR><?=$companyArr["Name"];?><BR>
				<?=$addr["ADR6"];?> <br><?=$addr["ADR1"]?><br> <?=$addr["ADR7"];?><BR><?=$addr["ADR8"];?></td>
                    </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item">&nbsp;</td>
                    <td class="listing-item">&nbsp;</td>
                  </tr>
                  <tr>
                    <td class="listing-item"><table width="300" border="0" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="fieldName" style="line-height:normal">Declaration:</td>
                  </tr>
                  <tr>
                    <td class="listing-item">We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct </td>
                  </tr>
                </table>                    </td>
                    <td class="listing-item" valign="top"><table width="200" align="right" cellpadding="0" cellspacing="0" class="tdBoarder">
                  <tr>
                    <td class="listing-item" nowrap><strong>For <?=$companyArr["Name"];?></strong><br><br><br><br><div align="right">Authorised Signatory</div></td>
                  </tr>
                </table></td>
                  </tr>
                </table></td>
	    </tr>
	     </table></td>
            </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
	