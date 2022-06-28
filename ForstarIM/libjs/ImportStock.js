function validateStockImport()
{	
	var file = document.getElementById("uploadFile").value;

	if( file == "" )
	{
		alert("Please select a valid csv file.");
		document.getElementById("uploadFile").focus();
		return false;
	}
	else 
	{
		if( file.toLowerCase().lastIndexOf(".csv")==-1 ) 
		{
			alert("Invalid file type. Please select a valid csv file.");
			return false;
		}
	}

	if( confirm("Do you wish to continue?" ) )
	{
		return true;
	}
	return false;
}

function printUnit(id)
{
	var idexValue = document.getElementById("selUnit_"+id).selectedIndex;
	if (idexValue!=0) {
		var displayText = document.getElementById("selUnit_"+id).options[idexValue].text;
		document.getElementById("unitDisp_"+id).innerHTML = displayText;
		document.getElementById("minOrderUnitTxt_"+id).innerHTML = displayText;

		if (document.getElementById("minOrderUnit_"+id).value!="") {
				//document.getElementById("minOrderQtyRowTxt").innerHTML = "&nbsp;("+ document.getElementById("minOrderUnit").value+"&nbsp;"+ document.getElementById("unit").options[idexValue].text+")";
				
				// Total qty
				var minOrderQtyPerUnit = document.getElementById("minOrderQtyPerUnit_"+id).value;
				var minOrderUnit = document.getElementById("minOrderUnit_"+id).value;
				var calcTotalOrderQty = 0;
				if (minOrderQtyPerUnit!="") {
					calcTotalOrderQty = parseFloat(minOrderUnit*minOrderQtyPerUnit);
					if (!isNaN(calcTotalOrderQty))
						document.getElementById("minOrderQtyPerUnitTxt_"+id).innerHTML = "&nbsp;"+calcTotalOrderQty+"&nbsp;"+displayText;
					}
		}
		
	} else {
		document.getElementById("unitDisp_"+id).innerHTML = "";
		document.getElementById("minOrderUnitTxt_"+id).innerHTML = "";
		document.getElementById("minOrderQtyPerUnitTxt_"+id).innerHTML = "";
	}
	
	disableBulkDimensionOption(id);
}

// Disable Dimension Option
	function disableBulkDimensionOption(id)
	{
		var dimensionLength  = document.getElementById("dimensionLength_"+id).value;
		var dimensionBreadth = document.getElementById("dimensionBreadth_"+id).value;
		var dimensionDiameter = document.getElementById("dimensionDiameter_"+id).value;
		var dimensionRadius = document.getElementById("dimensionRadius_"+id).value;
		if (dimensionLength!="" || dimensionBreadth!="") {
			document.getElementById("dimensionDiameter_"+id).disabled = true;
			document.getElementById("dimensionRadius_"+id).disabled = true;
			document.getElementById("dimensionLength_"+id).disabled = false;
			document.getElementById("dimensionBreadth_"+id).disabled = false;
		} else if (dimensionDiameter!="" || dimensionRadius!="") {
			document.getElementById("dimensionLength_"+id).disabled = true;
			document.getElementById("dimensionBreadth_"+id).disabled = true;
			document.getElementById("dimensionDiameter_"+id).disabled = false;
			document.getElementById("dimensionRadius_"+id).disabled = false;		
		} else {
			document.getElementById("dimensionLength_"+id).disabled = false;
			document.getElementById("dimensionBreadth_"+id).disabled = false;
			document.getElementById("dimensionDiameter_"+id).disabled = false;
			document.getElementById("dimensionRadius_"+id).disabled = false;
		}
	}


function validateBulkStockEntry()
{

	var rc = document.getElementById("RowCount").value;
	var fieldRowCount = document.getElementById("FieldRowCount").value;
	var stockType = document.getElementById("hidStockType").value;


	
	if (!checkAnySelected()) {
		alert("Please select a stock to import.")
		return false;
	}

	var fieldBlank = false;
	for (var i=0;i<rc ;i++ )
	{
		for (var j=0;j<fieldRowCount ;j++ )
		{
			$("*[rel=validate_"+i+"_"+j+"]").each(function()
			{
				$("#"+i+"_"+j).css("background-color","");
				if ($(this).attr("value")=="")
				{
					fieldBlank = true;
					alert("Please select/enter a value");
					$(this).focus();
					$("#"+i+"_"+j).css("background-color","maroon");

					return false;
				}
			});
		}
	}

	if (fieldBlank)
	{
		return false;
	}	
	
	if (!confirm("This will import all selected stocks to the database. Do you wish to continue? ")) 
	{
		return false;
	}

	return true;
}



function checkAnySelected()
{
	var chkCount = 0;
	var rc = document.getElementById("RowCount").value ;
	for (s=0; s<rc;s++ )
	{
		if( document.getElementById("chkImport_"+s).checked == true ) chkCount++;
	}
	if( chkCount == 0 ) return false;
	return true;
}


function validateGenerateCSV()
{	

	var inventoryType = document.getElementById("inventoryType");
	var categoryId = document.getElementById("categoryId");

	if (inventoryType.value=="" && categoryId.value=="")
	{
			alert("Please select a Category or Inventory type.");
			return false;
	}
	
	if( confirm("Do you wish to continue?" ) )
	{
		return true;
	}
	return false;
}

function invTypeChange(type)
{
	if (type==1)
	{
		document.getElementById("inventoryType").value = "";
	}

	if (type==2)
	{
		document.getElementById("categoryId").value = "";
	}
}

function uncheckExistingRec()
{
	var rc = document.getElementById("RowCount").value ;
	for (s=0; s<rc;s++ )
	{
		var recExist = document.getElementById("hdnRecExist_"+s).value;
		if (recExist>0)
		{
			document.getElementById("chkImport_"+s).checked = false;
		}
	}
}