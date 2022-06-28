function validateClaim(form)
{
// 	var productSelected = false;
	var salesOrderSelected = false;
	//var selSalesOrder = form.selSalesOrder.value;	
	var lastDate		= form.lastDate.value;
	var lastDateStatus	= form.lastDateStatus.value;
	var editMode		= form.editMode.value;
	var claimNumber		= document.getElementById("claimNumber").value;
	var genClaimId = document.getElementById("genClaimId").value;

	var claimTypeMR		= document.getElementById("claimTypeMR").checked;
	var claimTypeFA		= document.getElementById("claimTypeFA").checked;
		
		if( claimNumber=='' && genClaimId==0 ) {
			alert("Please enter a Claim ID.");
			form.claimNumber.focus();
			return false;
		}	

	/*
	if (selSalesOrder=="") {
		alert("Please select a Sales Order.");
		form.selSalesOrder.focus();
		return false;
	}
	*/
	if (!claimTypeMR && !claimTypeFA) {
		alert("Please select a claim type.");
		document.getElementById("claimTypeMR").focus();
		return false;
	}

	if (lastDate=="") {
		alert("Please select a date of Settling.");
		form.lastDate.focus();
		return false;
	}

	if (lastDateStatus!="" && editMode!="") {
		var dateExtended = form.dateExtended.checked;
		if ((lastDateStatus!=lastDate) && dateExtended=="") {
			alert("Please select Extended option");
			form.dateExtended.focus();
			return false;			
		}		
	}	
	
	if (!claimExtendedDateCheck(form)) {
		return false;	
	}
	// Claim Type MR
	if (claimTypeMR) {
		var tableRowCount	= document.getElementById("hidTableRowCount").value;
		var selDistributor	= document.getElementById("selDistributor").value;
		var startDate		= document.getElementById("startDate").value;
		var endDate		= document.getElementById("endDate").value;
		if (selDistributor=="") {
			alert("Please select a distributor");
			document.getElementById("selDistributor").focus();
			return false;
		}
		if (startDate=="" && editMode=="") {
			alert("Please select from date");
			document.getElementById("startDate").focus();
			return false;
		}
		if (endDate=="" && editMode=="") {
			alert("Please select end date");
			document.getElementById("endDate").focus();
			return false;
		}

		
		for (i=0; i<tableRowCount; i++) {
			var selStatus 	= document.getElementById("status_"+i).value; 
			var selSalesOrderId = document.getElementById("selSalesOrder_"+i).value; 
			if (selStatus!='N') {	
				if (selSalesOrderId=="") {
					alert("Please select a sales order");
					document.getElementById("selSalesOrder_"+i).focus();
					return false;
				}
				
			    if (selSalesOrderId!="") {
				var itemCount	= document.getElementById("hidItemCount_"+i).value;
				//alert(itemCount);
				var productSelected = false;				
				for (j=1; j<=itemCount; j++) {
					var selProduct	=	document.getElementById("selProduct_"+j+"_"+i);
					var unitPrice	=	document.getElementById("unitPrice_"+j+"_"+i);
					var quantity	=	document.getElementById("quantity_"+j+"_"+i);
					var defectQty	=	document.getElementById("defectQty_"+j+"_"+i);
					
					if (selProduct.checked) {	
						if (defectQty.value == "") {
							alert("Please enter a return quantity.");
							defectQty.focus();
							return false;
						}
						if (parseFloat(defectQty.value) > parseFloat(quantity.value)) {
							alert("Please check the return quantity.");
							defectQty.focus();
							return false;
						}
						productSelected = true;
					}
				}
				if (!productSelected) {
					alert("Please select atleast one product against each Sales Order");
					return false;
				}
				salesOrderSelected = true;
			    }		
			}
		}

		if (!salesOrderSelected) {
			alert("Please select atleast one Sales Order");
			return false;
		}
		if (!validateSORepeat()) {
			return false;
		}		
	}

	// Claim Type FA
	if (claimTypeFA) {
		var selDistributor	= document.getElementById("selFADistributor").value;
		var toalClaimAmt	= document.getElementById("toalClaimAmt").value;
		if (selDistributor=="") {
			alert("Please select a distributor");
			document.getElementById("selFADistributor").focus();
			return false;
		}
		if (toalClaimAmt=="") {
			alert("Please enter total amount");
			document.getElementById("toalClaimAmt").focus();
			return false;
		}
	}	
	
	if (!confirmSave()) {
		return false;
	}
	return true;
}

//Add a New Line 
/*function claimNewLine()
{
	document.frmSalesOrder.newline.value = '1';
	document.frmSalesOrder.submit();
}*/

// sales Order extended date check
function claimExtendedDateCheck(form)
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
		alert("Last Date should be greater than or equal to current date");
		document.getElementById("lastDate").focus();
		return false;
	}
	return true;	
}


function enableClaimButton(mode)
{
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = false;
		document.getElementById("cmdAdd1").disabled = false;
	} else if (mode==2) {
		document.getElementById("cmdSaveChange").disabled = false;
		document.getElementById("cmdSaveChange1").disabled = false;
	}
}

function disableClaimButton(mode)
{		
	if (mode==1) {
		document.getElementById("cmdAdd").disabled = true;
		document.getElementById("cmdAdd1").disabled = true;
	} else if (mode==2) {
		document.getElementById("cmdSaveChange").disabled = true;
		document.getElementById("cmdSaveChange1").disabled = true;
	}
}

	// hide When Load
	function HideClaimReturnType()
	{
		document.getElementById("materialReturn").style.display = "none";
		document.getElementById("fixedClaimAmt").style.display = "none";
	}

	function showMaterialReturn()
	{
		document.getElementById( "materialReturn" ).style.display = "block";
		document.getElementById( "fixedClaimAmt" ).style.display = "none";		
	}
	function showFixedClaimAmt()
	{
		document.getElementById( "fixedClaimAmt" ).style.display = "block";
		document.getElementById( "materialReturn" ).style.display = "none";
	}

	// Stock issuance, 
function addNewSalesOrderItemRow(tableId, selSalesOrderId, mode, claimSOEntryId)
{
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "row_"+fieldId;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);	
	var cell3			= row.insertCell(2);

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";cell2.width="70%";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input name='hidSalesOrderId_"+fieldId+"' type='hidden' id='hidSalesOrderId_"+fieldId+"' value='"+selSalesOrderId+"'><input name='hidClaimSOEntryId_"+fieldId+"' type='hidden' id='hidClaimSOEntryId_"+fieldId+"' value='"+claimSOEntryId+"'>";

	var opt		= "<select name='selSalesOrder_"+fieldId+"' Style='display:display;' id='selSalesOrder_"+fieldId+"' tabindex=1  onchange=\"xajax_getSalesOrderItems(document.getElementById('selSalesOrder_"+fieldId+"').value,"+fieldId+", "+mode+");\"  >";
	<?
		if (sizeof($salesOrderRecords)>0) {
			foreach ($salesOrderRecords as $sor) { 
				$salesOrderId	= $sor[0];
				$salesOrderNum	= $sor[1];
				$selected = "";
				if ($selSOId==$salesOrderId) $selected = "Selected";
	?>		
		if( selSalesOrderId == "<?=$salesOrderId?>")  var sel = "Selected";
		else var sel = "";
		opt += "<option value='<?=$salesOrderId?>' "+sel+" <?=$selected?>><?=$salesOrderNum?></option>";
	<?
			}
		}
	?>	
	opt +="</select>";
	
	cell1.innerHTML	= opt;
	cell2.innerHTML	= "<div id='salesOrderedListDiv_"+fieldId+"'></div>";	
	cell3.innerHTML = imageButton + hiddenFields;
	
	fieldId		= parseInt(fieldId)+1;
	document.getElementById("hidTableRowCount").value = fieldId;
}

function setIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none'; 
		calcRtQtyAmt();		
	}
	return false;
}

//Validate repeated
function validateSORepeat()
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
        	var rv = document.getElementById("selSalesOrder_"+j).value;
        	if ( arr.indexOf(rv) != -1 )    {
            		alert("Sales order cannot be duplicate.");
            		document.getElementById("selSalesOrder_"+j).focus();
            		return false;
        	}
        	arr[arri++]=rv;
	}
    }
    return true;
}

	/*
		calcReturn Amt
	*/
	function calcRtQtyAmt()
	{
		var tableRowCount	= document.getElementById("hidTableRowCount").value;		

		var totSOReturnAmt = 0;
		for (i=0; i<tableRowCount; i++) {
			var selStatus 	= document.getElementById("status_"+i).value; 
			var selSalesOrderId = document.getElementById("selSalesOrder_"+i).value; 
			var returnQtyAmt = 0;
			var calcReturnQtyAmt = 0;
			if (selStatus!='N') {								
			    if (selSalesOrderId!="") {
				var itemCount	= document.getElementById("hidItemCount_"+i).value;		
				var productSelected = false;
				
				for (j=1; j<=itemCount; j++) {
					var selProduct	=	document.getElementById("selProduct_"+j+"_"+i);
					var unitPrice	=	document.getElementById("unitPrice_"+j+"_"+i);
					var quantity	=	document.getElementById("quantity_"+j+"_"+i);
					var defectQty	=	document.getElementById("defectQty_"+j+"_"+i);
					
					if (selProduct.checked) {						
						calcReturnQtyAmt = parseFloat(defectQty.value) * parseFloat(unitPrice.value);					
						returnQtyAmt += parseFloat(calcReturnQtyAmt);
						productSelected = true;
					}
				}				
				salesOrderSelected = true;
			    }		
			}			
			totSOReturnAmt += parseFloat(returnQtyAmt);
		}

		if (tableRowCount>0) {
			document.getElementById("grandTotalReturnAmt").value = number_format(totSOReturnAmt,2,'.','');
		}	
	}
