function validateAddUser(form)
{
	var userName		=	form.userName.value;
	var userPassword	=	form.userPassword.value;
	var userRePassword	=	form.userRePassword.value;
	var selRole			=	form.selRole.value;
		
	if(userName=="" )
	{
		alert("Please enter a Email ID.");
		form.userName.focus();
		return false;
	}
	if ( !checkemailUsername(userName) )
	{
		form.userName.focus();
		return false;
	}
	
	if(userPassword=="" )
	{
		alert("Please enter a Password.");
		form.userPassword.focus();
		return false;
	}
	
	if(userRePassword=="" )
	{
		alert("Please Retype your Password.");
		form.userRePassword.focus();
		return false;
	}
	
	if(userPassword!=userRePassword){
		alert("The entered passwords doesn't match");
		return false;
	}
	
	if(selRole=="" )
	{
		alert("Please select a Role.");
		form.selRole.focus();
		return false;
	}


	var hidUnitRowCount= document.getElementById("hidUnitRowCount").value;	
	var scount = 0; var j=0;
		for (i=0; i<hidUnitRowCount; i++)
		{	//alert("hii");
			var Status = document.getElementById("Status_"+i).value;		    
	    	if (Status!='N') 
		    {
			
			var company		=	document.getElementById("company_"+i).value;
			var unit		=	document.getElementById("unit_"+i).value;
			var department		=	document.getElementById("department_"+i).value;
			if(j==0)
			{
				if(company == "0" && unit =="0" && department=="0")
				{
					alert("Are you that the user have permission for all company and unit");
					//return false;
				}
			}
			
			
			if(j>0)
			{
				if(company == "0" && unit =="0" && department=="0")
				{
					alert("cannot use select all.because already selected single data ");
					return false;
				}
			}
			j++;

						
		} else {
			scount++;
		}
	 }
	 
	
	

	 
	
	if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}
}


function addNewRow(tableId,editUserDetail,company,unit,department,mode)
{

	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "Row_"+fldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	
	cell1.id = "srNo_"+fldId;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";

	var companies	= "<select name='company_"+fldId+"' id='company_"+fldId+"' onchange=\"xajax_addNewRow(document.getElementById('company_"+fldId+"').value,document.getElementById('unit_"+fldId+"').value,document.getElementById('department_"+fldId+"').value); xajax_getAllUnits(document.getElementById('company_"+fldId+"').value,'"+fldId+"');\"><option value='0'>--SelectAll--</option>";
	<?php
		if (sizeof($companyRecs)>0) {	
			foreach ($companyRecs as $cr) {
				$companyIds		= $cr[0];
				$companyName	= stripSlash($cr[9]);
	?>	
	if (company=="<?=$companyIds?>")  var sel = "Selected";
		else var sel = "";

	companies += "<option value=\"<?=$companyIds?>\" "+sel+"><?=$companyName?></option>";	
	<?php
			}
		}
		
	?>	
	companies += "</select>";

	var units	= "<select name='unit_"+fldId+"' id='unit_"+fldId+"' onchange=\"xajax_addNewRow(document.getElementById('company_"+fldId+"').value,document.getElementById('unit_"+fldId+"').value,document.getElementById('department_"+fldId+"').value);\"><option value='0'>--SelectAll--</option>";
	<?php
		if (sizeof($unitRecs)>0) {	
			foreach ($unitRecs as $ur) {
				$unitIds		= $ur[0];
				$unitName	= stripSlash($ur[2]);
	?>	
	if (unit=="<?=$unitIds?>")  var sel = "Selected";
		else var sel = "";

	units += "<option value=\"<?=$unitIds?>\" "+sel+"><?=$unitName?></option>";	
	<?php
			}
		}
		
	?>	
	units += "</select>";

	var departments	= "<select name='department_"+fldId+"' id='department_"+fldId+"'  onchange=\"xajax_addNewRow(document.getElementById('company_"+fldId+"').value,document.getElementById('unit_"+fldId+"').value,document.getElementById('department_"+fldId+"').value);\"><option value='0'>--SelectAll--</option>";
	<?php
		if (sizeof($departmentRecs)>0) {	
			foreach($departmentRecs as $dr) {
				$departmentIds		= $dr[0];
				$departmentName	= stripSlash($dr[1]);
	?>	
	if (department=="<?=$departmentIds?>")  var sel = "Selected";
		else var sel = "";

	departments += "<option value=\"<?=$departmentIds?>\" "+sel+"><?=$departmentName?></option>";	
	<?php
			}
		}
		
	?>	
	departments += "</select>";

	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setTestRowItemStatusVal('"+fldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='Status_"+fldId+"' type='hidden' id='Status_"+fldId+"' value=''><input name='IsFromDB_"+fldId+"' type='hidden' id='IsFromDB_"+fldId+"' value='"+ds+"'><input type='hidden' name='editUserDetail_"+fldId+"' id='editUserDetail_"+fldId+"' value='"+editUserDetail+"'>";
	//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
	cell1.innerHTML	= companies;
	cell2.innerHTML	=units;	
	cell3.innerHTML = departments;	
	cell4.innerHTML = imageButton+hiddenFields;	
	fldId		= parseInt(fldId)+1;	
	//document.getElementById("hidTestMethodTableRowCount").value = fldId;	
	document.getElementById("hidUnitRowCount").value = fldId;	

}

function setTestRowItemStatusVal(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("Status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		//alert('hai');
		document.getElementById("Row_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}

function displayRow(company,unit,department)
{
	//alert(company+','+unit+','+department);
	if(company=="0" && unit=="0" && department=="0")
	{
		document.getElementById('rowUser').style.display = 'none';
	}
	else
	{
		document.getElementById('rowUser').style.display = 'block';
	}
	
}


