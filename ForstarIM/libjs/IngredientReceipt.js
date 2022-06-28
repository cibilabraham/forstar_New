function validateIngredientReceipt(form)
{
	var selPoId		=	form.selPoId.value;
	var selDepartment	=	form.selDepartment.value;
	var gateEntryNo		=	form.gateEntryNo.value;
	var storeEntry		=	form.storeEntry.value;
	
	
	if (selPoId=="") {
		alert("Please select a Purchae Order Number.");
		form.selPoId.focus();
		return false;
	}
	
	if (selDepartment=="") {
		alert("Please select a Department.");
		form.selDepartment.focus();
		return false;
	}
	
	if (gateEntryNo=="") {
		alert("Please enter a Gate Entry Number.");
		form.gateEntryNo.focus();
		return false;
	}
	
	if (storeEntry=="") {
		alert("Please enter a Store Entry Number.");
		form.storeEntry.focus();
		return false;
	}	
	
	if (selPoId!="") {
		var rejCount = 0;
		var itemCount	=	document.getElementById("hidItemCount").value;
		for (i=1; i<=itemCount; i++) {
			var qtyReceived	= document.getElementById("qtyReceived_"+i);
			var qtyActual   = document.getElementById("quantity_"+i);

			if (qtyActual.value=="") {
				alert("Please enter Actual Quantity.");
				qtyActual.focus();
				return false;
			}

			if (qtyReceived.value== "") {
				alert("Please enter Received Quantity.");
				qtyReceived.focus();
				return false;
			}

			if (document.getElementById("qtyRejected_"+i).value<0) {
				alert("Please check the Accepted Quantity.");
				qtyReceived.focus();
				return false;
			}

			if( document.getElementById("qtyRejected_"+i).value !="" &&  document.getElementById("qtyRejected_"+i).value !=0 ) {
				rejCount++;
				if (document.getElementById("remarks_"+i).value == "") {
					alert("Please enter Remarks for rejected quantity.")
					document.getElementById("remarks_"+i).focus();	
					return false;
				}
			}
		}
		if (rejCount!=0  && document.getElementById("rejectedEntry").value =="")  {
			alert("Please enter rejected material gate pass number.");
			document.getElementById("rejectedEntry").focus();
			return false;
		}
			 //return false;
	}
	if (!confirmSave()) {
		return false;
	}
	return true;
}

// Calc Ing Reject Qty
function calcIngredientReject(form)
{
	var rowCount	=	document.getElementById("hidItemCount").value;
	var total	= 0;
	
	var pQty		=	"quantity_";
	var qtyReceived		=	"qtyReceived_";
	var qtyRejected		=	"qtyRejected_";	
	
	for (i=1; i<=rowCount; i++) {
	  	var quantity	=	0;
	 	if(document.getElementById(qtyReceived+i).value!=""){
			document.getElementById(qtyRejected+i).value	 = document.getElementById(pQty+i).value - document.getElementById(qtyReceived+i).value;
	  	} else {
			document.getElementById(qtyRejected+i).value =0;
		}
	}	

	// Calculate Amt
	calcIngReceivedAmt();
}

// Calculate Ing Received Amt
function calcIngReceivedAmt()
{
	var rowCount	= document.getElementById("hidItemCount").value;
	var total	= 0;	
	var pUnit	= "unitPrice_";
	var pQty	= "qtyReceived_";
	var newUnitPrice	= "newUnitPrice_";
	var pTotal	= "totalAmt_";	
	
	for (i=1; i<=rowCount; i++) {
		var quantity	= 0;		
	 	if (document.getElementById(pQty+i).value!="") {
			if(document.getElementById(newUnitPrice+i).value=="")
			{
				document.getElementById(pTotal+i).value = document.getElementById(pUnit+i).value * document.getElementById(pQty+i).value;
			}
			else
			{
				document.getElementById(pTotal+i).value = document.getElementById(newUnitPrice+i).value * document.getElementById(pQty+i).value;
			}
	  	} else {
			document.getElementById(pTotal+i).value =0;
		}
		quantity= document.getElementById(pTotal+i).value;
		total	= parseFloat(total)+parseFloat(quantity);	  
	}
	
	if (!isNaN(total)) {
		document.getElementById("grandTotalAmt").value = number_format(total,2,'.','');	
	}
}

function getPONumber(formObj)
{
	showFnLoading(); 
	formObj.form.submit();
}