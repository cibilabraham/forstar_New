function processCodeLoad(formObj)
{
	showFnLoading(); 
	formObj.form.submit();
}

function totPercentage()
{

	var totalPer=0;
	var secondaryRowCount=document.getElementById("hidSecondaryRowCount").value ;
	
	for(i=0; i<secondaryRowCount; i++)
	{
		var sstatus=document.getElementById("sstatus_"+i).value 
		if(sstatus!="N")
		{
			var percentage=document.getElementById("percentage_"+i).value ;
			if(percentage!="")
			{
				totalPer+=parseFloat(percentage);
			}
		}
	}
	document.getElementById("totalPercentage").value =Math.round(totalPer);
}

function addNew(tableId,editProcurmentId,fish1,processCode1, percentage,gradeName1,mode)
{
	var tbl			= document.getElementById(tableId);
	var lastRow		= tbl.rows.length;
	var iteration		= lastRow+1;
	var row			= tbl.insertRow(lastRow);
	row.height		= "22";
	row.className 		= "whiteRow";
	row.id 			= "srow_"+fieldvalue;

	var cell1			= row.insertCell(0);
	var cell2			= row.insertCell(1);
	var cell3			= row.insertCell(2);
	var cell4			= row.insertCell(3);
	var cell5			= row.insertCell(4);

	cell1.className	=	"fieldName"; cell1.align = 'left'; 
	cell2.className	=	"fieldName"; cell2.align = "center";
	cell3.className	=	"fieldName"; cell3.align = 'center';
	cell4.className	=	"fieldName"; cell4.align = "center"; cell4.noWrap = "true";
	cell5.className	=	"fieldName"; cell5.align = "center"; 

	var ds = "N";	
	var imageButton = "<a href='###' onClick=\"setSecondaryStatus('"+fieldvalue+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";

	var hiddenFields = "<input name='sstatus_"+fieldvalue+"' type='hidden' id='sstatus_"+fieldvalue+"' value=''><input name='IsFromDB_"+fieldvalue+"' type='hidden' id='IsFromDB_"+fieldvalue+"' value='"+ds+"'><input type='hidden' name='srmId_"+fieldvalue+"' id='srmId_"+fieldvalue+"' value='"+editProcurmentId+"'>";
	
	var fish= "<select name='fish_"+fieldvalue+"' Style='display:display;' id='fish_"+fieldvalue+"' tabindex=1  onchange=\"xajax_getProcessCode(document.getElementById('fish_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''>--select--</option>";
	<?php 
		foreach($fishRecs as $fr)
		{
			//alert($sr[0]);
			$fishId		=	$fr[0];
			$fishName	=	stripSlash($fr[1]);
		?>
			if(fish1=="<?=$fishId?>") var sel="Selected";
			else var sel = "";
			fish+="<option value=\"<?=$fishId?>\" "+sel+"><?=$fishName?></option>";
        <? }
		?>	
		fish += "</select>";

	var processCode= "<select name='processCode_"+fieldvalue+"' Style='display:display;' id='processCode_"+fieldvalue+"' tabindex=1  onchange=\"xajax_getGrade(document.getElementById('fish_"+fieldvalue+"').value,document.getElementById('processCode_"+fieldvalue+"').value,"+fieldvalue+",''); \"  ><option value=''>--select--</option>";
	<?php 
		foreach($processCodeRecs as $pr)
		{
			//alert($sr[0]);
			$processCodeId		=	$pr[0];
			$processCode	=	stripSlash($pr[1]);
		?>
			if(processCode1=="<?=$processCodeId?>") var sel="Selected";
			else var sel = "";
			processCode+="<option value=\"<?=$processCodeId?>\" "+sel+"><?=$processCode?></option>";
        <? }
		?>	
		processCode += "</select>";

	var grade= "<select name='grade_"+fieldvalue+"' Style='display:display;' id='grade_"+fieldvalue+"' tabindex=1    ><option value=''>--select--</option>";
	<?php 	
		foreach($gradeRecs as $gradeval)
		{
			foreach($gradeval as $grd)
			{
				//alert($sr[0]);
				$gradeId		=	$grd[1];
				$gradeValue	=	stripSlash($grd[2]);
	?>
		if(gradeName1=="<?=$gradeId?>") var sel="Selected";
		 else var sel = "";
					  
   grade+="<option value=\"<?=$gradeId?>\" "+sel+"><?=$gradeValue?></option>";
   <? }
	}
	?>	
	grade += "</select>";
	
	cell1.innerHTML	= fish;
	cell2.innerHTML	= processCode;
	cell3.innerHTML	= grade;
	cell4.innerHTML	= "<input name='percentage_"+fieldvalue+"' type='text' id='percentage_"+fieldvalue+"' value='"+percentage+"' size='15'  onkeyUp='totPercentage();' style='text-align:right; '/>%";
	cell5.innerHTML = imageButton+hiddenFields;
	if(mode=="addmode")
	{
	//xajax_rmProcurmentSupplierName(document.getElementById('selRMSupplierGroup').value,fieldvalue,'');
	}
	fieldvalue		= parseInt(fieldvalue)+1;
	document.getElementById("hidSecondaryRowCount").value = fieldvalue;
	
}


function setSecondaryStatus(id)
{
	if (confirmRemoveItem())
	{
	
		document.getElementById("sstatus_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("srow_"+id).style.display = 'none'; 	
		 totPercentage();
	}
	return false;
}



function validateAddSecondaryProcessCode(form)
{		
	var name		=	form.name.value;
	
	if (name=="") {
		alert("Please select a name.");
		form.name.focus();
		return false;
	}
	
	var secondaryCount	=	document.getElementById("hidSecondaryRowCount").value;

		var scount = 0;
		for (i=0; i<secondaryCount; i++)
		{
		   var status = document.getElementById("sstatus_"+i).value;		    
	    	 if (status!='N') 
		    {
				var fish		=	document.getElementById("fish_"+i);
				var processCode		=	document.getElementById("processCode_"+i);
				var grade		=	document.getElementById("grade_"+i);
				var percentage=document.getElementById("percentage_"+i);

				if( fish.value == "" )
				{
					alert("Please Select a Fish.");
					fish.focus();
					return false;
				}	

				if( processCode.value == "" )
				{
					alert("Please Select a ProcessCode");
					processCode.focus();
					return false;
				}

				if( grade.value == "" )
				{
					alert("Please Select a grade");
					grade.focus();
					return false;
				}

				if( percentage.value == "" )
				{
					alert("Please enter Percentage");
					percentage.focus();
					return false;
				}
			} else {
			scount++;
			}
		 }
	var totalPercentage=	document.getElementById("totalPercentage").value;

	if(!validateRepeatIssuance()){
		return false;
	}

	if(Math.round(totalPercentage)!="100")
	{
		alert("Total percentage must be 100");
		return false;
	}

	if(!confirmSave()){
			return false;
	}

	return true;
}


//Validate repeated
function validateRepeatIssuance()
{
//alert('aaa');
	if (Array.indexOf != 'function') {  
	Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
		for (var i = s; i < this.length; i++) {   
		if (f === this[i]) return i; 
		}    
		return -1;  
		}
	}
	
	var secondaryCount = document.getElementById("hidSecondaryRowCount").value;
	var prevOrders = 0;
	
	var arry = new Array();
	var arriy=0;
	for( l=0; l<secondaryCount; l++ )	{
	    var status = document.getElementById("sstatus_"+l).value;
	    if (status!='N') 
	    {
		var fish = document.getElementById("fish_"+l).value;	
		var processCode = document.getElementById("processCode_"+l).value;	
		var grade = document.getElementById("grade_"+l).value;	
		var dv=fish+','+processCode+','+grade;
		if ( arry.indexOf(dv) != -1 )	{
			alert("Fish,Process code and Grade combination cannot be duplicate.");
			return false;
		}
		arry[arriy++]=dv;
            }
	}
	
	return true;
}
		








































function validateAddProcessCode(form)
{		
	var fishId		=	form.processCodeFish.value;
	var code		=	form.processCode.value;
	var basketWt 		=	form.processBasketWt.value;	
	var available		=	form.available.value;	
	var hidAvailable	=	form.hidAvailable.value;
	var hidFrozenAvailable	=	form.hidFrozenAvailable.value;	
	var copyFish		=	form.selCopyFrom.value;
	var copyCode		=	form.selProcessCode.value;	

	if (fishId=="") {
		alert("Please select a fish.");
		form.processCodeFish.focus();
		return false;
	}

	if (code=="") {
		alert("Please enter a Process Code.");
		form.processCode.focus();
		return false;
	}	
	
	if (copyFish=="") {		
		if ( basketWt=="" ) {
			alert("Please enter Basket Weight.");
			form.processBasketWt.focus();
			return false;
		}
			
		if (available=="") {
			alert("Please select any Received option.");
			form.available.focus();
			return false;
		}
			
		if (hidAvailable=='G' || hidAvailable=='B') {
			var gradeUnitRaw	=	form.gradeUnitRaw.value;
			//var selGrade		=	document.getElementById("selGrade").value;
			var selGrade		=	document.getElementById("selRawGrade").value; // imploded value
			
			if(selGrade==""){
				alert("Please select one or more Grades for Raw");
				form.selGrade.focus();
				return false;
			}
			
			if(gradeUnitRaw==""){
				alert("Please select Grade Unit for Raw Material");
				form.gradeUnitRaw.focus();
				return false;
			}
		
		}
		if(hidAvailable=='C' || hidAvailable=='B'){
			var countUnitRaw	=	form.countUnitRaw.value;
			if(countUnitRaw==""){
				alert("Please select Count Unit for Raw Material");
				form.countUnitRaw.focus();
				return false;
			}
			
		
		}
		
			if(hidFrozenAvailable=='G' || hidFrozenAvailable=='B'){
				
				var gradeUnitFrozen		=	form.gradeUnitFrozen.value;
				//var selGradeFrozen		=	document.getElementById("selGradeFrozen").value;
				var selGradeFrozen		=	document.getElementById("selFrozenGrade").value;				
			
				if(selGradeFrozen==""){
						alert("Please select one or more  Grades for Frozen");
						form.selGradeFrozen.focus();
						return false;
				}
				if(gradeUnitFrozen==""){
					alert("Please select Grade Unit for Frozen Material");
					form.gradeUnitFrozen.focus();
					return false;
				}
			}
			
			if(hidFrozenAvailable=='C' || hidFrozenAvailable=='B'){
				
				var countUnitFrozen	=	form.countUnitFrozen.value;
				if(countUnitFrozen==""){
					alert("Please select Count Unit for Frozen Material");
					form.countUnitFrozen.focus();
					return false;
				}
				
			} 			
	} else { 	
		if (copyFish=="") {
			alert("Please select a Fish for copy the data.");
			form.selCopyFrom.focus();
			return false;
		}
		if (copyCode=="") {
			alert("Please select a Process Code.");
			form.selProcessCode.focus();
			return false;
		}
	}
	if(!confirmSave()){
			return false;
	}
	return true;
}




function validateEditProcessCode(form)
{		
	var fishId				=	form.processCodeFish.value;
	var code				=	form.processCode.value;
	var basketWt 			=	form.processBasketWt.value;	
	var available			=	form.available.value;	
	var hidAvailable		=	form.hidAvailable.value;	
	var hidFrozenAvailable	=	form.hidFrozenAvailable.value;

	if (fishId=="") {
		alert("Please select a fish.");
		form.processCodeFish.focus();
		return false;
	}

	if (code=="") {
		alert("Please enter a Process Code.");
		form.processCode.focus();
		return false;
	}
	if (basketWt=="") {
		alert("Please enter Basket Weight.");
		form.processBasketWt.focus();
		return false;
	}
		if( available=="" )
		{
			alert("Please select one availble option.");
			form.available.focus();
			return false;
		}
		
		if(hidAvailable=='G' || hidAvailable=='B'){
				var gradeUnitRaw	=	form.gradeUnitRaw.value;
				//var selGrade		=	document.getElementById("selGrade").value;
				var selGrade		=	document.getElementById("selRawGrade").value; // imploded value
			if(selGrade==""){
				alert("Please select one or more Grades for Raw");
				form.selGrade.focus();
				return false;
			}
			
			if(gradeUnitRaw==""){
				alert("Please select Grade Unit for Raw Material");
				form.gradeUnitRaw.focus();
				return false;
			}
		
		}
		if(hidAvailable=='C' || hidAvailable=='B'){
			var countUnitRaw	=	form.countUnitRaw.value;
			if(countUnitRaw==""){
				alert("Please select Count Unit for Raw Material");
				form.countUnitRaw.focus();
				return false;
			}
			
		
		}
		
			if(hidFrozenAvailable=='G' || hidFrozenAvailable=='B'){
				
				var gradeUnitFrozen		=	form.gradeUnitFrozen.value;
				//var selGradeFrozen		=	document.getElementById("selGradeFrozen").value;
				var selGradeFrozen		=	document.getElementById("selFrozenGrade").value;
			
				if(selGradeFrozen==""){
						alert("Please select one or more  Grades for Frozen");
						form.selGradeFrozen.focus();
						return false;
				}
				if(gradeUnitFrozen==""){
					alert("Please select Grade Unit for Frozen Material");
					form.gradeUnitFrozen.focus();
					return false;
				}
			}
			
			if(hidFrozenAvailable=='C' || hidFrozenAvailable=='B'){
				
				var countUnitFrozen	=	form.countUnitFrozen.value;
				if(countUnitFrozen==""){
					alert("Please select Count Unit for Frozen Material");
					form.countUnitFrozen.focus();
					return false;
				}
			}
	
	if(!confirmSave()){
			return false;
	}
	return true;
}



function disable(form)
{
	var copyFrom	=	form.selCopyFrom.value;
	form.processBasketWt.disabled 	= 	true;
	form.available.disabled			=	true;
	form.frozenAvailable.disabled	=	true;
	form.gradeUnitFrozen.disabled	=	true;
	document.getElementById("selGradeFrozen").disabled=true;
	
	if(copyFrom==""){
		enable(form);
	}
}

function enable(form)
{
	form.processBasketWt.disabled 	= 	false;
	form.available.disabled			=	false;
	form.frozenAvailable.disabled	=	false;
	form.gradeUnitFrozen.disabled	=	false;
	document.getElementById("selGradeFrozen").disabled=false;
}


	// Moving values from one selection box to another Starts here
	function selectNone(list1,list2)
	{
		list1.selectedIndex = -1;
		list2.selectedIndex = -1;
		addIndex = -1;
		selIndex = -1;
	}
	
	function addAll(availableList, selectedList, selectType)
	{
		var len = availableList.length -1;
		for(i=len; i>0; i--) {
			selectedList.appendChild(availableList.item(i));
		}
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);
		sortList(selectedList);
	}
	
	function addAttribute(availableList, selectedList, selectType)
	{
		var addIndex = availableList.selectedIndex;
		if(addIndex <= 0) return;
		selectedList.appendChild(availableList.options.item(addIndex));
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);
		
		sortList(selectedList);
	}
	
	
	function delAttribute(availableList, selectedList, selectType)
	{
		var selIndex = selectedList.selectedIndex;
		if(selIndex <=0) return;
			if (!chkGradeInUse(selectedList.value)) {	
				availableList.appendChild(selectedList.options.item(selIndex))
			} else selectedList.item(selIndex).style.color="Red";	
			
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);	

		sortList(availableList);
	}
	
	function delAll(availableList, selectedList, selectType)
	{
		var len = selectedList.length -1;
		for(i=len; i>0; i--){
			if (!chkGradeInUse(selectedList.options[i].value)) {
				availableList.appendChild(selectedList.item(i));
			} else selectedList.item(i).style.color="Red";	
		}
		selectNone(selectedList,availableList);
		if (selectType=='R') selRawArrVal(selectedList);
		else selFrznArrVal(selectedList);	

		sortList(availableList);	
	}
	
	
	function selRawArrVal(selectedList)
	{
		var len = selectedList.length -1;
		var grArray = new Array();
		for (var i=0; i<len; i++) {
			grArray[i] = selectedList.options[i+1].value;		
		}
		selGrade = implode(",",grArray);
	
		document.getElementById("selRawGrade").value = selGrade;
	}

	// Frozen imploded value (grade seperation)
	function selFrznArrVal(selectedList)
	{
		var len = selectedList.length -1;
		var grArray = new Array();
		for (var i=0; i<len; i++) {
			grArray[i] = selectedList.options[i+1].value;		
		}
		selGrade = implode(",",grArray);
	
		document.getElementById("selFrozenGrade").value = selGrade;
	}
	/*Moving values from one selection box to another Ends here*/

	// Check Grade rec using any where
	function chkGradeInUse(gradeId)
	{
		var processCodeId = document.getElementById('hidProcessCodeId').value;
		if (processCodeId) return xajax_chkPCGradeUsage(processCodeId, gradeId);
		else return false;
	}

	// Sort Selection list Starts here
	function compareOptionText(a,b) 
	{
		/*	* return >0 if a>b
			* 0 if a=b
			* <0 if a<b
			*/
		// textual comparison
		return a.text!=b.text ? a.text<b.text ? -1 : 1 : 0;
		// numerical comparison
		// return a.text - b.text;
      	}
      	function sortList(list) 
	{
	      var items = list.options.length;
	      // create array and make copies of options in list		
	      	var tmpArray = new Array();
		var ci = 0;
	      	for (var i=1; i<items; i++ ) {
		      if (list.options[i].value!="") {
				tmpArray[ci] = new Option(list.options[i].text,list.options[i].value);
				ci++;
			}
		      // sort options using given function
		}
		
		tmpArray.sort(compareOptionText);
		//list.length=0;
		list.options[0] = new Option(list.options[0].text,list.options[0].value);
		// make copies of sorted options back to list		
		for (var j=0; j<tmpArray.length; j++) {		
			if (tmpArray[j].value!="")  list.options[j+1] = new Option(tmpArray[j].text,tmpArray[j].value);			
		}	
	}
	// Sort Selection list Ends here

