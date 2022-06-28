function validateWeightmentDatasheet(form)
{
//alert("hii");
var procurementAvailable	= form.procurementAvailable.value;
var procure_aval = document.getElementById('checkbox1');
if(procure_aval.checked == true)
{
	var procurementGatePass	= form.procurementGatePass.value;
	var purchase_supervisor	= form.purchase_supervisor.value;
	var gate_pass_details	= form.gate_pass_details.value;
	var farmer_at_harvest	= form.farmer_at_harvest.value;
	
	if (procurementGatePass=="") {
		alert("GatePass could not be null.");
		form.procurementGatePass.focus();
		return false;
	}
	
	if (purchase_supervisor=="") {
		alert("Please select Purchase supervisor.");
		form.purchase_supervisor.focus();
		return false;
	}
	
	if (gate_pass_details=="") {
		alert("Please select Gate pass details.");
		form.gate_pass_details.focus();
		return false;
	}
	
	if (farmer_at_harvest=="") {
		alert("Please select farmer at harvest.");
		form.farmer_at_harvest.focus();
		return false;
	}
	
	var supplierCountVal	=	document.getElementById("hidTableRowCountsVal").value;

		var scount = 0;
		for (i=0; i<supplierCountVal; i++)
		{
		   var status = document.getElementById("wstatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var supplierNamepro		=	document.getElementById("supplierNamepro_"+i);
			var pondNamepro	=	document.getElementById("pondNamepro_"+i);
			var product_speciespro		=	document.getElementById("product_speciespro_"+i);
			var count_codepro	 	= 	document.getElementById("count_codepro_"+i);
			var weightpro		=	document.getElementById("weightpro_"+i);
			var soft_precentpro	=	document.getElementById("soft_precentpro_"+i);
			var soft_weightpro		=	document.getElementById("soft_weightpro_"+i);
			var packageTypepro	 	= 	document.getElementById("packageTypepro_"+i);
			var pkg_nospro	 	= 	document.getElementById("pkg_nospro_"+i);
			if( supplierNamepro.value == "" )
			{
				alert("Please Select a Supplier Name.");
				supplierNamepro.focus();
				return false;
			}	
			
			if( pondNamepro.value == "" )
			{
				alert("Please enter a Pond Name.");
				pondNamepro.focus();
				return false;
			}	
			if( product_speciespro.value == "" )
			{
				alert("Please enter a Species Name.");
				product_speciespro.focus();
				return false;
			}	
			
			if( count_codepro.value == "" )
			{
				alert("Please enter a Count code.");
				count_codepro.focus();
				return false;
			}	
			if( weightpro.value == "" )
			{
				alert("Please enter a Weight.");
				weightpro.focus();
				return false;
			}	
			
			if( soft_precentpro.value == "" )
			{
				alert("Please enter a Soft precent code.");
				soft_precentpro.focus();
				return false;
			}	
			
			if( soft_weightpro.value == "" )
			{
				alert("Please enter a Soft Weight.");
				soft_weightpro.focus();
				return false;
			}	
			if( packageTypepro.value == "" )
			{
				alert("Please enter a Package Type.");
				packageTypepro.focus();
				return false;
			}	
			if( pkg_nospro.value == "" )
			{
				alert("Please enter a Package No.");
				pkg_nospro.focus();
				return false;
			}	
			
			
			
			
			
		} else {
			scount++;
		}
	 }
	 
	 
	var itemCount	=	document.getElementById("hidTableRowCount").value;

		var count = 0;
		for (i=0; i<itemCount; i++)
		{
		   var status = document.getElementById("status_"+i).value;		    
	    	   if (status!='N') 
		    {
			var equipmentName		=	document.getElementById("equipmentName_"+i);
			
			var quantity		=	document.getElementById("equipmentIssued_"+i);
			var equipmentReturned	=	document.getElementById("equipmentReturned_"+i);
			var balanceQty	 	= 	document.getElementById("balanceQty_"+i);
			
			
			if( equipmentName.value == "" )
			{
				alert("Please Select a Equipment Name.");
				equipmentName.focus();
				return false;
			}	
			
			if( quantity.value == "" )
			{
				alert("Please enter a equipmentIssued_.");
				quantity.focus();
				return false;
			}	
			if( equipmentReturned.value == "" )
			{
				alert("Please enter a equipmentReturned.");
				equipmentReturned.focus();
				return false;
			}	
			
			if (equipmentName.value!="" && quantity.value == 0 )
			{
				alert("Sorry!! Selected Stock Item is not Present.");
				equipmentName.focus();
				return false;
			}	
			if (balanceQty.value<0) {
				alert("Required Stock quantity is not available.");
				quantity.focus();
				return false;			
			}
		} else {
			count++;
		}
	 }
	 
	
	 
	 var chemicalCount	=	document.getElementById("hidChemicalRowCount").value;

		var ccount = 0;
		for (i=0; i<chemicalCount; i++)
		{
		   var status = document.getElementById("bstatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var chemicalName		=	document.getElementById("chemicalName_"+i);
			var chemicalIssued		=	document.getElementById("chemicalIssued_"+i);
			var chemicalUsed	=	document.getElementById("chemicalUsed_"+i);
			var chemicalReturned	=	document.getElementById("chemicalReturned_"+i);
			var differenceQty	=	document.getElementById("differenceQty_"+i);
			
			
			if( chemicalName.value == "" )
			{
				alert("Please Select a Chemical Name.");
				chemicalName.focus();
				return false;
			}	
			if( chemicalIssued.value == "" )
			{
				alert("Please enter a Chemical Issued.");
				chemicalIssued.focus();
				return false;
			}	
			if( chemicalUsed.value == "" )
			{
				alert("Please enter a chemical Used.");
				chemicalUsed.focus();
				return false;
			}	
			if( chemicalReturned.value == "" )
			{
				alert("Please enter a chemical Returned.");
				chemicalReturned.focus();
				return false;
			}	
			if (differenceQty.value<0) {
				alert("Required Stock quantity is not available.");
				quantity.focus();
				return false;			
			}
			
			
			
			
		} else {
			ccount++;
		}
	 }
	 
	 
	 
	
	
	
	
	
	//alert(procurmentGatePass);
}
else
{	var supplyArea	= form.supplyArea.value;
	var selRMSupplierGroup	= form.selRMSupplierGroup.value;
	if (supplyArea=="") {
		alert("Please select supply Area.");
		form.supplyArea.focus();
		return false;
	}
	if (selRMSupplierGroup=="") {
		alert("Please select RM Supplier Group.");
		form.selRMSupplierGroup.focus();
		return false;
	}
	
	var supplierCount	=	document.getElementById("hidTableRowCounts").value;

		var scount = 0;
		for (i=0; i<supplierCount; i++)
		{
		   var status = document.getElementById("mstatus_"+i).value;		    
	    	   if (status!='N') 
		    {
			var supplierName		=	document.getElementById("supplierName_"+i);
			var pondName	=	document.getElementById("pondName_"+i);
			var product_species		=	document.getElementById("product_species_"+i);
			var count_code	 	= 	document.getElementById("count_code_"+i);
			var weight		=	document.getElementById("weight_"+i);
			var soft_precent	=	document.getElementById("soft_precent_"+i);
			var soft_weight		=	document.getElementById("soft_weight_"+i);
			
			if( supplierName.value == "" )
			{
				alert("Please Select a Supplier Name.");
				supplierName.focus();
				return false;
			}	
			
			if( pondName.value == "" )
			{
				alert("Please enter a Pond Name.");
				pondName.focus();
				return false;
			}	
			if( product_species.value == "" )
			{
				alert("Please enter a Species Name.");
				product_species.focus();
				return false;
			}	
			
			if( count_code.value == "" )
			{
				alert("Please enter a Count code.");
				count_code.focus();
				return false;
			}	
			if( weight.value == "" )
			{
				alert("Please enter a Weight.");
				weight.focus();
				return false;
			}	
			
			if( soft_precent.value == "" )
			{
				alert("Please enter a Soft precent code.");
				soft_precent.focus();
				return false;
			}	
			
			if( soft_weight.value == "" )
			{
				alert("Please enter a Soft Weight.");
				soft_weight.focus();
				return false;
			}	
			
			
			
			
			
			
		} else {
			scount++;
		}
	 }
	
	
}
//alert("hii333");
//alert(procurementAvailable);
 // if(checkboxVal=="1")
 // {
  // var procurmentGatePass	= form.procurementGatePass.value;
  // alert(procurmentGatePass);
 // }
 // else
 // {
 
 // }
//alert("hii");
if(!validateRepeatIssuance()){
	
		return false;
	}
	 
	if (!confirmSave()) return false;
	return true;
}
function addNewWeighmentMultiple(tableId, editweightmentId,supplierName1, pondName1, species1, count_code,weight,soft_precent,soft_weight,value1,mode)
{
//alert(supplierName1);
	var tbl			= document.getElementById(tableId);
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
	var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	
	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	
	
	var ds = "N";	
	var imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatusWeight('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='mstatus_"+fieldvalue+"' type='hidden' id='mstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='mrmId_"+fieldvalue+"' id='mrmId_"+fieldvalue+"' value='"+editweightmentId+"'>";
	var supplierName= "<select name='supplierName_"+fieldvalue+"' Style='display:display;' id='supplierName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_weightmentSupplierAddress(document.getElementById('supplierName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''> -- Select --</option>";
	<?php 
										foreach($supplierRecs as $sr)
										{
										//alert($sr[0]);
						$supplierNameId		=	$sr[1];
						$supplierNameValue	=	stripSlash($sr[2]);
						?>
						//alert(supplierName1);
						if(supplierName1=="<?=$supplierNameId?>") var sel="Selected";
					  else var sel = "";
					  
                       supplierName+="<option value=\"<?=$supplierNameId?>\" "+sel+"><?=$supplierNameValue?></option>";
                                                    <? }
								?>	
					supplierName += "</select>";
	
	var pondName= "<select name='pondName_"+fieldvalue+"' Style='display:display;' id='pondName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_weightmentSpecies(document.getElementById('pondName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''> -- Select --</option>";
	<?php 	foreach($pondRecs as $pondval)
			{
										foreach($pondval as $pnd)
										{
										//alert($sr[0]);
						$pondNameId		=	$pnd[1];
						$pondNameValue	=	stripSlash($pnd[2]);
						?>
						if(pondName1=="<?=$pondNameId?>") var sel="Selected";
					  else var sel = "";
					  
                       pondName+="<option value=\"<?=$pondNameId?>\" "+sel+"><?=$pondNameValue?></option>";
                                                    <? }
													}
								?>	
					pondName += "</select>";

	
	var cell1Val = '<select  id="product_species_'+fieldvalue+'" name="product_species_'+fieldvalue+'"><option value=""> -- Select --</option>';
	<?php 	foreach($speciesRecs as $speciesval)
			{
										foreach($speciesval as $species)
										{
										//alert($sr[0]);
						$speciesId		=	$species[1];
						$speciesValue	=	stripSlash($species[2]);
						?>
						if(species1=="<?=$speciesId?>") var sel="Selected";
					  else var sel = "";
					  
                       cell1Val+="<option value=\"<?=$speciesId?>\" "+sel+"><?=$speciesValue?></option>";
                                                    <? }
													}
								?>	
					cell1Val += '</select>';
	//cell1Val+='</select>';

	cell1.innerHTML	=supplierName ;
	cell2.innerHTML	=pondName;
																	
	cell3.innerHTML	= cell1Val;
	//cell4.innerHTML	= '<input type="text" name="grade_count'+fieldId+'" id="grade_count'+fieldId+'" size="10" required />';
	cell4.innerHTML	= '<input type="text" name="count_code_'+fieldvalue+'" id="count_code_'+fieldvalue+'" size="10" value="'+count_code+'" />';
	cell5.innerHTML	= '<input type="text" name="weight_'+fieldvalue+'" id="weight_'+fieldvalue+'" size="10" value="'+weight+'" onkeyup="checkValue();" />';
	cell6.innerHTML	= '<input type="text" name="soft_precent_'+fieldvalue+'" id="soft_precent_'+fieldvalue+'" value="'+soft_precent+'" size="10" onkeyup="checksSoftValue();"  />';
	cell7.innerHTML	= '<input type="text" name="soft_weight_'+fieldvalue+'" id="soft_weight_'+fieldvalue+'" value="'+soft_weight+'" size="10"  />';
	
	cell8.innerHTML = imageButton+hiddenFields;
	if(mode=="addmode")
	{
	xajax_weightmentSupplierName(document.getElementById('selRMSupplierGroup').value,fieldvalue,'');
	}
	
	fieldvalue		= parseInt(fieldvalue)+1;
	document.getElementById("hidTableRowCounts").value = fieldvalue;
}
function setIssuanceItemStatusWeight(id)
{
	if (confirmRemoveItem())
	{
		document.getElementById("mstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("mrow_"+id).style.display = 'none'; 
		
		var wt=document.getElementById("weight_"+id).value;
		var tot=document.getElementById("total_quantity").value;
		document.getElementById("total_quantity").value=parseFloat(tot)	- parseFloat(wt);
		
		var sft=document.getElementById("soft_precent_"+id).value;
		var sfttot=document.getElementById("total_soft").value;
		document.getElementById("total_soft").value=parseFloat(sfttot)	- parseFloat(sft);
		
	}
	return false;
}



function addNewWeighmentMultipleVal(tableId, editweightmentId,supplierName1, pondName1, species1, count_code,weight,soft_precent,soft_weight,packageType1,package_nos,mode)
{
	var tbl			= document.getElementById(tableId);
	//alert(document.getElementById(tableId).getElementsByTagName("tr").length);
	var tblRows = document.getElementById(tableId).getElementsByTagName('tr');
	var count=0;
	for(var i=0; i<tblRows.length; i++) {
		if(tblRows[i].style.display != 'none') {
			count++;   
		}
	} 
	// alert(count);
	var selectBoxLength = document.getElementById("hidTableRowCountsValhid").value;
	// alert(selectBoxLength);
	if(selectBoxLength != '' && count == selectBoxLength)
	{
		// alert("You reached maximum row");
		document.getElementById('hiderow').style.display = 'none';
	}
	else
	{
		var lastRow		= tbl.rows.length;
		var iteration		= lastRow+1;
		var row			= tbl.insertRow(lastRow);
		row.height		= "22";
		row.className 		= "whiteRow";
		row.id 			= "wrow_"+fieldvalueId;

		var cell1			= row.insertCell(0);
		var cell2			= row.insertCell(1);
		var cell3			= row.insertCell(2);
		var cell4			= row.insertCell(3);
		var cell5			= row.insertCell(4);
		var cell6			= row.insertCell(5);
		var cell7			= row.insertCell(6);
		var cell8			= row.insertCell(7);
		var cell9			= row.insertCell(8);
		var cell10			= row.insertCell(9);

		cell1.className	=	"fieldName"; cell1.align = 'left';
		cell2.className	=	"fieldName"; cell2.align = "center";
		cell3.className	=	"fieldName"; cell3.align = 'center';
		cell4.className	=	"fieldName"; cell4.align = "center";
		cell5.className	=	"fieldName"; cell5.align = "center";
		cell6.className	=	"fieldName"; cell6.align = "center";
		cell7.className	=	"fieldName"; cell7.align = "center";
		cell8.className	=	"fieldName"; cell8.align = "center";
		cell9.className	=	"fieldName"; cell9.align = "center";
		cell10.className	=	"fieldName"; cell9.align = "center";
		
		var ds = "N";	
		var imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatusMultiple('"+fieldvalueId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

		var hiddenFields = "<input name='wstatus_"+fieldvalueId+"' type='hidden' id='wstatus_"+fieldvalueId+"' value=''><input name='IsFromDB_"+fieldvalueId+"' type='hidden' id='IsFromDB_"+fieldvalueId+"' value='"+ds+"'><input type='hidden' name='wrmId_"+fieldvalueId+"' id='wrmId_"+fieldvalueId+"' value='"+editweightmentId+"'>";
		var supplierName= "<select name='supplierNamepro_"+fieldvalueId+"' Style='display:display;' id='supplierNamepro_"+fieldvalueId+"' tabindex=1  onchange=\"xajax_rmProcurmentPondName(document.getElementById('supplierNamepro_"+fieldvalueId+"').value,"+fieldvalueId+",'',document.getElementById('procurementGatePass').value); \"  ><option value=''> -- Select --</option>";
		<?php 
											foreach($supplierRecs as $sr)
											{
											//alert($sr[0]);
							$supplierNameId		=	$sr[1];
							$supplierNameValue	=	stripSlash($sr[2]);
							?>
							if(supplierName1=="<?=$supplierNameId?>") var sel="Selected";
						  else var sel = "";
						  
						   supplierName+="<option value=\"<?=$supplierNameId?>\" "+sel+"><?=$supplierNameValue?></option>";
														<? }
									?>	
						supplierName += "</select>";
		
		var pondName= "<select name='pondNamepro_"+fieldvalueId+"' Style='display:display;' id='pondNamepro_"+fieldvalueId+"' tabindex=1  onchange=\"xajax_weightmentSpeciespro(document.getElementById('pondNamepro_"+fieldvalueId+"').value,"+fieldvalueId+",''); \"  ><option value=''> -- Select --</option>";
		<?php 	foreach($pondRecs as $pondval)
				{
											foreach($pondval as $pnd)
											{
											//alert($sr[0]);
							$pondNameId		=	$pnd[1];
							$pondNameValue	=	stripSlash($pnd[2]);
							?>
							if(pondName1=="<?=$pondNameId?>") var sel="Selected";
						  else var sel = "";
						  
						   pondName+="<option value=\"<?=$pondNameId?>\" "+sel+"><?=$pondNameValue?></option>";
														<? }
														}
									?>	
						pondName += "</select>";

		
		var cell1Val = '<select  id="product_speciespro_'+fieldvalueId+'" name="product_speciespro_'+fieldvalueId+'" ><option value=""> -- Select --</option>';
		<?php 	foreach($speciesRecs as $speciesval)
				{
											foreach($speciesval as $species)
											{
											//alert($sr[0]);
							$speciesId		=	$species[1];
							$speciesValue	=	stripSlash($species[2]);
							?>
							//alert(speciesId);
							if(species1=="<?=$speciesId?>") var sel="Selected";
						  else var sel = "";
						  
						   cell1Val+="<option value=\"<?=$speciesId?>\" "+sel+"><?=$speciesValue?></option>";
														<? }
														}
									?>	
						
		cell1Val+='</select>';

		cell1.innerHTML	=supplierName ;
		cell2.innerHTML	=pondName;
																		
		cell3.innerHTML	= cell1Val;
		//cell4.innerHTML	= '<input type="text" name="grade_count'+fieldId+'" id="grade_count'+fieldId+'" size="10" required />';
		cell4.innerHTML	= '<input type="text" name="count_codepro_'+fieldvalueId+'" id="count_codepro_'+fieldvalueId+'" size="10"   value="'+count_code+'"  />';
		cell5.innerHTML	= '<input type="text" name="weightpro_'+fieldvalueId+'" id="weightpro_'+fieldvalueId+'" size="10" onkeyup="checkValuepro();"  value="'+weight+'"  />';
		cell6.innerHTML	= '<input type="text" name="soft_precentpro_'+fieldvalueId+'" id="soft_precentpro_'+fieldvalueId+'"  size="10"  onkeyup="checksSoftValuepro();"  value="'+soft_precent+'"  />';
		cell7.innerHTML	= '<input type="text" name="soft_weightpro_'+fieldvalueId+'" id="soft_weightpro_'+fieldvalueId+'" size="10" value="'+soft_weight+'" />';
		var packageType			= "<select name='packageTypepro_"+fieldvalueId+"' Style='display:display;' id='packageTypepro_"+fieldvalueId+"' tabindex=1    ><option value=''> -- Select --</option>";
			//alert(packageType1);
			<?php
			if (sizeof($packageTypeRecs)>0) {	
				foreach ($packageTypeRecs as $dcw) {
							$packageTypeId = $dcw[1];
							$packageType	= stripSlash($dcw[2]);
							
		?>	
		
			if (packageType1=="<?=$packageTypeId?>")  var sel = "Selected";
			else var sel = "";

		packageType += "<option value=\"<?=$packageTypeId?>\" "+sel+"><?=$packageType?></option>";	
		<?php
				}
			}
			
		?>	
		packageType += "</select>";
		cell8.innerHTML	= packageType;
		//cell8.innerHTML	= '<input type="text" name="pkg_typepro_'+fieldvalueId+'" id="pkg_typepro_'+fieldvalueId+'" size="10"  />';
		
		cell9.innerHTML	= '<input type="text" name="pkg_nospro_'+fieldvalueId+'" id="pkg_nospro_'+fieldvalueId+'" size="10" value="'+package_nos+'" />';
		
		cell10.innerHTML = imageButton+hiddenFields;
		if(mode=="addmode")
		{
		xajax_ProcurmentDetail(document.getElementById('procurementGatePass').value,fieldvalue,'');
		}
		fieldvalueId		= parseInt(fieldvalueId)+1;
		count = count + 1;
		if(selectBoxLength != '' && count == selectBoxLength)
		{
			// alert("You reached maximum row");
			document.getElementById('hiderow').style.display = 'none';
		}
	}
	 document.getElementById("hidTableRowCountsVal").value = fieldvalueId;
}
function setIssuanceItemStatusMultiple(id)
{
	if (confirmRemoveItem())
	{
		document.getElementById("wstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("wrow_"+id).style.display = 'none'; 
		document.getElementById('hiderow').style.display = 'block';
		
		var wt=document.getElementById("weightpro_"+id).value;
		var tot=document.getElementById("total_quantitypro").value;
		document.getElementById("total_quantitypro").value=parseFloat(tot)	- parseFloat(wt);
		
		var sft=document.getElementById("soft_precentpro_"+id).value;
		var sfttot=document.getElementById("total_softpro").value;
		document.getElementById("total_softpro").value=parseFloat(sfttot)	- parseFloat(sft);
		
		
	}
	return false;
}






function addNewProcurmentItemRow(tableId,equipId,equipmentName1,equipmentIssued,equipmentReturned,difference,valueeqp,mode)
{

	//alert("HIII");
//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var tblRows = document.getElementById(tableId).getElementsByTagName('tr');
	var count=0;
	for(var i=0; i<tblRows.length; i++) {
		if(tblRows[i].style.display != 'none') {
			count++;   
		}
	} 
	// alert(count);
	var selectBoxLength = document.getElementById("hidTableRowCounthid").value;
	// alert(selectBoxLength);
	if(selectBoxLength != '' && count == selectBoxLength)
	{
		// alert("You reached maximum row");
		document.getElementById('hiderowequipment').style.display = 'none';
	}
	else
	{
	
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "row_"+fieldId;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	/*var cell6			= row.insertCell(5);
	var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	/*cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='rmId_"+fieldId+"' id='rmId_"+fieldId+"' value='"+equipId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
	
	/*var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentQuantity(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('vehicle').value,"+fieldId+");  balanceQty();\"  >";
		equipmentName += "<option value=''>--select--</option>";
	equipmentName +="</select>";*/
	var equipmentName			= "<select name='equipmentName_"+fieldId+"' Style='display:display;' id='equipmentName_"+fieldId+"' tabindex=1  onchange=\"xajax_equipmentIssued(document.getElementById('equipmentName_"+fieldId+"').value,document.getElementById('procurementGatePass').value,"+fieldId+");  balanceQty();\"  >";
		<?php
		if (sizeof($harvestingequipmentNameRecs)>0) {	
			foreach ($harvestingequipmentNameRecs as $dcw) {
						$equipmentNameId = $dcw[0];
						$equipmentName	= stripSlash($dcw[1]);
						
	?>	
	
		if (equipmentName1=="<?=$equipmentNameId?>")  var sel = "Selected";
		else var sel = "";

	equipmentName += "<option value=\"<?=$equipmentNameId?>\" "+sel+"><?=$equipmentName?></option>";	
	<?php
			}
		}
		
	?>	
	equipmentName += "</select>";
	
	
	/*var chemicalName			= "<select name='chemicalName_"+fieldId+"' Style='display:display;' id='chemicalName_"+fieldId+"' tabindex=1  onchange=\"xajax_chemicalQuantity(document.getElementById('chemicalName_"+fieldId+"').value,document.getElementById('vehicleNo_"+fieldId+"').value,"+fieldId+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	chemicalName +="</select>";*/

	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= equipmentName;
	cell2.innerHTML	= "<input name='equipmentIssued_"+fieldId+"' type='text' id='equipmentIssued_"+fieldId+"' size='4' style='text-align:right; border:none;' readonly value='"+equipmentIssued+"' tabindex="+fieldId+" >";
	
	cell3.innerHTML	= "<input name='equipmentReturned_"+fieldId+"' type='text' id='equipmentReturned_"+fieldId+"' value='"+equipmentReturned+"' size='4'  style='text-align:right; ' onKeyUp='return balanceQty();'/>";
	
	//cell3.innerHTML	= "<input name='equipmentIssued_"+fieldId+"' type='text' id='equipmentIssued_"+fieldId+"' size='4' style='text-align:right' value='"+equipmentIssued+"' tabindex="+fieldId+" onKeyUp='return balanceQty();'>";
	cell4.innerHTML	= "<input name='balanceQty_"+fieldId+"' type='text' id='balanceQty_"+fieldId+"' size='4' readonly style='text-align:right; border:none;' tabindex="+fieldId+"  value='"+difference+"'>";
	//cell5.innerHTML	= chemicalName;
	//cell6.innerHTML	= "<input name='chemicalQty_"+fieldId+"' type='text' id='chemicalQty_"+fieldId+"' value='"+chemicalQty+"' size='4' readonly style='text-align:right; border:none;'/>";
	//cell7.innerHTML	= "<input name='chemicalIssued_"+fieldId+"' type='text' id='chemicalIssued_"+fieldId+"' size='4' style='text-align:right' value='"+chemicalIssued+"' tabindex="+fieldId+" >"+ hiddenFields;
	cell5.innerHTML = imageButton+hiddenFields+vehicle;
	//if(mode=="addmode")
	//{
	//xajax_getDetails(document.getElementById('vehicleNo').value,'',fieldId,'');
	//}
	if(mode=="addmode")
		{
	xajax_ProcurmentDetailEquipment(document.getElementById('procurementGatePass').value,fieldvalue,'');
	//xajax_ProcurmentDetail(document.getElementById('procurementGatePass').value,fieldvalue,'');
	}
	fieldId		= parseInt(fieldId)+1;
	count = count + 1;
		if(selectBoxLength != '' && count == selectBoxLength)
		{
			// alert("You reached maximum row");
			document.getElementById('hiderowequipment').style.display = 'none';
		}
	}
	
	document.getElementById("hidTableRowCount").value = fieldId;
	
}

function setIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none'; 		
	}
	return false;
}



function addNewRMProcurmentChemicalItemRow(tableId,chemicalId,chemicalName1,chemicalIssued,chemicalUsed,chemicalReturned,differenceQty,editWeightmentId,mode)
{
//alert("hii");
//	alert(editProcurmentId);
	//var rowCountObj	= formObj.rowCount;
	var tbl			= document.getElementById(tableId);
	
	var tblRows = document.getElementById(tableId).getElementsByTagName('tr');
	var count=0;
	for(var i=0; i<tblRows.length; i++) {
		if(tblRows[i].style.display != 'none') {
			count++;   
		}
	} 
	// alert(count);
	var selectBoxLength = document.getElementById("hidChemicalRowCounthid").value;
	// alert(selectBoxLength);
	if(selectBoxLength != '' && count == selectBoxLength)
	{
		// alert("You reached maximum row");
		document.getElementById('hiderowchemical').style.display = 'none';
	}
	else
	{
	
	
	var lastRow		= tbl.rows.length;
	//alert(lastRow);
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "brow_"+fld;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);
	var cell6			= row.insertCell(5);
	/*var cell7			= row.insertCell(6);
	var cell8			= row.insertCell(7);
	var cell9			= row.insertCell(8);
	var cell10			= row.insertCell(9);*/

	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "center";
	/*cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className	=	"fieldName"; cell10.align = "center";*/
	/*cell11.className	=	"fieldName"; cell11.align = "center";*/
	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"bsetIssuanceItemStatus('"+fld+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='bstatus_"+fld+"' type='hidden' id='bstatus_"+fld+"' value=''><input name='IsFromDB_"+fld+"' type='hidden' id='IsFromDB_"+fld+"' value='"+ds+"'><input type='hidden' name='brmId_"+fld+"' id='brmId_"+fld+"' value='"+chemicalId+"'>";

	var vehicle="<input type='hidden' id='vehicle' name='vehicle' >";
	
	
	
	var chemicalName			= "<select name='chemicalName_"+fld+"' Style='display:display;' id='chemicalName_"+fld+"' tabindex=1  onchange=\"xajax_chemicalIssued(document.getElementById('chemicalName_"+fld+"').value,document.getElementById('procurementGatePass').value,"+fld+");\"  >";
	chemicalName += "<option value=''>--select--</option>";
	<?php
		if (sizeof($harvestingchemicalNameRecs)>0) {	
			foreach ($harvestingchemicalNameRecs as $dcw) {
						$chemicalNameId = $dcw[1];
						$chemicalName	= stripSlash($dcw[2]);
						
	?>	
	
		if (chemicalName1=="<?=$chemicalNameId?>")  var sel = "Selected";
		else var sel = "";

	chemicalName += "<option value=\"<?=$chemicalNameId?>\" "+sel+"><?=$chemicalName?></option>";	
	<?php
			}
		}
		
	?>	
	chemicalName +="</select>"
	
	
	
	
	//cell1.innerHTML	= driverName;
	//cell2.innerHTML	= vehicleNo;
	cell1.innerHTML	= chemicalName;
	
	cell2.innerHTML	= "<input name='chemicalIssued_"+fld+"' type='text' id='chemicalIssued_"+fld+"' value='"+chemicalIssued+"' size='4' readonly style='text-align:right; border:none;'/>";
	cell3.innerHTML	= "<input name='chemicalUsed_"+fld+"' type='text' id='chemicalUsed_"+fld+"' size='4' style='text-align:right' value='"+chemicalUsed+"' tabindex="+fld+" onKeyUp='return differenceQty();' >";
	cell4.innerHTML	= "<input name='chemicalReturned_"+fld+"' type='text' id='chemicalReturned_"+fld+"' size='4' style='text-align:right' value='"+chemicalReturned+"' tabindex="+fld+" onKeyUp='return differenceQty();' >";
	cell5.innerHTML	= "<input name='differenceQty_"+fld+"' type='text' id='differenceQty_"+fld+"' size='4' style='text-align:right; border:none;' readonly value='"+differenceQty+"' tabindex="+fld+" >";
	cell6.innerHTML = imageButton+hiddenFields+vehicle;
	
	//xajax_getDetailvalue(document.getElementById('vehicleNo').value,'',fld,'');
	//xajax_ProcurmentDetail(document.getElementById('procurementGatePass').value,fieldvalue,'');
	if(mode=="addmode")
		{
	xajax_ProcurmentDetailChemical(document.getElementById('procurementGatePass').value,fieldvalue,'');
	}
	fld		= parseInt(fld)+1;
	count = count + 1;
		if(selectBoxLength != '' && count == selectBoxLength)
		{
			// alert("You reached maximum row");
			document.getElementById('hiderowchemical').style.display = 'none';
		}
	}
	
	document.getElementById("hidChemicalRowCount").value = fld;
}




function bsetIssuanceItemStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("bstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("brow_"+id).style.display = 'none'; 		
	}
	return false;
}
function balanceQty()
{
	//alert("hii");
	var stockStatus = false;
	var rowCount	= document.getElementById("hidTableRowCount").value;
	
	var total	= 0;
	
	var equipmentQty = "equipmentIssued_";
	var pQty	 = "equipmentReturned_";
	var balanceQty	 = "balanceQty_";	
	
	//var equipmentQty = "equipmentQty_";
	//var pQty	 = "equipmentIssued_";
	//var balanceQty	 = "balanceQty_";	
	
	for (i=0; i<rowCount; i++) {
	   var status = document.getElementById("status_"+i).value;		
	   if (status!='N') 
	    {
		
	  	var quantity =	0;
	 	 if (document.getElementById(pQty+i).value!="") {
			 document.getElementById(balanceQty+i).value	 = document.getElementById(equipmentQty+i).value - document.getElementById(pQty+i).value;
	  	} else {
			document.getElementById(balanceQty+i).value =0;
		}

		if (document.getElementById(balanceQty+i).value<0) {
			stockStatus = true;			
		} 
	  }
	}

	if (stockStatus==true) {
		document.getElementById("hidStockItemStatus").value='P';
	} else {
		document.getElementById("hidStockItemStatus").value='C';
	}	
}


function differenceQty()
{
	
	var stockStatus = false;
	var rowCounts	= document.getElementById("hidChemicalRowCount").value;
	
	var total	= 0;
	
	var chemicalIssued = "chemicalIssued_";
	var chemicalUsed	 = "chemicalUsed_";
	var chemicalReturned	 = "chemicalReturned_";	
	var differenceQty	 = "differenceQty_";	
	
	//var equipmentQty = "equipmentQty_";
	//var pQty	 = "equipmentIssued_";
	//var balanceQty	 = "balanceQty_";	
	var quantity =	0;
	for (i=0; i<rowCounts; i++) {
	   var status = document.getElementById("bstatus_"+i).value;		
	   if (status!='N') 
	    {
		var ss=document.getElementById(chemicalReturned+i).value;
		
	  	if ((document.getElementById(chemicalUsed+i).value!="") && (document.getElementById(chemicalReturned+i).value!="")) {
		//alert(ss);
			 document.getElementById(differenceQty+i).value	 = document.getElementById(chemicalIssued+i).value - document.getElementById(chemicalUsed+i).value - document.getElementById(chemicalReturned+i).value;
	  	
		}
	 	else  if(document.getElementById(chemicalUsed+i).value!="") {
		document.getElementById(differenceQty+i).value	 = document.getElementById(chemicalIssued+i).value - document.getElementById(chemicalUsed+i).value;
		}
		else {
			document.getElementById(differenceQty+i).value =0;
		}
			stockStatus = true;			
		if (document.getElementById(differenceQty+i).value<0) {
		} 
	  }
	}

	if (stockStatus==true) {
		document.getElementById("hidStockItemStatus").value='P';
	} else {
		document.getElementById("hidStockItemStatus").value='C';
	}	
}


function checkValue()
{
var total=0;

var cntval=parseInt(document.getElementById("hidTableRowCounts").value);
//alert(cntval);
 for(i=0; i<cntval; i++)
	{
	
		var x=parseInt(document.getElementById("weight_"+i).value);
		//alert(x);
		var stsus=document.getElementById("mstatus_"+i).value;
		if(x!="" &&  stsus!="N")
		{ 
			
			total = parseInt(total) + x;
			
		}
		
	}
//alert(total);
document.getElementById("total_quantity").value=total;
}
function checksSoftValue()
{
var softtotal=0;

var cntval=parseInt(document.getElementById("hidTableRowCounts").value);
//alert(cntval);
 for(i=0; i<cntval; i++)
	{
	
		var s=parseInt(document.getElementById("soft_precent_"+i).value);
		//alert(x);
		var stsus=document.getElementById("mstatus_"+i).value;
		if(s!="" &&  stsus!="N")
		{ 
			
			softtotal = parseInt(softtotal) + s;
			
		}
		
	}
//alert(total);
document.getElementById("total_soft").value=softtotal;
}

function checkValuepro()
{
var totalval=0;
var cntval=parseInt(document.getElementById("hidTableRowCountsVal").value);
//alert(cntval);
 for(i=0; i<cntval; i++)
	{
	
		var y=parseInt(document.getElementById("weightpro_"+i).value);
		
		var stsus=document.getElementById("wstatus_"+i).value;
		if(y!="" &&  stsus!="N")
		{ 
			//alert(y);
			totalval = parseInt(totalval) + y;
			
		}
		
	}
//alert(totalval);
document.getElementById("total_quantitypro").value=totalval;
}


function checksSoftValuepro()
{
var softtotalval=0;

var cntvals=parseInt(document.getElementById("hidTableRowCountsVal").value);
//alert(cntval);
 for(j=0; j<cntvals; j++)
	{
	
		var sh=parseInt(document.getElementById("soft_precentpro_"+j).value);
		//alert(x);
		var stsus=document.getElementById("wstatus_"+j).value;
		if(sh!="" &&  stsus!="N")
		{ 
		
			softtotalval = parseInt(softtotalval) + sh;
		
		}
		
	}
//alert(total);
document.getElementById("total_softpro").value=softtotalval;
}

function validateRepeatIssuance()
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
var procure_aval = document.getElementById('checkbox1');
if(procure_aval.checked == true)
{	
var sc = document.getElementById("hidTableRowCountsVal").value;
	
	
	var arra = new Array();
	var arrk=0;
	for( j=0; j<sc; j++ )	{
	    var status = document.getElementById("wstatus_"+j).value;
		//alert('aaa');
	    if (status!='N') 
	    {
		var rv = document.getElementById("pondNamepro_"+j).value;	
		if ( arra.indexOf(rv) != -1 )	{
			alert("Pond Name  Cannot be duplicate.");
			document.getElementById("pondNamepro_"+j).focus();
			return false;
		}
		arra[arrk++]=rv;
            }
	}
	
	
	
	
	var rc = document.getElementById("hidTableRowCount").value;
	
	var prevOrder = 0;
	var arr = new Array();
	var arri=0;
	for( j=0; j<rc; j++ )	{
	    var status = document.getElementById("status_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("equipmentName_"+j).value;	
		if ( arr.indexOf(rv) != -1 )	{
			alert("Equipment  Cannot be duplicate.");
			document.getElementById("equipmentName_"+j).focus();
			return false;
		}
		arr[arri++]=rv;
            }
	}
	
	var ch = document.getElementById("hidChemicalRowCount").value;
	var ar = new Array();
	var arrl=0;
	for( j=0; j<ch; j++ )	{
	    var status = document.getElementById("bstatus_"+j).value;
	    if (status!='N') 
	    {
		var rv = document.getElementById("chemicalName_"+j).value;	
		if ( ar.indexOf(rv) != -1 )	{
			alert("Chemical Cannot be duplicate.");
			document.getElementById("chemicalName_"+j).focus();
			return false;
		}
		ar[arrl++]=rv;
            }
	}
	
}
else
{	
	
	var sc1 = document.getElementById("hidTableRowCounts").value;
	
	
	var arra1 = new Array();
	var arrk1=0;
	for( h=0; h<sc1; h++ )	{
	    var status = document.getElementById("mstatus_"+h).value;
		
	    if (status!='N') 
	    {
		var rv1 = document.getElementById("pondName_"+h).value;
		
		if ( arra1.indexOf(rv1) != -1 )	{
		//alert('aaa');	
			alert("Pond Name  Cannot be duplicate.");
			document.getElementById("pondName_"+h).focus();
			return false;
		}
		arra1[arrk1++]=rv1;
            }
	}
	
	
	}
	
	return true;	
}

