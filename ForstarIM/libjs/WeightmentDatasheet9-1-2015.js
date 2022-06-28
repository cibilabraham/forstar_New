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
			var processCodeValue		=	document.getElementById("processCodeValue_"+i);
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
			if( processCodeValue.value == "" )
			{
				alert("Please enter a Process code.");
				processCodeValue.focus();
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
			var process_code		=	document.getElementById("process_code_"+i);
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
			if( process_code.value == "" )
			{
				alert("Please enter a process code.");
				process_code.focus();
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

if(!validateRepeatIssuance())
	{
		return false;
	}
	 
	if (!confirmSave()) return false;
	return true;
}

function addNewWeighmentMultiple(tableId, editweightmentId,supplierName1, pondName1, species1,procesCode1, count_code,weight,soft_precent,soft_weight,value1,mode,qualty_id)
{
	//alert(procesCode1);
	// alert(qualty_id);
	var fieldvalue  = document.getElementById('rowcount').value;
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
	cell10.className=	"fieldName"; cell10.align = "center";
	
	
	var ds = "N";	
	var imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatusWeight('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='mstatus_"+fieldvalue+"' type='hidden' id='mstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='mrmId_"+fieldvalue+"' id='mrmId_"+fieldvalue+"' value='"+editweightmentId+"'>";
	// alert(hiddenFields);
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
	var pondName = "<select name='pondName_"+fieldvalue+"' Style='display:display;' id='pondName_"+fieldvalue+"' tabindex=1  onchange=\"xajax_weightmentSpecies(document.getElementById('pondName_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''> -- Select --</option>";
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

	var cell1Val = "<select  id='product_species_"+fieldvalue+"' name='product_species_"+fieldvalue+"' tabindex=1  onchange=\"xajax_processCode(document.getElementById('product_species_"+fieldvalue+"').value,"+fieldvalue+",''); \" ><option value=''> -- Select --</option>";
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
					cell1Val += "</select>";
	
	var processCde = '<select  id="process_code_'+fieldvalue+'" name="process_code_'+fieldvalue+'"><option value=""> -- Select --</option>';
	<?php 	foreach($processCodeRecs as $processCodeVal)
			{
										foreach($processCodeVal as $processCode)
										{
										//alert($sr[0]);
						$processCodeId		=	$processCode[0];
						$processCodeValue	=	stripSlash($processCode[1]);
						?>
						if(procesCode1=="<?=$processCodeId?>") var sel="Selected";
					  else var sel = "";
					  
                       processCde+="<option value=\"<?=$processCodeId?>\" "+sel+"><?=$processCodeValue?></option>";
                                                    <? }
													}
								?>	
					processCde += '</select>';
	
	
	var qualityDropDown = '<select name="quality_'+fieldvalue+'"><option value=""> -- Select --</option>';
	<?php 
		if(sizeof($qualityList) > 0)
		{
			foreach($qualityList as $quality)
			{
				$qualityId = $quality[0];
				$qualityName = $quality[1];
			?>
				sel = '';
				if(qualty_id == '<?=$qualityId?>')
				{
					sel = 'selected';
				}
			 qualityDropDown+="<option value=\"<?=$qualityId?>\" "+sel+"><?=$qualityName?></option>";
			<?php
			}
		}
	?>
	qualityDropDown+= '</select>';
	cell1.innerHTML	=supplierName ;
	cell2.innerHTML	=pondName;
																	
	cell3.innerHTML	= cell1Val;
	cell4.innerHTML	= processCde;
	cell5.innerHTML = qualityDropDown;
	//cell4.innerHTML	= '<input type="text" name="grade_count'+fieldId+'" id="grade_count'+fieldId+'" size="10" required />';
	cell6.innerHTML	= '<input type="text" name="count_code_'+fieldvalue+'" id="count_code_'+fieldvalue+'" size="10" value="'+count_code+'" />';
	cell7.innerHTML	= '<input type="text" name="weight_'+fieldvalue+'" id="weight_'+fieldvalue+'" size="10" value="'+weight+'" onkeyup="checkValue(); calWeight(); checksSoftValue();" />';
	cell8.innerHTML	= '<input type="text" name="soft_precent_'+fieldvalue+'" id="soft_precent_'+fieldvalue+'" value="'+soft_precent+'" size="10" onkeyup="calWeight(); checksSoftValue();"  />';
	cell9.innerHTML	= '<input type="text" name="soft_weight_'+fieldvalue+'" id="soft_weight_'+fieldvalue+'" value="'+soft_weight+'" size="10" readonly />';
	
	cell10.innerHTML = imageButton+hiddenFields;
	fieldvalue		= parseInt(fieldvalue)+1;
	document.getElementById("hidTableRowCounts").value = fieldvalue;
	document.getElementById('rowcount').value = fieldvalue;
	document.getElementById("hidTableRowCountsVal").value = fieldvalue; 
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

function addNewWeighmentMultipleVal(tableId, editweightmentId,supplierName1, pondName1, species1,procesCode1, count_code,weight,soft_precent,soft_weight,packageType1,package_nos,mode)
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
		var cell11			= row.insertCell(10);
		var cell12			= row.insertCell(11);
		
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
		cell11.className	=	"fieldName"; cell10.align = "center";
		cell12.className	=	"fieldName"; cell11.align = "center";
		
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

		
		var cell1Val = "<select  id='product_speciespro_"+fieldvalueId+"' name='product_speciespro_"+fieldvalueId+"'  tabindex=1  onchange=\"xajax_productCodeDetails(document.getElementById('product_speciespro_"+fieldvalueId+"').value,"+fieldvalueId+",''); \" ><option value=''> -- Select --</option>";
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
						
		cell1Val+="</select>";
		
		
		var processCde = '<select  id="processCodeValue_'+fieldvalueId+'" name="processCodeValue_'+fieldvalueId+'"><option value=""> -- Select --</option>';
	<?php 	foreach($processCodeRecs as $processCodeVal)
			{
										foreach($processCodeVal as $processCode)
										{
										//alert($sr[0]);
						$processCodeId		=	$processCode[0];
						$processCodeValue	=	stripSlash($processCode[1]);
						?>
						if(procesCode1=="<?=$processCodeId?>") var sel="Selected";
					  else var sel = "";
					  
                       processCde+="<option value=\"<?=$processCodeId?>\" "+sel+"><?=$processCodeValue?></option>";
                                                    <? }
													}
								?>	
					processCde += '</select>';
		
		var qualityDropDown = '<select name="quality_pro_'+fieldvalue+'"><option value=""> -- Select --</option>';
	<?php 
		if(sizeof($qualityList) > 0)
		{
			foreach($qualityList as $quality)
			{
				$qualityId = $quality[0];
				$qualityName = $quality[1];
			?>
				sel = '';
			 qualityDropDown+="<option value=\"<?=$qualityId?>\" "+sel+"><?=$qualityName?></option>";
			<?php
			}
		}
	?>
	
		cell1.innerHTML	=supplierName ;
		cell2.innerHTML	=pondName;
																		
		cell3.innerHTML	= cell1Val;
		cell4.innerHTML	= processCde;
		cell5.innerHTML	= qualityDropDown;
		//cell4.innerHTML	= '<input type="text" name="grade_count'+fieldId+'" id="grade_count'+fieldId+'" size="10" required />';
		cell6.innerHTML	= '<input type="text" name="count_codepro_'+fieldvalueId+'" id="count_codepro_'+fieldvalueId+'" size="10"   value="'+count_code+'"  />';
		cell7.innerHTML	= '<input type="text" name="weightpro_'+fieldvalueId+'" id="weightpro_'+fieldvalueId+'" size="10" onkeyup=" calculateWeight(); checksSoftValuepro();"  value="'+weight+'" />';
		cell8.innerHTML	= '<input type="text" name="soft_precentpro_'+fieldvalueId+'" id="soft_precentpro_'+fieldvalueId+'"  size="10"  onkeyup="checkValuepro(); calculateWeight(); checksSoftValuepro();"  value="'+soft_precent+'"  />';
		cell9.innerHTML	= '<input type="text" name="soft_weightpro_'+fieldvalueId+'" id="soft_weightpro_'+fieldvalueId+'" size="10" value="'+soft_weight+'"  read/>';
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
		cell10.innerHTML	= packageType;
		//cell8.innerHTML	= '<input type="text" name="pkg_typepro_'+fieldvalueId+'" id="pkg_typepro_'+fieldvalueId+'" size="10"  />';
		
		cell11.innerHTML	= '<input type="text" name="pkg_nospro_'+fieldvalueId+'" id="pkg_nospro_'+fieldvalueId+'" size="10" value="'+package_nos+'" />';
		
		cell12.innerHTML = imageButton+hiddenFields;
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
	 // document.getElementById("hidTableRowCountsVal").value = fieldvalueId; 
	 
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

	var tbl			= document.getElementById(tableId);
	
	var tblRows = document.getElementById(tableId).getElementsByTagName('tr');
	var count=0;
	for(var i=0; i<tblRows.length; i++) {
		if(tblRows[i].style.display != 'none') 
		{
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


function calculateWeight()
{
	
	var stockStatus = false;
	var rowCounts	= document.getElementById("hidTableRowCounts").value;
	
	var total	= 0;
	
	var weightpro = "weightpro_";
	var soft_precentpro	 = "soft_precentpro_";
	var soft_weightpro	 = "soft_weightpro_";	
	//var differenceQty	 = "differenceQty_";	
	
	//var equipmentQty = "equipmentQty_";
	//var pQty	 = "equipmentIssued_";
	//var balanceQty	 = "balanceQty_";	
	var quantity =	0;
	for (i=0; i<rowCounts; i++) {
	   var status = document.getElementById("wstatus_"+i).value;		
	   if (status!='N') 
	    {
		//var ss=document.getElementById(chemicalReturned+i).value;
		
	  	if ((document.getElementById(weightpro+i).value!="") && (document.getElementById(soft_precentpro+i).value!="")) {
		//alert(ss);
			 document.getElementById(soft_weightpro+i).value	 = (document.getElementById(weightpro+i).value * document.getElementById(soft_precentpro+i).value )/ 100;
	  	
		}
	 	
		else {
			document.getElementById(soft_weightpro+i).value =0;
		}
			stockStatus = true;			
		if (document.getElementById(soft_weightpro+i).value<0) {
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
// alert(cntval);
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
	
		var s=parseFloat(document.getElementById("soft_weight_"+i).value);
		//alert(x);
		var stsus=document.getElementById("mstatus_"+i).value;
		if(s!="" &&  stsus!="N")
		{ 
			
			softtotal = parseFloat(softtotal) + s;
			
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

var cntvals=parseFloat(document.getElementById("hidTableRowCountsVal").value);
//alert(cntval);
 for(j=0; j<cntvals; j++)
	{
	
		var sh=parseFloat(document.getElementById("soft_weightpro_"+j).value);
		//alert(x);
		var stsus=document.getElementById("wstatus_"+j).value;
		if(sh!="" &&  stsus!="N")
		{ 
		
			softtotalval = parseFloat(softtotalval) + sh;
		
		}
		
	}
//alert(total);
document.getElementById("total_softpro").value=softtotalval;
}


function calWeight()
{
	
	var stockStatus = false;
	var rowCounts	= document.getElementById("hidTableRowCounts").value;
	
	var total	= 0;
	
	var weight = "weight_";
	var soft_precent	 = "soft_precent_";
	var soft_weight	 = "soft_weight_";	
	//var differenceQty	 = "differenceQty_";	
	
	//var equipmentQty = "equipmentQty_";
	//var pQty	 = "equipmentIssued_";
	//var balanceQty	 = "balanceQty_";	
	var quantity =	0;
	for (i=0; i<rowCounts; i++) {
	   var status = document.getElementById("mstatus_"+i).value;		
	   if (status!='N') 
	    {
		//var ss=document.getElementById(chemicalReturned+i).value;
		
	  	if ((document.getElementById(weight+i).value!="") && (document.getElementById(soft_precent+i).value!="")) {
		//alert(ss);
			 document.getElementById(soft_weight+i).value	 = (document.getElementById(weight+i).value * document.getElementById(soft_precent+i).value )/ 100;
	  	
		}
	 	
		else {
			document.getElementById(soft_weight+i).value =0;
		}
			stockStatus = true;			
		if (document.getElementById(soft_weight+i).value<0) {
		} 
	  }
	}

	if (stockStatus==true) {
		document.getElementById("hidStockItemStatus").value='P';
	} else {
		document.getElementById("hidStockItemStatus").value='C';
	}	
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
function addNewRowProcurementNotAvailable()
{
	
	//var lotid=document.getElementById('rm_lot_id').value;
	//alert(lotid);
	var rowcount  = document.getElementById('rowcount').value;
	var tbl			= document.getElementById('tblWeighmentMultiple');
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "mrow_"+rowcount;
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
	cell10.className=	"fieldName"; cell10.align = "center";
	
	speciesdropDown = '<select name="product_species[]" id="product_species_'+rowcount+'" onchange="xajax_processCode(this.value,'+rowcount+',0);">';
	speciesdropDown+= '<option value=""> --Select-- </option>';
	<?php
		if(sizeof($speciesArray) > 0)
		{		
			foreach($speciesArray as $species)
			{
	?>
				speciesdropDown+= '<option value="<?=$species[0];?>"><?=$species[1]?></option>';
	<?php
				
			}
		}
	?>
	speciesdropDown+= '</select>';
	var qualityDropDown = '<select name="quality[]"><option value=""> -- Select --</option>';
	<?php 
		if(sizeof($qualityList) > 0)
		{
			foreach($qualityList as $quality)
			{
				$qualityId = $quality[0];
				$qualityName = $quality[1];
			?>
				sel = '';
			 qualityDropDown+="<option value=\"<?=$qualityId?>\" "+sel+"><?=$qualityName?></option>";
			<?php
			}
		}
	?>
	qualityDropDown+= '</select>';
	var imageButton = "<a href='javascript:void(0);' onClick=\"hideTableRow('"+rowcount+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='mstatus[]' type='hidden' id='mstatus_"+rowcount+"'><input name='IsFromDB[]' type='hidden' id='IsFromDB_"+rowcount+"'><input type='hidden' name='mrmId_"+rowcount+"' id='mrmId_"+rowcount+"'>";
	var supplierNameoptions = document.getElementById('supplierName_0').innerHTML; 
	cell1.innerHTML	= '<select name="supplierName[]" id="supplierName_'+rowcount+'" />'+supplierNameoptions+'</option></select>';
	cell2.innerHTML	= '<select name="pondName[]" id="pondName_'+rowcount+'" /><option value=""> --Select-- </option></select>';
																	
	cell3.innerHTML	= speciesdropDown;
	cell4.innerHTML	= '<select name="process_code[]" id="process_code_'+rowcount+'" /><option value=""> --Select-- </option></select>';
	cell5.innerHTML = qualityDropDown;
	cell6.innerHTML	= '<input type="text" name="count_code[]" id="count_code_'+rowcount+'" size="10"  />';
	cell7.innerHTML	= '<input type="text" name="weight[]" id="weight_'+rowcount+'" size="10"  onkeyup="calculateWeightAndPercent();" />';
	cell8.innerHTML	= '<input type="text" name="soft_precent[]" id="soft_precent_'+rowcount+'" size="10" onkeyup="calculateWeightAndPercent();"  />';
	cell9.innerHTML	= '<input type="text" name="soft_weight[]" id="soft_weight_'+rowcount+'" size="10" readonly />';
	cell10.innerHTML = imageButton+hiddenFields;
	
	rowcount		= parseInt(rowcount)+1;
	document.getElementById("hidTableRowCounts").value = rowcount;
	document.getElementById('rowcount').value = rowcount;
}
function addNewRowProcurementAvailable()
{
	//var lotid=document.getElementById('rm_lot_id').value;
	//alert(lotid);
	var rowcount  = document.getElementById('rowcount').value;
	var tbl			= document.getElementById('tblWeighmentMultiple');
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "mrow_"+rowcount;
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
	var cell11			= row.insertCell(10);
	cell1.className	=	"fieldName"; cell1.align = 'left';
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center";
	cell5.className	=	"fieldName"; cell5.align = "center";
	cell6.className	=	"fieldName"; cell6.align = "center";
	cell7.className	=	"fieldName"; cell7.align = "center";
	cell8.className	=	"fieldName"; cell8.align = "center";
	cell9.className	=	"fieldName"; cell9.align = "center";
	cell10.className=	"fieldName"; cell10.align = "center";
	cell11.className=	"fieldName"; cell11.align = "center";

	var supplierDropDown = "<select name='supplierName[]' onchange=\"xajax_getPondBasedOnRmLotIdAndSupplier(document.getElementById('rm_lot_id').value,this.value,"+rowcount+",'');\" id='supplierName_"+rowcount+"' ></select>";

	
	//var supplierNameoptions = document.getElementById('supplierName_0').innerHTML; 
	//var supplierDropDown = '<select name="supplierName[]" id="supplierName_'+rowcount+'" onchange="xajax_weightmentSupplierAddress(this.value,'+rowcount+'); ">';
	//supplierDropDown+= '<option value=""> --Select-- </option>';
	<?php
	/*
		$suppliersList = $objWeighmentDataSheet->getTableRowBasedRmLotId("+lotid+");

		if(sizeof($suppliersList) > 0)
		{		
			foreach($suppliersList as $suppliers)
			{
	?>
				supplierDropDown+= '<option value="<?=$suppliers[0];?>"><?=$suppliers[1]?></option>';
	<?php
				
			}
		}
		*/
	?>
	//supplierDropDown+= '</select>';
	
	speciesdropDown = '<select name="product_species[]" id="product_species_'+rowcount+'" onchange="xajax_processCode(this.value,'+rowcount+',0);">';
	speciesdropDown+= '<option value=""> --Select-- </option>';
	<?php
	//	if(sizeof($speciesArray) > 0)
		//{		
			//foreach($speciesArray as $species)
			//{
	?>
				//speciesdropDown+= '<option value="<?=$species[0];?>"><?=$species[1]?></option>';
	<?php
				
			//}
		//}
	?>
	speciesdropDown+= '</select>';
	var qualityDropDown = '<select name="quality[]"><option value=""> -- Select --</option>';
	<?php 
		if(sizeof($qualityList) > 0)
		{
			foreach($qualityList as $quality)
			{
				$qualityId = $quality[0];
				$qualityName = $quality[1];
			?>
				sel = '';
			 qualityDropDown+="<option value=\"<?=$qualityId?>\" "+sel+"><?=$qualityName?></option>";
			<?php
			}
		}
	?>
	qualityDropDown+= '</select>';
	var imageButton = "<a href='javascript:void(0);' onClick=\"hideTableRow('"+rowcount+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='mstatus[]' type='hidden' id='mstatus_"+rowcount+"'><input name='IsFromDB[]' type='hidden' id='IsFromDB_"+rowcount+"'><input type='hidden' name='mrmId_"+rowcount+"' id='mrmId_"+rowcount+"'><input type='hidden' name='newData[]' id='newData_"+rowcount+"' value='0'><input type='hidden' name='phtTagData[]' id='phtTagData_"+rowcount+"' value=''>";
	cell1.innerHTML	= supplierDropDown;
	cell2.innerHTML	= '<select name="pondName[]" id="pondName_'+rowcount+'" onchange="xajax_weightmentSpecies(this.value,'+rowcount+');" /><option value=""> --Select-- </option></select>';
																	
	cell3.innerHTML	= speciesdropDown;
	cell4.innerHTML	= '<select name="process_code[]" id="process_code_'+rowcount+'" /><option value=""> --Select-- </option></select>';
	cell5.innerHTML = qualityDropDown;
	cell6.innerHTML	= '<input type="text" name="count_code[]" id="count_code_'+rowcount+'" size="10"  />';
	cell7.innerHTML	= '<input type="text" name="weight[]" id="weight_'+rowcount+'" size="10"  onkeyup="calculateWeightAndPercent();" />';
	cell8.innerHTML	= '<input type="text" name="soft_precent[]" id="soft_precent_'+rowcount+'" size="10" onkeyup="calculateWeightAndPercent();"  />';
	cell9.innerHTML	= '<input type="text" name="soft_weight[]" id="soft_weight_'+rowcount+'" size="10" readonly />';
	cell10.innerHTML ='<a class="link1" onclick="getPhtCertificate('+rowcount+');" href="#">link tag</a>';
	cell11.innerHTML = imageButton+hiddenFields;
	
	//if(mode=="addmode")
		//{
			xajax_getSupplierBasedOnRmLotId(document.getElementById('rm_lot_id').value,rowcount,'');
			// xajax_getGrading(document.getElementById('pondName').value,document.getElementById('rm_lot_id').value,fieldId,'');
		//}
	
	rowcount		= parseInt(rowcount)+1;
	document.getElementById("hidTableRowCounts").value = rowcount;
	document.getElementById('rowcount').value = rowcount;
	
	
	
}
function hideTableRow(id)
{
	if (confirmRemoveItem())
	{
		// document.getElementById('mrow_'+id).innerHTML = '';
		document.getElementById('mrow_'+id).style.display = 'none';
		document.getElementById('mstatus_'+id).value = 'N';
	}
	calculateWeightAndPercent();
}
function calculateWeightAndPercent()
{
	var weight       = document.getElementsByName('weight[]');
	var soft_precent = document.getElementsByName('soft_precent[]');
	var soft_weight  = document.getElementsByName('soft_weight[]');
	var mstatus  = document.getElementsByName('mstatus[]');
	var totalWeight = 0;
	var totalSoftWeight = 0;
	for(i=0;i<weight.length;i++)
	{
		if(mstatus[i].value != 'N')
		{
			if(!isNaN(weight[i].value))
			{
				if(weight[i].value != '')
					totalWeight = totalWeight + parseFloat(weight[i].value);
			}
			else
			{
				alert('Please enter valid weight ');
				break;
			}
			if(!isNaN(soft_precent[i].value))
			{
				if(soft_precent[i].value != '')
					totalSoftWeight = totalSoftWeight + parseFloat(soft_precent[i].value);
			}
			else
			{
				alert('Please enter valid soft precent ');
				break;
			}
			if(!isNaN(weight[i].value) && weight[i].value != '' && !isNaN(soft_precent[i].value) && soft_precent[i].value != '')
			{
				soft_weight[i].value = parseFloat(soft_precent[i].value) * (parseFloat(weight[i].value)) / 100;
			}
		}
	}
	document.getElementById('total_quantity').value = totalWeight;
	document.getElementById('total_soft').value     = totalSoftWeight;
}
function weighmentFormValidation()
{
	var data_sheet_date      = document.getElementById('data_sheet_date');
	var receiving_supervisor = document.getElementById('receiving_supervisor');
	var data_sheet_slno      = document.getElementById('data_sheet_slno');
	var rm_lot_id            = document.getElementById('rm_lot_id');
	if(data_sheet_date.value == '')
	{
		alert('Please enter the date');
		data_sheet_date.focus();
		return false;
	}
	else if(receiving_supervisor.value == '')
	{
		alert('Please choose receiving supervisor');
		// receiving_supervisor.focus();
		return false;
	}
	else if(data_sheet_slno.value == '')
	{
		alert('Please enter data sheet slno');
		data_sheet_slno.focus();
		return false;
	}
	else if(rm_lot_id.value == '')
	{
		alert('Please choose rm lot id');
		// rm_lot_id.focus();
		return false;
	}
	
		
			var mstatus  = document.getElementsByName('mstatus[]');
			var supplierName     = document.getElementsByName('supplierName[]');
			var product_species  = document.getElementsByName('product_species[]');
			var process_code     = document.getElementsByName('process_code[]');
			var quality          = document.getElementsByName('quality[]');
			var count_code       = document.getElementsByName('count_code[]');
			var weight           = document.getElementsByName('weight[]');
			var soft_precent     = document.getElementsByName('soft_precent[]');
			var soft_weight      = document.getElementsByName('soft_weight[]');
			var process_codes = [];
			for(i=0;i<count_code.length;i++)
			{
				if(mstatus[i].value != 'N')
				{
					process_codes.push(process_code[i].value);
					if(supplierName[i].value == '')
					{
						alert('Please choose supplier');
						return false;
					}
					if(product_species[i].value == '')
					{
						alert('Please choose product species');
						return false;
					}
					if(process_code[i].value == '')
					{
						alert('Please choose product code');
						return false;
					}
					if(quality[i].value == '')
					{
						alert('Please choose quality');
						return false;
					}
					if(count_code[i].value == '')
					{
						alert('Please enter valid count code');
						count_code[i].focus();
						return false;
					}
					else if(weight[i].value == '' || isNaN(weight[i].value))
					{
						alert('Please enter valid weight');
						weight[i].focus();
						return false;
					}
					else if(soft_precent[i].value == '' || isNaN(soft_precent[i].value))
					{
						alert('Please enter valid soft precent');
						soft_precent[i].focus();
						return false;
					}
					else if(soft_weight[i].value == '' || isNaN(soft_weight[i].value))
					{
						alert('Please enter valid soft weight');
						soft_weight[i].focus();
						return false;
					}
				}
			}
			/*var sorted_arr = process_codes.sort(); // You can define the comparing function here. 
                             // JS by default uses a crappy string compare.
			var results = [];
			for (var i = 0; i < process_codes.length - 1; i++) {
				if (sorted_arr[i + 1] == sorted_arr[i]) {
					results.push(sorted_arr[i]);
				}
			}*/
				/*if(results != '')
				{
					alert("Process code must be unique");
					return false;
				}*/

				var arr = new Array();
				var arri=0;
				//alert(count_code.length);
				for(j=0; j<count_code.length; j++)
				{
				//alert(j);
					var status = document.getElementById("mstatus_"+j).value;
					//alert(status);
					if (status=='') 
					{
					//alert(j);
					var supplier = document.getElementById("supplierName_"+j).value;
					var pondName = document.getElementById("pondName_"+j).value;
					var product_species = document.getElementById("product_species_"+j).value;
					var process_code = document.getElementById("process_code_"+j).value;
					var quality = document.getElementById("quality_"+j).value;
					var countCodeVal = document.getElementById("count_code_"+j).value;
					//alert(supplier);
					/*
					alert(supplier);
					*/
								
					var rv=supplier+','+pondName+','+product_species+','+process_code+','+quality+','+countCodeVal;
					//alert(rv);	
						if ( arr.indexOf(rv) != -1 )	{
						alert("Duplicate entry for row.");
						document.getElementById("count_code_"+j).focus();
						return false;
					}
					arr[arri++]=rv;
					
						}
				}
		
	
}

function getPhtCertificate(i)
{	
	var supplierName=document.getElementById("supplierName_"+i).value;
	var pondName=document.getElementById("pondName_"+i).value;
	var product_species=document.getElementById("product_species_"+i).value;
	var weight=document.getElementById("weight_"+i).value;
	var phtTagData=document.getElementById("phtTagData_"+i).value;
	//alert(phtTagData);
	//alert(supplierName+"--"+pondName+"--"+product_species);
	if(pondName=="" && product_species=="" && weight=="")
	{
		alert("Cannot link pht cerificate");
	}
	else if(pondName!="" && product_species=="")
	{
		alert("Please select species");
	}
	else if(pondName!="" && product_species!="" && weight=="")
	{
		alert("Please enter weight");
	}
	else if(pondName!="" && product_species!="" && weight!="")
	{
		//alert("dialog");
		xajax_getPhtCertificate(i,supplierName,pondName,product_species,weight,phtTagData);
		$( "#dialog" ).dialog({ width: 500, height:500, resizable: true, modal: true   });
	}
	
}

function addCerificate(tableId,editProcurmentVehicleId,VehicleId,VehicleNumber,mode,rowCnt)
{
	var fieldvalue=document.getElementById("certificateSize").value;
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "srow_"+fieldvalue;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
		
	cell1.id = "srNo_"+fieldvalue;		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	
	var allCerificate=document.getElementById('certificateNo_0').innerHTML;
	var certificate	= "<select name='certificateNo_"+fieldvalue+"' id='certificateNo_"+fieldvalue+"' onchange=\"xajax_certificateNo(this.value,'"+fieldvalue+"','"+rowCnt+"');\" >";
		certificate+=allCerificate;	
			certificate += "</select>";	
	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setIssuanceCertificateStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	var hiddenFields = "<input name='sstatus_"+fieldvalue+"' type='hidden' id='sstatus_"+fieldvalue+"' value=''><input name='sIsFromDB_"+fieldvalue+"' type='hidden' id='sIsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='srmId_"+fieldvalue+"' id='srmId_"+fieldvalue+"' value=''>";
	cell1.innerHTML	= certificate;
	cell2.innerHTML = "<input id='availableQnty_"+fieldvalue+"' type='text' name='availableQnty_"+fieldvalue+"' value='' size='15' readonly style='text-align:right; border:none;'>";	
	cell3.innerHTML	= "<input id='balanceQnty_"+fieldvalue+"' type='text' name='balanceQnty_"+fieldvalue+"' value='' size='15'  style='text-align:right; border:none;'><input id='qntyStatus_"+fieldvalue+"' type='hidden' value='' tabindex='0' style='text-align:right; border:none;' size='15' name='qntyStatus_"+fieldvalue+"'>";
	cell4.innerHTML = imageButton+hiddenFields;	
	
	fieldvalue		= parseInt(fieldvalue)+1;	
	document.getElementById("certificateSize").value = fieldvalue;	
	
	
//code end
	
}

function setIssuanceCertificateStatus(id)
{  
	if(id==0)
	{
		alert("Cannot delete first row");
	}
	else
	{
		if (confirmRemoveItem()) {
			//alert(document.getElementById("IsFromDB_"+id).value);
			document.getElementById("sstatus_"+id).value = document.getElementById("sIsFromDB_"+id).value;
			//document.getElementById("sstatus_"+id).value = 'N';
			document.getElementById("srow_"+id).style.display = 'none';
			blnQnty();
		}
	}
	return false;
}

function hideRowVal(id)
{  
	if(id==0)
	{
		alert("Cannot delete first row");
	}
	else
	{	//document.getElementById("sstatus_"+id).value ='N';
		document.getElementById("sstatus_"+id).value = document.getElementById("sIsFromDB_"+id).value;
		document.getElementById("srow_"+id).style.display = 'none';
	}
	return false;
}

function availableQnty(rowCnt,certificateId,availableQnty,supplyRow)
{
		getAVail(certificateId);
		if(getAVail(certificateId)=='1')
		{	//alert(availableQnty);
			document.getElementById("availableQnty_"+rowCnt).value=availableQnty;
			blnQnty();
		}
		else
		{
			//alert(getAVail(certificateId)+'---'+rowCnt);
			document.getElementById("availableQnty_"+rowCnt).value=getAVail(certificateId);
			blnQnty();
		}
	
}

function getAVail(certificateId)
{	var supplyQnty=document.getElementById("supplyQnty").value;
	var hidTableCnt=document.getElementById("hidTableRowCounts").value;
	var certifyArray=new Array(); var supplyArray=new Array(); var vals=''; var supply='0'; var balanceQnty='0';
	var pkgArr  = new Array(); 
	for(i=0; i<hidTableCnt; i++)
	{ 
		var phtTagData=document.getElementById("phtTagData_"+i).value;
			if(phtTagData!='')
			{	
				//alert(phtTagData);
				var obj = JSON.parse(phtTagData);
				var itemval =obj.items;
				var len=itemval.length;
				for(j=0; j<len; j++)
				{
					certificateNo=itemval[j].certificateNo;
					availQnty=itemval[j].availableQnty;
					qntyStatus=itemval[j].qntyStatus;
					balanceQnty=itemval[j].balanceQnty;
					//alert(certificateNo+'-----'+availQnty);
					if(certificateNo==certificateId)
					{ 
						if(qntyStatus==1)
						{
							certifyArray[certificateNo] =0;
						}
						else if(qntyStatus==0)
						{
							certifyArray[certificateNo]=balanceQnty;
						}
					}
				}
			}
	}
	if(i==hidTableCnt)
	{	//alert(certifyArray);
		if(certifyArray=="")
		{
			vals=1;
		}
		else
		{
			vals=parseInt(certifyArray[certificateId]);
			//vals=parseInt(supplyArray[certificateId])-parseInt(certifyArray[certificateId]);
			//alert(vals);
		}
		return vals;
	}
	//alert(certifyArray[certificateNo]);
}

function requiredRow(rowCnt,supplyRowCnt)
{  
	var supplyVal=document.getElementById("weight_"+supplyRowCnt).value;
	var supplyQnty=document.getElementById("supplyQnty").value;
	var certificateSize=document.getElementById("certificateSize").value;
	var available="0"; var requiredArray=new Array();
	for(i=0; i<certificateSize; i++)
	{	//alert("hii");
		var status=document.getElementById("sstatus_"+i).value;
		if(status!='N')
		{	var balanceQnty=document.getElementById("balanceQnty_"+i).value;
			var availableQnty=document.getElementById("availableQnty_"+i).value;
			var tavailQnty = availableQnty;
			if (typeof(requiredArray[supplyVal])!="undefined" && tavailQnty!=0) {
				tavailQnty = parseInt(tavailQnty) + parseInt(requiredArray[supplyVal]);
			}
			if (tavailQnty!=0) requiredArray[supplyVal] = parseInt(tavailQnty);
			
			//available+=availableQnty;
			if((parseInt(requiredArray[supplyVal])>=parseInt(supplyQnty)) )
			//if((parseInt(requiredArray[supplyVal])> parseInt(supplyVal)) )
			{	
				//alert(supplyQnty);
				document.getElementById('addNew').style.display = 'none';
				var l=i+1;
				hideRowVal(l);
			}
			else if(balanceQnty=="NaN")
			{
				hideRowVal(i);
				document.getElementById('addNew').style.display = 'none';
			}
			else
			{	//requiredArray[supplyVal]='';
				document.getElementById('addNew').style.display = '';
			}
		}
	}
	//alert(supplyRowCnt);
}


function blnQnty()
{	
	var qntyStatus='';//if value=1 means balanceQnty for certificate=0; else value=positive of that difference value;
	var supplyQnty=document.getElementById("supplyQnty").value;
	var certificateSize=document.getElementById("certificateSize").value;
	for(i=0; i<certificateSize; i++)
	{	//alert(i);
		if(i=='0')
		{	
			var sstatus=document.getElementById("sstatus_"+i).value;
			if(sstatus!='N')
			{
				var availableQnty=document.getElementById("availableQnty_"+i).value;
				//alert(supplyQnty+'---'+availableQnty);
				if(parseInt(supplyQnty)>parseInt(availableQnty))
				{ 
					var diff=parseInt(supplyQnty)-parseInt(availableQnty);
					document.getElementById("balanceQnty_"+i).value=diff;
					document.getElementById("qntyStatus_"+i).value=1;
					var newSupplyQnty=diff;
					jQuery("#addNew").show();
				}
				else
				{
					var diff=parseInt(availableQnty)-parseInt(supplyQnty);
					document.getElementById("balanceQnty_"+i).value=diff;
					document.getElementById("qntyStatus_"+i).value=0;
					jQuery("#addNew").hide();	
				}
			}
		}
		else
		{
			var sstatus=document.getElementById("sstatus_"+i).value;
			if(sstatus!='N')
			{
				if(newSupplyQnty!='')
				{	var availableQnty=document.getElementById("availableQnty_"+i).value;
					if(parseInt(newSupplyQnty)>parseInt(availableQnty))
					{
						var diff=parseInt(newSupplyQnty)-parseInt(availableQnty);
						document.getElementById("balanceQnty_"+i).value=diff;
						document.getElementById("qntyStatus_"+i).value=1;
						newSupplyQnty=diff;
						jQuery("#addNew").show();
					}
					else
					{
						var diff=parseInt(availableQnty)-parseInt(newSupplyQnty);
						document.getElementById("balanceQnty_"+i).value=diff;
						document.getElementById("qntyStatus_"+i).value=0;
						jQuery("#addNew").hide();
					}
				}
			}
		}
	}
}

function checkCertificate(rowCnt)
{	var saveStatus='1';
	//document.getElementById("availableQnty_0").value
	var certificateSize=document.getElementById("certificateSize").value;
	//alert(certificateSize);
	for(i=0; i<certificateSize; i++)
	{
		var status=document.getElementById("sstatus_"+i).value;
		if(status!='N')
		{
			var certificateNo=document.getElementById("certificateNo_"+i).value;
			if(certificateNo=="")
			{
				alert("Please select certificate No");
				document.getElementById("certificateNo_"+i).focus;	
				saveStatus='0';
			}
			var availableQnty=document.getElementById("availableQnty_"+i).value;
			if((certificateNo!="" && availableQnty=="") || (certificateNo!="" && availableQnty=='0'))
			{
				alert("pht certificate with out available qnty cannot be used");
				document.getElementById("availableQnty_"+i).focus;
				saveStatus='0';
			}
			
		}
	}
	if(saveStatus=='1')
	{
		validateRepeatIssuance(rowCnt);
	}
}

function validateRepeatIssuance(rowCnt)
{
//alert('aaa');
	var result='';
	if (Array.indexOf != 'function') {  
	Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
		for (var i = s; i < this.length; i++) {   
		if (f === this[i]) return i; 
		}    
		return -1;  
		}
	}
		
	var vd = document.getElementById("certificateSize").value;
	var prevOrders = 0;
	var arry = new Array();
	var arriy=0;
	for( l=0; l<vd; l++ )	
	{
	    var status = document.getElementById("sstatus_"+l).value;
	    if (status!='N') 
	    {
			var dv = document.getElementById("certificateNo_"+l).value;	
			if (arry.indexOf(dv) != -1 )	
			{
				alert("Cerificate No  Cannot be duplicate.");
				document.getElementById("certificateNo_"+l).focus();
				//return false;
				result+=1;
			}
			else
			{
				result+=2;
			}
			if(l<(parseInt(vd)-1))
			{
				result+=',';
			}
			arry[arriy++]=dv;
		}
	}
	
	if(result.indexOf('1')>=0)
	{
		//cannot save data
	}
	else
	{	var tag=[];
		//alert("success");
		var supplierId=document.getElementById("supplierId").value;
		var pondId=document.getElementById("pondId").value;
		var speciesId=document.getElementById("speciesId").value;
		var supplyQnty=document.getElementById("supplyQnty").value;
		var data_sheet_date=document.getElementById("data_sheet_date").value;
		var rm_lot_id=document.getElementById("rm_lot_id").value;
		var certificateSize=document.getElementById("certificateSize").value;
		//alert(supplierId+'---'+pondId+'---'+speciesId+'---'+supplyQnty+'---'+data_sheet_date+'---'+rm_lot_id+'---'+certificateSize);
		var tagDetail={"Supplier":supplierId,"Pond":pondId,"Species":speciesId,"SupplyQnty":supplyQnty,"DatasheetDate":data_sheet_date,"RmLotId":rm_lot_id,"CertificateSize":certificateSize};
		//console.log(tagDetail);
		var itemArray =[]; 
		for(i=0; i<certificateSize; i++)
		{	
			var sstatus=document.getElementById("sstatus_"+i).value;
			if(sstatus!="N")
			{
				var certificateNo=document.getElementById("certificateNo_"+i).value;
				var availableQnty=document.getElementById("availableQnty_"+i).value;
				var balanceQnty=document.getElementById("balanceQnty_"+i).value;
				var qntyStatus=document.getElementById("qntyStatus_"+i).value;
				//alert(certificateNo+"---"+availableQnty+"---"+balanceQnty+"---"+qntyStatus);
				var tagValue={"RowCnt":i,"certificateNo":certificateNo,"availableQnty":availableQnty,"balanceQnty":balanceQnty,"qntyStatus":qntyStatus};
				itemArray.push(tagValue);
			}
		}
		
		var ModifiedJsonData ='{"tag":'+JSON.stringify(tagDetail)+',"items": '+JSON.stringify(itemArray)+'  }';
		document.getElementById("phtTagData_"+rowCnt).value=ModifiedJsonData;
		$('#dialog').dialog('close');

	}
	//return true;
}
		


