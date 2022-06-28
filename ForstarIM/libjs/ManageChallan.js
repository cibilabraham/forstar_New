	function validateManageRMChallan(form)
	{
		var functionType	= form.functionType.value;
		var idDateFrom		= form.idDateFrom.value;
		var idDateTo		= form.idDateTo.value;
		//var alphaCodePrefix=form.alpha_code_prefix.value; KD ON 26-9-19
		//var alpha=document.getElementById('alpha_code_prefix').value; KD ON 26-9-19
		//var unitidInv=form.unitidInv.value;
		var startNo		= form.startNo.value;
		var endNo		= form.endNo.value;

		if (functionType=="") {
			alert("Please select a function name.");
			form.functionType.focus();
			return false;
		} 

		// var billingCompany	= "";
		// if (functionType=='RM') {
			var billingCompany = form.billingCompany.value;
			if (billingCompany=="") {
				alert("Please select a billing company.");
				form.billingCompany.focus();
				return false;
			} 
		//}
		
		/*if (alphaCodePrefix=="") {
			alert("Please enter alpha prefix code.");
			form.alpha_code_prefix.focus();
			return false;
		} KD ON 26-9-19 */
		//alert(alpha);
		//if(alpha.length>3)
		/*if(alpha.length>12)
		{
			alert("Maximun character of alpha prefix must be 12");
			//form.alpha_code_prefix.focus();
			return false;
		} KD ON 26-9-19 */
		
		// if (unitidInv=="") {
			// alert("Please select unit.");
			// form.unitidInv.focus();
			// return false;
		//} 
		
		var billingCompany	= "";
		if (functionType=='SO' || functionType=='SPO') {
			var soInvoiceType = form.soInvoiceType.value;
			if (soInvoiceType=="") {
				alert("Please select a Invoice Type.");
				form.soInvoiceType.focus();
				return false;
			}

			if (functionType=='SPO' && soInvoiceType=='TA')
			{
				var exporter = document.getElementById("exporter");
				if (exporter.value=="0" || exporter.value=="")
				{
					alert("Please select a exporter");
					exporter.focus();
					return false;
				}
			}
		}
		
		
		if (idDateFrom=="") {
			alert("Please select starting date.");		
			form.idDateFrom.focus();
			return false;
		} 

		if (idDateTo=="") {
			alert("Please select ending date.");		
			form.idDateTo.focus();
			return false;
		}

		if (startNo=="") {
			alert("Please enter a starting number.");
			form.startNo.focus();
			return false;
		} 

		if (endNo=="") {
			alert("Please enter a ending number.");
			form.endNo.focus();
			return false;
		} 

		if (parseInt(startNo)>parseInt(endNo)) {
			alert("Please make sure starting number should be less than ending number.");
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}


	function validateUpdateRec(form)
	{
		var soDEntryLimitDays		= form.soDEntryLimitDays.value;
		var challanDEntryLimitDays	= form.challanDEntryLimitDays.value;

		if (!isInteger(challanDEntryLimitDays)) {
			alert("Please enter challan Delayed entry limit days.");
			form.challanDEntryLimitDays.focus();
			return false;
		}

		if (!isInteger(soDEntryLimitDays)) {
			alert("Please enter no. of days.");
			form.soDEntryLimitDays.focus();
			return false;
		}	
		if (!confirmSave()) return false;
		else return true;
	}

	/*function validateManageRMChallan(form)
	{
		var challanNumberFrom		= form.challanNumberFrom.value;
		var challanNumberTo		= form.challanNumberTo.value;
		var soDEntryLimitDays		= form.soDEntryLimitDays.value;
		var challanDEntryLimitDays	= form.challanDEntryLimitDays.value;
		
		if (challanNumberFrom>challanNumberTo) {
			alert("Please make sure Challan No. From should be less than Challan No. To.");
			return false;
		}

		if (!isInteger(challanDEntryLimitDays))
		{
			alert("Please enter challan Delayed entry limit days.");
			form.challanDEntryLimitDays.focus();
			return false;
		}

		if (!isInteger(soDEntryLimitDays)) {
			alert("Please enter no. of days.");
			form.soDEntryLimitDays.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}*/

	function showBillingComapny()
	{

		var functionType = document.getElementById("functionType").value;
		// if ((functionType=='RM') || (functionType=='PO') )document.getElementById("billingComapanyRow").style.display='';
		// else {
			// document.getElementById("billingComapanyRow").style.display='none';
			// document.getElementById("billingCompany").value='';
		// }
		
	
		
		if (functionType=='SO' || functionType=='SPO') document.getElementById("salesOrderRow").style.display='';

		else {
			//document.getElementById("salesOrderRow").style.display='none';
			//document.getElementById("soInvoiceType").value='';
		}

		
		document.getElementById("exporterRow").style.display='none';
		document.getElementById("unitRow").style.display='none';

		//document.getElementById("unitRowInv").style.display='none';
		//document.getElementById("alphaCode").style.display='none';
		//if (functionType=='PO')document.getElementById("unitRowInv").style.display='';
		if (functionType=='SPO') document.getElementById("exporterRow").style.display='';
		//if (functionType=='SPO')document.getElementById("unitRow").style.display='';
		//if ((functionType=='MG')|| (functionType=='LF') || (functionType=='LU') || (functionType=='LC') ||(functionType=='LT') || (functionType=='WC') )document.getElementById("alphaCode").style.display='';
		if (functionType=='SL') 
		{	document.getElementById("unitRowInv").style.display='none';
			document.getElementById("selectSlNoType").style.display='';
		}
		else
		{
			document.getElementById("unitRowInv").style.display="";	
			document.getElementById("selectSlNoType").style.display='none';
		}
		
		
	}

	function displayExporter()
	{
		var exporterId = document.getElementById('exporter').value;
		//alert(exporterId);
		xajax_getExporterUnit(exporterId);
		//chkValidInvNum();
	}

		function filterLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}