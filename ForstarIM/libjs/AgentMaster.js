function validateShippingCompanyMaster(form)
{
	var companyName		= form.companyName.value;	
	var selCity		= form.selCity.value;
	var state		= form.state.value;
	var selCustomerId	= document.getElementById("selCustomerId").value;
	var email		= document.getElementById("email").value;

	
	if (companyName=="") {
		alert("Please enter a agent company name.");
		form.companyName.focus();
		return false;
	}

	if (selCity=="") {
		alert("Please select a city.");
		form.selCity.focus();
		return false;
	}

	if (state=="") {
		alert("Please select a state.");
		form.state.focus();
		return false;
	}

	if (email!="") {
		if(!checkemail(email)) {
			document.getElementById("email").focus();
			 return false;
		}
	}

	if (selCustomerId=="") {
		alert("Please select atleast one customer.");
		document.getElementById("selAllCustomer").focus();
		return false;
	}

	
	/*
	var mode   = document.getElementById("hidMode").value; // Mode =1 : addmode, mode =2 : edit Mode
	var rowCount	= document.getElementById("hidTableRowCount").value;
	var itemsSelected = false;
		
	if (rowCount>0) {
		for (i=0; i<rowCount; i++) {
			var rowStatus = document.getElementById("status_"+i).value;
			if (rowStatus!='N') {
				var portName = document.getElementById("portName_"+i);
					
				if (portName.value=="") {
					alert("Please enter a port name.");
					portName.focus();
					return false;
				}			
					
				if (portName.value!="") {
					itemsSelected = true;
				}			
			}
		    }  // For Loop Ends Here
		} // Row Count checking End

	if (itemsSelected==false) {
		alert("Please add atleast one item");
		return false;
	}
	*/

	if (!validateItemRepeat()) {
		return false;
	}
	

	if (!confirmSave()) {
		return false;
	}
	return true;
}


// ADD MULTIPLE Item- ADD ROW START
function addNewRow(tableId, shipCompanyContactId, personName, designation, role, contactNo)
{
	var tbl		= document.getElementById(tableId);
	var lastRow	= tbl.rows.length;
	// alert(lastRow);
	var row		= tbl.insertRow(lastRow);
	
	row.height	= "28";
	row.className 	= "whiteRow";
	row.align 	= "center";
	row.id 		= "row_"+fieldId;	
	
	var cell1	= row.insertCell(0);
	var cell2	= row.insertCell(1);
	var cell3	= row.insertCell(2);
	var cell4	= row.insertCell(3);
	var cell5	= row.insertCell(4);
		
	cell1.className	= "listing-item"; cell1.align	= "center";
	cell2.className	= "listing-item"; cell2.align	= "center";
	cell3.className	= "listing-item"; cell3.align	= "center";
	cell4.className	= "listing-item"; cell4.align	= "center";
	cell5.className	= "listing-item"; cell5.align	= "center";
	
		
	var ds = "N";	
	//if( fieldId >= 1) 
	var imageButton = "<a href='###' onClick=\"setRowItemStatus('"+fieldId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
	//else var imageButton = "&nbsp;&nbsp;&nbsp;&nbsp;";

	var hiddenFields = "<input name='status_"+fieldId+"' type='hidden' id='status_"+fieldId+"' value=''><input name='IsFromDB_"+fieldId+"' type='hidden' id='IsFromDB_"+fieldId+"' value='"+ds+"'><input type='hidden' name='shipCompanyContactId_"+fieldId+"' id='shipCompanyContactId_"+fieldId+"' value='"+shipCompanyContactId+"'>";

	cell1.innerHTML	= "<input name='personName_"+fieldId+"' type='text' id='personName_"+fieldId+"' value=\""+unescape(personName)+"\" size='24'>";
	cell2.innerHTML	= "<input name='designation_"+fieldId+"' type='text' id='designation_"+fieldId+"' value=\""+unescape(designation)+"\" size='24'>";
	cell3.innerHTML	= "<input name='role_"+fieldId+"' type='text' id='role_"+fieldId+"' value=\""+unescape(role)+"\" size='24'>";
	cell4.innerHTML	= "<input name='contactNo_"+fieldId+"' type='text' id='contactNo_"+fieldId+"' value=\""+unescape(contactNo)+"\" size='24'>";
	cell5.innerHTML = imageButton+hiddenFields;	
	//alert(fieldId);
	fieldId		= parseInt(fieldId)+1;	
	document.getElementById("hidTableRowCount").value = fieldId;		
}

function setRowItemStatus(id)
{
	if (confirmRemoveItem()) {
		document.getElementById("status_"+id).value = document.getElementById("IsFromDB_"+id).value;
		document.getElementById("row_"+id).style.display = 'none';		
	}
	return false;
}

// ------------------------------------------------------
// Duplication check starts here
// ------------------------------------------------------
var cArr = new Array();
var cArri = 0;	
function validateItemRepeat()
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
	var pArr	= new Array();	
	var pa		= 0;
	for (j=0; j<rc; j++) {
		var status = document.getElementById("status_"+j).value;
		if (status!='N') {
			var personName = document.getElementById("personName_"+j).value;
					
			if (pArr.indexOf(personName)!=-1) {
				alert("Contact cannot be duplicate.");
				document.getElementById("personName_"+j).focus();
				return false;	
			}						
			pArr[pa++]	= personName;					
		}
	}	
	return true;
}

// ------------------------------------------------------
// Duplication check Ends here
// ------------------------------------------------------

		
	function enableCmdButton(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableCmdButton(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}
	

	// Moving values from one selection box to another Starts here
	function selectNone(list1,list2)
	{
		list1.selectedIndex = -1;
		list2.selectedIndex = -1;
		addIndex = -1;
		selIndex = -1;
	}
	
	function addAll(availableList, selectedList)
	{
		var len = availableList.length -1;
		for(i=len; i>0; i--) {
			selectedList.appendChild(availableList.item(i));
		}
		selectNone(selectedList,availableList);
		selArrVal(selectedList);
		sortList(selectedList);
	}
	
	function addAttribute(availableList, selectedList)
	{
		var addIndex = availableList.selectedIndex;
		if(addIndex <= 0) return;
		selectedList.appendChild(availableList.options.item(addIndex));
		selectNone(selectedList,availableList);
		selArrVal(selectedList);
		sortList(selectedList);
	}
	
	
	function delAttribute(availableList, selectedList)
	{
		var selIndex = selectedList.selectedIndex;
		if(selIndex <=0) return;
		availableList.appendChild(selectedList.options.item(selIndex));
		/*
			if (!chkGradeInUse(selectedList.value)) {	
				availableList.appendChild(selectedList.options.item(selIndex))
			} else selectedList.item(selIndex).style.color="Red";	
		*/	
		selectNone(selectedList,availableList);
		selArrVal(selectedList);
		sortList(availableList);
	}
	
	function delAll(availableList, selectedList)
	{
		var len = selectedList.length -1;
		for(i=len; i>0; i--){
			availableList.appendChild(selectedList.item(i));
			/*
			if (!chkGradeInUse(selectedList.options[i].value)) {
				availableList.appendChild(selectedList.item(i));
			} else selectedList.item(i).style.color="Red";	
			*/
		}
		selectNone(selectedList,availableList);
		selArrVal(selectedList);
		sortList(availableList);	
	}
	
	
	function selArrVal(selectedList)
	{
		var len = selectedList.length -1;
		var grArray = new Array();
		for (var i=0; i<len; i++) {
			grArray[i] = selectedList.options[i+1].value;		
		}
		selGrade = implode(",",grArray);
	
		document.getElementById("selCustomerId").value = selGrade;
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

	function closeLightBox()
	{
		closeModalBox("spo-box", "spo-filter");
	}

	function loadCustomer()
	{
		ifId=gmobj("addNewIFrame");
		ifId.src="Customer.php?returnUrl=AGENTM";
		openModalBox("", 1, "spo-filter", "spo-box", "");
	}

	function reloadCustomer()
	{
		var agentId = document.getElementById("hidAgentId").value;
		xajax_getCustomers(agentId);
	}