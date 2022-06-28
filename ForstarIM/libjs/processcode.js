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


	function processCodeLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}