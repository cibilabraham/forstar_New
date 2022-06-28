function validateShipmentInvoice(form, confirmed)
{	
	var invoiceNo		= document.getElementById("invoiceNo");
	var invoiceDate		= document.getElementById("invoiceDate");
	var discount 		= document.getElementById("discount").checked;
	var exporter		= document.getElementById("exporter");

	if (exporter.value=="")
	{
		alert("Please select a Exporter");
		exporter.focus();
		return false;
	}

	if ((invoiceNo.value=="" || invoiceNo.value==0) && confirmed) {
		alert("Please enter a Invoice No.");
		invoiceNo.focus();
		return false;
	}

	if (invoiceDate.value=="" && confirmed) {
		alert("Please select a invoice date.");
		invoiceDate.focus();
		return false;
	}

	if (discount) {
		var discountRemark 	= document.getElementById("discountRemark");
		var discountAmt 	= document.getElementById("discountAmt");
		if (discountRemark.value=="") {
			alert("Please enter Discount remark.");
			discountRemark.focus();
			return false;
		} 
		
		if (discountAmt.value=="") {
			alert("Please enter Discount amount.");
			discountAmt.focus();
			return false;
		}

		/*
		if (!checkNumber(discountAmt.value)) {
			discountAmt.focus();
			return false;
		}
		*/

	}
	
	if (confirmed) {
		var validInvoiceNo = document.getElementById("validInvoiceNo").value;
		var validInvoiceDate = document.getElementById("validInvoiceDate").value;
		var containerRowCount = document.getElementById("hidTableRowCount").value;		
		var containerSelected = false;
		var productRowCount	= document.getElementById("hidProductItemCount").value;
		var finalDestination    = document.getElementById("finalDestination");
		var containerMarks	= document.getElementById("containerMarks");
		var goodsDescription	= document.getElementById("goodsDescription");
		var vessalRecSize	= document.getElementById("vessalRecSize").value;
		var totGrossWt		= document.getElementById("totGrossWt");
		var totNetWt		= document.getElementById("totNetWt");
		var shipBillNo		= document.getElementById("shipBillNo");
		var shipBillDate	= document.getElementById("shipBillDate");
		var billLaddingNo	= document.getElementById("billLaddingNo");
		var billLaddingDate	= document.getElementById("billLaddingDate");
		var loadingPort		= document.getElementById("loadingPort");
	

		if (loadingPort.value=="") {
			alert("Please select a port of loading.");
			loadingPort.focus();	
			return false;
		}

		if (finalDestination.value=="") {
			alert("Please enter final destination.");
			finalDestination.focus();	
			return false;
		}

		if (containerMarks.value=="") {
			alert("Please enter container marks.");
			containerMarks.focus();	
			return false;
		}

		if (goodsDescription.value=="") {
			alert("Please enter Description of Goods.");
			goodsDescription.focus();	
			return false;
		}

		for (var i=1; i<=productRowCount; i++) {					
			var productDescr  = document.getElementById("productDescr_"+i); 
			var netWt	= document.getElementById("netWt_"+i); 
			var grossWt	= document.getElementById("grossWt_"+i); 
			var hidRowParentId = document.getElementById("hidRowParentId_"+i).value;
			var prodOriginType	= document.getElementById("prodOriginType_"+i); 

				
			if (productDescr.value=="") {
				alert("Please enter Product description.");
				productDescr.focus();
				return false;
			}
			
			/*
			if (hidRowParentId!="" && prodOriginType.value=="")
			{
				alert("Please select product type");
				prodOriginType.focus();
				return false;
			}
			*/

			if (grossWt.value=="") {
				alert("Please enter product wise gross weight.");
				grossWt.focus();
				return false;
			} 
			
			if (!checkNumber(parseFloat(grossWt.value))) {
				grossWt.focus();
				return false;
			}
			
			if (parseFloat(grossWt.value)<parseFloat(netWt.value)) {
				alert("Please check product wise gross weight.");
				grossWt.focus();
				return false;
			}
		} // Loop Ends

		for (i=0; i<containerRowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;	
			if (rowStatus!='N') {
				var selContainer = document.getElementById("selContainer_"+i);
				if (selContainer.value=="") {
					alert("Please select a container.");
					selContainer.focus();
					return false;
				} 

				if (selContainer.value!="") {
					containerSelected = true;
				} 
			}
		} // Loop Ends here
		
		if (!containerSelected) {
			alert("Please select atleast one container.");
			return false;
		}

		if (validInvoiceNo=='N') {
			alert("Please enter a valid Invoice no.");
			invoiceNo.focus();
			return false;
		}

		if (validInvoiceDate=='N') {
			alert("Please select a valid Invoice date.");
			invoiceDate.focus();
			return false;
		}

		/*
		if (vessalRecSize>1) {
			alert("Please check Vessel details/Container type.\nDuplicate exist in Vessel Details.");
			return false;
		}
		*/
		if (totGrossWt.value=="") {
			alert("Please enter total gross weight.");
			totGrossWt.focus();
			return false;
		}

		if (!checkNumber(parseFloat(totGrossWt.value))) {
			totGrossWt.focus();
			return false;
		}
		if (parseFloat(totGrossWt.value)<parseFloat(totNetWt.value)) {
			alert("Please check total gross weight.");
			totGrossWt.focus();
			return false;
		}

		if (shipBillNo.value=="") {
			alert("Please enter shipment bill number.");
			shipBillNo.focus();	
			return false;
		}	
		
		if (shipBillNo.value!="") {
			var sBillNo = shipBillNo.value;
			if (parseInt(sBillNo.length)<7)
			{
				alert("Please enter valid shipment bill number.");
				shipBillNo.focus();	
				return false;
			}
		}

		if (shipBillDate.value=="") {
			alert("Please enter shipment bill date.");
			shipBillDate.focus();	
			return false;
		}

		if (billLaddingNo.value=="") {
			alert("Please enter bill of ladding number.");
			billLaddingNo.focus();	
			return false;
		}
		
		if (billLaddingDate.value=="") {
			alert("Please enter bill of ladding date.");
			billLaddingDate.focus();	
			return false;
		}
	}

	if (!validateItemRepeat()) {
		return false;
	}
	
	if (!confirmSave()) return false;
	else return true;
}

function validateInvoiceSearch(form)
{
	var selectFrom	=	form.selectFrom.value;
	var selectTill	=	form.selectTill.value;
	
	if (selectFrom=="") {
		alert("Please select From Date.");
		form.selectFrom.focus();
		return false;
	}
	
	if (selectTill=="") {
		alert("Please select Till Date.");
		form.selectTill.focus();
		return false;
	}

return true;
}

//ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId, selContainerId)
{
	var tbl		= document.getElementById(tableId);		
	var lastRow	= tbl.rows.length;	
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	
	cell1.id = "srNo_"+fieldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";	
        cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";cell3.noWrap = "true";
	
	var selectFish	= "<select name='selContainer_"+fieldId+"' id='selContainer_"+fieldId+"'><option value=''>--Select--</option>";
	<?php
		if (sizeof($containerRecords)>0) {	
			foreach($containerRecords as $fr) {
				$containerId	= $fr[0];
				$containerNo = stripSlash($fr[2]);
	?>	
		if (selContainerId== "<?=$containerId?>")  var sel = "Selected";
		else var sel = "";

	selectFish += "<option value=\"<?=$containerId?>\" "+sel+"><?=$containerNo?></option>";	
	<?php
			}
		}
	?>
	selectFish += "</select>";

	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><!--input type='hidden' name='containerEntryId_"+fieldId+"' id='containerEntryId_"+fieldId+"' value=''-->";	
	
	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectFish;
	cell3.innerHTML = imageButton+hiddenFields;	
	
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;	
	assignSrNo();
}
// Add New Product Ends here
	

	function setRowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			assignSrNo();
		}
		return false;
	}

	function assignSrNo()
	{
		var itemCount	=	document.getElementById("hidTableRowCount").value;

		var j = 0;
		for (i=0; i<itemCount; i++) {
			var sStatus = document.getElementById("status_"+i).value;	
			if (sStatus!='N') {
				j++;	
				document.getElementById("srNo_"+i).innerHTML = j;
			}
		}		
	}


	// ------------------------------------------------------
	// Duplication check starts here
	// ------------------------------------------------------	
	function validateItemRepeat()
	{	
		if (Array.indexOf != 'function') {  
			Array.prototype.indexOf = function(f, s) {
			if (typeof s == 'undefined') s = 0;
				for (var i = s; i < this.length; i++) {   
				if (f === this[i]) return i; 
				} 
			return -1;  
			}
		}
		
		var rc = document.getElementById("hidTableRowCount").value;		
		var pArr	= new Array();	
		var pa		= 0;
	
		for (i=0; i<rc; i++) {
			var status = document.getElementById("status_"+i).value;
			if (status!='N') {
				var selContainer	= document.getElementById("selContainer_"+i).value;	
				var addVal = selContainer;
				if (pArr.indexOf(addVal)!=-1) {
					alert(" Container cannot be duplicate.");
					document.getElementById("selContainer_"+i).focus();
					return false;	
				}
				pArr[pa++]	= addVal;
			}
		}	
		return true;
	}	
	// ------------------------------------------------------
	// Duplication check Ends here
	// ------------------------------------------------------

	function enableInvoiceButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
			document.getElementById("cmdSaveAndConfirm").disabled = false;
		}
	}
	
	function disableInvoiceButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
			document.getElementById("cmdSaveAndConfirm").disabled = true;
		}
	}

	function showDiscount()
	{
		var discount = document.getElementById("discount").checked;
		if (discount) document.getElementById("discountRow").style.display = "";
		else {
			document.getElementById("discountRemark").value = "";
			document.getElementById("discountAmt").value = "";
			document.getElementById("discountRow").style.display = "none";
		}
		calcTotalUSDAmt();
	}

	function calcTotalUSDAmt()
	{
		var discount = document.getElementById("discount").checked;
		var productRowCount	= document.getElementById("hidProductItemCount").value;
		var discountAmt		= document.getElementById("discountAmt").value;		
		discountAmt = (discountAmt!="" && discount)?discountAmt:0;

		valueInUSD = 0;
		totValueInUSD = 0;
		for (var i=1; i<=productRowCount; i++) {					
			var valueInUSD  = document.getElementById("valueInUSD_"+i).value; 
			totValueInUSD += parseFloat(valueInUSD);
		} // Loop Ends
		totValueInUSD += parseFloat(discountAmt);
		if (!isNaN(totValueInUSD)) document.getElementById("totalValueInUSD").value = number_format(totValueInUSD,2,'.',''); 
	}

	function calcTotGrossWt()
	{		
			var productRowCount	= document.getElementById("hidProductItemCount").value;
			var totGrossWt = 0;
			for (var i=1; i<=productRowCount; i++) {	
				var grossWt	= document.getElementById("grossWt_"+i).value;
				if (!isNaN(grossWt) && grossWt!="")
				{
					totGrossWt += parseFloat(grossWt);
				}				
			} // Loop Ends
		
			if (!isNaN(totGrossWt))  document.getElementById("totGrossWt").value = number_format(totGrossWt,3,'.','') ;
	}


	function validateBRCInvoice()
	{
		var brcIECCodeNo	= document.getElementById("brcIECCodeNo");
		var brcDEPBEnrolNo	= document.getElementById("brcDEPBEnrolNo");
		var brcExportBillTo	= document.getElementById("brcExportBillTo");
		var brcGoodsDescription	= document.getElementById("brcGoodsDescription");
		var brcBillAmt			= document.getElementById("brcBillAmt");
		var brcFreightAmt	= document.getElementById("brcFreightAmt");
		var brcInsuranceAmt	= document.getElementById("brcInsuranceAmt");
		var brcCommissionDiscount	= document.getElementById("brcCommissionDiscount");
		var brcFreeConvert	= document.getElementById("brcFreeConvert");
		var brcFOBValue	= document.getElementById("brcFOBValue");
		var brcRealisationDate	= document.getElementById("brcRealisationDate");
		var brcLicenceCategory	= document.getElementById("brcLicenceCategory");
		var brcRefNo	= document.getElementById("brcRefNo");
		var brcRefNoDate	= document.getElementById("brcRefNoDate");
		var brcFgnExDealerCodeNo	= document.getElementById("brcFgnExDealerCodeNo");
		var brcExporterName			= document.getElementById("brcExporterName");

		if (brcIECCodeNo.value=="")
		{	
			alert("Please enter IEC Code No");
			brcIECCodeNo.focus();
			return false;
		}

		if (brcDEPBEnrolNo.value=="")
		{	
			alert("Please enter DEPB Enrolment No");
			brcDEPBEnrolNo.focus();
			return false;
		}

		if (brcExportBillTo.value=="")
		{	
			alert("Please select export bill to (bank)");
			brcExportBillTo.focus();
			return false;
		}

		if (brcGoodsDescription.value=="")
		{	
			alert("Please enter description of goods");
			brcGoodsDescription.focus();
			return false;
		}

		if (brcBillAmt.value=="")
		{	
			alert("Please enter Bill Amount");
			brcBillAmt.focus();
			return false;
		}

		if (brcFreightAmt.value=="")
		{	
			alert("Please enter Freight Amount");
			brcFreightAmt.focus();
			return false;
		}

		if (brcInsuranceAmt.value=="")
		{	
			alert("Please enter Insurance Amount");
			brcInsuranceAmt.focus();
			return false;
		}

		if (brcCommissionDiscount.value=="")
		{	
			alert("Please enter Commission Discount");
			brcCommissionDiscount.focus();
			return false;
		}

		if (brcFreeConvert.value=="")
		{	
			alert("Please enter freely convertable details");
			brcFreeConvert.focus();
			return false;
		}

		if (brcFOBValue.value=="")
		{	
			alert("Please enter FOB value");
			brcFOBValue.focus();
			return false;
		}

		if (brcRealisationDate.value=="")
		{	
			alert("Please select a realisation date");
			brcRealisationDate.focus();
			return false;
		}

		if (brcLicenceCategory.value=="")
		{	
			alert("Please enter category of applicable license");
			brcLicenceCategory.focus();
			return false;
		}

		if (brcExporterName.value=="")
		{
			alert("Please enter name of Authorised Signatory");
			brcExporterName.focus();
			return false;
		}
		
		/*
		if (brcRefNo.value=="")
		{	
			alert("Please enter Ref No");
			brcRefNo.focus();
			return false;
		}
		*/

		if (brcRefNoDate.value=="")
		{	
			alert("Please select a ref no date");
			brcRefNoDate.focus();
			return false;
		}

		if (brcFgnExDealerCodeNo.value=="")
		{	
			alert("Please enter Authorised Foreign Exchange Dealer Code no");
			brcFgnExDealerCodeNo.focus();
			return false;
		}
		
		if (!confirmSave()) return false;
		return true;
	}

	function displaySCMsg(prodOriginType, msgDiv)
	{
		$("#"+msgDiv).hide();
		if (prodOriginType=='SC') $("#"+msgDiv).show();
	}

	function displayExporter()
	{
		var exporterId = document.getElementById('exporter').value;
		xajax_getExporterUnit(exporterId);
		chkValidInvNum();
	}

	function getUnitAlphacode()
	{
		var unitId = document.getElementById('unitid').value;
		var exporterId = document.getElementById('exporter').value;
		//alert(unitId,exporterId);
		xajax_getAlphacode(unitId,exporterId);
		//chkValidInvNum();
	}

	function chkValidInvNum()
	{
		xajax_chkInvoiceNoExist(document.getElementById('invoiceNo').value, document.getElementById('hidMode').value, document.getElementById('mainId').value, document.getElementById('invoiceDate').value, document.getElementById('hidInvoiceType').value,document.getElementById('exporter').value);
	}

	function printShipmentInvoice(invoiceId, status)
	{
		$("#printInvoiceId").val(invoiceId);
		$("#txtNewInvoiceHead").val('');
		if (status=='Y')
		{			
			ShowDialog(true);
			return false;
		} else { 
			printWindow('PrintInvoice.php?invoiceId='+invoiceId+'&print=Y',700,600);
			return true;
		}
	}

	function printDRInvoice(invoiceId, status)
	{
		//alert(invoiceId);
		$("#printDrInvoiceId").val(invoiceId);
		$("#companyDet").val('');
		ShowDialogDr(true);
			return false;
		/*if (status=='Y')
		{			
			ShowDialogDr(true);
			return false;
		} else { 
			//printWindow('PrintDN.php?invoiceId='+invoiceId+'&print=Y',700,600);
			//return true;
			alert("Invoice not confirmed!");
			return true;
		}*/
	}
	
	

	function printNewInvoiceHead()
	{
		var txtNewInvoiceHead = $("#txtNewInvoiceHead").val();
		var invoiceId = $("#printInvoiceId").val();
		printWindow('PrintInvoice.php?invoiceId='+invoiceId+'&print=Y&newTitle='+txtNewInvoiceHead,700,600);
		HideDialog();
		return true;
	}
	
   function ShowDialog(modal)
   {
	   $("#Box_shipInvDialog").dialog({title: "Print Invoice", modal: true, resizable: false, minWidth: 450}).height('auto');
   }

   function HideDialog()
   {
	   $("#Box_shipInvDialog").dialog('close');	 
   }
	
	function calcDNBkgFreight()
	{
		var dnFreight		= $("#dnFreight").val();
		var calcBkgFreight = (parseFloat(dnFreight)*2)/100;
		$("#dnBkgFreight").val(number_format(calcBkgFreight,2,'.',''));
		calcDNBkg();
	}

   function calcDNBkg()
   {
		var dnBkgFreight	= $("#dnBkgFreight").val();
		var dnExRate		= $("#dnExRate").val();
		var calcTotalBkg    = parseFloat(dnBkgFreight)*parseFloat(dnExRate);
		$("#dnTotalBkg").val(number_format(calcTotalBkg,2,'.',''));
   }

   function calcDNNetAmt()
   {
		var dnGrossAmt	= $("#dnGrossAmt").val();
		var dnTdsAmt	= $("#dnTdsAmt").val();
		var calcDiff	= parseFloat(dnGrossAmt)-parseFloat(dnTdsAmt);
		$("#dnNetAmt").val(number_format(calcDiff,2,'.',''));
   }


   function validateDebitNote()
	{
		
		var dnFreight		= $("#dnFreight").val();
		var dnExRate		= $("#dnExRate").val();

		if (dnFreight=="")
		{
			alert("Please enter freight");
			$("#dnFreight").focus();
			return false;
		}

		if (!checkNumber(parseFloat(dnFreight))) {
			$("#dnFreight").focus();
			return false;
		}

		if (dnExRate=="")
		{
			alert("Please enter exchange rate");
			$("#dnExRate").focus();
			return false;
		}
		
		if (!checkNumber(parseFloat(dnExRate))) {
			$("#dnExRate").focus();
			return false;
		}
		

		if (!confirmSave()) return false;
		return true;
	}


	function splitInvoiceAmt(invoiceId, status)
	{
		if (fldId>0)
		{
			removeSIARow();
			fldId = 0;
		}

		$("#splitAmtInvoiceId").val(invoiceId);		
		if (status=='Y')
		{			
			addNewSIAItem();
			splitInvAmt(invoiceId);
			
			var invNum = $("#hdnInvoiceNumber_"+invoiceId).val();

			$("#Box_Alert").dialog({title: "Invoice #"+invNum+" Amount Split-up", minWidth: 450, modal: true, resizable: false }).height('auto');
			return false;
		} else { 
			alert("Invoice not confirmed!");
			return true;
		}
	}

	function closeSIADialog()
	{
		if (confirm("Do you wish to cancel?"))
		{
			$("#Box_Alert").dialog('close');
			return true;
		}
		return false;
	}




function splitInvAmt(invId)
{
	var invAmt = $("#invAmt_"+invId).val();
	var invCurrencyCode = $("#invCurrencyCode_"+invId).val();
	$("#splitupTotalAmt").html(invAmt);
	$("#hdnSplitupTotalAmt").val(invAmt);
	$("#hdnSplitupBalAmt").val(0);
	$("#hdnSplitupVal").val("");
	$("#hdnSIAInvoiceId").val(invId);
	$("#hdnSIATotCurrency").val("");
	$("#hdnSIATotRs").val("");
	
	$(".siaCurrencyCode").html(invCurrencyCode);	

	xajax_GetSplitup(invId);

}


function addNewSIAItem()
{
	addNewSIARow('tblSIAItem','','','');
}

function addNewSIARow(tableId, splitCurrency, INRPerCurrency, totalRs)
{
	var tbl		= document.getElementById(tableId);		
	var lastRow	= tbl.rows.length-1;	
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "siaRow_"+fldId;	
	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	
	cell1.id = "siaSrNo_"+fldId;	
	cell1.className	= "listing-item"; cell1.align	= "center";	
    cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";cell4.noWrap = "true";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setSIARowItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";
	var hiddenFields = "<input name='SIARowStatus_"+fldId+"' type='hidden' id='SIARowStatus_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'>";	
	
	cell1.innerHTML = "";	
	cell2.innerHTML	= "<input name='actualCurrency_"+fldId+"' type='text' id='actualCurrency_"+fldId+"' value='"+splitCurrency+"' size='12' style='text-align:right;' onkeyup='calcSIA();' >";
	cell3.innerHTML	= "<input name='rsPerCurrency_"+fldId+"' type='text' id='rsPerCurrency_"+fldId+"' value='"+INRPerCurrency+"' size='6' style='text-align:right;' onkeyup='calcSIA();'>";
	cell4.innerHTML	= "<input name='actualRs_"+fldId+"' type='text' id='actualRs_"+fldId+"' value='"+totalRs+"' size='14' style='text-align:right;border:none;' readonly />";
	cell5.innerHTML = imageButton+hiddenFields;	
	
	if (fldId==0)
	{
	}

	fldId		= parseInt(fldId)+1;	
	document.getElementById("hidTblSIAItemRowCount").value = fldId;
	assignSIASrNo();
}

function setSIARowItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("SIARowStatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("siaRow_"+id).style.display = 'none';
			calcSIA();
			assignSIASrNo();
		}
		return false;
	}

function removeSIARow()
{
	var tRowCount = document.getElementById("hidTblSIAItemRowCount").value;
	for (k=0;k<tRowCount; k++ )
	{
		if (tRowCount>0) {			
			if(document.getElementById("siaRow_"+k)!=null) {
				var tRIndex = document.getElementById("siaRow_"+k).rowIndex;	
				document.getElementById('tblSIAItem').deleteRow(tRIndex);	
			}
		}
	}	
}

function calcSIA()
{
	var tRowCount = document.getElementById("hidTblSIAItemRowCount").value;
	var splitupTotalAmt	= parseFloat($("#hdnSplitupTotalAmt").val());
	var SIATotCurrency = 0;
	var SIATotRs = 0;
	var splitArr = new Array();

	for (k=0;k<tRowCount; k++ )
	{
		var status = document.getElementById("SIARowStatus_"+k).value;	
		if (status!='N')
		{
				var actualCurrency = parseFloat(document.getElementById("actualCurrency_"+k).value);
				var rsPerCurrency  = parseFloat(document.getElementById("rsPerCurrency_"+k).value);
				if (actualCurrency>0)
				{
					splitupTotalAmt = parseFloat(number_format(splitupTotalAmt,2,'.',''))-actualCurrency;
					SIATotCurrency += actualCurrency;
				}

				if (actualCurrency>0 && rsPerCurrency>0)
				{
					var calcActualRs = actualCurrency*rsPerCurrency;
					calcActualRs = number_format(calcActualRs,2,'.','');
					SIATotRs += parseFloat(calcActualRs);
					document.getElementById("actualRs_"+k).value = calcActualRs;
					splitArr.push(actualCurrency+":"+rsPerCurrency+":"+calcActualRs);
				}
		}
	}

	if (splitArr.length>0)
	{
		var splitStr = implode(",",splitArr);
		$("#hdnSplitupVal").val(splitStr);
	}

	$("#hdnSIATotCurrency").val(SIATotCurrency);
	$("#hdnSIATotRs").val(number_format(SIATotRs,2,'.',''));


	document.getElementById("hdnSplitupBalAmt").value = parseFloat(splitupTotalAmt);

	if (parseFloat(splitupTotalAmt)<0)
	{
		alert("Split-up amount and actual amount are not matching. Please check the split amount.");
	}
}


function SaveSplitAmt()
{
	var tRowCount = document.getElementById("hidTblSIAItemRowCount").value;

	for (k=0;k<tRowCount; k++ )
	{
		var status = document.getElementById("SIARowStatus_"+k).value;	
		if (status!='N')
		{
				var actualCurrency = parseFloat(document.getElementById("actualCurrency_"+k).value);
				var rsPerCurrency  = parseFloat(document.getElementById("rsPerCurrency_"+k).value);

				if (isNaN(actualCurrency))
				{
					alert("Please enter split currency");
					document.getElementById("actualCurrency_"+k).focus();
					return false;
				}

				if (isNaN(rsPerCurrency))
				{
					alert("Please enter INR per currency");
					document.getElementById("rsPerCurrency_"+k).focus();
					return false;
				}
		}
	}

	var splitupTotalAmt	= parseFloat($("#hdnSplitupTotalAmt").val());
	var hdnSIATotCurrency = parseFloat($("#hdnSIATotCurrency").val());

	if (splitupTotalAmt!=hdnSIATotCurrency)
	{
		alert("Splitup not matching");
		return false;
	}


	var invId	 = $("#hdnSIAInvoiceId").val();
	var splitStr = $("#hdnSplitupVal").val();
	var totValueInRs = $("#hdnSIATotRs").val();

	// Save
	if (confirm("Do you wish to save the changes?"))
	{
			xajax_SaveSplitup(invId, totValueInRs, splitStr)
	}

	return true;	
}


function closeSIADialogAfterInsert()
{
	$("#Box_Alert").dialog('close');	
	return false;
}

function getSIAItemArr(json)
{	
	var myObject = eval('(' + json + ')');

	if (myObject.length>0)
	{
		
		if (fldId>0)
		{
			removeSIARow();
			fldId = 0;
		}

		for (var key in myObject) {		
			var rowVal = myObject[key].toString();
			var rowArr = rowVal.split(",");

			var splitCurrency = rowArr[0];
			var INRPerCurrency = rowArr[1];
			var totalRs			= rowArr[2];
			
			addNewSIARow('tblSIAItem', splitCurrency, INRPerCurrency, totalRs);			
		}
		
		// Recalc
		calcSIA();
	}
}


function assignSIASrNo()
{
	var itemCount	=	document.getElementById("hidTblSIAItemRowCount").value;
	var j = 0;
	for (i=0; i<itemCount; i++) {
		var sStatus = document.getElementById("SIARowStatus_"+i).value;	
		if (sStatus!='N') {
			j++;	
			document.getElementById("siaSrNo_"+i).innerHTML = j;
		}
	}		
}

function closePrintDialog()
{
	if (confirm("Do you wish to cancel?"))
	{
		$("#Box_shipInvDialog").dialog('close');
		return true;
	}
	return false;
}

////popupfor dr note

function closePrintDialogDr()
{
	if (confirm("Do you wish to cancel?"))
	{
		$("#Box_shipInvDialogDr").dialog('close');
		return true;
	}
	return false;
}

function ShowDialogDr(modal)
{
	  // $("#Box_shipInvDialogDr").dialog({title: "Dr Note", modal: true, resizable: false, minWidth: 450}).height('auto');
	 $("#Box_shipInvDialogDr").dialog({title: "Dr Note", modal: true, resizable: false, minWidth: 340}).height('auto');	
 }

function HideDialogDr()
   {
	   $("#Box_shipInvDialogDr").dialog('close');	 
   }
 function printNewDRInvoiceHead()
	{
		var companyDet = $("#companyDet").val();
		if(companyDet=="0")
		{
			alert("please select company name ");
			return false;
		}
		else
		{
			var invoiceId = $("#printDrInvoiceId").val();
			printWindow('PrintDN.php?invoiceId='+invoiceId+'&print=Y&companyDetail='+companyDet,700,600);
			HideDialogDr();
			return true;
		}
	}

	function printDRNoteInvoice(invoiceId, status)
	{
		//alert(invoiceId);
		$("#printDrInvoiceId").val(invoiceId);
		$("#companyDetail").val('');
		ShowDialogDrNote(true);
			return false;
		/*if (status=='Y')
		{			
			ShowDialogDr(true);
			return false;
		} else { 
			//printWindow('PrintDN.php?invoiceId='+invoiceId+'&print=Y',700,600);
			//return true;
			alert("Invoice not confirmed!");
			return true;
		}*/
	}






function ShowDialogDrNote(modal)
{
	  // $("#Box_shipInvDialogDr").dialog({title: "Dr Note", modal: true, resizable: false, minWidth: 450}).height('auto');
	 $("#Box_shipInvDialogDrNote").dialog({title: "Debit Note", modal: true, resizable: false, minWidth: 340}).height('auto');	
 }
function closePrintDialogDrNote()
{
	if (confirm("Do you wish to cancel?"))
	{
		$("#Box_shipInvDialogDrNote").dialog('close');
		return true;
	}
	return false;
}
function HideDialogDrNote()
   {
	   $("#Box_shipInvDialogDrNote").dialog('close');	 
   }
 function printNewDRNoteInvoiceHead()
	{
		var companyDetail = $("#companyDetail").val();
		if(companyDetail=="0")
		{
			alert("please select company name ");
			return false;
		}
		else
		{
			var invoiceId = $("#printDrInvoiceId").val();
			$("#debitNoteEditId").val(invoiceId);
			//printWindow('PrintDN.php?invoiceId='+invoiceId+'&print=Y&companyDetail='+companyDet,700,600);
			HideDialogDrNote();
			window.location="Invoice.php?debitNoteEditId="+invoiceId+"&companyDetail="+companyDetail;
			return true;
		}
	}






function calcBRCRs(fField,sField,resultField)
{
	var fieldA = $("#"+fField).val();
	var fieldB = $("#"+sField).val();
	if (!isNaN(fieldA) && !isNaN(fieldB))
	{
		var calcField = number_format((fieldA*fieldB),0,'.','');
		$("#"+resultField).val(number_format(calcField,2,'.',''));

		calcBRCVal();
	}
}

function calcBRCVal()
{
	var brcBillAmtUSD = ($("#brcBillAmtUSD").val()!="")?$("#brcBillAmtUSD").val():0;
	var brcBillAmtRs = ($("#brcBillAmtRs").val()!="")?$("#brcBillAmtRs").val():0;

	var brcFreightAmtUSD = ($("#brcFreightAmtUSD").val()!="")?$("#brcFreightAmtUSD").val():0;
	var brcFreightAmtRs	 = ($("#brcFreightAmtRs").val()!="")?$("#brcFreightAmtRs").val():0;
	
	var brcInsuranceAmtUSD  = ($("#brcInsuranceAmtUSD").val()!="")?$("#brcInsuranceAmtUSD").val():0;
	var brcInsuranceAmtRs	= ($("#brcInsuranceAmtRs").val()!="")?$("#brcInsuranceAmtRs").val():0;

	var brcCommissionDiscountUSD = ($("#brcCommissionDiscountUSD").val()!="")?$("#brcCommissionDiscountUSD").val():0;
	var brcCommissionDiscountRs  = ($("#brcCommissionDiscountRs").val()!="")?$("#brcCommissionDiscountRs").val():0;

	if (brcBillAmtUSD>0 && brcBillAmtRs>0)
	{
		var calcFOBValueUSD = parseFloat(brcBillAmtUSD)-parseFloat(brcFreightAmtUSD)-parseFloat(brcInsuranceAmtUSD)-parseFloat(brcCommissionDiscountUSD);
		var calcFOBValueRs = parseFloat(brcBillAmtRs)-parseFloat(brcFreightAmtRs)-parseFloat(brcInsuranceAmtRs)-parseFloat(brcCommissionDiscountRs);
		$("#brcFOBValueUSD").val(number_format(calcFOBValueUSD,2,'.',''));
		$("#brcFOBValueRs").val(number_format(calcFOBValueRs,2,'.',''));
	}


}