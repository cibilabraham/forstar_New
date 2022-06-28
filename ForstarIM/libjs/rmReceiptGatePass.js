function validateRMReceiptGatePass(form)
{
	
	var processType	=	form.processType.value;
	var lotId	=	form.lotId.value;
	var procurmentGatePassId 	=	form.procurmentGatePassId .value;
	var vehicleNumbers	=	form.vehicleNumbers.value;
	var driver	=	form.driver.value;
	var outSeal	=	form.outSeal.value;
	var verified	=	form.verified.value;
	//var selCompanyName	=	form.selCompanyName.value;
	//var unit	=	form.unit.value;
	var supplierChallanNo	=	form.supplierChallanNo.value;
	var dateOfEntry	=	form.dateOfEntry.value;
	
	
	if (processType=="") {
		alert("Please select processType.");
		form.processType.focus();
		return false;
	}
	if (lotId=="") {
		alert("Please generate lotId.");
		form.lotId.focus();
		return false;
	}
	if (procurmentGatePassId =="") {
		alert("Please select procurmentGatePassId .");
		form.procurmentGatePassId .focus();
		return false;
	}
	if (vehicleNumbers=="") {
		alert("Please select vehicleNumbers.");
		form.vehicleNumbers.focus();
		return false;
	}
	if (driver=="") {
		alert("Please select driver.");
		form.driver.focus();
		return false;
	}
	if (outSeal=="") {
		alert("Please select seal Returned.");
		form.outSeal.focus();
		return false;
	}
	
	if (verified=="") {
		alert("Please select verified.");
		form.verified.focus();
		return false;
	}
	// if (selCompanyName=="") {
		// alert("Please select Company Name.");
		// form.selCompanyName.focus();
		// return false;
	// }
	// if (unit=="") {
		// alert("Please select unit.");
		// form.unit.focus();
		// return false;
	// }
	if (supplierChallanNo=="") {
		alert("Please enter supplierChallanNo.");
		form.supplierChallanNo.focus();
		return false;
	}
	if (dateOfEntry=="") {
		alert("Please select dateOfEntry.");
		form.dateOfEntry.focus();
		return false;
	}

	
	
	if (!confirmSave()) return false;
	return true;

}
function displayDiv()
{
	jQuery('#supplier_display').show();
	jQuery('#equipment_display').show();
	jQuery('#chemical_display').show();
	jQuery('#blocked_seal_details').show();
}
function calculateEquipDiff(returnedQnty,rowid)
{
	//alert(returnedQnty+'---'+rowid);
	if(isNaN(returnedQnty))
	{
		alert('Please enter the valid quantity');
	}
	else if(returnedQnty!="")
	{
		var issuedQuantity=document.getElementById('equipmentIssuedQuantity_'+rowid).value;
		if(parseInt(returnedQnty) > parseInt(issuedQuantity))
		{
			var result = parseInt(issuedQuantity) - parseInt(returnedQnty);
			document.getElementById('equipmentDifferenceQuantity_'+rowid).value = result;
			alert('Required quantity must be less than Issued quantity');
		}
		else
		{
			var result = parseInt(issuedQuantity) - parseInt(returnedQnty);
			document.getElementById('equipmentDifferenceQuantity_'+rowid).value = result;
		}
	}
}

function calculateChemicalDiff(returnedQnty,rowid)
{
	//alert(returnedQnty+'---'+rowid);
	if(isNaN(returnedQnty))
	{
		alert('Please enter the valid quantity');
	}
	else
	{
		var issuedQuantity=document.getElementById('chemicalIssuedQuantity_'+rowid).value;
		if(parseInt(returnedQnty) > parseInt(issuedQuantity))
		{	
			var result = parseInt(issuedQuantity) - parseInt(returnedQnty);
			document.getElementById('chemicalDifferenceQuantity_'+rowid).value = result;
			alert('Required quantity must be less than Issued quantity');
		}
		else
		{
			var result = parseInt(issuedQuantity) - parseInt(returnedQnty);
			document.getElementById('chemicalDifferenceQuantity_'+rowid).value = result;
		}
	}
}

function getField()
{
	//alert("hii"+material);
	var material=document.getElementById('material').value;
    $('.rawtype').hide();
    $('#'+material).show();
	var supplier=document.getElementById('supplier').value;
	var addMode=document.getElementById('addMode').value;
	if (supplier!="" && addMode=='1')
	{
		xajax_getCenter(supplier,material,"");
	}
}


function chkChallanStatMul(i)
{
			//alert("hii");
	var Supplier=$("#supplier_id_"+i).val();
	var ChallanNo=$("#challan_no_"+i).val();
	if(Supplier!="" && ChallanNo!="")
	{
		var datas={"SubSupplier":Supplier,"SupplierChallanNo":ChallanNo};
		$.ajax
		({
			type:"POST",
			//dataType:'json',// if specifies datatype then sucess data will be in the json array format $response_array['status'] = 'success'; and also header('Content-type: application/json');
			url:"RMReceiptGatePass.php?action=displayMsg",
			data:{myData:JSON.stringify(datas)},
			success:function(data)
			{
				$('#challan_stat_'+i).html(data);
				if(data=="")
				{
					$("#cmdAdd").attr('disabled', false);
				}
				else
				{
					$("#cmdAdd").attr('disabled', true);
				}
			}
		});
	}
}


function chkChallanStat()
{
			//alert("hii");
	var Supplier=$("#supplier").val();
	var ChallanNo=$("#supplier_Challan_No").val();
	if(Supplier!="" &&  ChallanNo!="")
	{
		var datas={"SubSupplier":Supplier,"SupplierChallanNo":ChallanNo};
	
		$.ajax
		({
			type:"POST",
			//dataType:'json',// if specifies datatype then sucess data will be in the json array format $response_array['status'] = 'success'; and also header('Content-type: application/json');
			url:"RMReceiptGatePass.php?action=displayMsg",
			data:{myData:JSON.stringify(datas)},
			success:function(data)
			{
				$('#challan_stat').html(data);
				if(data=="")
				{
					$("#cmdAdd").attr('disabled', false);
				}
				else
				{
					$("#cmdAdd").attr('disabled', true);
				}
			}
		});
	}
}

function checkReceiptGatePass(gatePass)
{
	if(gatePass!="")
	{
		var n = gatePass.charAt(0);
		var str=/^[A-z]+$/;
		if(n.match(str))
		{
			var upperCase   = gatePass.toUpperCase(); 
			var newGatePass = upperCase.replace(/([~!@#$%^&*()_+=`{}\[\]\|\\:;'<>,.\/? ])+/g, '-').replace(/^(-)+|(-)+$/g,'');
			document.getElementById('receiptGatePass').value = newGatePass;
			xajax_checkReceiptGatePass(newGatePass);
			enableButton();
			
		}
		else
		{
			alert("Invalid format for Receipt gate pass");
			disableButton();
		}
	}
}


function enableButton()
{
	document.getElementById("cmdAdd1").disabled = false;
}

function disableButton()
{
	document.getElementById("cmdAdd1").disabled = true;
}