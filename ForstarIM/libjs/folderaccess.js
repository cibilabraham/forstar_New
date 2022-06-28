var selectedf = false;

function validateAddFolder()
{ 
	var checkSelect="";
	var rowCount2		= document.getElementById("hidRowCount2").value;
	var hidRowCount1	= "hidRowCount1_";
	var selAccess		= "selAccess_";
	for(var i=1; i<=rowCount2; i++)	
	{
		var rowCount1	=	document.getElementById(hidRowCount1+i).value;
		for (var j=0; j<=rowCount1; j++) 
		{
			var accessId	=	selAccess+i+"_"+j;
			//var fieldPrefix	=	selFunction+i+"_";
			if(document.getElementById(accessId).checked) 
			{
				checkSelect	= true;
			}
		}
	}
	if (checkSelect==false) {
		alert("Please select atleast one Function");
		return false;
	}

	if (!confirmSave()) 
		return false;
	else return true;	
}


function anyChecked(rowCount,fieldPrefix)
{
	for ( i=0; i<=rowCount; i++ ) {
		if (document.getElementById(fieldPrefix+i).checked) {
			//alert(fieldPrefix+i);
			return true;
		}		
	}
	return false;
}




/*function selAllinv(i,j)
{
if (document.getElementById("selAccess_"+i+"_"+j).checked) {
	document.getElementById("selAccess1").checked = true;
	document.getElementById("selAccess2").checked = true;
	document.getElementById("selAccess3").checked = true;
}
else
{
	document.getElementById("selAccess1").checked = false;
	document.getElementById("selAccess2").checked = false;
	document.getElementById("selAccess3").checked = false;
}
}
*/ 

function selindv(i,j)
{
if ((!document.getElementById("supdChkbx2").checked) || (!document.getElementById("supdChkbx3").checked) || (!document.getElementById("supdChkbx4").checked)) {

document.getElementById("supdChkbx1").checked = false;


}
else if ((document.getElementById("supdChkbx2").checked) && (document.getElementById("supdChkbx3").checked) && (document.getElementById("supdChkbx4").checked))
{
document.getElementById("supdChkbx1").checked = true;
}

}

function assignval(i,j)
{
	var selAccess	=	"selAccess_"+i+"_"+j;
	var functionId="functionId_"+i+"_"+j;
	//alert("hai");
	var st=0;
	if (document.getElementById(functionId).checked)
	{
	document.getElementById(selAccess).checked=true;
	st=1;
	}
	else{
	document.getElementById(selAccess).checked=false;
	document.getElementById("supdChkbx1").checked = false;
	document.getElementById("supdChkbx2").checked = false;
	document.getElementById("supdChkbx3").checked = false;
	document.getElementById("supdChkbx4").checked = false;

	st=2;
	}
	
	
}

function selAllInv(i,j)
	{
		//alert("Inv");
		var CheckAll	=	"CheckAll1";
		var selAccess	=	"selAccess1";
		var selAdd	=	"selAdd1";
		var selEdit	=	"selEdit1";
		var selDelete	=	"selDelete1";
		var selPrint	=	"selPrint1";
		var selConfirm	=	"selConfirm1";
		var selReEdit	=	"selReEdit1";
		var selCpnySpeci=	"selCompanySpecific1";
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(CheckAll).checked) {
			document.getElementById(selAccess).checked = true;
			document.getElementById(selAdd).checked = true;
			document.getElementById(selEdit).checked = true;
			document.getElementById(selDelete).checked = true;
			document.getElementById(selPrint).checked = true;
			document.getElementById(selConfirm).checked = true;
			document.getElementById(selReEdit).checked = true;
			document.getElementById(selCpnySpeci).checked = true;
			//document.getElementById(selActive).checked = true;
		} else {
			if (!document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (!document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (!document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (!document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (!document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (!document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (!document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;				
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		// selected check
		//checkSel(i,j);		
	}



function checkSelInv(i,j)
	{
		
		var selFunction	= 	"supdChkbx1";
		var CheckAll	=	"CheckAll1";
		var selAccess	=	"selAccess1";
		var selAdd	=	"selAdd1";
		var selEdit	=	"selEdit1";
		var selDelete	=	"selDelete1";
		var selPrint	=	"selPrint1";
		var selConfirm	=	"selConfirm1";
		var selReEdit	=	"selReEdit1";
		var selCpnySpeci=	"selCompanySpecific1";
				
		if (document.getElementById(selAdd).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selDelete).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selPrint).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selConfirm).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selReEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selCpnySpeci).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;			
		}
		/*if(document.getElementById(selActive).checked){
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
		}*/
		// Un select check all
		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}	
	}






function checkSelFrn(i,j)
	{
		
		var selFunction	= 	"supdChkbx2";
		var CheckAll	=	"CheckAll2";
		var selAccess	=	"selAccess2";
		var selAdd	=	"selAdd2";
		var selEdit	=	"selEdit2";
		var selDelete	=	"selDelete2";
		var selPrint	=	"selPrint2";
		var selConfirm	=	"selConfirm2";
		var selReEdit	=	"selReEdit2";
		var selCpnySpeci=	"selCompanySpecific2";
				
		if (document.getElementById(selAdd).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selDelete).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selPrint).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selConfirm).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selReEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selCpnySpeci).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;			
		}
		/*if(document.getElementById(selActive).checked){
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
		}*/
		// Un select check all
		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}	
	}





function checkSelRTE(i,j)
	{
		
		var selFunction	= 	"supdChkbx3";
		var CheckAll	=	"CheckAll3";
		var selAccess	=	"selAccess3";
		var selAdd	=	"selAdd3";
		var selEdit	=	"selEdit3";
		var selDelete	=	"selDelete3";
		var selPrint	=	"selPrint3";
		var selConfirm	=	"selConfirm3";
		var selReEdit	=	"selReEdit3";
		var selCpnySpeci=	"selCompanySpecific3";
				
		if (document.getElementById(selAdd).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selDelete).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selPrint).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selConfirm).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selReEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selCpnySpeci).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;			
		}
		/*if(document.getElementById(selActive).checked){
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
		}*/
		// Un select check all
		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}	
	}











function selAllInv1(i,j)
	{
		//alert("Inv");
		var CheckAll	=	"CheckAll2";
		var selAccess	=	"selAccess2";
		var selAdd	=	"selAdd2";
		var selEdit	=	"selEdit2";
		var selDelete	=	"selDelete2";
		var selPrint	=	"selPrint2";
		var selConfirm	=	"selConfirm2";
		var selReEdit	=	"selReEdit2";
		var selCpnySpeci=	"selCompanySpecific2";
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(CheckAll).checked) {
			document.getElementById(selAccess).checked = true;
			document.getElementById(selAdd).checked = true;
			document.getElementById(selEdit).checked = true;
			document.getElementById(selDelete).checked = true;
			document.getElementById(selPrint).checked = true;
			document.getElementById(selConfirm).checked = true;
			document.getElementById(selReEdit).checked = true;
			document.getElementById(selCpnySpeci).checked = true;
			//document.getElementById(selActive).checked = true;
		} else {
			if (!document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (!document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (!document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (!document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (!document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (!document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (!document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;				
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		// selected check
		//checkSel(i,j);		
	}






function selAllInv3(i,j)
	{
		//alert("Inv");
		var CheckAll	=	"CheckAll3";
		var selAccess	=	"selAccess3";
		var selAdd	=	"selAdd3";
		var selEdit	=	"selEdit3";
		var selDelete	=	"selDelete3";
		var selPrint	=	"selPrint3";
		var selConfirm	=	"selConfirm3";
		var selReEdit	=	"selReEdit3";
		var selCpnySpeci=	"selCompanySpecific3";
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(CheckAll).checked) {
			document.getElementById(selAccess).checked = true;
			document.getElementById(selAdd).checked = true;
			document.getElementById(selEdit).checked = true;
			document.getElementById(selDelete).checked = true;
			document.getElementById(selPrint).checked = true;
			document.getElementById(selConfirm).checked = true;
			document.getElementById(selReEdit).checked = true;
			document.getElementById(selCpnySpeci).checked = true;
			//document.getElementById(selActive).checked = true;
		} else {
			if (!document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (!document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (!document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (!document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (!document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (!document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (!document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;				
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		// selected check
		//checkSel(i,j);		
	}


	function selAll(i,j)
	{
		var CheckAll	=	"CheckAll_"+i+"_"+j;
		var selAccess	=	"selAccess_"+i+"_"+j;
		var selAdd	=	"selAdd_"+i+"_"+j;
		var selEdit	=	"selEdit_"+i+"_"+j;
		var selDelete	=	"selDelete_"+i+"_"+j;
		var selPrint	=	"selPrint_"+i+"_"+j;
		var selConfirm	=	"selConfirm_"+i+"_"+j;
		var selReEdit	=	"selReEdit_"+i+"_"+j;
		var selCpnySpeci=	"selCompanySpecific_"+i+"_"+j;
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(CheckAll).checked) {
			document.getElementById(selAccess).checked = true;
			document.getElementById(selAdd).checked = true;
			document.getElementById(selEdit).checked = true;
			document.getElementById(selDelete).checked = true;
			document.getElementById(selPrint).checked = true;
			document.getElementById(selConfirm).checked = true;
			document.getElementById(selReEdit).checked = true;
			document.getElementById(selCpnySpeci).checked = true;
			//document.getElementById(selActive).checked = true;
			
		} else {
			if (!document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (!document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (!document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (!document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (!document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (!document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (!document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;				
		}
		// selected check
		checkSel(i,j);		
	}

	function checkSel(i,j)
	{
		var CheckAll	=	"CheckAll_"+i+"_"+j;
		var selFunction	= 	"functionId_"+i+"_"+j;
		var selAccess	=	"selAccess_"+i+"_"+j;
		var selAdd	=	"selAdd_"+i+"_"+j;
		var selEdit	=	"selEdit_"+i+"_"+j;
		var selDelete	=	"selDelete_"+i+"_"+j;
		var selPrint	=	"selPrint_"+i+"_"+j;
		var selConfirm	=	"selConfirm_"+i+"_"+j;
		var selReEdit	=	"selReEdit_"+i+"_"+j;
		var selCpnySpeci=	"selCompanySpecific_"+i+"_"+j;
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(selAdd).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selDelete).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selPrint).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
				
		if (document.getElementById(selConfirm).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selReEdit).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
			//document.getElementById(selActive).checked	= true;
		}
		if (document.getElementById(selCpnySpeci).checked) {
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;			
		}
		/*if(document.getElementById(selActive).checked){
			document.getElementById(selFunction).checked = true;
			document.getElementById(selAccess).checked = true;
		}*/
		// Un select check all
		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}	
	}


// Show Table Row
function showTableRow(tableRowId, moduleName)	
{
	var displayRow  = false;
	var rowCount	= document.getElementById("hidRowCount1_"+tableRowId).value;
	var moduleName1;
	moduleName1="SupplierData Filteration";
	for (var j=1; j<=rowCount; j++) {		
		if (document.getElementById(tableRowId+"_"+j).style.display == "none" ) {
			document.getElementById(tableRowId+"_"+j).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId+"_"+j).style.display = "none";
		}		
	}
	if (displayRow) {
		document.getElementById("t_"+tableRowId).innerHTML = "<a href=\"javascript:void(0);\"  onClick=\"showTableRow('"+tableRowId+"','"+moduleName+"');\" class=\"expandLink\">-</a>&nbsp;"+moduleName;
	} else {
		document.getElementById("t_"+tableRowId).innerHTML = "<a href=\"javascript:void(0);\" onClick=\"showTableRow('"+tableRowId+"','"+moduleName+"');\" class=\"expandLink\">+</a>&nbsp;"+moduleName;
		/*
		document.getElementById("invfrz").style.display = "none";
		document.getElementById("invfrz1").style.display = "none";
		var tableRowId1="invfrz";
		var tableRowId2="invfrz1";
		document.getElementById("t1").innerHTML = "<a href=\"###\" onClick=\"showTableRow1('"+tableRowId1+"','"+tableRowId2+"','"+moduleName1+"');\" class=\"expandLink1\">+</a>&nbsp;"+moduleName1;
		*/
	}
	//alert(document.getElementById("t_"+tableRowId).innerHTML);
}

	function displayRoleFunctionList()
	{
		var copyRoleId = document.getElementById("copyRoleId").value;
		//var roleFnHead = document.getElementById("roleFnHead").value;
		if (copyRoleId!="") {
			document.getElementById("roleFnHead").style.display = "none";
			document.getElementById("roleFnList").style.display = "none";
		} else {
			document.getElementById("roleFnHead").style.display = "";
			document.getElementById("roleFnList").style.display = "";
		}
		
	}



function showTableRow1(tableRowId1,tableRowId2,moduleName)	
{
	//alert("hai");
	//alert(tableRowId1);
	var displayRow  = false;
	//var rowCount	= document.getElementById("hidRowCount1_"+tableRowId).value;
	var rowCount=1;
	//alert(rowCount);

	if (document.getElementById(tableRowId1).style.display == "none" ) {
			document.getElementById(tableRowId1).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId1).style.display = "none";
		}
		if (document.getElementById(tableRowId2).style.display == "none" ) {
			document.getElementById(tableRowId2).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId2).style.display = "none";
		}

		if (document.getElementById("invfrz2").style.display == "none" ) {
			document.getElementById("invfrz2").style.display = '';
			displayRow = true;
		} else {
			document.getElementById("invfrz2").style.display = "none";
		}
	/*for (var j=1; j<=rowCount; j++) {		
		if (document.getElementById(tableRowId+"_"+j).style.display == "none" ) {
			document.getElementById(tableRowId+"_"+j).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId+"_"+j).style.display = "none";
		}		
	}*/
	moduleName="Supplier Data-SubModule";
	if (displayRow) {
		document.getElementById("t1").innerHTML = "<a href=\"###\"  onClick=\"showTableRow1('"+tableRowId1+"','"+tableRowId2+"','"+moduleName+"');\" class=\"expandLink1\">-</a>&nbsp;"+moduleName;
	} else {
		document.getElementById("t1").innerHTML = "<a href=\"###\" onClick=\"showTableRow1('"+tableRowId1+"','"+tableRowId2+"','"+moduleName+"');\" class=\"expandLink1\">+</a>&nbsp;"+moduleName;
	}
	//alert(document.getElementById("t_"+tableRowId).innerHTML);
document.getElementById("flagvalue").value=1;

}

function showTableRow2(tableRowId1,tableRowId2,moduleName)	
{
	//alert("hai");
	//alert(tableRowId1);
	var displayRow  = false;
	//var rowCount	= document.getElementById("hidRowCount1_"+tableRowId).value;
	var rowCount=1;
	//alert(rowCount);

	if (document.getElementById(tableRowId1).style.display == "none" ) {
			document.getElementById(tableRowId1).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId1).style.display = "none";
		}
		if (document.getElementById(tableRowId2).style.display == "none" ) {
			document.getElementById(tableRowId2).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId2).style.display = "none";
		}

		
	/*for (var j=1; j<=rowCount; j++) {		
		if (document.getElementById(tableRowId+"_"+j).style.display == "none" ) {
			document.getElementById(tableRowId+"_"+j).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId+"_"+j).style.display = "none";
		}		
	}*/
	moduleName="Stock Entry Sub Module";
	if (displayRow) {
		document.getElementById("t2").innerHTML = "<a href=\"###\"  onClick=\"showTableRow2('"+tableRowId1+"','"+tableRowId2+"','"+moduleName+"');\" class=\"expandLink1\">-</a>&nbsp;"+moduleName;
	} else {
		document.getElementById("t2").innerHTML = "<a href=\"###\" onClick=\"showTableRow2('"+tableRowId1+"','"+tableRowId2+"','"+moduleName+"');\" class=\"expandLink1\">+</a>&nbsp;"+moduleName;
	}
	//alert(document.getElementById("t_"+tableRowId).innerHTML);
document.getElementById("flagvalue").value=1;

}


function fieldState(fld)
{
	var rows=$("#hidRowCount1_"+fld).val();
	var i=1;
	if($("#selAccess_"+fld+"_0").attr("checked") == true)
	{
		$(".selAcc_"+fld).each(function()
		{
			$("#selAccess_"+fld+"_"+i).attr("disabled",true);
			i++;
		});
		
	}
	else if($("#selAccess_"+fld+"_0").attr("checked") == false)
	{
		$(".selAcc_"+fld).each(function()
		{
			$("#selAccess_"+fld+"_"+i).attr("disabled",false);
			i++;
		});
		
	}

}

/*function chkData(k,j)
{
	if(($("#selAccess1").attr("checked") == true) || ($("#selAccess2").attr("checked") == true) || ($("#selAccess3").attr("checked") == true))
	{
		$("#selAccess_"+k+"_"+j).attr("checked",true);
	}

}*/
