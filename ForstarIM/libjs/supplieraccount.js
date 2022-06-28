	function validateSupplierAccount(form)
	{	
		var supplyFrom	= form.supplyFrom.value;
		var supplyTill	= form.supplyTill.value;
		var recExist	= document.getElementById("recExist").value;
		
		var dws = document.getElementById("paymentMode2").checked; // Decl Wt in Supplier challan no
		

		if (supplyFrom=="") {
			alert("Please select Supply From Date");
			form.supplyFrom.focus();
			return false;
		}
		
		if (supplyTill=="") {
			alert("Please select Supply Till Date");
			form.supplyTill.focus();
			return false;
		}
	
		if (!validateSupplierAccountSearch(form)) {
			return false;
		}
		
		if (recExist>0) {
			var rowCount = 	document.getElementById("hidRowCount").value;
			if (rowCount>0) {
				for (i=1;i<=rowCount;i++) {
					settled = "";
					if (dws) {
						var settled	  = document.getElementById("settled_"+i).checked;
					} else {
						var settled	  = document.getElementById("paid_"+i).checked;
					}
					var suppSetldDate = document.getElementById("suppSetldDate_"+i).value;
					
					if (settled && suppSetldDate=="") {
						alert("Please select a settled Date");
						document.getElementById("suppSetldDate_"+i).focus();
						return false;
					}
					if (suppSetldDate!="" && !isDate(suppSetldDate)) {
						document.getElementById("suppSetldDate_"+i).focus();
						return false;
					}
					if (settled && findDaysDiff(suppSetldDate)>0) {
						alert("Settled Date should be less than or equal to current date");
						document.getElementById("suppSetldDate_"+i).focus();
						return false;	
					}
					
				}
			}
		}		

		if (confirmSave()) return true;
		else return false;
	}

	function actualAmount(form)
	{
		var rowCount			=	document.getElementById("hidRowCount").value;
		var total	= 0;
		var weight	=	"weight_";
		var rate	=	"rate_";
		var actualRate	=	"totalRate_";
		var payableWt	=	"payableWt_";
		var declWt		=	"declWt_";
		var payableRate	=	"payableRate_";
		var declRate	=	"declRate_";
		
		for (i=1; i<=rowCount; i++)
		{
			
			if(document.getElementById(weight+i).value=="")
			{
				document.getElementById(weight+i).value	=	0;
			}
			if(document.getElementById(rate+i).value=="")
			{
				document.getElementById(rate+i).value	=	0;
			}
			
			if(parseFloat(document.getElementById(weight+i).value) < (document.getElementById(payableWt+i).value || document.getElementById(declWt+i).value)){
				document.getElementById(weight+i).className='err2';  
			} else {
				document.getElementById(weight+i).className='input';
			}
			
			if(parseFloat(document.getElementById(rate+i).value) > (document.getElementById(payableRate+i).value || document.getElementById(declRate+i).value) || (document.getElementById(payableRate+i).value || document.getElementById(declRate+i).value)==""){
				
				document.getElementById(rate+i).className='err2';  
				
			} else{
				document.getElementById(rate+i).className='input';
				}
			
			if(document.getElementById(weight+i).value!="" && document.getElementById(rate+i).value!="")				
			{			 
			document.getElementById(actualRate+i).value = document.getElementById(weight+i).value * document.getElementById(rate+i).value ;
			}
		
		total		=	parseFloat(total)+parseFloat(document.getElementById(actualRate+i).value);
		}
		
		if(!isNaN(total)){
			form.netPayable.value = total;	
			form.grandTotalRate.value=total;
		}
	}

/*
	function validateSupplierAccountSearch(form)
	{
		var landingCenter	=	form.landingCenter.value;
		var supplier		=	form.supplier.value;
		var selSettlement	=	form.selSettlement.value;			
		form.selChallan.value="";		
		if( supplier=="") {
			alert("Please select a Supplier");
			form.supplier.focus();
			return false;
		}
			
		if( landingCenter=="")	{
			alert("Please select a Landing Center");
			form.landingCenter.focus();
			return false;
		}
		
		if( selSettlement=="")	{
			alert("Please select a Settlement Date");
			form.selSettlement.focus();
			return false;
		}
	}
*/

	function nextBox(e,form,name)
	{
		var ecode = getKeyCode(e);
		var sName = name.split("_");
		dArrowName = sName[0]+"_"+(sName[1]-2);
		
		if ((ecode==13) || (ecode == 9) || (ecode==40)){
			var nextControl = eval(form+"."+name);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}
		if ((ecode==38)){
			var nextControl = eval(form+"."+dArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}		
	}

	function calcAmount(form)
	{
		var rowCount			=	document.getElementById("hidRowCount").value;
		var total	= 0;
		var totalAmount = 0;
		var declWt	=	"declWt_";
		var rate	=	"rate_";
		var amount	=	"amount_";
	
		for (i=1; i<=rowCount; i++)
		{
			total = parseFloat(document.getElementById(declWt+i).value) * parseFloat(document.getElementById(rate+i).value);
			if(!isNaN(total)){
			document.getElementById(amount+i).value = formatNumber(Math.abs(total),2,'','.','','','','','');
			} else {
				document.getElementById(amount+i).value = 0;
			}
			totalAmount = parseFloat(totalAmount) + total;
		}
		
		if(!isNaN(total)){
			document.getElementById("totalAmount").value = formatNumber(Math.abs(totalAmount),2,'','.','','','','','');
		}
	}


	function validateSupplierAccountSearch(form)
	{
		var supplyFrom			=	form.supplyFrom.value;
		var supplyTill			=	form.supplyTill.value;
		var declaredWeighNo		=	form.paymentMode[0].checked;
		var declaredSuppNo		=	form.paymentMode[1].checked;
		var effectiveWeighNo		=	form.paymentMode[2].checked;
		var summaryView			=	form.viewType[0].checked;
		var detailedView		=	form.viewType[1].checked;
		var supplier			=	form.supplier.value;
		
		if(supplyFrom==""){
			
				alert("Please select Supply From Date");
				form.supplyFrom.focus();
				return false;
		}
		
		if(supplyTill==""){
			
				alert("Please select Supply Till Date");
				form.supplyTill.focus();
				return false;
		}
		if(declaredWeighNo=="" && declaredSuppNo=="" && effectiveWeighNo==""){
			
				alert("Please select atleast one payment mode option");
				//form.declaredWeighNo.focus();
				return false;
		}
		
		if(summaryView=="" && detailedView==""){
			
				alert("Please select atleast one View Type");
				//form.declaredWeighNo.focus();
				return false;
		}
		
		if(declaredSuppNo!="" && supplier==""){
				alert("Please select a supplier");
				form.supplier.focus();
				return false;
		}
		
		return true;
	}

	function nextDeclWtBox(e,form,name)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);
		var sName = name.split("_");
		dArrowName = sName[0]+"_"+(sName[1]-2);
		
		//Down Arrow
		if ((ecode==40)) {
			var nextControl = eval(form+"."+name);
			//alert(name);
			if (nextControl) { nextControl.focus(); }
			return false;
		}
	
		//Up Arrow
		if ((ecode==38)) {
			var nextControl = eval(form+"."+dArrowName);
			//alert(dArrowName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
		}		
	}
