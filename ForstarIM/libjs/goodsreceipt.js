function validateGoodsReceipt(form)
{
	var selPoId		=	form.selPoId.value;
	var selDepartment		=	form.selDepartment.value;
	var challanNo		=	form.challanNo.value;
	var gateEntryNo		=	form.gateEntryNo.value;
	var storeEntry		=	form.storeEntry.value;
	
	
	if( selPoId=="" )
	{
		alert("Please select a Purchae Order Number.");
		form.selPoId.focus();
		return false;
	}
	
	if( selDepartment=="" )
	{
		alert("Please select a Department.");
		form.selDepartment.focus();
		return false;
	}
	
	if( challanNo=="" )
	{
		alert("Please enter a Challan No.");
		form.challanNo.focus();
		return false;
	}
	
	if( gateEntryNo=="" )
	{
		alert("Please enter a Gate Entry Number.");
		form.gateEntryNo.focus();
		return false;
	}
	
	if( storeEntry=="" )
	{
		alert("Please enter a Store Entry Number.");
		form.storeEntry.focus();
		return false;
	}
	
	
	if (selPoId!="") {
		var rejCount = 0;
		var itemCount	=	document.getElementById("hidItemCount").value;

		for (i=1; i<=itemCount; i++) {
			var qtyReceived	=	document.getElementById("qtyReceived_"+i);
			var qtyActual = document.getElementById("quantity_"+i);
			var chkPointRowCount = document.getElementById("chkPointRowCount_"+i).value;
									
			if (qtyActual.value == "") {
				alert("Please enter Actual Quantity.");
				qtyActual.focus();
				return false;
			}
				
			if (qtyReceived.value == "") {
				alert("Please enter Accepted Quantity.");
				qtyReceived.focus();
				return false;
			}

			if (document.getElementById("qtyRejected_"+i).value<0) {
				alert("Please check the Accepted Quantity.");
				qtyReceived.focus();
				return false;
			}

			if (document.getElementById("qtyRejected_"+i).value !="" &&  document.getElementById("qtyRejected_"+i).value !=0 ) {
				rejCount++;
				if (document.getElementById("remarks_"+i).value == "") {
					alert("Please enter Remarks for rejected quantity.");
					document.getElementById("remarks_"+i).focus();	
					return false;
				}
			}

			if (chkPointRowCount>0) {
				for (j=1; j<=chkPointRowCount; j++) {
					var chkPointAnswer = document.getElementById("chkPointAnswer_"+i+"_"+j).checked;		
					var chkPointRemarks =  document.getElementById("chkPointRemarks_"+i+"_"+j);
					if (!chkPointAnswer && chkPointRemarks.value=="") {
						alert("Please enter remarks for Check Point.");
						chkPointRemarks.focus();
						return false;
					}
				}
			}
		}
	
		if (rejCount!=0  && document.getElementById("rejectedEntry").value=="") 
		{
			alert("Please enter rejected material gate pass number.");
			document.getElementById("rejectedEntry").focus();
			return false;
		}
		//return false;
	}

	if(!confirmSave()){
			return false;
	}
	return true;
}


function disableButtons(mode)
{
	if( mode==1 )
	{
		document.getElementById("cmdAdd1").disabled = true;
		document.getElementById("cmdAdd2").disabled = true;
	}
	else if (mode==2)
	{
		document.getElementById("cmdSaveChange1").disabled = true;
		document.getElementById("cmdSaveChange2").disabled = true;
	}
}

function enableButtons(mode)
{
	var ce = document.getElementById("ce").value;
	var ge = document.getElementById("ge").value;
	var se = document.getElementById("se").value;
	var rm = document.getElementById("rm").value;

	if( mode==1 )
	{
		if( ce == "" && ge=="" && se=="" && rm=="" )
		{
			document.getElementById("cmdAdd1").disabled = false;
			document.getElementById("cmdAdd2").disabled = false;
		}
		else
		{
			document.getElementById("cmdAdd1").disabled = true;
			document.getElementById("cmdAdd2").disabled = true;
		}
	
	}
	else if( mode==2 )
	{
		if( ce == "" && ge=="" && se=="" && rm=="" )
		{
			document.getElementById("cmdSaveChange1").disabled = false;
			document.getElementById("cmdSaveChange2").disabled = false;
		}
		else
		{
			document.getElementById("cmdSaveChange1").disabled = true;
			document.getElementById("cmdSaveChange2").disabled = true;
		}
	
	}
}

function calcReject(form){

	var rowCount			=	document.getElementById("hidItemCount").value;
	var total	= 0;
	var regc = 0;

	var pQty			=	"quantity_";
	var qtyReceived		=	"qtyReceived_";
	var qtyRejected		=	"qtyRejected_";	
	
	for (i=1; i<=rowCount; i++)
	  {
	  	var quantity		=	0;
	 	 if(document.getElementById(qtyReceived+i).value!=""){
				 
		document.getElementById(qtyRejected+i).value	 = document.getElementById(pQty+i).value - document.getElementById(qtyReceived+i).value;
	  	}
		else {
			document.getElementById(qtyRejected+i).value =0;
		}
		if( document.getElementById(qtyRejected+i).value != 0 ) regc ++;
	 // quantity= document.getElementById(pTotal+i).value;
		//alert(quantity);
	 // total				=	parseInt(total)+parseInt(quantity);
	}

	if( regc == 0 )
	{
		document.getElementById("rejectedEntry").value = "";
	} else document.getElementById("rejectedEntry").value = document.getElementById("hidRejectedEntry").value;
	
	//if(!isNaN(total)){
		//form.totalQuantity.value = total;	
	//}
}

function getPOId(formObj)
{
showFnLoading(); 
formObj.form.submit();
}

function CalculateExtraqty(j,formObj)
{
	//alert(j);
var orginalQty=parseInt(document.getElementById("orginalqty_"+j).value);
var notoverQty=parseInt(document.getElementById("notover_"+j).value);
var extraQty1=0;
var radio1=document.getElementById("confirmextraQty1_"+j).value;
//alert(radio1);
if (orginalQty>notoverQty)
{
	extraQty1=orginalQty-notoverQty;

}

else if (orginalQty<notoverQty)
{

	
	extraQty1=0;
	notoverQty=orginalQty;

}
//alert(extraQty1);
//alert((formObj."confirmextraQty_"+j+"[0]").checked);

var accepqty=0;


/*if ((formObj."confirmextraQty_"+j[0].checked)==true)
{
alert("y");
accepqty=extraQty1+notoverQty;
document.getElementById("qtyRejected_"+j).value=0;
document.getElementById("qtyReceived_"+j).value=accepqty;

}
else if ((formObj."confirmextraQty_"+j[1].checked)==true)
{
	alert("n"+extraQty1);
	document.getElementById("qtyRejected_"+j).value=extraQty1;
document.getElementById("qtyReceived_"+j).value=notoverQty;
}*/

if (document.getElementById("confirmextraQty1_"+j).checked)
{
	accepqty=extraQty1+notoverQty;
document.getElementById("qtyRejected_"+j).value=0;
document.getElementById("qtyReceived_"+j).value=accepqty;
}
else if (document.getElementById("confirmextraQty2_"+j).checked)
{
	document.getElementById("qtyRejected_"+j).value=extraQty1;
document.getElementById("qtyReceived_"+j).value=notoverQty;
}
//alert(orginalQty);
//alert(notoverQty);

document.getElementById("extraQty_"+j).value=extraQty1;
document.getElementById("quantity_"+j).value=notoverQty;


}

function confirmQty(j)
{	var accepqty;
	var Qty=parseInt(document.getElementById("quantity_"+j).value);
	var extraQty=parseInt(document.getElementById("extraQty_"+j).value);
		
		accepqty=extraQty+Qty;
		if(isNaN(accepqty))
		{
		}
		else
		{
		document.getElementById("qtyReceived_"+j).value=accepqty;
		document.getElementById("qtyRejected_"+j).value=0;
		}
	
}

function rejectQty(j)
{	var accepqty;
	var Qty=parseInt(document.getElementById("quantity_"+j).value);
	var extraQty=parseInt(document.getElementById("extraQty_"+j).value);
		
		accepqty=extraQty+Qty;
		if(isNaN(accepqty))
		{
		}
		else
		{
		document.getElementById("qtyRejected_"+j).value=extraQty;
		document.getElementById("qtyReceived_"+j).value=Qty;
		}

}