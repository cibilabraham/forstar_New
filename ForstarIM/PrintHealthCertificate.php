<?php	
	require("include/include.php");
	require("lib/LanguageResource.php");

	$langResObj	=	new LanguageResource("resource_bundle/","hc_");
	
	# Get Id
	$selHCId = $g["selHCId"];

	# Find PO Records
	$healthCertificateRec	= $healthCertificateObj->find($selHCId);
		$editHealthCertificateId = $healthCertificateRec[0];
		$selLanguage		= $healthCertificateRec[1];
		$langResHandle	=	$langResObj->loadBundle($selLanguage);
		
		$consignorName		= $healthCertificateRec[2];
		$consignorAddress	= $healthCertificateRec[3];
		$consignorPostalCode	= $healthCertificateRec[4];
		$consignorTelNo		= $healthCertificateRec[5];
		$consigneeName		= $healthCertificateRec[6];
		$consigneeAddress	= $healthCertificateRec[7];
		$consigneePostalCode	= $healthCertificateRec[8];
		$consigneeTelNo		= $healthCertificateRec[9];
		$originCompanyName	= $healthCertificateRec[10];
		$originCompanyAddress	= $healthCertificateRec[11];
		$originCompanyPostalCode = $healthCertificateRec[12];
		$originCompanyTelNo	= $healthCertificateRec[13];
		$isoCode		= $healthCertificateRec[14];
		$regionOfOrigin		= $healthCertificateRec[15];
		$originCode		= $healthCertificateRec[16];
		$destinationCountry	= $healthCertificateRec[17];
		$approvalNumber		= $healthCertificateRec[18];
		$departureDate		= dateFormat($healthCertificateRec[19]);
		$identification		= $healthCertificateRec[20];
		$entryBPEU		= $healthCertificateRec[21];
		$commodityDesciption	= $healthCertificateRec[22];
		$commodityCode		= $healthCertificateRec[23];
		$netWt			= $healthCertificateRec[24];
		$grWt			= $healthCertificateRec[25];
		$noOfPackage		= $healthCertificateRec[26];
		$containerNo		= $healthCertificateRec[27];
		$sealNo			= $healthCertificateRec[28];
		$typeOfPackaging	= $healthCertificateRec[29];
		$species		= $healthCertificateRec[30];
		$natureOfCommodity	= $healthCertificateRec[31];
		

		$destinationIsoCode	= $healthCertificateRec[32];
		$transportType		= $healthCertificateRec[33];
		if ($transportType=='PLANE') 		$transportType1 = "checked";
		else if ($transportType=='SHIP')  	$transportType2 = "checked";
		else if ($transportType=='AIR')  	$transportType3 = "checked";
		else if ($transportType=='RAIL')  	$transportType4 = "checked";
		else if ($transportType=='ROAD')  	$transportType5 = "checked";
		else if ($transportType=='OTHER')  	$transportType6 = "checked";
		$proTempType		= $healthCertificateRec[34];  // Temperture of Product

		if ($proTempType=='AMB') 	$proTempType1= "checked";
		else if ($proTempType=='CHI') 	$proTempType2= "checked";
		else if ($proTempType=='FRO') 	$proTempType3= "checked"; 

		$humanConsumption	= $healthCertificateRec[35];
		$admissionEU		= $healthCertificateRec[36];
	
	/* Company Rec Starts Here */
	$companyRec	=	$companydetailsObj->find($editIt);
	$cName		=	stripSlash($companyRec[1]);
	$cAddress	=	stripSlash($companyRec[2]);
	$cPlace		=	stripSlash($companyRec[3]);
	$cPinCode	=	stripSlash($companyRec[4]);
	$cCountry	=	stripSlash($companyRec[5]);
	$cTelNo		=	stripSlash($companyRec[6]);
	$cFaxNo		=	stripSlash($companyRec[7]);
	$vatTin		=	stripSlash($companyRec[8]);
	$cstTin		= 	stripSlash($companyRec[9]);
	/* Company Rec Ends Here */
?>
<html>
<head>
<title>HEALTH CERTIFICATE</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="libjs/style.css" rel="stylesheet" type="text/css"><script language="javascript" type="text/javascript">
function printDoc()
{	
	window.print();	
	return false;
}

function displayBtn()
{
	document.getElementById("printButton").style.display="block";			
}

function printThisPage(printbtn)
{	
	document.getElementById("printButton").style.display="none";	
	if (!printDoc()) {
		setTimeout("displayBtn()",3500);			
	}		
}
</script>
</head>
<body>
<form name="frmPrintHealthCertificate">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td align="right">
		<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
		</td>
	</tr>
</table>
<table width='95%' cellspacing='0' cellpadding='0' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White">
		<TD>
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="right" style="padding-left:5px; padding-right:5px;">PAGE 1/3</TD>
				</TR>
			</table>
		</TD>
	</tr>	
	<tr bgcolor="White">
		<TD style="padding-left:5px; padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:11px;"><?=$langResHandle["headT.txt"];?><!--For Slovinia Lang--></span>
					</td>
				</TR>
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:11px;"><?=$langResHandle["head.txt"];?><!--HEALTH  CERTIFICATE  FOR EXPORT OF FISHERY PRODUCTS INTENDED FOR HUMAN CONSUMPTION--></span>
					</td>
				</TR>
				<TR>
					<td class="pageName" valign="bottom" align="center" style="font-size:11px;">
						<?=$langResHandle["head1.txt"];?><!--HEALTH  CERTIFICATE  FOR EXPORT OF FISHERY PRODUCTS INTENDED FOR HUMAN CONSUMPTION-->
					</td>
				</TR>
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:11px;"><?=$langResHandle["head2.txt"];?><!--HEALTH  CERTIFICATE  FOR EXPORT OF FISHERY PRODUCTS INTENDED FOR HUMAN CONSUMPTION--></span>
					</td>
				</TR>
				<TR>
					<td class="print-listing-item" valign="bottom" align="center">
						<!--Using in GER.txt -->
						<span style="font-size:8px;"><?=$langResHandle["subHead.txt"];?><!--HEALTH  CERTIFICATE  FOR EXPORT OF FISHERY PRODUCTS INTENDED FOR HUMAN CONSUMPTION--></span>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
	<tr bgcolor='white'>
		<td height="5"></td>
 	</tr>
	<tr bgcolor="White">
		<TD>
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="left" style="padding-left:5px; padding-right:5px;">Book No : </TD>
					<TD align="right" class="print-listing-item" style="padding-left:5px; padding-right:25px;">Sl. No.: </TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr bgcolor="White">
		<TD style="padding-left:5px; padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:9px;font-weight:normal;"><?=$langResHandle["slo.head2.txt"];?><!--For Slovinia Lang--></span>
					</td>
				</TR>
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:10px;"><?=$langResHandle["slo.head3.txt"];?><!--For Slovinia Lang--></span>
					</td>
				</TR>
				<TR>
					<td class="pageName" valign="bottom" align="center">
						<span style="font-size:11px;"><?=$langResHandle["slo.head4.txt"];?><!--For Slovinia Lang--></span>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
	<tr bgcolor='white'>
		<td height="5"></td>
 	</tr>
	<tr bgcolor="White">
		<TD>
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="left" style="padding-left:5px; padding-right:5px;"><strong><?=$langResHandle["country.txt"];?><!--COUNTRY  :-->  INDIA</strong></TD>
					<TD align="right" class="print-listing-item" style="padding-left:5px; padding-right:25px;"><strong><?=$langResHandle["vceu.txt"];?><!--Veterinary certificate to EU--></strong></TD>
				</TR>
			</table>
		</TD>
	</tr>	
 <tr bgcolor='white'>
	<td height="5"></td>
 </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top">
<!-- Page Ist -->
	<table width='50%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD valign="top">
				<table class="tblNOutline" cellpadding="0" cellspacing="0">
					<TR><TD style="border-right:none" width="100%" height="400">
						<IMG SRC="HCVerticalPage1.php?selHCId=<?=$selHCId?>" width="25" height="350">
					</TD></TR>
				</table>
			</TD>
		<td rowspan="3">
	<table width='50%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr>
			<TD rowspan="3" width="350px" colspan="4" valign="top"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["consigner.txt"];?><!--1.1 Consignor : -->
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						<?=$langResHandle["consigner.name"];?>
						<!--Name-->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consignorName?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" valign="top">
						<?=$langResHandle["consigner.address"];?>
						<!--Address-->
						</td>
						<td class="print-listing-item" style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consignorAddress?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						<?=$langResHandle["consigner.po"];?>
						<!--Postal Code--> 
						</td>
						<td class="print-listing-item" nowrap="nowrap" style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consignorPostalCode?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						<?=$langResHandle["consigner.phone"];?>
						<!--Tel.No.-->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consignorTelNo?></strong>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="2" rowspan="1" class="print-listing-item" align="left"  width="175px">
			<?=$langResHandle["cert.ref"];?>			
			<!--1.2 Certificate reference number--></TD>	
			<TD colspan="2" rowspan="1" class="print-listing-item" align="left" width="175px" valign="top">	
				<div style="position:absolute;">1.2a</div>
				<div><img src="images/1_2a_line.gif"></div>
			</TD>
		</tr>
		<tr>			
			<TD colspan="4">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:2px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.3.txt1"];?>			
						<!--1.3 Central Competent Authority-->
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.3.txt2"];?>
							<!--Export Inspection Council of India, New Delhi-->
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.3.txt3"];?>
							<!--(Ministry of Commerce & Industry, Govt. of India)-->
						</td>
					</tr>
				</table>
			</TD>						
		</tr>
		<tr>			
			<TD colspan="4">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:2px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.4.txt1"];?>
							<!--1.4 Local Competent Authority-->
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.4.txt2"];?>
							<!--Export Inspection Agency-Mumbai-->
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.4.txt3"];?>
							<!--(Ministry of Commerce & Industry, Govt. of India)-->
						</td>
					</tr>
				</table>
			</TD>						
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.5.txt1"];?>
						<!--1.5 Consignee :-->
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
							<?=$langResHandle["1.5.name"];?>
						<!--Name-->				
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consigneeName?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" valign="top">
						<?=$langResHandle["1.5.addr"];?>				
						<!--Address-->
						</td>
						<td class="print-listing-item" style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consigneeAddress?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						<?=$langResHandle["1.5.po"];?>
						<!--Postal Code -->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consigneePostalCode?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						<?=$langResHandle["1.5.tel"];?>					
						<!--Tel.No.-->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$consigneeTelNo?></strong>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="print-listing-item" align="left"  valign="top" >	
				<div style="position:absolute;">1.6</div>
				<div ><img src="images/1_6_line.gif"></div>
                      </TD>		
		</tr>
		<tr>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item">
							<?=$langResHandle["1.7.txt1"];?>
							<!--1.7 Country of origin -->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong>INDIA </strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item">
							<?=$langResHandle["1.7.iso"];?>			
							<!--ISO Code-->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$isoCode?></strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td class="print-listing-item">
							<?=$langResHandle["1.8.txt1"];?>
							<!--1.8 Region of origin -->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$regionOfOrigin?></strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item">
							<?=$langResHandle["1.8.code"];?>			
							<!--Code-->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$originCode?></strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td class="print-listing-item" nowrap="true">
							<?=$langResHandle["1.9.txt1"];?>			
							<!--1.9 Country of destination-->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$destinationCountry?></strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item">
							<?=$langResHandle["1.9.iso"];?>			
							<!--ISO Code-->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$destinationIsoCode?></strong>
						</td>
					</tr>					
				</table>
			</TD>
			<TD class="print-listing-item" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top">
							<div style="position:absolute;">1.10</div>
							<div><img src="images/1_10_line.gif"></div>		
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong></strong>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.11.txt1"];?>
						<!--1.11 Place of origin-->
					</td>
					<td class="print-listing-item" align="center"><strong>INDIA</strong></td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
							<?=$langResHandle["1.11.name"];?>
						<!--Name-->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$originCompanyName?></strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.11.addr"];?>			
						<!--Address-->
						</td>
						<td class="print-listing-item" style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$originCompanyAddress?></strong>
						</td>
					</tr>
					<tr><Td ></Td></tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" valign="top">
						</td>
						<td class="print-listing-item" align="center">
							<table cellspacing='0' cellpadding='0' class="tdBoarder">
								<tr>
								<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
									<strong>
									<?=$langResHandle["1.11.approval"];?>
									<!--APPROVAL NUMBER :--> 
									</strong>
								</td>
								<td class="print-listing-item" align="center"><strong><?=$approvalNumber?></strong></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="print-listing-item" align="left"  valign="top">
				<div style="position:absolute;">1.12</div>
				<div><img src="images/1_12_line.gif"></div>
				<!--<div align="center" style="text-align:center;padding-top:10px;"><strong>NA</strong></div>-->
			<!--1.12<br>	
				<span> <img src="images/FormLine.png" width="250" height="65"></span>-->
			</TD>		
		</tr>
		<tr>
			<TD width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
						<?=$langResHandle["1.13.txt1"];?>				
						<!--1.13 Place of loading-->
					</td>					
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" align="center" colspan="2">
						<strong>JNPT INDIA</strong>
						</td>						
					</tr>					
				</table>
			</TD>
			<TD colspan="4" rowspan="1" class="print-listing-item" align="left"  valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
						<?=$langResHandle["1.14.txt1"];?>				
						<!--1.14 Date of departure-->
					</td>					
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;" align="center" colspan="2">
						<strong><?=$departureDate?></strong>
						</td>						
					</tr>					
				</table>
			</TD>		
		</tr>
		<tr>
			<TD rowspan="2" width="300px" colspan="4"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.15.txt1"];?>				
						<!--1.15 Means of Transport : -->
					</td>
					</tr>
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:30px;pading-right:5px;">
						<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100">
						<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
							<?//$langResHandle["1.15.aero"];?>
						  	<!--Aeroplane-->
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.aero"];?>
									<!--Ship-->
								</td>
								<td valign="top">
								<? if ($transportType1) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
								<? }?>
								</td>
								</tr>
							</table>
						</td>
						<td></td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;" colspan="2">
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.ship"];?>
									<!--Ship-->
								</td>
								<td valign="top">
									<? if ($transportType2) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
										<!--&#10004;-->
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
									<? }?>
								</td>
								</tr>
							</table>
							
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
							<?//$langResHandle["1.15.air"];?>
						  <!--BY AIR-->
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.air"];?>
								</td>
								<td valign="top">
								<? if ($transportType3) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
								<? }?>
								</td>
								</tr>
							</table>
						</td>
						<td></td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:5px;">
							<?//$langResHandle["1.15.railway"];?>
							<!--Railway wagon-->
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.railway"];?>
								</td>
								<td valign="top">
								<? if ($transportType4) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
								<? }?>
								</td>
								</tr>
							</table>
						</td>
						<td>							
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
							<?//$langResHandle["1.15.road"];?>
						<!-- Road Vehicle-->
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.road"];?>
								</td>
								<td valign="top">
								<? if ($transportType5) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
								<? }?>
								</td>
								</tr>
							</table>
						</td>
						<td></td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:5px;">
							<?//$langResHandle["1.15.other"];?>
							<!--Other-->
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="150">
								<tr>
								<td nowrap="nowrap" class="print-listing-item" style="padding-left:5px;pading-right:10px;">
									<?=$langResHandle["1.15.other"];?>
								</td>
								<td valign="top">
								<? if ($transportType5) {?>
									<table class="print" width="50">
										<tr><TD style="padding-left:20px;padding-right:20px;">
											<img src="images/by.gif"/>
										</TD></tr>
									</table>
								<? }?>
								</td>
								</tr>
							</table>
						</td>
						<td>							
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
						<strong>
							<?=$langResHandle["1.15.identity"];?>			
							<!--Identification--> 
						</strong>
						</td>						
						<td class="print-listing-item"   style="padding-left:5px;pading-right:5px;font-size:8px;" colspan="2">
							<strong><?=$identification?></strong>
						</td>					
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;" colspan="2">
							<?=$langResHandle["1.15.document"];?>			
						<!-- Documentary refrences:-->
						</td>						
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:5px;pading-right:5px;" colspan="2">		
						</td>					
					</tr>
						</table>
					</td>
					</tr>					
				</table>
			</TD>
			<TD colspan="4" class="print-listing-item" align="left"  width="250px" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.16.txt1"];?>				
						<!--1.16 Entry B/P in EU-->
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:30px;pading-right:5px;">
						
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:10px;pading-right:10px;font-size:8px;">
							<strong><?=$entryBPEU?></strong>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
		<tr>			
			<TD colspan="4" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" style="padding-left:2px;pading-right:5px;" valign="top">
							<div style="position:absolute;">1.17</div>
							<div><img src="images/1_17_line.gif"></div>		
						</td>
					</tr>					
				</table>
			</TD>						
		</tr>
		<tr>
			<TD width="300px" colspan="4" rowspan="2" valign="top"> 
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.18.txt1"];?>				
						<!--1.18 Description of Commodity:-->
					</td>					
					</tr>
					<tr>
						<td class="print-listing-item"  style="padding-left:30px;pading-right:5px;font-size:8px;">
						<strong><?=$commodityDesciption?></strong>
						</td>
					</tr>
				</table>
			</TD>
			<TD colspan="2" class="print-listing-item" align="left"  rowspan="2" valign="top">
				<?=$langResHandle["1.19.txt1"];?>				
				<!--1.19 Commodity code(HS Code)-->
			</TD>	
			<TD class="print-listing-item" align="left" valign="top"><strong><?=$commodityCode?></strong></TD>
		</tr>
		<tr>
			<td>
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-item" colspan="2"  style="padding-left:5px;pading-right:5px;">
						<?=$langResHandle["1.20.txt1"];?>				
						<!--1.20 Quantity :-->
					</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:20px;pading-right:5px;">
							<?=$langResHandle["1.20.net"];?>			
							<!--NET.WT.:-->
						</td>
						<td class="print-listing-item" nowrap="nowrap"  style="padding-left:5px;pading-right:5px;font-size:8px;">
							<strong><?=$netWt?> KGS.</strong>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" class="print-listing-item"  style="padding-left:20px;pading-right:5px;" valign="top">
							<?=$langResHandle["1.20.gr"];?>
							<!--GR.WT.:-->
						</td>
						<td class="print-listing-item" style="padding-left:5px;pading-right:5px;font-size:8px;" nowrap="true">
							<strong><?=$grWt?> KGS</strong>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<tr>
			<TD colspan="6">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top">
							<?=$langResHandle["1.21.txt1"];?>
							<!--1.21 Temperature of product-->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;">
							<table cellspacing='0' cellpadding='0' width="80%">
							<tr><TD height="5"></TD></tr>
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<?//$langResHandle["1.21.amb"];?>
									<!--Ambient-->
									<table cellspacing='0' cellpadding='0' width="150">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
										<?=$langResHandle["1.21.amb"];?>
										</td>
										<td nowrap="nowrap" valign="top" align="right">
										<? if ($proTempType1) {?>
										<table class="tblNOutline" width="50" cellpadding="0" cellspacing="0">
											<tr><TD style="padding-left:20px;padding-right:20px; border-bottom:none;" >
											<!--&#10004;-->
											<img src="images/by.gif"/>
											</TD></tr>
										</table>
										<? }?>
										</td>
									</tr>
									</table>
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top">
									<?//$langResHandle["1.21.chi"];?>
									<!--Chilled-->
									<table cellspacing='0' cellpadding='0' width="150">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
										<?=$langResHandle["1.21.chi"];?>
										</td>
										<td nowrap="nowrap" valign="top" align="right">
										<? if ($proTempType2) {?>
										<table class="tblNOutline" width="50" cellpadding="0" cellspacing="0">
											<tr><TD style="padding-left:20px;padding-right:20px; border-bottom:none;" >
											<!--&#10004;-->
											<img src="images/by.gif"/>
											</TD></tr>
										</table>
										<? }?>
										</td>
									</tr>
									</table>
								</td>
								<td nowrap="nowrap" valign="top">
									<table cellspacing='0' cellpadding='0' width="100%">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
										<?=$langResHandle["1.21.fzn"];?>
										<!--FROZEN-->
										</td>
										<td nowrap="nowrap" valign="top" align="right">
										<? if ($proTempType3) {?>
										<table class="tblNOutline" width="90%" cellpadding="0" cellspacing="0">
											<tr><TD style="padding-left:20px;padding-right:20px; border-bottom:none;" >
											<!--&#10004;-->
											<img src="images/by.gif"/>
											</TD></tr>
										</table>
										<? }?>
										</td>
									</tr>
									</table>
								</td>
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
			<TD class="print-listing-item" valign="top">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td class="print-listing-item" valign="top">
							<?=$langResHandle["1.22.txt1"];?>			
							<!--1.22 Number of Packages : -->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" class="print-listing-item" align="center">
							<strong><?=$noOfPackage?> M/CTN.</strong>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>
<tr>
			<TD colspan="4">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top">
							<?=$langResHandle["1.23.txt1"];?>			
							<!--1.23 Identification of container / Seal number:-->
						</td>
					</tr>	
					<tr>
						<td style="padding-left:30px;pading-right:5px;" >
							<table cellspacing='0' cellpadding='0' width="80%">
							<tr><TD height="5"></TD></tr>
							<tr>
								<td class="print-listing-item" valign="top" align="center">
									<table cellspacing='0' cellpadding='0' class="tdBoarder">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top">
											<?=$langResHandle["1.23.container"];?>	
											<!--CONTAINER NO.:--> 
										</td>
										<td nowrap="nowrap" class="print-listing-item" align="center">
											<strong><?=$containerNo?></strong>
										</td>
									</tr>					
									</table>
								</td>					
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									
									<table cellspacing='0' cellpadding='0' class="tdBoarder">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top">
											<?=$langResHandle["1.23.seal"];?>
											<!--SEAL NO.:-->
										</td>
										<td nowrap="nowrap" class="print-listing-item" align="center">
											<strong><?=$sealNo?></strong>
										</td>
									</tr>					
								</table>
								</td>					
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
			<TD valign="top" colspan="3">
				<table cellspacing='0' cellpadding='0' class="tdBoarder">
					<tr>
						<td  class="print-listing-item" valign="top">
							<?=$langResHandle["1.24.txt1"];?>			
							<!--1.24 Type of Packaging-->
						</td>
					</tr>	
					<tr>
						<td class="print-listing-item" align="center">
							<strong><?=$typeOfPackaging?></strong>
						</td>
					</tr>					
				</table>
			</TD>
		</tr>	
	<tr>
			<TD colspan="7">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top">
							<?=$langResHandle["1.25.txt1"];?>			
							<!--1.25 Commodities certified for -->
						</td>
					</tr>	
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;" colspan="5">
							<table cellspacing='0' cellpadding='0' width="80%">
							<tr><TD height="5"></TD></tr>
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="left">
									<table cellspacing='0' cellpadding='0' width="200">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
										  <?=$langResHandle["1.25.human"];?>
										   <!--Human consumption-->
										</td>
										<td nowrap="nowrap" valign="top" align="right">
										<? if ($humanConsumption=='Y') {?>
										<table class="tblNOutline" width="100" cellpadding="0" cellspacing="0">
											<tr><TD style="padding-left:20px;padding-right:20px; border-bottom:none;" >
											<!--&#10004;-->
											<img src="images/by.gif"/>	
											</TD></tr>
										</table>
										<? }?>
										</td>
									</tr>
									</table>
								</td>						
							</tr>	
							</table>
						</td>
					</tr>				
				</table>
			</TD>
		</tr>	
	<tr>
			<TD width="300px" colspan="4" class="print-listing-item">
				<div style="position:absolute;">1.26</div>
				<div><img src="images/1_26_line.gif"></div>
			</TD>
			<TD colspan="3" rowspan="1" class="print-listing-item" align="left" valign="bottom">
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
							<tr><TD height="5"></TD></tr>
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="bottom" align="left">
									<table cellspacing='0' cellpadding='0' width="100%">
									<tr>
										<td nowrap="nowrap" class="print-listing-item" valign="bottom" align="left">
										 <?=$langResHandle["1.27.txt1"];?>
										<!--1.27 For import or admission into EU-->
										</td>
										<td nowrap="nowrap" valign="top" align="right">
										<? if ($admissionEU=='Y') {?>
										<table class="tblNOutline" width="100" cellpadding="0" cellspacing="0">
											<tr><TD style="padding-left:20px;padding-right:20px; border-bottom:none; border-right:none" >
											<!--&#10004;-->
											<img src="images/by.gif"/>
											</TD></tr>
										</table>
										<? 
											}
										?>
										</td>
									</tr>
									</table>
								</td>						
							</tr>	
							</table>
			</TD>			
		</tr>
	<tr>
			<TD colspan="7">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top">		
							<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="top">
									<?=$langResHandle["1.28.txt1"];?>
									<!--1.28 Identification of the commodities-->
								</td>
								<td class="print-listing-item" valign="top">
									<table cellspacing='0' cellpadding='0' class="tdBoarder" width="90%">
									<tr>
										<td class="print-listing-item" valign="top">
											<?=$langResHandle["1.28.approval"];?>
										</td>
										<td class="print-listing-item" valign="top">
											<strong><?=$approvalNumber?></strong>
										</td>
									</tr>
									</table>			
									<!--Approval no of establishment:-->	
								</td>
							</tr>	
							</table>
						</td>
					</tr>	
					<tr><TD height="5"></TD></tr>
					<tr>
						<td nowrap="nowrap" style="padding-left:30px;pading-right:5px;" colspan="5">
							<table cellspacing='0' cellpadding='0' width="100%">
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">	
									<?=$langResHandle["1.28.species"];?>
									<!--Species-->				
								</td>	
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.nature"];?>
									<!--Nature of commodity-->
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.treatment"];?>
									<!--Treatment type-->
								</td>
								<td class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.manuplant"];?>	
									<!--Manufacturing plant-->
								</td>
								<td class="print-listing-item" valign="top" align="center">	
									<?=$langResHandle["1.28.numpack"];?>	
									<!--Number of packages-->	
								</td>
								<td class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.netwt"];?>
									<!--Net weight-->
								</td>					
							</tr>	
							<tr>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.sciname"];?>	
									<!--(Scientific name)-->
								</td>	
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<?=$langResHandle["1.28.cases"];?>
									<!--( Cases)-->
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									( KGS )
								</td>					
							</tr>
							<tr>
								<td class="print-listing-item" valign="top" align="center">	
									<strong><?=$species?></strong>	
								</td>	
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<strong><?=$natureOfCommodity?></strong>
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<strong>FROZEN</strong>
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<strong>PROCESSING PLANT</strong>
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">	
									<strong><?=$noOfPackage?> M/CTN</strong>	
								</td>
								<td nowrap="nowrap" class="print-listing-item" valign="top" align="center">
									<strong><?=$netWt?> KGS.</strong>
								</td>					
							</tr>
							</table>
						</td>
					</tr>				
				</table>
			</TD>
		</tr>
	</table>
	</tr>
	<tr><TD></TD></tr>
	<tr><TD></TD></tr>
	</table>
<!--  Border table ends Here-->
	</td>
  </tr>
	</table>
	</td>
</tr>
</table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<!--  Page 2 Starts Here-->
<table width='100%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White">
		<TD height="5">
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="right" style="padding-left:5px; padding-right:5px;">PAGE 2/3</TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr bgcolor="White"><TD height="5"></TD></tr>
		<tr bgcolor="White">
		<TD height="5">
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="left" style="padding-left:5px; padding-right:5px;">
						<strong><?=$langResHandle["II.country"];?><!--COUNTRY  :-->  INDIA</strong>
					</TD>
					<TD align="right" class="print-listing-item" style="padding-left:5px; padding-right:25px;">
						<strong><?=$langResHandle["II.fishery"];?><!--Fishery products--></strong>
					</TD>
				</TR>
			</table>
		</TD>
	</tr>	
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top">
<!-- Page IInd -->
<table width='100%' cellpadding='0' cellspacing='0' align="center">
		<tr>
			<TD valign="top">
				<table class="tblNOutline" cellpadding="0" cellspacing="0" width="100%">
					<TR><TD style="border-right:none" width="100%" height="400">
						<IMG SRC="HCVerticalPage2.php?selHCId=<?=$selHCId?>" width="30" height="350">
					</TD></TR>
				</table>
			</TD>
		<td rowspan="3">
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
				<tr>
					<td nowrap="nowrap" class="print-listing-item" valign="top">
						<strong><?=$langResHandle["II.health"];?>
						<!--II. Health attestation-->
						</strong>
					</td>
					<td nowrap="nowrap" class="print-listing-item" valign="top" align="right">	
						<table class="tblNOutline" width="350" cellpadding="0" cellspacing="0">
							<tr>
								<TD style="padding-left:20px;padding-right:20px; border-top:none;" class="print-listing-item" nowrap="true" width="250">
								<?=$langResHandle["II.a.certificate"];?>	
									<!--II.a.Certificate reference number.-->
								<br><br><br>
								</TD>
								<TD style="padding-left:20px;padding-right:20px; border-top:none;border-right:none;" class="print-listing-item" width="250" valign="top"> II.b
								</TD>
							</tr>
						</table>	
					</td>
				</tr>	
		<tr><TD height="20"></TD></tr>
		<tr>
			<td nowrap="nowrap" class="print-listing-item" valign="top" colspan="2">
				<strong>
					<?=$langResHandle["II.1.public"];?>
					<!--II.1 Public Health attestation-->
				</strong>
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:40px; pading-right:10px;" class="print-listing-item">	
				<?=$langResHandle["II.1.txt1"];?>					
				<!--I, the undersigned, declare that I am aware of the relevant provisions of Regulations (EC) No 178/2002, (EC) No 852/2004, (EC) No 853/2004, and  (EC) No 854/2004, and certify that the fishery products described above were produced in accordance with those requirements, in particular that they : -->		
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">
				<?=$langResHandle["II.1.txt2"];?>
				<!--come from (an) establishment(s) implementing a programme based on the HACCP principles in accordance with Regulation(EC) No 852/2004,-->
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">	
				<?=$langResHandle["II.1.txt3"];?>			
				<!--have beeb caught and handled on board vessels , landed, handled and where appropriate prepared, processed, frozen and thawed hygienically in compliance with the requirements laid down in Section VIII, Chapter I to IV of Annex III to Regulation(EC) No 853/2004-->
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">
				<?=$langResHandle["II.1.txt4"];?>
				<!--satisfy the health standards laid down in Section VIII , Chapter V of Annex III to Regulation(EC) No 853/2004 and the criteria laid down in Regulation(EC) No 2073/2005 on microbiological criteria for foodstuffs, -->
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">	
				<?=$langResHandle["II.1.txt5"];?>
				<!--have been packaged, stored and transported in compliance with Section VIII , Chapter VI to VIII of Annex III to Regulation (EC) No 853/2004-->
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">
				<?=$langResHandle["II.1.txt6"];?>
				<!--have been marked in accordance with Section I of Annex II to Regulation(EC) No 853/2004-->
			</td>
		</tr>	
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">
				<?=$langResHandle["II.1.txt7"];?>				
				<!--the guarantees covering live animals and products thereof, if from aquaculture origin, provided by the residue plans submitted in accordance with Directive 96/23/EC, and in particular Article 29 thereof are fulfilled-->
			</td>
		</tr>	
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">	
				<?=$langResHandle["II.1.txt8"];?>			
				<!--and-->
			</td>
		</tr>
		<tr><TD height="10"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:60px; pading-right:10px;" class="print-listing-item">	
				<?=$langResHandle["II.1.txt9"];?>
				<!--have satisfactorily undergone the official controls laid down in Annex III to Regulation (EC) No 854/2004.-->
			</td>
		</tr>		
		<tr><TD height="20"></TD></tr>
		</table>
			</TD>
		</tr>
<!-- 	IIN d row -->
		<tr>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<tr>
						<td nowrap="nowrap" class="print-listing-item" valign="top" colspan="2">
							<strong>II.2</strong>&nbsp;&nbsp;(1)<span class="print-listing-item" style="padding-left:10px; pading-right:10px;"></span>
								<?=$langResHandle["II.2.1.txt"];?>
							 <!--[Animal health attestation for products of aquaculture origin-->
						</td>
					</tr>
					<tr><TD height="10"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:60px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt1"];?>	
							<!--I, the undersigned, declare that the fishery products described above originate from fish or crusteceans that were clinically healthy on the day of harvest, and have been transported under conditions that do not alter the animal health status of the products and certify, in particular that: -->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:100px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt2"];?>
							<!--(1)[(2) if from species susceptible (3) to ISA and/or EHN , they:-->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:140px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt3"];?>
							<!--(1) [originate from a source(4) considered free from ISA and /or EHN in accordance with the relevant EU legislation or OIE Standard (5) ] , -->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:140px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt4"];?>
							<!--(1)[have been slaughtered and eviscerated]] -->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:100px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt5"];?>
							<!--(1)[(6) if from species susceptible (3) to VHS and/or IHN , they :-->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:140px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt6"];?>
							<!--(1) originate from a source (4) considered free from (1) VHS/(1) IHN in accordance with the relevant EU legislation or OIE Standard (5)], -->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
					<tr>
						<TD class="print-listing-item" style="padding-left:140px; pading-right:10px;">
							<?=$langResHandle["II.2.1.txt7"];?>
							<!--(1) [have been slaughtered and eviscerated]]].-->
						</TD>
					</tr>
					<tr><TD height="20"></TD></tr>
				</table>
			</TD>
		</tr>
	</table>
	</td>
	</tr>
	<tr><TD></TD></tr>
	<tr><TD></TD></tr>
	</table>
<!--  II Border table ends Here-->
	</td>
  </tr>
	</table>
	</td>
</tr>
</table>
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
<!--  Page 2 Starts Here-->
<table width='100%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White">
		<TD height="5">
			<table width="100%">
				<TR>
					<TD class="print-listing-item" align="right" style="padding-left:5px; padding-right:5px;">PAGE 3/3</TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr bgcolor="White"><TD height="5"></TD></tr>		
   <tr bgcolor=white>
	<td align="LEFT" valign="top">
<!-- Page Ist -->
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center">
		<tr>
			<TD>
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
				<tr>
					<td nowrap="nowrap" class="print-listing-item" valign="top">
						<strong><?=$langResHandle["III.notes"];?><!--Notes--></strong>
					</td>
				</tr>	
		<tr><TD height="5"></TD></tr>
		<tr>
			<td nowrap="nowrap" class="print-listing-item" valign="top">
				<strong><?=$langResHandle["III.p.I"];?><!--Part I :--></strong>
			</td>
		</tr>
		<tr><TD height="5"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:45px; pading-right:10px;" class="print-listing-item">	
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.8"];?>
							<!--Box reference 1.8     :-->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.8.txt"];?>
							<!--Region of Origin and if appropriate, indicate zones as listed in commission Decisions 2002/308/EC and 2003/634/EC.For frozen or processed bivalve molluscs, indicate the production area.-->
						</td>
					</TR>
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.11"];?>
							<!--Box reference 1.11   : -->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">	
							<?=$langResHandle["III.1.11.txt"];?>
							<!--Place of origin, name  and address of the dispatch establishment.-->
						</td>
					</TR>
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.15"];?>			
							<!--Box reference 1.15   :-->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.15.txt"];?>
							<!--Registration number(railway wagons or container and lorries),flight number(aircraft) or name(ship), saperate information is to be provided in the event of unloading and reloading.-->
						</td>
					</TR>
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.19"];?>	
							<!--Box reference 1.19   :-->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.19.txt"];?>
							<!--Use the appropriate HS Codes: 03.01, 03.02, 03.04, 03.05, 03.06, 03.07, 05.11.91, 15.04, 15.18.00, 16.03, 16.04, 16.05.-->
						</td>
					</TR>
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.23"];?>			
							<!--Box reference 1.23   :-->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.23.txt"];?>
							<!--Identification of container/seal number : only when applicable-->
						</td>
					</TR>
					<tr><TD height="5"></TD></tr>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
							<?=$langResHandle["III.1.28"];?>
							<!--Box reference 1.28   :-->
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.28.txt"];?>
							<!--Nature of commodity, specify if aquaculture or wild origin.-->
						</td>
					</TR>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.28.txt1"];?>
							<!--Treatment type : live ,chilled,frozen, processed.-->
						</td>
					</TR>
					<TR>
						<TD class="print-listing-item" valign="top" nowrap="true">
						</TD>
						<td class="print-listing-item" valign="top" style="pading-left:5px;padding-right:5px;">
							<?=$langResHandle["III.1.28.txt2"];?>
							<!--Manufacturing plant: includes factory vessel, freezer vessel, cold store, processing plant.-->
						</td>
					</TR>
				</table>				
			</td>
		</tr>		
		<tr>
			<td nowrap="nowrap" class="print-listing-item" valign="top">
				<strong><?=$langResHandle["III.p.II"];?><!--Part II :--></strong>
			</td>
		</tr>
		<tr><TD height="5"></TD></tr>
		<tr>
			<td valign="top" colspan="2" style="padding-left:45px; pading-right:10px;" class="print-listing-item">	
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">
					<TR>
						<TD class="print-listing-item" valign="top">
							<?=$langResHandle["III.p.II.txt"];?>			
							<!--Part II.2 is not relevant for consignments intended for retail , provided they comply with the rules applying to packaging and labelling laid down in Regulation (EC) No.853/2004-->
						</TD>					
					</TR>								
					<tr><TD height="5"></TD></tr>					
				</table>				
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2"  class="print-listing-item">	
				<TR cellspacing='0' cellpadding='0' class="tdBoarder">
					<TR>
						<td class="print-listing-item" nowrap="true" valign="top">(1)</td>
						<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">	
							<?=$langResHandle["III.p.II.txt1"];?>
							<!--Delete as appropriate.-->
						</TD>					
					</TR>	
					<tr><TD height="5"></TD></tr>	
					<TR>
						<td class="print-listing-item" nowrap="true" valign="top">(2)</td>
						<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
							<?=$langResHandle["III.p.II.txt2"];?>	
							<!--This part of the animal health certificate is only relevant if the consignment comprises species referred to as susceptible to ISA and / or EHN.-->
						</TD>					
					</TR>
					<tr><TD height="5"></TD></tr>	
					<TR>
						<td class="print-listing-item" nowrap="true" valign="top"></td>
						<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
							<?=$langResHandle["III.p.II.txt2.1"];?>
							<!--The requirement applies to exports to all member states , whereby one of the two statements should be retained , unless the consignment is intended for further processing in an approved import centre.-->
						</TD>					
					</TR>	
					<tr><TD height="5"></TD></tr>	
					<TR>
						<td class="print-listing-item" nowrap="true" valign="top">(3)</td>
						<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
							<?=$langResHandle["III.p.II.txt3"];?>
							<!--Known susceptible species.-->
						</TD>					
					</TR>	
					<tr><TD height="5"></TD></tr>	
					<TR>
						<td class="print-listing-item" nowrap="true" valign="top"></td>
						<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
							<table cellpadding="1" cellspacing="2" class="tdBoarder">
								<TR>
									<TD class="print-listing-item" nowrap="true" style="padding-right:40px">
									<strong>
									<?=$langResHandle["III.p.II.disease"];?>
										<!--Disease-->
									</strong>
									</TD>
									<TD class="print-listing-item" nowrap="true" style="padding-left:40px">
									<strong>
									   <?=$langResHandle["III.p.II.species"];?>
										<!--Susceptible host species.-->
									</strong>
									</TD>
								</TR>
								<TR>
									<TD class="print-listing-item" nowrap="true" style="padding-right:40px" valign="top">
									<?=$langResHandle["III.p.II.EHN.h"];?>
									<!--EHN-->
									</TD>
									<TD class="print-listing-item" style="padding-left:40px">
										<?=$langResHandle["III.p.II.EHN"];?>
										<!--Redfin perch(Perca fluviatilis), rainbow trout(Oncorhynchus mykiss)-->
									</TD>
								</TR>
								<TR>
									<TD class="print-listing-item" nowrap="true" style="padding-right:40px" valign="top">
									<?=$langResHandle["III.p.II.ISA.h"];?>	
									<!--ISA--> 
									</TD>
									<TD class="print-listing-item" style="padding-left:40px">
									  <?=$langResHandle["III.p.II.ISA"];?>
									<!--Atlantic salmon (Salmo salar) , rainbow trout(Oncorhynchus mykiss),brown trout(Salmo trutta),-->
									</TD>
								</TR>
								<TR>
									<TD class="print-listing-item" nowrap="true" style="padding-right:40px" valign="top">
									<?=$langResHandle["III.p.II.VHS.h"];?>
									<!--VHS--> 
									</TD>
									<TD class="print-listing-item" style="padding-left:40px">
									<?=$langResHandle["III.p.II.VHS"];?>
									<!--Atlantic cod(Gadus morthua),Atlantic herring(Clupea harengus),brown trout(Salmo trutta), Chinook salmon(Oncorhynchus tshawytscha),coho salmon(O.kisutch), grayling(thymallus thymallus), haddock(melanogrammus aeglefinus),Pacific cod(Gadus macrocephalus), Pacific herring(Cluteaharengus pallasi), pike(Esox lucius), Rainbow trout(Oncorhynchus mykiss) rockling(Rhinonemus cimbrius), sprat(Sprattus sprattus),turbot (Scophthalmus maximus), white fish (Corugonus sp.)-->
									</TD>
								</TR>
								<TR>
									<TD class="print-listing-item" nowrap="true" style="padding-right:40px" valign="top">
									<?=$langResHandle["III.p.II.IHN.h"];?>
									<!--IHN -->
									</TD>
									<TD class="print-listing-item" style="padding-left:40px">
									<?=$langResHandle["III.p.II.IHN"];?>	
									<!--Rainbow or steelhead trout(Oncorhynchus mykiss), the Pacific salmon species(chinook salmon (O.tshawytscha),sockeye salmon(O.nerka), chum salmon(O.keta),masou salmon(O.masou), pink salmon(O.rhodurus) and coho salmon(O.kisutch), and Atlantic salmon(Salmo salar).-->
									</TD>
								</TR>
							</table>
						</TD>					
					</TR>	
		<tr><TD height="5"></TD></tr>	
		<TR>
			<td class="print-listing-item" nowrap="true" valign="top">(4)</td>
			<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
				<?=$langResHandle["III.p.II.txt4"];?>
			<!--Source may be a country, zone, or an individual farm.-->
			</TD>					
		</TR>	
		<tr><TD height="5"></TD></tr>	
		<TR>
			<td class="print-listing-item" nowrap="true" valign="top">(5)</td>
			<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
				<?=$langResHandle["III.p.II.txt5"];?>
				<!--Freedom according to the provisions laid down in Annex B or C to Directive 91/67/EEC, and Commission Decisions 2001/183/EEC and 2003/466/EC. Freedom according to the most current edition of the OIE Code and Manual is also recognised.-->
			</TD>					
		</TR>	
		<tr><TD height="5"></TD></tr>	
		<TR>
			<td class="print-listing-item" nowrap="true" valign="top">(6)</td>
			<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
				<?=$langResHandle["III.p.II.txt6"];?>
				<!--This part of the animal health certificate is only relevent if the consignment comprises species referred to as susceptible to VHS and/or IHN.In order for the consignment to be authorised into a Member State or part thereof (boxes 1.9 and 1.10 of part I of the certificate) declared free from VHS , and/or IHN, or undergoing a programme for such freedom, one of the two statements must be retained , unless the consignment is intended for further processing in an approved import centre.-->
			</TD>					
		</TR>
		<tr><TD height="5"></TD></tr>	
		<TR>
			<td class="print-listing-item" nowrap="true" valign="top"></td>
			<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
				<?=$langResHandle["III.p.II.txt6.1"];?>
				<!--A list of such Member States and Zones are listed in Commission Decisions 2002/308/EC and 2003/634/EC.-->
			</TD>					
		</TR>
		<tr><TD height="5"></TD></tr>	
		<TR>
			<td class="print-listing-item" nowrap="true" valign="top"></td>
			<TD class="print-listing-item" valign="top" style="padding-left:25px; pading-right:10px;">
				<?=$langResHandle["III.p.II.txt6.2"];?>
				<!--The colour of the stamp and signature must be different from that of the most particulars in the certificate.-->
			</TD>					
		</TR>
		</table>				
			</td>
		</tr>
		<tr>
			<TD style="padding-left:5px;">
				<table cellspacing='0' cellpadding='0' class="tdBoarder" width="100%">		
					<TR>
						<TD class="print-listing-item">
							<strong><?=$langResHandle["III.OFFI.txt"];?><!--Official Inspector--></strong>
						</TD>
					</TR>
					<tr><TD height="5"></TD></tr>	
					<TR>
						<TD class="print-listing-item">
							<strong> <?=$langResHandle["III.OFFI.txt1"];?><!--Name (In Capitals):--> </strong>
						</TD>
						<td width="5"></td>
						<TD class="print-listing-item">
							<strong> <?=$langResHandle["III.OFFI.txt2"];?> <!--Qualification and Title :--> </strong>
						</TD>
					</TR>
					<tr><TD height="5"></TD></tr>	
					<TR>
						<TD class="print-listing-item">
							<strong> <?=$langResHandle["III.OFFI.txt3"];?> <!--Date :-->  </strong>
						</TD>
						<td width="5"></td>
						<TD class="print-listing-item">
							<strong><?=$langResHandle["III.OFFI.txt4"];?><!--Signature :--> </strong>
						</TD>
					</TR>
					<tr><TD height="5"></TD></tr>	
					<TR>
						<TD class="print-listing-item">
							<strong> <?=$langResHandle["III.OFFI.txt5"];?>  <!--Stamp :--> </strong>
						</TD>
						<td width="5"></td>
						<TD class="print-listing-item">			
						</TD>
					</TR>	
					<tr><TD height="5"><br><br></TD></tr>		
				</table>
			</TD>
		</tr>
		
		</table>
			</TD>
		</tr>
		
	</table>
<!--  III Border table ends Here-->
	</td>
  </tr>
	</table>
	</td>
</tr>
</table>
<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->