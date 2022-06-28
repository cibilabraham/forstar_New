function validateSalesOrder(form, confirmed)
{
	var productSelected = false;
	var selDistributor = form.selDistributor.value;	
	var lastDate		= form.lastDate.value;	
	var editMode		= form.editMode.value;
	var selState		= form.selState.value;
	var selCity		= form.selCity.value;	
	var invoiceType 	= document.getElementById("invoiceType").value;
	var validDespatchDate = document.getElementById("validDespatchDate").value;
	var selArea		= form.selArea.value;
	var invoiceDate		= document.getElementById("invoiceDate").value;	
	var discount		= document.getElementById("discount").checked;
	var octroiExempted	= document.getElementById("octroiExempted").value;
	var mode		= document.getElementById("hidMode").value;
	var poDate		= document.getElementById("poDate").value;
	var entryDate		= document.getElementById("entryDate").value;
	var chbTransCharge	= document.getElementById("chbTransCharge").checked;

	if (confirmed!="") {
		var distributorInactive = document.getElementById("distributorInactive").value;
		if (distributorInactive!="") {
			alert("Please make sure the selected distributor is active");
			return false;
		}
	}


	if (invoiceType=='S') {
		var sampleInvoiceNo = document.getElementById("sampleInvoiceNo");
		if (sampleInvoiceNo.value=="") {
			alert("Please enter a Sample Invoice Number.");
			sampleInvoiceNo.focus();
			return false;
		}
	} else {
		var proformaInvoiceNo	= document.getElementById("proformaInvoiceNo");
		if (proformaInvoiceNo.value=="") {
			alert("Please enter a Proforma Invoice Number.");
			proformaInvoiceNo.focus();
			return false;
		}
	}	

	if (lastDate=="") {
		alert("Please select a date of Despatch.");
		form.lastDate.focus();
		return false;
	}

	if (validDespatchDate==1) {
		alert(" Please make sure the selected date of Despatch is a valid date. ");
		document.getElementById("lastDate").focus();
		return false;
	}

	if (entryDate=="") {
		alert("Please select a entry date.");
		document.getElementById("entryDate").focus();
		return false;
	}

	if (convertTime(lastDate)<convertTime(entryDate)) {
			alert("Please check date of Despatch and entry date.");
			form.lastDate.focus();
			return false;
	}

	if (selDistributor=="") {
		alert("Please select a Distributor.");
		form.selDistributor.focus();
		return false;
	}

	if (selState=="") {
		alert("Please select a state.");
		form.selState.focus();
		return false;
	}
	
	if (selCity=="") {
		alert("Please select a city.");
		form.selCity.focus();
		return false;
	}
	
	var itemCount	=	document.getElementById("hidTableRowCount").value;

	for (i=0; i<itemCount; i++) {
		var status = document.getElementById("status_"+i).value;	
       		if (status!='N') {
			var selProduct	= document.getElementById("selProduct_"+i);
			var unitPrice	= document.getElementById("unitPrice_"+i);
			var quantity	= document.getElementById("quantity_"+i);
			var selMcPkg	= document.getElementById("selMcPkg_"+i);			
			var freePkts	= document.getElementById("freePkts_"+i);
			var basicRate   = document.getElementById("basicRate_"+i);
		
			if (selProduct.value == "") {
				alert("Please select a Product.");
				selProduct.focus();
				return false;
			}

			if ((basicRate.value == "" || basicRate.value == 0)) {
				alert("Please define MRP/Dist Margin Structure for the selected product.");				
				selProduct.focus();
				return false;
			}
			
			if ((quantity.value == "" || quantity.value==0) && (freePkts.value==0 || freePkts.value=="")) {
				alert("Please enter a quantity.");
				quantity.focus();
				return false;
			}
			if (selMcPkg.value == "" && invoiceType=='T') {
				alert("Please select a MC Packing.");
				selMcPkg.focus();
				return false;
			}
			
			if (selProduct.value!="") {	
				productSelected = true;
			}
	  	}					
	}
	if (!productSelected) {
		alert("Please select atleast one Product");
		return false;
	}	

	if (!validateSOProductRepeat()) {
		return false;
	}
	// Chk Discount 
	if (discount) {
		var discountRemark = document.getElementById("discountRemark").value;
		var discountPercent = document.getElementById("discountPercent").value;
		if (discountRemark=="") {
			alert("Please enter discount remark.");
			document.getElementById("discountRemark").focus();
			return false;
		}
		if (discountPercent=="") {
			alert("Please enter discount percent.");
			document.getElementById("discountPercent").focus();
			return false;
		}
		if (!checkNumber(discountPercent)) {
			return false;
		}
	}

	if (chbTransCharge) {
		var transportCharge = $("#transportCharge").val();
		if (transportCharge=="") {
			alert("Please enter freight charge");
			$("#transportCharge").focus();
			return false;
		}
		
		if (!checkNumber(transportCharge)) {
			$("#transportCharge").focus();
			return false;
		}
	}
	/* Criteria
		Credit Limit : 10000
			Out Amt      	: +10000
			Invoice Amt 	:+ 5000
			Total		: 15000  Not Bill
	*/
	if (productSelected && invoiceType=='T' && confirmed!="") {
		var totOutStandAmt = 0;
		var grandTotalAmt 	= document.getElementById("grandTotalAmt").value;
		var outStandAmt		= document.getElementById("outStandAmt").value;
		var creditLimit		= document.getElementById("creditLimit").value;
		var cPeriodOutStandAmt	= document.getElementById("cPeriodOutStandAmt").value;

		totOutStandAmt	= parseFloat(grandTotalAmt)+parseFloat(outStandAmt);
		
		if (totOutStandAmt>creditLimit) {
			alert("The selected distributor billed amount is greater than the credit limit. ");
			return false;
		}

		if (parseFloat(cPeriodOutStandAmt)>parseFloat(creditLimit)) {
			alert("The distributor has "+cPeriodOutStandAmt+" outstanding amount.");
			return false;
		}		
	}

	if (invoiceType=='T') {
		var taxRowCount = document.getElementById("hidTaxRowCount").value;
		if (taxRowCount<=0) {
			alert("No Tax applied.");
		}
	} 
	
	if (invoiceType=='S') {
		var grossWt = document.getElementById("grossWt").value;
		var numBox = document.getElementById("numBox").value;
		if (grossWt=="" || grossWt==0) {
			alert("Please enter Gross Wt.");
			document.getElementById("grossWt").focus();
			return false;
		}

		if (numBox=="" || numBox==0) {
			alert("Please enter No. of Box.");
			document.getElementById("numBox").focus();
			return false;
		}
	}

	if (octroiExempted=='Y') {
		var oecNo 	 = document.getElementById("oecNo");
		var oecValidDate = document.getElementById("oecValidDate");
		var oecIssuedDate  = document.getElementById("oecIssuedDate");
		if (oecNo.value=="") {
			alert("Please enter OEC No.");
			oecNo.focus();
			return false;
		}
		
		if (oecIssuedDate.value=="") {
			alert("Please select a OEC Issued Date.");
			oecIssuedDate.focus();
			return false;
		}

		if (oecValidDate.value=="") {
			alert("Please select a OEC Valid Date.");
			oecValidDate.focus();
			return false;
		}
		// Check Date With In (Despatch Date should be Between OEC Issued On and Valid Up to
		if (!dateWithin(oecIssuedDate.value, oecValidDate.value, lastDate)) {
			alert("Please select a Valid Despatch Date.\nDespatch Date should be between the OEC Issued On and Valid Up to date.");
			return false;
		}
	}

	// Confirm section
	if (confirmed!="") {
		var selTransporter	= document.getElementById("selTransporter");
		var validInvoiceDate	= document.getElementById("validInvoiceDate").value;
		var invoiceDate		= document.getElementById("invoiceDate");
		var invoiceNo		= document.getElementById("invoiceNo");
		var hideLastDate	= form.hideLastDate.value;
		var validInvoiceNo	= document.getElementById("validInvoiceNo").value;

		if (selTransporter.value=="") {
			alert("Please select a Transporter.");
			selTransporter.focus();
			return false;
		}

		if (invoiceNo.value=="") {
			alert("Please enter a Sales Order Number.");
			invoiceNo.focus();
			return false;
		}

		if (validInvoiceNo=='N') {
			alert("Please enter a valid invoice number.");
			invoiceNo.focus();
			return false;
		}
	
		if (invoiceDate.value=="") {
			alert("Please select a invoice date");
			invoiceDate.focus();
			return false;
		}

		if (validInvoiceDate==1) {
			alert(" Please make sure the selected invoice date is a valid date. ");
			document.getElementById("invoiceDate").focus();
			return false;
		}

		if (convertTime(lastDate)<convertTime(invoiceDate.value)) {
			alert("Please check date of Despatch and invoice date.");
			form.lastDate.focus();
			return false;
		}

		if (hideLastDate!=lastDate) {			
			document.getElementById("dateExtended").value = 'E';
		}
	}

	if (!confirmSave()) return false;
	return true;
}

//Add a New Line 
function salesOrderNewLine()
{
	document.frmSalesOrder.newline.value = '1';
	document.frmSalesOrder.submit();
}


var ruleArr = new Array();
var pkgWtArr = new Array();

// Find the total Amount
function multiplySalesOrderItem()
{		
	

	
	var rowCount 	= document.getElementById("hidTableRowCount").value;
	var taxType 	= document.getElementById("taxType").value;
	var invoiceType = document.getElementById("invoiceType").value;
	var discount	= document.getElementById("discount").checked;
	var discountCalc = 0;
	var discountPercent = 0;
	if (discount) {
		discountPercent = document.getElementById("discountPercent").value; 
	}
	var totalAmount = 0;	
	var calcTotalAmount = 0;
	var grandTotalAmount = 0;
	var calcTaxAmt	= 0;	
	var taxArr = new Array();
	
	var calcTotalSOAmt = 0	
	var totalTaxAmt = 0;
	var taxAmt = 0;	
	var totalNetWt = 0;	// Total Product Gross Wt
	var totalGrossWt = 0;
	var totalMCPkgGrossWt = 0;
	var totalNumMCPack = 0;
	var combArr = new Array();
	var wtArr   = new Array();	
	var pkgArr  = new Array(); 
	var rrArr   = new Array();	
	var numP    = new Array();	
	var gTotalAmount = 0;	
	var grTotPkts = 0;
	var grTotPktsUnderScheme = 0;	
	var grTotMC = 0;
	var grTotLP = 0;
	var totExAmt = 0;
	var totgstAmt = 0;
	var totc_gstAmt = 0;
	var tots_gstAmt = 0;
	var totigstAmt = 0;
	var eduCess = $("#hidEduCess").val();
	var secEduCess = $("#hidSecEduCess").val();
	var transChargeActive = document.getElementById("chbTransCharge").checked;
	var transCharge = 0;
	if (transChargeActive) {
		tsCharge = $("#transportCharge").val();
		transCharge = (!isNaN(tsCharge) && tsCharge!="" && tsCharge!=0)?tsCharge:0;
	}
	var totEduCessAmt	= 0;
	var totSecEduCess	= 0;
	var grTotCentralTaxAmt 	= 0;

	
	
	
	for (i=0; i<rowCount; i++) {
		var status = document.getElementById("status_"+i).value;	
	    	var selProduct  = document.getElementById("selProduct_"+i).value;
	    	if (status!='N' && selProduct!="")
	    	{
			var unitPrice = 0;
			var quantity = 0;
			var selProduct = document.getElementById("selProduct_"+i).value;
			var taxPercent = parseFloat(document.getElementById("taxPercent_"+i).value);
			//alert("taxPercent1"+taxPercent);
			
			var pGrossWt   = parseFloat(document.getElementById("pGrossWt_"+i).value);
			var pMCPkgGrossWt = parseFloat(document.getElementById("pMCPkgGrossWt_"+i).value);
			var mcPack 	= parseFloat(document.getElementById("mcPack_"+i).value);
			var loosePack 	= parseInt(document.getElementById("loosePack_"+i).value);
			var freePkts	= (document.getElementById("freePkts_"+i).value!="")?parseInt(document.getElementById("freePkts_"+i).value):0;
			var totalPkts	= parseInt(document.getElementById("quantity_"+i).value);
			var basicRate  = parseFloat(document.getElementById("basicRate_"+i).value);
			var exciseDuty	= parseFloat(document.getElementById("exciseDuty_"+i).value);
			var gst	= parseFloat(document.getElementById("gst_"+i).value);
			var c_gst	= parseFloat(document.getElementById("c_gst_"+i).value);
			var s_gst	= parseFloat(document.getElementById("s_gst_"+i).value);
			
			
			var igst	= parseFloat(document.getElementById("igst_"+i).value);

			/* Grand Total Section */
			if (!isNaN(totalPkts)) {
				grTotPkts  = parseInt(grTotPkts)+parseInt(totalPkts);	
			}
			if (!isNaN(freePkts)) {
				grTotPktsUnderScheme  = parseInt(grTotPktsUnderScheme)+parseInt(freePkts);	
			}
			if (!isNaN(mcPack)) {
				grTotMC  = parseInt(grTotMC)+parseInt(mcPack);	
			}
			if (!isNaN(loosePack)) {
				grTotLP  = parseInt(grTotLP)+parseInt(loosePack);	
			}
			/* Grand Total Section Ends Here*/

			var pCategoryComb = document.getElementById("pCategoryComb_"+i).value;
			var numPacks   	= document.getElementById("numPacks_"+i).value;
			var mcPackageWt	= document.getElementById("mcPackageWt_"+i).value;
			var mcpComb	= document.getElementById("mcpComb_"+i).value;
			var joinComb    = numPacks+","+mcPackageWt;	
					
			if (mcpComb.match(pCategoryComb) && loosePack!=0) {
				wtArr[pCategoryComb] = joinComb;
			}

			/* Packing Starts*/
			var pkgGroup 	= document.getElementById("pkgGroup_"+i).value;
			var leftPkgRule 	= document.getElementById("leftPkgRule_"+i).value;
			var selPkgRightRule 	= document.getElementById("rightPkgRule_"+i).value;
			
			if (pkgGroup.match(leftPkgRule) && pkgGroup!="") {
				pkgWtArr[leftPkgRule] = numPacks;
				numP[numPacks] = mcPackageWt;
			}
			

			if (selPkgRightRule!="" && pkgGroup!="") {
				ruleArr[leftPkgRule] = selPkgRightRule;
			}

			if (leftPkgRule!="" && selPkgRightRule=="") {
				rrArr[i] = 1;
			}
			// Find the Total Loose Pack and convert to MC PAck
			if (loosePack!=0 && loosePack!="" && !isNaN(loosePack) && pkgGroup!="" && invoiceType=='T') {
				var tLoosePack = loosePack;
				if (typeof(pkgArr[leftPkgRule])!="undefined" && tLoosePack!=0) {
					tLoosePack = parseInt(tLoosePack) + parseInt(pkgArr[leftPkgRule]);
				}
				if (tLoosePack!=0) pkgArr[leftPkgRule] = parseInt(tLoosePack);
			}

			
	
			/* Packing ends*/
			if (!isNaN(pGrossWt)) {
				totalNetWt = parseFloat(totalNetWt)+parseFloat(pGrossWt);
			}

			if (!isNaN(pMCPkgGrossWt)) {
				totalMCPkgGrossWt = parseFloat(totalMCPkgGrossWt)+parseFloat(pMCPkgGrossWt);
			}
			
			if (!isNaN(mcPack)) {
				totalNumMCPack  = parseInt(totalNumMCPack)+parseInt(mcPack);
			}
									
			
			//if (document.getElementById("unitPrice_"+i).value!="") {
			var calcUnitPrice = 0;
			if (basicRate!="") {
				//(basicRate*totalPkts)/ (totalPkts+freePkts) = Unit Price				
				calcUnitPrice = number_format((basicRate*totalPkts),4,'.','')/(totalPkts+freePkts);
				
				if (!isNaN(calcUnitPrice)) {
					unitPrice =  number_format(calcUnitPrice,4,'.','');
					document.getElementById("unitPrice_"+i).value = number_format(calcUnitPrice,4,'.','');
				}
			}
			if (document.getElementById("quantity_"+i).value!="") {
				quantity = parseInt(totalPkts)+parseInt(freePkts);
			}
		
			if (selProduct!="") {				
				calcTotalAmount = parseFloat(number_format((unitPrice*quantity),2,'.','')); // Find Each Row Amount			
				grandTotalAmount += parseFloat(calcTotalAmount); // Find the Grand total Amount
			} else {
				calcTotalAmount = 0;
				document.getElementById("quantity_"+i).value = 0;
				document.getElementById("totalAmount_"+i).value = 0;
			}
			// Find the Total Loose Pack and convert to MC PAck
			if (loosePack!=0 && loosePack!="" && !isNaN(loosePack) && pkgGroup=="" && invoiceType=='T') {			
				var tLoosePack = loosePack;
				if (typeof(combArr[pCategoryComb])!="undefined" && tLoosePack!=0) {
					tLoosePack = parseInt(tLoosePack) + parseInt(combArr[pCategoryComb]);
				}
				if (tLoosePack!=0) combArr[pCategoryComb] = parseInt(tLoosePack);
			}
			
			
			if (!isNaN(calcTotalAmount) && invoiceType=='T') {
				document.getElementById("totalAmount_"+i).value = number_format(calcTotalAmount,2,'.','');
				// calculating Discount (Basic Total-Discount)
				var discountCalc = calcTotalAmount-((calcTotalAmount*discountPercent)/100);
				
				calcTotalAmount = (discountPercent=="" || discountPercent==0)?calcTotalAmount:discountCalc;

				var calcTotCExDuty = 0;	
				var exDutyPercent = '';
				if(exciseDuty>0){
				var exDutyPercent = parseFloat(exciseDuty/100);	
				}
				
				/*rekha added code */
				var gstPercent = parseFloat(gst/100);
				
				var c_gstPercent = parseFloat(c_gst/100);
				var s_gstPercent = parseFloat(s_gst/100);
				
				var igstPercent = parseFloat(igst/100);				
				
				var calcgstAmt	= number_format((calcTotalAmount*gstPercent),2,'.','');	
				var calcc_gstAmt	= number_format((calcTotalAmount*c_gstPercent),2,'.','');	
				var calcs_gstAmt	= number_format((calcTotalAmount*s_gstPercent),2,'.','');	
				var calcigstAmt	= number_format((calcTotalAmount*igstPercent),2,'.','');	
								
				document.getElementById("gstAmt_"+i).value = calcgstAmt;
				document.getElementById("c_gstAmt_"+i).value = calcc_gstAmt;
				document.getElementById("s_gstAmt_"+i).value = calcs_gstAmt;
				
				
				
				document.getElementById("igstAmt_"+i).value = calcigstAmt;
				
				
				if (calcgstAmt>0) {
					totgstAmt += parseFloat(calcgstAmt);
				}
				if (calcc_gstAmt>0) {
					totc_gstAmt += parseFloat(calcc_gstAmt);
				}
				if (calcs_gstAmt>0) {
					tots_gstAmt += parseFloat(calcs_gstAmt);
				}

				
				
				if (calcigstAmt>0) {
					totigstAmt += parseFloat(calcigstAmt);
				}
				

				
				calcTotalAmount += parseFloat(totigstAmt);
				
				
				
				/*end code */
				
				
	
				
				
			if(exDutyPercent>0){
				var calcExDutyAmt	= number_format((calcTotalAmount*exDutyPercent),2,'.','');				
				
			}else{
				
				calcExDutyAmt =0;
				
			}
				document.getElementById("excDutyAmt_"+i).value = calcExDutyAmt;
				
				
				if (calcExDutyAmt>=0) {
					calcTotCExDuty += parseFloat(calcExDutyAmt);
					addOtherTax();
					totExAmt += parseFloat(calcExDutyAmt);

					if (eduCess!=0) {
						var calcEduCess = number_format(((calcExDutyAmt*eduCess)/100),2,'.','');
						$("#eduCessAmt_"+i).attr("value",calcEduCess);
						calcTotCExDuty += parseFloat(calcEduCess);
						totEduCessAmt += parseFloat(calcEduCess);
					}

					if (secEduCess!=0) {
						var calcSecEduCess = number_format(((calcExDutyAmt*secEduCess)/100),2,'.','');
						$("#secEduCessAmt_"+i).attr("value",calcSecEduCess);
						calcTotCExDuty += parseFloat(calcSecEduCess);
						totSecEduCess += parseFloat(calcSecEduCess);
					}
					
					calcTotalAmount += parseFloat(calcTotCExDuty);
					grTotCentralTaxAmt += parseFloat(calcTotCExDuty);
				}
				


				/*rekha added code */
					/*
					if(calcgstAmt>0){
						calcTotalAmount += parseFloat(calcgstAmt);
						grTotCentralTaxAmt += parseFloat(calcgstAmt);
						
					}*/
				
					/*
					if(calcigstAmt>0){
						calcTotalAmount += parseFloat(calcigstAmt);
						grTotCentralTaxAmt += parseFloat(calcigstAmt);
						
					}
				*/
				
				/*end code */ 
				
				
				// After Discount
				gTotalAmount += parseFloat(calcTotalAmount); // Find the Grand total Amount
				
				calcTaxAmt	= parseFloat((calcTotalAmount*taxPercent)/100);
				$("#taxAmt_"+i).attr("value",number_format(calcTaxAmt,2,'.',''));

				var tAmt = calcTaxAmt;
				if (typeof(taxArr[taxPercent])!="undefined" && (tAmt!=0 && tAmt!="")) {
					tAmt = parseFloat(tAmt) + parseFloat(taxArr[taxPercent]);
				}
				if (tAmt!=0 && tAmt!="") 
					taxArr[taxPercent] = number_format(tAmt,2,'.','');
			} else {
				document.getElementById("totalAmount_"+i).value = number_format(calcTotalAmount,2,'.','');
			}

			
	   	}  // Status=Y End
		else if (status!='N' && selProduct=="") {
			document.getElementById("unitPrice_"+i).value 	= 0;
			document.getElementById("totalAmount_"+i).value = 0;
			document.getElementById("mrp_"+i).value 	= 0;
			document.getElementById("excDutyAmt_"+i).value = 0;
		}
	} // For Loop Ends (Product Loop)
	
	//alert("taxArrvar1"+ taxArr);
	
	if (invoiceType=='T') {
		var k	= 0;
		var tRowCount = document.getElementById("hidTaxRowCount").value;		
		// Delete If no tax amt
		if (tRowCount>0 && taxArr.length==0) {
			for(var tr=0; tr<tRowCount; tr++) {
				if(document.getElementById("tRow_"+tr)!=null) {
					var taxRIndex = document.getElementById("tRow_"+tr).rowIndex;	
					document.getElementById('tblAddSOItem').deleteRow(taxRIndex);	
				}	
			}
		}
		
		
		for (var vPercent in taxArr)
		{
			if (tRowCount>0) {			
				if(document.getElementById("tRow_"+k)!=null) {
					var tRIndex = document.getElementById("tRow_"+k).rowIndex;	
					document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
				}
			}
			
			//alert(vPercent);
			taxAmt = taxArr[vPercent];
			addTaxRow(k, vPercent, taxAmt);
			totalTaxAmt = parseFloat(totalTaxAmt) + parseFloat(taxAmt);		
			k++;
			
		}	
		document.getElementById("hidTaxRowCount").value = k;
	}
		
	
	
	// Find Grand Total Amount	 
	if (!isNaN(calcTotalAmount) && invoiceType=='T') {
		if ((discountPercent!="" || discountPercent!=0)) {
			var calcDiscountAmt = (grandTotalAmount*discountPercent)/100;			
			if (!isNaN(calcDiscountAmt)) {
				var calcDisAmt = number_format(calcDiscountAmt,2,'.','');
				document.getElementById("discountAmt").value = calcDisAmt;
				grandTotalAmount = grandTotalAmount-calcDisAmt;
			}
		}
		
		
		document.getElementById("grandTotalAmt").value = number_format(grandTotalAmount,2,'.','');
		
		var totCentralTax = 0; 
		if (grandTotalAmount>0) totCentralTax = calcCentalTax(grandTotalAmount, totExAmt,totgstAmt,totc_gstAmt,tots_gstAmt,totigstAmt,totEduCessAmt, totSecEduCess, grTotCentralTaxAmt);
		/* 
		//22 JULY 11
		if ((discountPercent!="" || discountPercent!=0)) {
			var calcDiscountAmt = (grandTotalAmount*discountPercent)/100;			
			if (!isNaN(calcDiscountAmt)) {
				document.getElementById("discountAmt").value = number_format(calcDiscountAmt,2,'.','');
			}
		}
		*/
		
		
		
		document.getElementById("totalTaxAmt").value = number_format(totalTaxAmt,2,'.',''); 
		
		// Calculate Grand Total SO AMt
		calcTotalSOAmt = parseFloat(gTotalAmount)+parseFloat(totalTaxAmt)+parseFloat(transCharge);
		
		
		document.getElementById("totalSOAmt").value = number_format(calcTotalSOAmt,2,'.','');
		
		
		//Rekha added code here 
		

		if(taxType=='GST'){
			ttc_gst = document.getElementById("totcgstAmt").value;
			tts_gst = document.getElementById("totsgstAmt").value;
			
			if(ttc_gst>0 || tts_gst>0){
				
				//calcTotalSOAmt = parseFloat(gTotalAmount)+ parseFloat(ttc_gst)+ parseFloat(tts_gst)
				//calcTotalSOAmt = parseFloat(gTotalAmount);
				calcTotalSOAmt = document.getElementById("subTotAfterExDuty").value ;
				document.getElementById("totalSOAmt").value = number_format(calcTotalSOAmt,2,'.','');	
				
			}
		
		}
		
		//OGst
		if(taxType=='IGST'){
			ttigst = document.getElementById("totigstAmt").value;
			if(ttigst>0){
				//calcTotalSOAmt = parseFloat(gTotalAmount) + parseFloat(ttigst)
				calcTotalSOAmt = document.getElementById("subTotAfterExDuty").value  ;
				document.getElementById("totalSOAmt").value = number_format(calcTotalSOAmt,2,'.','');
			}		
		}
		
		// end code 		
		
	}

	
	// ------------------------------------	
	var eachPackWt = 0;	
	var tlpGrossWt = 0;
	var tlpMCPack  = 0;
	var m =0;
	var convertArr = new Array();
	for (var tlp in combArr)
	{				
		var tLoosePack = parseInt(combArr[tlp]); // Get Total Loose Pack
		var wtComb      = wtArr[tlp].split(','); // Format like NumPacks,Package Wt	
		var wtCombNumPack  = wtComb[0];		
		var wtCombPackWt   = wtComb[1];	
		
		tlpMCPack  = Math.ceil(tLoosePack/wtCombNumPack); // Convert to MC Pack
		eachPackWt = parseFloat(wtCombPackWt)/wtCombNumPack;  // Find Each pack Wt
		tlpGrossWt = tLoosePack*parseFloat(eachPackWt);  // Find Total Gross Wt
		totalNumMCPack = parseInt(totalNumMCPack)+parseInt(tlpMCPack);
		totalMCPkgGrossWt = parseFloat(totalMCPkgGrossWt)+parseFloat(tlpGrossWt);
		m++;
	}
	
	
	
	// ------------------------------------
	// Packing	
	var tLoosePkg = 0	
	for (var lpr in pkgArr)
	{
		var tLoosePack = parseInt(pkgArr[lpr]); // Get Total Loose Pack
		
		if (chkSameRule(lpr)) {
			tLoosePkg = tLoosePkg+tLoosePack;
		}
	}
	if (tLoosePkg!=0) {		
		var pgNumPack  = getMaxPkts();		
		var pgPackWt   = numP[pgNumPack];	
		raMCPack  = Math.ceil(tLoosePkg/pgNumPack); // Convert to MC Pack		
		ePackWt = parseFloat(pgPackWt)/pgNumPack;  // Find Each pack Wt
		raGrossWt = tLoosePkg*parseFloat(ePackWt);  // Find Total Gross Wt
		totalNumMCPack = parseInt(totalNumMCPack)+parseInt(raMCPack);
		totalMCPkgGrossWt = parseFloat(totalMCPkgGrossWt)+parseFloat(raGrossWt);
	}	
	
	
	
	// Find the Net Wt (SO)
	if (!isNaN(totalNetWt)) {
		document.getElementById("netWt").value = number_format(totalNetWt,2,'.',''); 

	}
	// Find the Gross Wt
	calcGrossWt = 0;
	if (!isNaN(totalNetWt) && !isNaN(totalMCPkgGrossWt) && invoiceType=='T') {		
		calcGrossWt = parseFloat(totalMCPkgGrossWt);
		if (!isNaN(calcGrossWt)) {	
			document.getElementById("grossWt").value = number_format(calcGrossWt,2,'.',''); 
		}
	}

	// MC Pack
	if (!isNaN(totalNumMCPack) && invoiceType=='T') {
		document.getElementById("numBox").value = totalNumMCPack; 
	}	
	
	if (!isNaN(grTotPkts)) {
		document.getElementById("grTotPkts").value = grTotPkts; 
	}
	if (!isNaN(grTotPktsUnderScheme)) {
		document.getElementById("grTotPktsUnderScheme").value = grTotPktsUnderScheme; 
	}
	if (!isNaN(grTotMC)) {
		document.getElementById("grTotMC").value = grTotMC; 
	}
	if (!isNaN(grTotLP)) {
		document.getElementById("grTotLP").value = grTotLP; 
	}
	
	// Calc Total Gross Wt
	calcTotalGrossWt();
} // Function Ends Here

	function chkSameRule(lpr)
	{		
		for (var rpr in ruleArr)
		{			
			if (lpr==rpr) return true;
		}
		return false;
	}

	var maxVal = 0;
	function getMaxPkts()
	{
		for (var rpr in pkgWtArr)
		{
			if (pkgWtArr[rpr]>maxVal) maxVal = pkgWtArr[rpr];
		}	
		return maxVal;
	}

function addTaxRow(tRowId, taxPercent, taxAmt)
{	
	var tbl		= document.getElementById('tblAddSOItem');
	var taxType	= document.getElementById('taxType').value;
	
	//alert("rekha");
	
	var transportCharge = (document.getElementById("chbTransCharge").checked)?1:0;	
	var lastRow	= tbl.rows.length-(1+parseInt(transportCharge));
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "tRow_"+tRowId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);	
	var cell7	= row.insertCell(6);	
	var cell8	= row.insertCell(7);
	var cell9	= row.insertCell(8);
	var cell10	= row.insertCell(9);
	var cell11	= row.insertCell(10);	
	var cell12	= row.insertCell(11);
	var cell13	= row.insertCell(12);
	var cell14	= row.insertCell(13);
	var cell15	= row.insertCell(14);

	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";	
	cell4.className	= "listing-item"; cell4.align	= "center";
    cell5.className	= "listing-item"; cell5.align	= "center";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
	cell7.className	= "listing-thead"; cell7.align	= "center";cell7.noWrap = "true";
	cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
	cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
	cell10.className = "listing-item"; cell10.align	= "center";cell10.noWrap = "true";
	cell11.className = "listing-item"; cell11.align	= "center";cell11.noWrap = "true";
	cell12.className = "listing-item"; cell12.align	= "center";cell12.noWrap = "true";
	cell13.className = "listing-item"; cell13.align	= "center";cell13.noWrap = "true";
	cell14.className = "listing-item"; cell14.align	= "center";cell14.noWrap = "true";
	cell15.className = "listing-item"; cell15.align	= "center";cell15.noWrap = "true";

	
	cell1.innerHTML	= "";
	cell2.innerHTML	= "";
	cell3.innerHTML	= "";
	cell4.innerHTML	= "";
	cell5.innerHTML	= "";
	cell6.innerHTML = "";
	cell7.innerHTML = "";
	cell8.innerHTML = "";
	cell9.innerHTML = "";
	cell10.innerHTML = "<input name='hidTaxPercent_"+tRowId+"' type='hidden' class='listing-tshead' id='hidTaxPercent_"+tRowId+"' size='8' readonly style='text-align:right;border:none;' value='"+taxPercent+"'>";
	
	/*
	if(taxType=='GST'){
	//cell11.innerHTML = "<strong>Add"+"&nbsp;"+taxType+"</strong>(CGST + SGST)&nbsp;"+taxPercent+"%";
	cell13.innerHTML = "<strong>Add"+"&nbsp;"+taxType+"</strong>(CGST&nbsp;"+taxPercent/2 +"% + SGST&nbsp;"+taxPercent/2+"%)&nbsp;"+taxPercent+"%";

	}else{
	cell13.innerHTML = "<strong>Add"+"&nbsp;"+taxType+"</strong>&nbsp;"+taxPercent+"%";
	}
	*/
	if(taxType=='VAT' || taxType=='CST')
	{
	cell13.innerHTML = "<strong>Add"+"&nbsp;"+taxType+"</strong>&nbsp;"+taxPercent+"%";
	
	//cell14.innerHTML = "<input name='taxAmount_"+tRowId+"' type='text' id='taxAmount_"+tRowId+"' size='8' readonly style='text-align:right;border:none;' value='"+taxAmt+"'>";
	}
	
	if(taxType=='GST' || taxType=='IGST'){
		taxAmt=0;

		
	}
	
	cell14.innerHTML = "<input name='taxAmount_"+tRowId+"' type='text' id='taxAmount_"+tRowId+"' size='8' readonly style='text-align:right;border:none;' value='"+taxAmt+"'>";
	cell15.innerHTML = "";
}
// adding Discount Row
function addDiscountRow(disRemark, disPercent, discountAmt)
{		
	if(document.getElementById("tRow_d")!=null) {
		var tRIndex = document.getElementById("tRow_d").rowIndex;	
		document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
	}

	if (document.getElementById("discount").checked) {		
		var tbl		= document.getElementById('tblAddSOItem');
		var taxRowCount = document.getElementById('hidTaxRowCount').value;	
		//var lastRow	= tbl.rows.length-(1+parseInt(taxRowCount)); //22 JULY 11
		var tbleRowCount = document.getElementById("hidTableRowCount").value;
		tbleRowCount = (tbleRowCount!="")?tbleRowCount:0;
		var lastRow	= parseInt(tbleRowCount)+1;
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "tRow_d";	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell4	= row.insertCell(2);
		var cell7	= row.insertCell(3);	
		var cell8	= row.insertCell(4);
		var cell9	= row.insertCell(5);

		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-thead"; cell2.align	= "right";cell2.colSpan=5;		
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.colSpan=4;
		cell7.className	= "listing-tshead"; cell7.align	= "center";cell7.noWrap = "true";
		cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
		cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";		
		
		cell1.innerHTML	= "";
		cell2.innerHTML	= "Remark:&nbsp; ";
		cell4.innerHTML	= "<input name='discountRemark' type='text' id='discountRemark' size='32' value='"+disRemark+"'>";		
		cell7.innerHTML = "(Less) Discount<br/><input name='discountPercent' type='text' id='discountPercent' size='5' style='text-align:right;' value='"+disPercent+"' autocomplete='off' onkeyup='multiplySalesOrderItem();'>&nbsp;%";
		cell8.innerHTML	= "<input name='discountAmt' type='text' id='discountAmt' size='8' style='text-align:right;border:none;' readonly value='"+discountAmt+"' >";
		cell9.innerHTML = "";
	} else {
		multiplySalesOrderItem();		
	}	
}

function addTransportChargeRow(transportChargeAmt, isOnClick)
{		
	if(document.getElementById("tRow_trc")!=null) {
		var tRIndex = document.getElementById("tRow_trc").rowIndex;	
		document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
	}	
	
	if (document.getElementById("chbTransCharge").checked) {		
		var tbl		= document.getElementById('tblAddSOItem');	
		var lastRow	= tbl.rows.length-1;
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "tRow_trc";	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell4	= row.insertCell(2);
		var cell7	= row.insertCell(3);	
		var cell8	= row.insertCell(4);
		var cell9	= row.insertCell(5);


		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-thead"; cell2.align	= "right";cell2.colSpan=5;		
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.colSpan=4;
		cell7.className	= "listing-tshead"; cell7.align	= "center";cell7.noWrap = "true";
		cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
		cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
		
		
		cell1.innerHTML	= "";
		cell2.innerHTML	= "";
		cell4.innerHTML	= "";		
		cell7.innerHTML = "Freight (Rs.)";
		cell8.innerHTML	= "<input name='transportCharge' type='text' id='transportCharge' size='8' style='text-align:right;' value='"+transportChargeAmt+"' onkeyup='multiplySalesOrderItem();'>";
		cell9.innerHTML = "";
		
		if (isOnClick!="") multiplySalesOrderItem();

	} else {
		multiplySalesOrderItem();
	}
	
	
	
}

function displaySubTotAfterFrCharge()
{
	var rowType = "STAFC";
	if(document.getElementById("tRow_ot"+rowType)!=null) {
		var tRIndex = document.getElementById("tRow_ot"+rowType).rowIndex;	
		document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
	}

	if (document.getElementById("discount").checked || document.getElementById("chbTransCharge").checked) {
		addOtherTaxRow(rowType, 0, 'Total (Rs.)', 'subTotAfterFrCharge', 1,0);
	}
	
}

function displaySubTotalAfterDisOrTrns()
{
	var rowType = "STATD";
	if(document.getElementById("tRow_ot"+rowType)!=null) {
		var tRIndex = document.getElementById("tRow_ot"+rowType).rowIndex;	
		document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
	}

	if (document.getElementById("discount").checked || document.getElementById("chbTransCharge").checked) {
		addOtherTaxRow(rowType, 0, 'Total (Rs.)', 'subTotAfterTrDis', 1,0);
	}
	
}	

//Validate repeated
function validateSOProductRepeat()
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
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=0; j<rc; j++ )    {
	var status = document.getElementById("status_"+j).value;	
       if (status!='N')
       {
        var rv = document.getElementById("selProduct_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Please make sure the selected product is not duplicate.");
            document.getElementById("selProduct_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
     }
    }
    return true;
}

// sales Order extended date check
function salesOrderExtendedDateCheck(form)
{	
	var d = new Date();
	var t_date = d.getDate();      // Returns the day of the month
	if (t_date<10) {
		t_date = "0"+t_date;
	}
	var t_mon = d.getMonth() + 1;      // Returns the month as a digit
	if (t_mon<10) {
		t_mon = "0"+t_mon;
	}
	var t_year = d.getFullYear();  // Returns 4 digit year
	
	var curr_date	=	t_date + "/" + t_mon + "/" + t_year;
		
	CDT		=	curr_date.split("/");
	var CD_time	=	new Date(CDT[2], CDT[1], CDT[0]);
	
	var lastDate	=	document.getElementById("lastDate").value;	
	LDT		=	lastDate.split("/");
	var LD_time	=	new Date(LDT[2], LDT[1], LDT[0]);
	
	var one_day=1000*60*60*24

	//Calculate difference btw the two dates, and convert to days
	var extendedDays = Math.ceil((LD_time.getTime()-CD_time.getTime())/(one_day));
		
	if (extendedDays<0) {
		alert(" Date of Dispatch should be greater than or equal to current date");
		document.getElementById("lastDate").focus();
		return false;
	}
	return true;	
}

function getStkUnitRate(rowId)
{
	var selDistributor  = document.getElementById('selDistributor').value;
	var selProduct		= document.getElementById('selProduct_'+rowId).value;
	var selState		= document.getElementById('selState').value;
	var invoiceDate		= document.getElementById('invoiceDate').value;
	var selCity			= document.getElementById('selCity').value;
	var billingType		= document.getElementById('billingType').value;
	//var seltaxType		= document.getElementById('taxType').value;

	
	//alert(document.getElementById('gst_igst').style.display="block")
	
/*
	if(seltaxType=='GST'){
		document.getElementById('gst_igst').style.display="none";	
		//document.getElementById('gst_igst').innerHTML="GST";
	
	}
	
*/
	xajax_getStockUnitRate(selDistributor, selProduct, rowId, selState, invoiceDate, selCity, '', billingType);
}

//ADD MULTIPLE Item- ADD ROW START


			
			
function addNewSOItemRow(tableId, selStockId, unitPrice, qty, totalAmt, salesOrderEntryId, selMCPackId, numMCPack, numLoosePack, distMgnStateEntryId, taxPercent, pGrossWt, pMCPkgGrossWt, productCategoryComb, numPacks, mcPackageWt, mcCombination, pkgGroupComb, leftPkgRule, rightPkgRule, selDistributorId, stateId, productPriceRateListId, mode, distMgnRateListId, freePkts, basicRate, mrp, exDutyPercent, exDutyAmt, exDutyMasterId, exChapterSubhead, mcPkgWtId, gst_percent, gst_amt, igst_percent, igst_amt, cgst_percent, sgst_percent, cgst_amt, sgst_amt,gst_entry_id,igst_entry_id)
{

	var tbl		= document.getElementById(tableId);
	var taxRowCount = document.getElementById('hidTaxRowCount').value;
	var additionalRow = 0;
	additionalRow += (document.getElementById("discount").checked)?1:0;
	additionalRow += (document.getElementById("chbTransCharge").checked)?1:0;	

	var invGrTotAmt 	= $("#totalSOAmt").val();
	var exBillingForm	= $("#hidExBillingForm").val();
	
	if (mode=="" && parseInt(invGrTotAmt)>0 && exBillingForm!='FCT1') {		
		additionalRow += ($("#hidExDutyActive").val()!=0)?1:0;
		additionalRow += ($("#hidEduCess").val()!=0)?1:0;
		additionalRow += ($("#hidSecEduCess").val()!=0)?1:0;
		if (additionalRow>2) additionalRow += 2; // For total central tax and subtotal
		
	}

	var lastRow	= tbl.rows.length-(2+parseInt(taxRowCount)+additionalRow);	
	var row		= tbl.insertRow(lastRow);
		
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
	var cell6	= row.insertCell(5);	
	var cell7	= row.insertCell(6);	
	var cell8	= row.insertCell(7);	
	var cell9	= row.insertCell(8);
	var cell10	= row.insertCell(9);
	var cell11	= row.insertCell(10);
	var cell12	= row.insertCell(11);
	var cell13	= row.insertCell(12);
	var cell14	= row.insertCell(13);
	var cell15	= row.insertCell(14);
	
	cell1.id = "srNo_"+fieldId;
	
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
    cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";cell5.noWrap = "true";
	cell6.className	= "listing-item"; cell6.align	= "center";cell6.noWrap = "true";
	cell7.className	= "listing-item"; cell7.align	= "center";cell7.noWrap = "true";
	cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
	cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
	cell10.className = "listing-item"; cell10.align	= "center";cell10.noWrap = "true";
	cell11.className = "listing-item"; cell11.align	= "center";cell11.noWrap = "true";	
	cell12.className = "listing-item"; cell12.align	= "center";cell12.noWrap = "true";
	cell13.className = "listing-item"; cell13.align	= "center";cell13.noWrap = "true";
	cell14.className = "listing-item"; cell14.align	= "center";cell14.noWrap = "true";
	cell15.className = "listing-item"; cell15.align	= "center";cell15.noWrap = "true";

	//var selectStock	= "<select name='selProduct_"+fieldId+"' id='selProduct_"+fieldId+"' onchange=\"xajax_getStockUnitRate(document.getElementById('selDistributor').value, document.getElementById('selProduct_"+fieldId+"').value, "+fieldId+", document.getElementById('selState').value, document.getElementById('invoiceDate').value, document.getElementById('selCity').value, '', document.getElementById('billingType').value);\"><option value=''>--Select--</option>";
	var selectStock	= "<select name='selProduct_"+fieldId+"' id='selProduct_"+fieldId+"' onchange=\"getStkUnitRate("+fieldId+");\"><option value=''>--Select--</option>";
	<?php
		if (sizeof($productMRPMasterRecords)>0) {	
			foreach($productMRPMasterRecords as $pr) {
				$productId	= $pr[0];
				$productName	= $pr[1];
	?>	
		if (selStockId== "<?=$productId?>")  var sel = "Selected";
		else var sel = "";

	selectStock += "<option value=\"<?=$productId?>\" "+sel+"><?=$productName?></option>";	
	<?php
			}
		}
	?>
	selectStock += "</select>";


	var selectMCPkg	= "<select name='selMcPkg_"+fieldId+"' id='selMcPkg_"+fieldId+"' onchange=\"xajax_getPackageDetails(document.getElementById('selMcPkg_"+fieldId+"').value,document.getElementById('quantity_"+fieldId+"').value,"+fieldId+", document.getElementById('selProduct_"+fieldId+"').value, document.getElementById('freePkts_"+fieldId+"').value);multiplySalesOrderItem();\" style='width:80px;'><option value=''>--Select--</option>";
	<?php
		if (sizeof($mcpackingRecords)>0) {	
			foreach($mcpackingRecords as $mcp) {
				$mcpackingId	= $mcp[0];
				$mcpackingCode	= stripSlash($mcp[1]);
	?>	
		
		if (selMCPackId== "<?=$mcpackingId?>")  var sel = "Selected";
		else var sel = "";		

	selectMCPkg += "<option value=\"<?=$mcpackingId?>\" "+sel+"><?=$mcpackingCode?></option>";	
	<?php
			}
		}
	?>
	selectMCPkg += "</select>";
	
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setPOItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='distMgnStateEntryId_"+fieldId+"' type='hidden' id='distMgnStateEntryId_"+fieldId+"' value='"+distMgnStateEntryId+"'><input name='taxPercent_"+fieldId+"' type='hidden' id='taxPercent_"+fieldId+"' value='"+taxPercent+"' readonly><input name='pGrossWt_"+fieldId+"' type='hidden' id='pGrossWt_"+fieldId+"' value='"+pGrossWt+"'><input name='pMCPkgGrossWt_"+fieldId+"' type='hidden' id='pMCPkgGrossWt_"+fieldId+"' value='"+pMCPkgGrossWt+"'><input name='hidMCPkg_"+fieldId+"' type='hidden' id='hidMCPkg_"+fieldId+"' readonly value='"+selMCPackId+'_'+mcPkgWtId+"'><input type='hidden' name='basicRate_"+fieldId+"' id='basicRate_"+fieldId+"' readonly value='"+basicRate+"'><input name='hidMCPkgWtId_"+fieldId+"' type='hidden' id='hidMCPkgWtId_"+fieldId+"' readonly value='"+mcPkgWtId+"'>";	

	var hidOtherFields = "<input name='pCategoryComb_"+fieldId+"' type='hidden' id='pCategoryComb_"+fieldId+"' readonly value='"+productCategoryComb+"'><input name='numPacks_"+fieldId+"' type='hidden' id='numPacks_"+fieldId+"' readonly value='"+numPacks+"'><input name='mcPackageWt_"+fieldId+"' type='hidden' id='mcPackageWt_"+fieldId+"' readonly value='"+mcPackageWt+"'><input name='mcpComb_"+fieldId+"' type='hidden' id='mcpComb_"+fieldId+"' readonly value='"+mcCombination+"'><input name='pkgGroup_"+fieldId+"' type='hidden' id='pkgGroup_"+fieldId+"' readonly value='"+pkgGroupComb+"'><input name='leftPkgRule_"+fieldId+"' type='hidden' id='leftPkgRule_"+fieldId+"' readonly value='"+leftPkgRule+"'><input name='rightPkgRule_"+fieldId+"' type='hidden' id='rightPkgRule_"+fieldId+"' readonly value='"+rightPkgRule+"'><input type='hidden' name='taxAmt_"+fieldId+"' id='taxAmt_"+fieldId+"' readonly value=''/>";
	
	cell1.innerHTML	= "";//(fieldId+1);
	cell2.innerHTML	= selectStock;
	cell3.innerHTML	= "<input name='quantity_"+fieldId+"' type='text' id='quantity_"+fieldId+"' value='"+qty+"' size='8' style='text-align:right' autoComplete='off' onKeyUp=\"xajax_getPackageDetails(document.getElementById('selMcPkg_"+fieldId+"').value,document.getElementById('quantity_"+fieldId+"').value,"+fieldId+",  document.getElementById('selProduct_"+fieldId+"').value, document.getElementById('freePkts_"+fieldId+"').value);\"><input name='hidSelStock_"+fieldId+"' type='hidden' id='hidSelStock_"+fieldId+"' readonly value='"+selStockId+"'>"+hiddenFields+"";
	cell4.innerHTML	= "<input name='freePkts_"+fieldId+"' type='text' id='freePkts_"+fieldId+"' size='3' style='text-align:right;' value='"+freePkts+"' autocomplete='off' onkeyup=\"xajax_getPackageDetails(document.getElementById('selMcPkg_"+fieldId+"').value,document.getElementById('quantity_"+fieldId+"').value,"+fieldId+",  document.getElementById('selProduct_"+fieldId+"').value, document.getElementById('freePkts_"+fieldId+"').value); multiplySalesOrderItem();\">";	
	cell5.innerHTML = selectMCPkg+hidOtherFields;
	cell6.innerHTML = "<input name='mcPack_"+fieldId+"' type='text' id='mcPack_"+fieldId+"' value='"+numMCPack+"' size='8' style='text-align:right;border:none;' readonly>";
	cell7.innerHTML = "<input name='loosePack_"+fieldId+"' type='text' id='loosePack_"+fieldId+"' value='"+numLoosePack+"' size='8' style='text-align:right;border:none;' readonly>";
	cell8.innerHTML = "<input name='mrp_"+fieldId+"' type='text' id='mrp_"+fieldId+"' value='"+mrp+"' size='6' style='text-align:right; border:none;' readonly>";
	cell9.innerHTML	= "<input name='unitPrice_"+fieldId+"' type='text' id='unitPrice_"+fieldId+"' value='"+unitPrice+"' size='8' style='text-align:right;border:none;' readonly><input type=\"hidden\" name=\"salesOrderEntryId_"+fieldId+"\" value="+salesOrderEntryId+">";
	var exDutyHidden = "<input type='hidden' name='excDutyEntryId_"+fieldId+"' id='excDutyEntryId_"+fieldId+"' value='"+exDutyMasterId+"' size='4' readonly /><input type='hidden' name='excDutyAmt_"+fieldId+"' id='excDutyAmt_"+fieldId+"' value='"+exDutyAmt+"' size='4' readonly title='Excise Duty' /><input type='hidden' name='eduCessAmt_"+fieldId+"' id='eduCessAmt_"+fieldId+"' value='' size='4' readonly title='Edu Cess' /><input type='hidden' name='secEduCessAmt_"+fieldId+"' id='secEduCessAmt_"+fieldId+"' value='' size='4' readonly title='Sec.Edu Cess' />";
	cell10.innerHTML = "<input name='exciseDuty_"+fieldId+"' type='text' id='exciseDuty_"+fieldId+"' value='"+exDutyPercent+"' size='6' style='text-align:center; border:none;' readonly>"+exDutyHidden;

	
	
	var gstHidden = "<input type='hidden' name='gstEntryId_"+fieldId+"' id='gstEntryId_"+fieldId+"' value='"+gst_entry_id+"' size='4' readonly /><input type='hidden' name='gstAmt_"+fieldId+"' id='gstAmt_"+fieldId+"' value='"+gst_amt+"' size='4' readonly title='Gst' /><input type='hidden' name='c_gstAmt_"+fieldId+"' id='c_gstAmt_"+fieldId+"' value='"+cgst_amt+"' size='4' readonly title='CGST' /><input type='hidden' name='s_gstAmt_"+fieldId+"' id='s_gstAmt_"+fieldId+"' value='"+sgst_amt+"' size='4' readonly title='SGST' />";
	cell11.innerHTML = "<input name='gst_"+fieldId+"' type='hidden' id='gst_"+fieldId+"' value='"+gst_percent+"' size='6' style='text-align:center; border:none;' readonly>CGST: <input name='c_gst_"+fieldId+"' type='text' id='c_gst_"+fieldId+"' value='"+cgst_percent+"' size='6' style='text-align:center; border:none;' readonly><br>SGST: <input name='s_gst_"+fieldId+"' type='text' id='s_gst_"+fieldId+"' value='"+sgst_percent+"' size='6' style='text-align:center; border:none;' readonly>"+gstHidden;
	
	var igstHidden = "<input type='hidden' name='igstEntryId_"+fieldId+"' id='igstEntryId_"+fieldId+"' value='"+igst_entry_id+"' size='4' readonly /><input type='hidden' name='igstAmt_"+fieldId+"' id='igstAmt_"+fieldId+"' value='"+igst_amt+"' size='4' readonly title='iGst' />";
	cell12.innerHTML = "<input name='igst_"+fieldId+"' type='text' id='igst_"+fieldId+"' value='"+igst_percent+"' size='6' style='text-align:center; border:none;' readonly>"+igstHidden;

	cell13.innerHTML = "<span name='chaptSubhead_"+fieldId+"' id='chaptSubhead_"+fieldId+"'>"+exChapterSubhead+"</span>";
	cell14.innerHTML = "<input name='totalAmount_"+fieldId+"' type='text' id='totalAmount_"+fieldId+"' size='8' readonly style='text-align:right;border:none;' value='"+totalAmt+"'>";
	cell15.innerHTML = imageButton;	
	
	// When Edit Mode
	if (mode==0) {
		//getProductsInRow(selDistributorId, stateId, productPriceRateListId, distMgnRateListId, fieldId);
		getMCPack(selStockId, fieldId);
	}	

	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;
	assignSrNo();
}
	
	function getMCPack(selStockId, rowId)
	{
		xajax_getMCPackingRecs(selStockId, rowId);
	}

	function setPOItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("row_"+id).style.display = 'none';
			removeTaxRow();			
			multiplySalesOrderItem();
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
	
	function removeTaxRow()
	{
		var tRowCount = document.getElementById("hidTaxRowCount").value;
		for (k=0;k<tRowCount; k++ )
		{
			if (tRowCount>0) {			
				if(document.getElementById("tRow_"+k)!=null) {
					var tRIndex = document.getElementById("tRow_"+k).rowIndex;	
					document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
				}
			}
		}	
	}
	
	function printSalesOrderWindow(url, width, height)
	{
		var SOId = document.getElementById("selSOId").value;
		var displayUrl = url+"?selSOId="+SOId;
		var winl = (screen.width - width) / 2;
		var wint = (screen.height - height) / 2;
		eval("page = window.open(displayUrl, 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");
	}
	
	/* Disable and enable the Print PO Button */
	function disablePrintSOButton()
	{
		if (document.getElementById("selSOId").value=="") {
			document.getElementById("cmdPrintSO").disabled = true;
		} else {
			document.getElementById("cmdPrintSO").disabled = false;
		}
	}
	
	function enableSOButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableSOButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==0) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	/*
		Show/hide Invoice Tpe Selection
	*/
	function showInvoiceType()
	{
		var invoiceType = document.getElementById("invoiceType").value;
		if (invoiceType=='S') {
			var tRowCount = document.getElementById("hidTaxRowCount").value;
			if (tRowCount>0) {
				for (var k=0; k<tRowCount;k++) {			
					if(document.getElementById("tRow_"+k)!=null) {
						var tRIndex = document.getElementById("tRow_"+k).rowIndex;	
						document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
					}
				}
			}
			//document.getElementById("grandTotalRow").style.display = 'none';
			document.getElementById("additionalItemRow").style.display = '';
				
			document.getElementById("grossWtRow").innerHTML = "<input type=\'text\' name=\"grossWt\" id=\"grossWt\" value=\"<?=$grossWt?>\" size=\"4\" style=\"font-weight:bold;text-align:right;\" onkeyup=\"calcTotalGrossWt();\">";
			document.getElementById("numBoxRow").innerHTML = "<input type=\'text\' name=\"numBox\" id=\"numBox\" value=\"<?=$numBox?>\" size=\"3\" style=\"font-weight:bold;text-align:right;\" >";			
			document.getElementById("equalTo").style.display = '';					
		} else {
			
			document.getElementById("grandTotalRow").style.display = '';
			document.getElementById("additionalItemRow").style.display = 'none';
			document.getElementById("grossWtRow").innerHTML = "<input type=\'hidden\' name=\"grossWt\" id=\"grossWt\" value=\"<?=$grossWt?>\" size=\"4\" style=\"font-weight:bold;text-align:right;border:none;\" readonly=\"true\" onkeyup=\"calcTotalGrossWt();\">";
			document.getElementById("numBoxRow").innerHTML = "<input type=\'text\' name=\"numBox\" id=\"numBox\" value=\"<?=$numBox?>\" size=\"3\" style=\"font-weight:bold;border:none;text-align:right;\">";		
			document.getElementById("equalTo").style.display = 'none';
			multiplySalesOrderItem();
		}
	}

	function convertLoosePack()
	{
		var rowCount 	= document.getElementById("hidTableRowCount").value;	

	}


	/*
	* Add New Additional Item
	*/
	function addNewSOAdditionalItemRow(tableId, itemName, ItemWt)
	{
			
		var tbl		= document.getElementById(tableId);	
		var lastRow	= tbl.rows.length-1;	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "aRow_"+fldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
			
	
		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-item"; cell2.align	= "center";
		cell3.className	= "listing-item"; cell3.align	= "center";
	
		
		var ds = "N";			
		var imageButton = "<a href='###' onClick=\"setAdditionItemStatus('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
		
	
		var hiddenFields = "<input name='status_"+fldId+"' type='hidden' id='status_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'>";	
			
		cell1.innerHTML = "<input name='itemName_"+fldId+"' type='text' id='itemName_"+fldId+"' size='24' value='"+itemName+"'>"+hiddenFields;
		cell2.innerHTML = "<input name='itemWt_"+fldId+"' type='text' id='itemWt_"+fldId+"' size='6' style='text-align:right;' value='"+ItemWt+"' onkeyup='calcAdditionalItem();'>";	
		cell3.innerHTML = imageButton;	
		
		fldId		= parseInt(fldId)+1;	
		document.getElementById("hidItemTbleRowCount").value = fldId;		
	}

	function setAdditionItemStatus(id)
	{
		if (confirmRemoveItem()) {
			document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
			document.getElementById("aRow_"+id).style.display = 'none';
			calcAdditionalItem();	
		}
		return false;
	}

	// Calc Additional Item
	function calcAdditionalItem()
	{
		var rowCount 	= document.getElementById("hidItemTbleRowCount").value;
		var totalItemWt = 0;
		for (i=0; i<rowCount; i++) {
			var status = document.getElementById("status_"+i).value;	
	    		var itemName  = document.getElementById("itemName_"+i).value;
	    		if (status!='N' && itemName!="")
	    		{
				var itemWt = document.getElementById("itemWt_"+i).value;
				totalItemWt = parseFloat(totalItemWt)+parseFloat(itemWt);
			}
		}
		if (!isNaN(totalItemWt)) {
			document.getElementById("additionalItemTotalWt").value = totalItemWt;
		}	

		calcTotalGrossWt();	
	}

	// Calc Gross Wt
	function calcTotalGrossWt()
	{
		var totalItemWt = 0;
		var grossWt	= 0;
		var totalGrossWt = 0;
		var invoiceType = document.getElementById("invoiceType").value;
		if (invoiceType=='S') {
			var totalItemWt = (document.getElementById("additionalItemTotalWt").value!="")?document.getElementById("additionalItemTotalWt").value:0;		
		} 
		var grossWt     = document.getElementById("grossWt").value;
		totalGrossWt = parseFloat(totalItemWt)+parseFloat(grossWt);
		if (!isNaN(totalGrossWt)) {
			document.getElementById("totalGrossWt").value = totalGrossWt;
		}
	}

	function updatePendingSO()
	{
		var uptdMsg	= "Do you wish to update all pending Sales order?";
		if(confirm(uptdMsg)) {
			xajax_updatePendingSO();
			return true;
		}
		return false;	
	}

	// For isplaying or hiding OEC Row
	function OECRow(exempted)
	{
		if (exempted=='Y') document.getElementById('oecRow').style.display = "";
		else document.getElementById('oecRow').style.display = "none";
	}

	function updateSOTime(salesOrderId)
	{
		xajax_updateSOModifiedTime(salesOrderId);
	}

	// time ticker
	//to store timeout ID
	var tID;
	function tickTimer(t, salesOrderId)
	{		
		//if time is in range
		if (t>=0) {
			var timeCalc = Math.floor(t);
			
			document.getElementById("timeTickerRow").innerHTML= "Time Remaining "+Math.floor(t/60) + ":" + (t%60)+" seconds.";
			t=t-1;
			tID=setTimeout("tickTimer('"+t+"','"+salesOrderId+"')",1000);
		}
		//stop the timeout event
		else
		{
			xajax_updateSOModifiedTime(salesOrderId);
			setTimeout("killTimer('"+tID+"')",1000);
			//killTimer(tID);
			document.getElementById("timeTickerRow").innerHTML = "Edit Lock Released.";
		}
		
	}	
	//function to stop the timeout event
	function killTimer(id)
	{		
		clearTimeout(id);
		document.getElementById("frmSalesOrder").submit();
	}
	// time ticker Ends Here

	function showInvRow(mode, proformaInvNo, sampleInvNo)
	{		
		var invoiceType = document.getElementById("invoiceType").value;
		if (invoiceType=='S') {
			document.getElementById("proformaInvNoRow").style.display="none";
			document.getElementById("sampleInvNoRow").style.display="";
			document.getElementById("proformaInvoiceNo").value = "";
			if (mode==1) xajax_getSampleInvoiceNo();
			else document.getElementById("sampleInvoiceNo").value = sampleInvNo;
		} else {
			document.getElementById("sampleInvNoRow").style.display="none";
			document.getElementById("proformaInvNoRow").style.display="";
			document.getElementById("sampleInvoiceNo").value = "";
			if (mode==1) xajax_getProformaInvoiceNo();
			else document.getElementById("proformaInvoiceNo").value = proformaInvNo;
		}
	}

	function rloadSOList(frmElem)
	{
		xajax_reloadSOList(frmElem);
	}


/*
Auto Refresh Page with Time script
By JavaScript Kit (javascriptkit.com)
Over 200+ free scripts here!
*/

//enter refresh time in "minutes:seconds" Minutes should range from 0 to inifinity. Seconds should range from 0 to 59
	//var limit="1:05";	
	
	var t ='<?=$refreshTimeLimit?>';	
	var sTime = Math.floor(t/60)+":"+(t%60);	
	var limit= sTime;		
	
	if (document.images){	
		var parselimit=limit.split(":");
		parselimit=parselimit[0]*60+parselimit[1]*1;
	}
	var curtime = 0;
	function beginrefresh()
	{		
		if (!document.images) return;
		if (parselimit==1) {
			document.getElementById("frmSalesOrder").submit();
		}
		else { 			
			parselimit = parselimit-1 ;
			var curmin=Math.floor(parselimit/60);
			var cursec=parselimit%60;
			if (curmin!=0)  
				curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
			else
				curtime=cursec+" seconds left until page refresh!";
			//window.status=curtime;
			document.getElementById("refreshMsgRow").innerHTML = curtime;
			setTimeout("beginrefresh()",1000);
		}
	}

	// Generate Pakng Ins
	function validateGenPkgIns(fieldPrefix, rowCount, userId)
	{	
		var count = 0;
		var pkgGenerated = false;
		var soArr = new Array();
		var j=0;	
		for (i=1; i<=rowCount; i++ )
		{
			if(document.getElementById(fieldPrefix+i).checked)
			{
				count++;
				var soId 	= document.getElementById(fieldPrefix+i).value;
				var pkgGen	= document.getElementById("pkgGen_"+i).value;
				if (pkgGen=='Y') {
					pkgGenerated = true;	
				}
				if (pkgGen=='N') {
					soArr[j] =  soId;
					j++;
				}
			}
		}
		
		if (count==0) {
			alert("Please select a record to Generate Packing Instruction.");
			return false;
		}

		if (pkgGenerated) {
			alert("Pkg instruction already generated for the selected invoice.");
			return false;
		}
	
		var conDelMsg	= "Do you wish to Generate Packing Instruction?";
		if (confirm(conDelMsg)) {
			var recUptd = false;
			for (var rowId in soArr)
			{			
				var selSOId = soArr[rowId];
				// Insert Pkng instruction
				xajax_genPkgInstruction(selSOId, userId);
				recUptd = true;
			}
			if (recUptd) {
				alert("Packing Instruction generated successfully.");	
				document.getElementById('frmSalesOrder').submit();
			}
			return true;
		}
		return false;
	}

	// Update SO Main Rec
	function updateSOMainRec(soId, selDate)
	{		
		 xajax_updateSOMainRec(soId, selDate);
	}

	function redirectUrl(editId)
	{
		window.location="SalesOrder.php?editId="+editId;
	}

	/*
		Transporter Clear
	*/
	function clearTransporter()
	{
		document.getElementById("selTransporter").value = "";
	}

	// Packing instruction generation RowWise
	function validatePkgInstGen(salesOrderId, userId, rowId)
	{
		if (!confirm("Do you wish to generate packing instruction?")) {
			return false;
		}
		// Insert Packing Instruction
		xajax_genPkgInstruction(salesOrderId, userId);
		document.getElementById("pkgInstCol_"+rowId).innerHTML = "PENDING";
		return true;
	}

	// gate Pass generation RowWise
	function validateGatePassGen(salesOrderId, userId, rowId, dateFrom, dateTill, page, invoiceTypeFilter,company,unit,number_gen)
	{

		if (!confirm("Do you wish to generate a Gate Pass?")) {
			return false;
		}
		
		var redirectLoc ="selectFrom="+dateFrom+"&selectTill="+dateTill+"&pageNo="+page+"&invoiceTypeFilter="+invoiceTypeFilter+"&editMode=1&urlFrom=SO&soId="+salesOrderId;
		// Insert gate pass
		xajax_genGatePass(salesOrderId, userId,company,unit,number_gen);
		//window.location.href = "ManageGatePass.php?"+redirectLoc;
		
		setTimeout("displayGatePass('"+redirectLoc+"')",2500); //3500	
		document.getElementById("gatePassCol_"+rowId).innerHTML = "PENDING";
		return true;
	}
	function displayGatePass(redirectLoc)
	{
		window.location.href = "ManageGatePass.php?"+redirectLoc;		
	}

	// Display Rate 
	function disUnitRate(selStateId, selCityId)
	{
		var tableRowCount = document.getElementById('hidTableRowCount').value;
		var distributorId = document.getElementById('selDistributor').value;
		var stateId 	= document.getElementById('selState').value;
		var invoiceDate = document.getElementById('invoiceDate').value;
		var cityId 	= document.getElementById('selCity').value;
		var billingType	= document.getElementById('billingType').value;

		var unitRateArr = new Array();
		for (i=0; i<tableRowCount; i++) {
			var productId = document.getElementById('selProduct_'+i).value;
			unitRateArr[i] = productId;
		}
		var arrStr = unitRateArr.join(",");
		stateId = (selStateId!="")?selStateId:stateId;
		cityId  = (selCityId!="")?selCityId:cityId;		
		xajax_displayUnitRate(distributorId, stateId, invoiceDate, cityId, billingType, arrStr);
		setTimeout("multiplySalesOrderItem()",2000);
	}

	function clearFields()
	{
		var rowCount = document.getElementById('hidTableRowCount').value;
		var distributorId = document.getElementById('selDistributor').value;
		var stateId 	= document.getElementById('selState').value;
		var invoiceDate = document.getElementById('invoiceDate').value;
		var cityId 	= document.getElementById('selCity').value;
		var billingType	= document.getElementById('billingType').value;

		for (i=0; i<rowCount; i++) {
			var selStatus = document.getElementById("status_"+i).value;	
			var selProduct  = document.getElementById("selProduct_"+i).value;
			if (selStatus!='N' && selProduct=="") {
				xajax_getStockUnitRate(distributorId, selProduct, i, stateId, invoiceDate, cityId, '', billingType);
			}
		}
	}

	function changeInvoiceDate()
	{
		var mode	= document.getElementById("hidMode").value;
		if (mode==1) {
			var entryDate = document.getElementById('entryDate').value;			
			document.getElementById('invoiceDate').value =  entryDate;
			xajax_chkValidInvoiceDate(entryDate);
		}
	}

	function getItemArr(json, tableRowCount)
	{				
		var myObject = eval('(' + json + ')');

		for (var i=0; i<tableRowCount; i++) {
			document.getElementById("selProduct_"+i).length=0;	
			for (var key in myObject) {				
				addDropDownList("hidSelStock_"+i,"selProduct_"+i,key,myObject[key]);
			}
		}
	}

	function addOtherTax()
	{		
		
		//document.getElementById("taxType").value;
	
	var taxType  = document.getElementById("taxType").value;
	

		if(taxType!='GST' && taxType!='IGST'){
			var eduCess = $("#hidEduCess").val();
			var secEduCess = $("#hidSecEduCess").val();
			addOtherTaxRow('EXD', 0, 'Basic Excise Duty', 'totExDutyAmt', 0, 1);
			if (eduCess!=0) addOtherTaxRow('EDUC', eduCess, 'Edu Cess - '+eduCess+'%', 'totEduCess', 0, 1);
			if (secEduCess!=0) addOtherTaxRow('SEDUC', secEduCess, 'Sec. EduCess - '+secEduCess+'%', 'totSecEduCess', 0,1);
			addOtherTaxRow('GTEXD', secEduCess, 'Total C.Excise Duty', 'grandTotCExDuty', 0,1);
		}
	
		//var hidedit_taxtype  = document.getElementById("hidedit_taxtype").value;
		
		//alert(taxType);
		
		if(taxType=='GST'){
			addOtherTaxRow('CGST', 0, 'Total CGST', 'totcgstAmt', 0, 1);
			addOtherTaxRow('SGST', 0, 'Total SGST', 'totsgstAmt', 0, 1);
			
			
			
			//addOtherTaxRow('GST', 0, 'Total	GST', 'totgstAmt', 0, 1);
		}
		if(taxType=='IGST'){
			addOtherTaxRow('IGST', 0, 'Total IGST ', 'totigstAmt', 0, 1);
		}
		addOtherTaxRow('STAE', 0, 'Total (Rs.)', 'subTotAfterExDuty', 1,1);

	
	}

	function addOtherTaxRow(dutyType, tPercent, tTitle, tField, tFException, canAddTblCnt)
	{
		
		if(document.getElementById("tRow_ot"+dutyType)!=null) {
			var tRIndex = document.getElementById("tRow_ot"+dutyType).rowIndex;	
			document.getElementById('tblAddSOItem').deleteRow(tRIndex);	
		}
		
		var discountCnt = 0;
		if (canAddTblCnt==1) {
			//discountCnt += (document.getElementById("discount").checked)?1:0;
			discountCnt += (document.getElementById("chbTransCharge").checked)?1:0;
		}
		var tbl		= document.getElementById('tblAddSOItem');
		var taxRowCount = document.getElementById('hidTaxRowCount').value;	
		var lastRow	= tbl.rows.length-(1+parseInt(taxRowCount)+discountCnt);
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		row.className 	= "whiteRow";
		row.align 	= "center";
		row.id 		= "tRow_ot"+dutyType;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell4	= row.insertCell(2);
		var cell7	= row.insertCell(3);	
		var cell8	= row.insertCell(4);
		var cell9	= row.insertCell(5);


		cell1.className	= "listing-item"; cell1.align	= "center";
		cell2.className	= "listing-thead"; cell2.align	= "right";cell2.colSpan=5;		
		cell4.className	= "listing-item"; cell4.align	= "center";cell4.colSpan=6;
		cell7.className	= "listing-tshead"; cell7.align	= "right";cell7.noWrap = "true";
		cell8.className	= "listing-item"; cell8.align	= "center";cell8.noWrap = "true";
		cell9.className	= "listing-item"; cell9.align	= "center";cell9.noWrap = "true";
		var tFieldBold = "";
		if (tFException>0) {
			//tTitle = "<strong>"+tTitle+"</strong>";
			tFieldBold = "font-weight:bold;";
			cell7.className	= "listing-thead";
		}
		
		cell1.innerHTML	= "";
		cell2.innerHTML	= "";
		cell4.innerHTML	= "";		
		cell7.innerHTML = tTitle;
		
		cell8.innerHTML	= "<input name='"+tField+"' type='text' id='"+tField+"' size='8' style='text-align:right;border:none;"+tFieldBold+"' readonly value=''>";
		cell9.innerHTML = "";		
	}

	function calcCentalTax(totAmt, totExAmt, totgstAmt,totc_gstAmt,tots_gstAmt,totigstAmt,totEduCessAmt, totSecEduCess, grTotCentralTaxAmt)
	{		
		totAmt	 = number_format(totAmt,2,'.','');
		totExAmt = number_format(totExAmt,2,'.','');
		totgstAmt = number_format(totgstAmt,2,'.','');
		totc_gstAmt = number_format(totc_gstAmt,2,'.','');
		tots_gstAmt = number_format(tots_gstAmt,2,'.','');
		totigstAmt = number_format(totigstAmt,2,'.','');
		
		
		
		var totCTaxAmt = 0;
		var totgstTaxAmt = 0;
		var totc_gstTaxAmt = 0;
		var tots_gstTaxAmt = 0;
		var totigstTaxAmt = 0;

		var eduCess = $("#hidEduCess").val();
		var secEduCess = $("#hidSecEduCess").val();

		$("#totExDutyAmt").attr("value",totExAmt);
		totCTaxAmt += parseFloat(totExAmt);



		
		//var calcEduCess = number_format(((totExAmt*eduCess)/100),2,'.','');
		$("#totEduCess").attr("value",number_format(totEduCessAmt,2,'.',''));
		totCTaxAmt += parseFloat(totEduCessAmt);

		//var calcSecEduCess = number_format(((totExAmt*secEduCess)/100),2,'.','');
		$("#totSecEduCess").attr("value",number_format(totSecEduCess,2,'.',''));
		totCTaxAmt += parseFloat(totSecEduCess);

		$("#grandTotCExDuty").attr("value",number_format(totCTaxAmt,2,'.',''));
		
		var calcSubTotalAfterExDuty = parseFloat(totAmt)+parseFloat(totCTaxAmt);
		$("#subTotAfterExDuty").attr("value", number_format(calcSubTotalAfterExDuty,2,'.',''));
		
		var taxType  = document.getElementById("taxType").value;
	
		//gst
		if(taxType=='GST'){
		
			$("#totgstAmt").attr("value",totgstAmt);
			totgstTaxAmt += parseFloat(totgstAmt);
		
			if(totgstTaxAmt>0){
				var calcSubTotalAftergst = calcSubTotalAfterExDuty +parseFloat(totgstTaxAmt)
				$("#subTotAfterExDuty").attr("value", number_format(calcSubTotalAftergst,2,'.',''));		
			}
		
			$("#totcgstAmt").attr("value",totc_gstAmt);
			$("#totsgstAmt").attr("value",tots_gstAmt);

		
		
		
		}
	//end gst

// Igst	

	if(taxType=='IGST'){
		$("#totigstAmt").attr("value",totigstAmt);
		totigstTaxAmt += parseFloat(totigstAmt);
	
	
		if(totigstTaxAmt>0){
			var calcSubTotalAftergst = calcSubTotalAfterExDuty +parseFloat(totigstTaxAmt)
			$("#subTotAfterExDuty").attr("value", number_format(calcSubTotalAftergst,2,'.',''));		
		}
				
	}	
		
		
// end Igst	
		
		
		
		return 0;
	}

function displayPopUp(company,unit,salesOrderId)
{
	xajax_displayGatepass(company,unit,salesOrderId);
	$( "#dialog" ).dialog({ width: 370, resizable: true, modal: true   });
}

function displayPopUp_ewaybillno(dialog_billno,dialog_billfileName)
{
	xajax_displayPopUp_ewaybillno(dialog_billno,dialog_billfileName);
	$( "#dialog_billno" ).dialog({ width: 300, resizable: false, modal: true });
}




	