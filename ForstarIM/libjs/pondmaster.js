function validateAddPondMaster(form)
{
	var pondName	=	form.pondName.value;
	var supplier	=	form.supplier.value;
	var alloteeName	=	form.alloteeName.value;
	var state	=	form.state.value;
	var district	=	form.district.value;
	var taluk	=	form.taluk.value;
	var village	=	form.village.value;
	
	var location	=	form.location.value;
	// var registrationType	=	form.registrationType.value;
	// var registrationNo	=	form.registrationNo.value;
	// var registrationDate	=	form.registrationDate.value;
	// var registrationExpiryDate	=	form.registrationExpiryDate.value;
	var pondSize	=	form.pondSize.value;
	var pondSizeUnit	=	form.pondSizeUnit.value;
	var pondQty	=	form.pondQty.value;
	var returnDays	=	form.returnDays.value;
	
	if (pondName=="") {
		alert("Please enter a Pond Name.");
		form.pondName.focus();
		return false;
	}

	if (supplier=="") {
		alert("Select Supplier Name.");
		form.supplier.focus();
		return false;
	}
	if (alloteeName=="") {
		alert("Enter Allotee Name.");
		form.alloteeName.focus();
		return false;
	}
	if (state=="") {
		alert("Enter state.");
		form.state.focus();
		return false;
	}
	if (district=="") {
		alert("Enter district .");
		form.district.focus();
		return false;
	}
	if (taluk=="") {
		alert("Enter taluk .");
		form.taluk.focus();
		return false;
	}if (village=="") {
		alert("Enter village .");
		form.village.focus();
		return false;
	}
	
	if (location=="") {
		alert("select location.");
		form.location.focus();
		return false;
	}
	// if (registrationType=="") {
		// alert("Select registrationType.");
		// form.registrationType.focus();
		// return false;
	// }
	// if (registrationNo=="") {
		// alert("Enter registration No.");
		// form.registrationNo.focus();
		// return false;
	// }
	if (pondSize=="") {
		alert("Enter pond Size.");
		form.pondSize.focus();
		return false;
	}if (pondSizeUnit=="") {
		alert("Select pond Size Unit.");
		form.pondSizeUnit.focus();
		return false;
	}if (pondQty=="") {
		alert("Enter pond Qty.");
		form.pondQty.focus();
		return false;
	}
	if (returnDays=="") {
		alert("Enter Return days.");
		form.returnDays.focus();
		return false;
	}
	var registrationType		=	document.getElementsByName('registrationType[]');
	var registrationNo			=	document.getElementsByName('registrationNo[]');
	var registrationDate		=	document.getElementsByName('registrationDate[]');
	var registrationExpiryDate	=	document.getElementsByName('registrationExpiryDate[]');
	for(i=0;i<registrationNo.length;i++)
	{
		var visble = $('#mrow_'+i).is(':visible');
		if(visble == true)
		{
			if(registrationType[i].value == '')
			{
				alert("Please choose a registration type");
				return false;
			}
			else if(registrationNo[i].value == '')
			{
				alert("Please enter a registration number");
				return false;
			}
			else if(registrationDate[i].value == '')
			{
				alert("Please enter a registration date");
				return false;
			}
			else if(registrationExpiryDate[i].value == '')
			{
				alert("Please enter a registration expiry date");
				return false;
			}
			else 
			{
				var registrationDateVal = registrationDate[i].value.split('/');
				var registrationExpiryDateVal = registrationExpiryDate[i].value.split('/');
				date1 = new Date(registrationDateVal[2],registrationDateVal[1],registrationDateVal[0]);
				date2 = new Date(registrationExpiryDateVal[2],registrationExpiryDateVal[1],registrationExpiryDateVal[0]);
				// alert(date1.getTime()+'----'+date2.getTime());
				if(date2 <= date1)
				{
					alert("Expiry date must be greater than registration date");
					return false;
				}
				else if(i > 0)
				{
					var checkRegDate = checkRegDateCon(i,registrationType[i].value,date1,date2);
					if(checkRegDate == false)
					{
						alert("The registration date and expiry date conflict for same registration type");
						return false;
					}
				}				
			}
		}
	}
	
	if (!confirmSave()) return false;
	return true;

}
function checkRegDateCon(limitVal,registrationType,date1Va3,date4Val)
{
	var registrationTypeVal     =  document.getElementsByName('registrationType[]');
	var registrationDate		=  document.getElementsByName('registrationDate[]');
	var registrationExpiryDate	=  document.getElementsByName('registrationExpiryDate[]');
	for(i=0;i<limitVal;i++)
	{
		var visble = $('#mrow_'+i).is(':visible');
		registrationTypeChk = registrationTypeVal[i].value;
		if(visble == true && registrationTypeChk == registrationType)
		{
			var registrationDateVal = registrationDate[i].value.split('/');
			var registrationExpiryDateVal = registrationExpiryDate[i].value.split('/');
			date1Val = new Date(registrationDateVal[2],registrationDateVal[1],registrationDateVal[0]);
			date2Val = new Date(registrationExpiryDateVal[2],registrationExpiryDateVal[1],registrationExpiryDateVal[0]);
			if(date1Va3  <  date1Val && date4Val < date1Val)
			{
				retunVal = true;
			}
			else if(date1Va3  >  date2Val && date4Val > date1Va3)
			{
				retunVal = true;
			}
			else
			{
				return false;
			}
		}
	}
}
function addNewPondRegMultipleRow()
{
	fieldvalue		= parseInt(fieldvalue)+1;
	var tbl			= document.getElementById('tblPondRegMultiple');
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "mrow_"+fieldvalue;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	
	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	
	var regType = '<select name="registrationType[]"><option value="">--select--</option>';
	
	<?php
		foreach($registrationTypeRecords as $registration)
		{
			$registrationTypeId		=	$registration[0];
			$registrationTypeName	=	stripSlash($registration[1]);
			$selected = ($registrationType==$registrationTypeId)?"selected":""			
	?>
			regType+= '<option value="<?=$registrationTypeId?>" <?=$selected?>><?=$registrationTypeName?></option>';
	<?php 
		}
	?>
	regType+= '</select>';
	fieldIdInt = parseInt(fieldvalue) + 1;
	var regNo = '<INPUT TYPE="text" NAME="registrationNo[]" size="15">';
	var regDate = '<input type="text" name="registrationDate[]" id="registrationDate'+fieldIdInt+'" autocomplete="off" />';
	var regExDate = '<input type="text" name="registrationExpiryDate[]" id="registrationExpiryDate'+fieldIdInt+'" autocomplete="off" />';
	var delIcon = '<a onclick="setIssuanceItemStatusWeight('+fieldvalue+');" href="javascript:void(0);"><img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item" /></a>';
	// delIcon+= '<SCRIPT LANGUAGE="JavaScript" type="text/javascript">Calendar.setup({inputField:"registrationDate'+fieldvalue+'",eventName:"click",button:"registrationDate'+fieldvalue+'",ifFormat:"%d/%m/%Y",singleClick:true,step:1});';
	cell1.innerHTML	= regType;
	cell2.innerHTML	= regNo;
	cell3.innerHTML	= regDate;
	cell4.innerHTML	= regExDate;
	cell5.innerHTML	= delIcon;
	//document.getElementById("hidTableRowCounts").value = fieldvalue;
	fieldId = 'registrationDate'+fieldIdInt;
	displayCalender(fieldId);
	
	fieldId = 'registrationExpiryDate'+fieldIdInt;
	displayCalender(fieldId);
}
function setIssuanceItemStatusWeight(rowID)
{
	if(rowID == 0)
	{
		alert('You can not delete first row');
	}
	else
	{
		$('#mrow_'+rowID).hide();
		$('#mrow_'+rowID).html('');
	}
}
// Claender Display
	function displayCalender(fieldId)
	{
		// var rowCount = 	document.getElementById("hidMainTableRowCount").value;
		// for (i=0;i<rowCount;i++) {
			Calendar.setup 
			(	
				{
				inputField  : fieldId,         // ID of the input field
				eventName	  : "click",	    // name of event
				button : fieldId, 
				ifFormat    : "%d/%m/%Y",    // the date format
				singleClick : true,
				step : 1
				}
			);
		// }
	}



