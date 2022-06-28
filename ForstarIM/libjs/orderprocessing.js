function validateOrderProcessing(form)
{
	var isStockAvailable 	= false;
	var selPOId			=	form.selPOId.value;
	var labelling		=	form.labelling.value;
	var paymentStatus		=	form.paymentStatus.value;
	var invoiceNo			=	form.invoiceNo.value;
		
	var shipmentDate	=	form.shipmentDate.value;
	var selStatus		=	form.selStatus.value;
	//var noStock			=	form.noStock.value;
	var hidRowRMCount	=	form.hidRowRMCount.value;
	var stockAvailable  = "noStock_"; 
	var isComplete		=	form.isComplete.checked;
	
	if( selPOId=="" )
	{
		alert("Please select a PO ID.");
		form.selPOId.focus();
		return false;
	}
	
	if( labelling=="" )
	{
		alert("Please select Labelling.");
		form.labelling.focus();
		return false;
	}
	
	if( paymentStatus=="" )
	{
		alert("Please Enter Payment Status.");
		form.paymentStatus.focus();
		return false;
	}
	
	if( invoiceNo=="" )
	{
		alert("Please Enter an Export Invoice No.");
		form.invoiceNo.focus();
		return false;
	}
		
	
	if( shipmentDate=="" )
	{
		alert("Please select a Shipment Date.");
		form.shipmentDate.focus();
		return false;
	}
	
	
	if( selStatus=="" )
	{
		alert("Please select a status.");
		form.selStatus.focus();
		return false;
	}
	
	if( isComplete!="" )
	{
	
	for(var i=1; i<=hidRowRMCount; i++)
		{
			
			if(document.getElementById(stockAvailable+i).value!="") 
			{
				isStockAvailable = true;
			}				
		}
	
		if(isStockAvailable==true)
		{
			alert("Please check all RM stock available");
			return false;
		}
	}
		
/*
	if( isComplete=="" )
	{
		alert("Please select Confirm.");
		form.isComplete.focus();
		return false;
	}
	*/
	
	if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}
}

function calculateOrder(){
	var totalValueUSD = 0;
	var oneUSDToINR		=	0;
	var totalValueINR	= 0;
	
	var filledWt		=	document.getElementById("filledWt").value;
	var numPacks		=	document.getElementById("numPacks").value;

	var numMC		=	document.getElementById("numMC").value;
	var pricePerKg	=	document.getElementById("pricePerKg").value;
	
	oneUSDToINR 	=	document.getElementById("oneUSDToINR").value;
	
	totalValueUSD	=		filledWt * 	numPacks * numMC * pricePerKg;
	
	totalValueINR	=	totalValueUSD * oneUSDToINR;
	//alert(totalValueUSD);
	
	if(!isNaN(totalValueUSD)){
		document.getElementById("valueInUSD").value = totalValueUSD;
		document.getElementById("valueInINR").value	= formatNumber(Math.abs(totalValueINR),2,'','.','','','','','');
	}
	
}