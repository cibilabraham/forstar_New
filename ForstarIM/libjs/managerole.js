var selectedf = false;

function validateAddRoleFunction(form)
{
	var selModule		=	form.selModule.value;
	var selAccess		=	form.selAccess.checked;
	var selAdd			=	form.selAdd.checked;
	var selEdit			=	form.selEdit.checked;
	var selDelete		=	form.selDelete.checked;
	var selPrint		=	form.selPrint.checked;
	var selConfirm		=	form.selConfirm.checked;
	
	if (selModule=="") {
			alert("Please select a Module");
			form.selModule.focus();
			return false;
	}
	if(selAccess=="" && selAdd=="" && selEdit=="" && selDelete=="" && selPrint=="" && selConfirm=="" ) {
		alert("Please select any one control option.");
		return false;
	}	
	/*if(!confirmSave()) {
		return false;
	} else {
		return true;
	}*/

}

//Main Page validation 
function validateAddRole(form)
{
	var roleName		= form.roleName.value;
	var rowCount2		= document.getElementById("hidRowCount2").value;
	var hidRowCount1	= "hidRowCount1_";
	var selFunction		= "functionId_";
	var checkSelect		= false;
	var cpyFrom		= false;
	
	var addMode		= document.getElementById("hidAddMode").value;
	if (roleName=="") {
		alert("Please enter a role name");
		form.roleName.focus();
		return false;
	}

	if (addMode) {
		var copyRoleId = document.getElementById("copyRoleId").value;
		if (copyRoleId!="") cpyFrom = true;
	}
	if (!cpyFrom) {
		for(var i=1; i<=rowCount2; i++)	{
			var rowCount1	=	document.getElementById(hidRowCount1+i).value;
			for (var j=0; j<=rowCount1; j++) {
				var functionId	=	selFunction+i+"_"+j;
				var fieldPrefix	=	selFunction+i+"_";
				if (document.getElementById(functionId).checked) {
					checkSelect	= true;
				}
			}
		}
		if (checkSelect==false) {
			alert("Please select atleast one Function");
			return false;
		}
	}

var i1=document.getElementById("kvalue").value;
var j1=document.getElementById("jvalue").value;
var functionId="functionId_"+i1+"_"+j1;

if (document.getElementById(functionId).checked)
	{
if ((!document.getElementById("supdChkbx2").checked) && (!document.getElementById("supdChkbx3").checked) && (!document.getElementById("supdChkbx4").checked))
	{
	alert("Please select the filter option");
	return false;
	}}

	if (!confirmSave()) return false;
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

// Select all
/*
function selectAll(field)
{
	var rowCount2			=	document.getElementById("hidRowCount2").value;
	var hidRowCount1		=	"hidRowCount1_";	
	for(var i=1; i<=rowCount2; i++)	{
		var rowCount1	=	document.getElementById(hidRowCount1+i).value;
		//alert (rowCount2);
		for (var j=0; j<=rowCount1; j++) {
			var CheckAll	=	"CheckAll_"+i+"_"+j;
			var selAccess	=	"selAccess_"+i+"_"+j;
			var selAdd	=	"selAdd_"+i+"_"+j;
			var selEdit	=	"selEdit_"+i+"_"+j;
			var selDelete	=	"selDelete_"+i+"_"+j;
			var selPrint	=	"selPrint_"+i+"_"+j;
			var selConfirm	=	"selConfirm_"+i+"_"+j;
			var selReEdit	=	"selReEdit_"+i+"_"+j;
			//alert("CheckAll_"+i+"_"+j);
			//var selActive	=	"selActive_"+i+"_"+j;
				
			if (document.getElementById(CheckAll).checked) {
				document.getElementById(selAccess).checked = true;
				document.getElementById(selAdd).checked = true;
				document.getElementById(selEdit).checked = true;
				document.getElementById(selDelete).checked = true;
				document.getElementById(selPrint).checked = true;
				document.getElementById(selConfirm).checked = true;
				document.getElementById(selReEdit).checked = true;
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
				//document.getElementById(selActive).checked = false;
				}
			}
	}
	checkSelect(field);
}
*/
/*
function checkSelect(field)
{
	var rowCount2		= document.getElementById("hidRowCount2").value;
	var hidRowCount1	= "hidRowCount1_";	
	for(var i=1; i<=rowCount2; i++)	{
		var rowCount1	=	document.getElementById(hidRowCount1+i).value;
		for (var j=0; j<=rowCount1; j++) {
			var CheckAll	=	"CheckAll_"+i+"_"+j;
			var selFunction	= 	"functionId_"+i+"_"+j;
			var selAccess	=	"selAccess_"+i+"_"+j;
			var selAdd	=	"selAdd_"+i+"_"+j;
			var selEdit	=	"selEdit_"+i+"_"+j;
			var selDelete	=	"selDelete_"+i+"_"+j;
			var selPrint	=	"selPrint_"+i+"_"+j;
			var selConfirm	=	"selConfirm_"+i+"_"+j;
			var selReEdit	=	"selReEdit_"+i+"_"+j;
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
			/*if(document.getElementById(selActive).checked){
				document.getElementById(selFunction).checked = true;
				document.getElementById(selAccess).checked = true;
			}*/
			// Un select check all
/*			if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked) {
				document.getElementById(CheckAll).checked = false;
			}			
		}
	}
}
*/


function selAllSupplier(i,j)
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
			if (document.getElementById(selAccess).checked)
				document.getElementById(selAccess).checked = false;
			if (document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;	
			/*if (!document.getElementById(selAdd).checked)
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
				*/
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		checkAllData();
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
			//alert("asaS");
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

			if (document.getElementById(selAccess).checked)
				document.getElementById(selAccess).checked = false;
			if (document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;
			/*if (!document.getElementById(selAdd).checked)
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
				*/
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		// selected check
		//checkSel(i,j);
		checkAllData();
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
			if (document.getElementById(selAccess).checked)
				document.getElementById(selAccess).checked = false;
			if (document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;
		
			/*if (!document.getElementById(selAdd).checked)
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
				document.getElementById(selCpnySpeci).checked = false;	*/			
		}

		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		checkAllData();
		// selected check
		//checkSel(i,j);		
	}
	
	function setAllAccess(i,j)
	{
		if(document.getElementById("CheckAll_"+i+"_"+j).checked)
		{
			selAllSupplier(i,j);
			document.getElementById("CheckAll3").checked=true;
			document.getElementById("CheckAll2").checked=true;
			document.getElementById("CheckAll1").checked=true;
			selAllInv3('1','1');
			selAllInv1('1','1');
			selAllInv('1','1');
		}
		else
		{
			document.getElementById("CheckAll3").checked=false;
			document.getElementById("CheckAll2").checked=false;
			document.getElementById("CheckAll1").checked=false;
			selAllInv3('1','1');
			selAllInv1('1','1');
			selAllInv('1','1');
		}
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
		var functionId=	"functionId_"+i+"_"+j;
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
			document.getElementById(functionId).checked = true;
			//document.getElementById(selActive).checked = true;
			
			
		} else {
			if (document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;
			if (document.getElementById(selAccess).checked) 
				document.getElementById(selAccess).checked = false;
			if (document.getElementById(functionId).checked) 
				document.getElementById(functionId).checked = false;
			
			/*if (!document.getElementById(selAdd).checked)
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
				document.getElementById(selCpnySpeci).checked = false;	*/			
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
		document.getElementById("invfrz").style.display = "none";
		document.getElementById("invfrz1").style.display = "none";
		var tableRowId1="invfrz";
		var tableRowId2="invfrz1";
		document.getElementById("t1").innerHTML = "<a href=\"###\" onClick=\"showTableRow1('"+tableRowId1+"','"+tableRowId2+"','"+moduleName1+"');\" class=\"expandLink1\">+</a>&nbsp;"+moduleName1;
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

	//	if (document.getElementById("invfrz2").style.display == "none" ) {
	//		document.getElementById("invfrz2").style.display = '';
	//		displayRow = true;
	//	} else {
	//		document.getElementById("invfrz2").style.display = "none";
	//	}
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


function chkData(k,j)
{
	//alert("hii");
	if(($("#selAccess1").attr("checked") == true) || ($("#selAccess2").attr("checked") == true) || ($("#selAccess3").attr("checked") == true))
	{	
		$(".data").attr("checked",true);
	}

}

function checkAllData()
{
	if(($("#CheckAll1").attr("checked") == true) || ($("#CheckAll2").attr("checked") == true) || ($("#CheckAll3").attr("checked") == true))
	{	
		$(".data").attr("checked",true);
	}
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
			$("#selAdd_"+fld+"_"+i).attr("disabled",true);
			$("#selEdit_"+fld+"_"+i).attr("disabled",true);
			$("#selDelete_"+fld+"_"+i).attr("disabled",true);
			$("#selPrint_"+fld+"_"+i).attr("disabled",true);
			$("#selConfirm_"+fld+"_"+i).attr("disabled",true);
			$("#selReEdit_"+fld+"_"+i).attr("disabled",true);
			$("#selCompanySpecific_"+fld+"_"+i).attr("disabled",true);
			$("#CheckAll_"+fld+"_"+i).attr("disabled",true);

			//for supplier data-inventory 
			if($("#selAccess1").attr("disabled") == false)
			{
				$("#selAccess1").attr("disabled",true);
				$("#selAccess1").attr("checked",true);
			}
			if($("#selAdd1").attr("disabled") == false)
			{
				$("#selAdd1").attr("disabled",true);
				$("#selAdd1").attr("checked",true);
			}
			if($("#selEdit1").attr("disabled") == false)
			{
				$("#selEdit1").attr("disabled",true);
				$("#selEdit1").attr("checked",true);
			}
			if($("#selDelete1").attr("disabled") == false)
			{
				$("#selDelete1").attr("disabled",true);
				$("#selDelete1").attr("checked",true);
			}
			if($("#selPrint1").attr("disabled") == false)
			{
				$("#selPrint1").attr("disabled",true);
				$("#selPrint1").attr("checked",true);
			}
			if($("#selConfirm1").attr("disabled") == false)
			{
				$("#selConfirm1").attr("disabled",true);
				$("#selConfirm1").attr("checked",true);
			}
			if($("#selReEdit1").attr("disabled") == false)
			{
				$("#selReEdit1").attr("disabled",true);
				$("#selReEdit1").attr("checked",true);
			}
			if($("#selCompanySpecific1").attr("disabled") == false)
			{
				$("#selCompanySpecific1").attr("disabled",true);
				$("#selCompanySpecific1").attr("checked",true);
			}
			if($("#CheckAll1").attr("disabled") == false)
			{
				$("#CheckAll1").attr("disabled",true);
				$("#CheckAll1").attr("checked",true);
			}


			//for supplier data-frozen 
			if($("#selAccess2").attr("disabled") == false)
			{
				$("#selAccess2").attr("disabled",true);
				$("#selAccess2").attr("checked",true);
			}
			if($("#selAdd2").attr("disabled") == false)
			{
				$("#selAdd2").attr("disabled",true);
				$("#selAdd2").attr("checked",true);
			}
			if($("#selEdit2").attr("disabled") == false)
			{
				$("#selEdit2").attr("disabled",true);
				$("#selEdit2").attr("checked",true);
			}
			if($("#selDelete2").attr("disabled") == false)
			{
				$("#selDelete2").attr("disabled",true);
				$("#selDelete2").attr("checked",true);
			}
			if($("#selPrint2").attr("disabled") == false)
			{
				$("#selPrint2").attr("disabled",true);
				$("#selPrint2").attr("checked",true);
			}
			if($("#selConfirm2").attr("disabled") == false)
			{
				$("#selConfirm2").attr("disabled",true);
				$("#selConfirm2").attr("checked",true);
			}
			if($("#selReEdit2").attr("disabled") == false)
			{
				$("#selReEdit2").attr("disabled",true);
				$("#selReEdit2").attr("checked",true);
			}
			if($("#selCompanySpecific2").attr("disabled") == false)
			{
				$("#selCompanySpecific2").attr("disabled",true);
				$("#selCompanySpecific2").attr("checked",true);
			}
			if($("#CheckAll2").attr("disabled") == false)
			{
				$("#CheckAll2").attr("disabled",true);
				$("#CheckAll2").attr("checked",true);
			}


			//for supplier data-rte
			if($("#selAccess3").attr("disabled") == false)
			{
				$("#selAccess3").attr("disabled",true);
				$("#selAccess3").attr("checked",true);
			}
			if($("#selAdd3").attr("disabled") == false)
			{
				$("#selAdd3").attr("disabled",true);
				$("#selAdd3").attr("checked",true);
			}
			if($("#selEdit3").attr("disabled") == false)
			{
				$("#selEdit3").attr("disabled",true);
				$("#selEdit3").attr("checked",true);
			}
			if($("#selDelete3").attr("disabled") == false)
			{
				$("#selDelete3").attr("disabled",true);
				$("#selDelete3").attr("checked",true);
			}
			if($("#selPrint3").attr("disabled") == false)
			{
				$("#selPrint3").attr("disabled",true);
				$("#selPrint3").attr("checked",true);
			}
			if($("#selConfirm3").attr("disabled") == false)
			{
				$("#selConfirm3").attr("disabled",true);
				$("#selConfirm3").attr("checked",true);
			}
			if($("#selReEdit3").attr("disabled") == false)
			{
				$("#selReEdit3").attr("disabled",true);
				$("#selReEdit3").attr("checked",true);
			}
			if($("#selCompanySpecific3").attr("disabled") == false)
			{
				$("#selCompanySpecific3").attr("disabled",true);
				$("#selCompanySpecific3").attr("checked",true);
			}
			if($("#CheckAll3").attr("disabled") == false)
			{
				$("#CheckAll3").attr("disabled",true);
				$("#CheckAll3").attr("checked",true);
			}


			//for stock entry data-frozen
			if($("#selInvAccess1").attr("disabled") == false)
			{
				$("#selInvAccess1").attr("disabled",true);
				$("#selInvAccess1").attr("checked",true);
			}
			if($("#selInvAdd1").attr("disabled") == false)
			{
				$("#selInvAdd1").attr("disabled",true);
				$("#selInvAdd1").attr("checked",true);
			}
			if($("#selInvEdit1").attr("disabled") == false)
			{
				$("#selInvEdit1").attr("disabled",true);
				$("#selInvEdit1").attr("checked",true);
			}
			if($("#selInvDelete1").attr("disabled") == false)
			{
				$("#selInvDelete1").attr("disabled",true);
				$("#selInvDelete1").attr("checked",true);
			}
			if($("#selInvPrint1").attr("disabled") == false)
			{
				$("#selInvPrint1").attr("disabled",true);
				$("#selInvPrint1").attr("checked",true);
			}
			if($("#selInvConfirm1").attr("disabled") == false)
			{
				$("#selInvConfirm1").attr("disabled",true);
				$("#selInvConfirm1").attr("checked",true);
			}
			if($("#selInvReEdit1").attr("disabled") == false)
			{
				$("#selInvReEdit1").attr("disabled",true);
				$("#selInvReEdit1").attr("checked",true);
			}
			if($("#selInvCompanySpecific1").attr("disabled") == false)
			{
				$("#selInvCompanySpecific1").attr("disabled",true);
				$("#selInvCompanySpecific1").attr("checked",true);
			}
			if($("#CheckAllInv1").attr("disabled") == false)
			{
				$("#CheckAllInv1").attr("disabled",true);
				$("#CheckAllInv1").attr("checked",true);
			}
			

			//for stock entry data-rte
			if($("#selInvAccess2").attr("disabled") == false)
			{
				$("#selInvAccess2").attr("disabled",true);
				$("#selInvAccess2").attr("checked",true);
			}
			if($("#selInvAdd2").attr("disabled") == false)
			{
				$("#selInvAdd2").attr("disabled",true);
				$("#selInvAdd2").attr("checked",true);
			}
			if($("#selInvEdit2").attr("disabled") == false)
			{
				$("#selInvEdit2").attr("disabled",true);
				$("#selInvEdit2").attr("checked",true);
			}
			if($("#selInvDelete2").attr("disabled") == false)
			{
				$("#selInvDelete2").attr("disabled",true);
				$("#selInvDelete2").attr("checked",true);
			}
			if($("#selInvPrint2").attr("disabled") == false)
			{
				$("#selInvPrint2").attr("disabled",true);
				$("#selInvPrint2").attr("checked",true);
			}
			if($("#selInvConfirm2").attr("disabled") == false)
			{
				$("#selInvConfirm2").attr("disabled",true);
				$("#selInvConfirm2").attr("checked",true);
			}
			if($("#selInvReEdit2").attr("disabled") == false)
			{
				$("#selInvReEdit2").attr("disabled",true);
				$("#selInvReEdit2").attr("checked",true);
			}
			if($("#selInvCompanySpecific2").attr("disabled") == false)
			{
				$("#selInvCompanySpecific2").attr("disabled",true);
				$("#selInvCompanySpecific2").attr("checked",true);
			}
			if($("#CheckAllInv2").attr("disabled") == false)
			{
				$("#CheckAllInv2").attr("disabled",true);
				$("#CheckAllInv2").attr("checked",true);
			}




			i++;
		});
		
	}
	else if($("#selAccess_"+fld+"_0").attr("checked") == false)
	{
		
		$(".selAcc_"+fld).each(function()
		{	
			$("#selAccess_"+fld+"_"+i).attr("disabled",false);
			$("#selAdd_"+fld+"_"+i).attr("disabled",false);
			$("#selEdit_"+fld+"_"+i).attr("disabled",false);
			$("#selDelete_"+fld+"_"+i).attr("disabled",false);
			$("#selPrint_"+fld+"_"+i).attr("disabled",false);
			$("#selConfirm_"+fld+"_"+i).attr("disabled",false);
			$("#selReEdit_"+fld+"_"+i).attr("disabled",false);
			$("#selCompanySpecific_"+fld+"_"+i).attr("disabled",false);
			$("#CheckAll_"+fld+"_"+i).attr("disabled",false);

			//for supplier data-inventory 
			if($("#selAccess1").attr("disabled") == true)
			{
				$("#selAccess1").attr("disabled",false);
				$("#selAccess1").attr("checked",false);
			}
			if($("#selAdd1").attr("disabled") == true)
			{
				$("#selAdd1").attr("disabled",false);
				$("#selAdd1").attr("checked",false);
			}
			if($("#selEdit1").attr("disabled") == true)
			{
				$("#selEdit1").attr("disabled",false);
				$("#selEdit1").attr("checked",false);
			}
			if($("#selDelete1").attr("disabled") == true)
			{
				$("#selDelete1").attr("disabled",false);
				$("#selDelete1").attr("checked",false);
			}
			if($("#selPrint1").attr("disabled") == true)
			{
				$("#selPrint1").attr("disabled",false);
				$("#selPrint1").attr("checked",false);
			}
			if($("#selConfirm1").attr("disabled") == true)
			{
				$("#selConfirm1").attr("disabled",false);
				$("#selConfirm1").attr("checked",false);
			}
			if($("#selReEdit1").attr("disabled") == true)
			{
				$("#selReEdit1").attr("disabled",false);
				$("#selReEdit1").attr("checked",false);
			}
			if($("#selCompanySpecific1").attr("disabled") == true)
			{
				$("#selCompanySpecific1").attr("disabled",false);
				$("#selCompanySpecific1").attr("checked",false);
			}
			if($("#CheckAll1").attr("disabled") == true)
			{
				$("#CheckAll1").attr("disabled",false);
				$("#CheckAll1").attr("checked",false);
			}


			//for supplier data-frozen 
			if($("#selAccess2").attr("disabled") == true)
			{
				$("#selAccess2").attr("disabled",false);
				$("#selAccess2").attr("checked",false);
			}
			if($("#selAdd2").attr("disabled") == true)
			{
				$("#selAdd2").attr("disabled",false);
				$("#selAdd2").attr("checked",false);
			}
			if($("#selEdit2").attr("disabled") == true)
			{
				$("#selEdit2").attr("disabled",false);
				$("#selEdit2").attr("checked",false);
			}
			if($("#selDelete2").attr("disabled") == true)
			{
				$("#selDelete2").attr("disabled",false);
				$("#selDelete2").attr("checked",false);
			}
			if($("#selPrint2").attr("disabled") == true)
			{
				$("#selPrint2").attr("disabled",false);
				$("#selPrint2").attr("checked",false);
			}
			if($("#selConfirm2").attr("disabled") == true)
			{
				$("#selConfirm2").attr("disabled",false);
				$("#selConfirm2").attr("checked",false);
			}
			if($("#selReEdit2").attr("disabled") == true)
			{
				$("#selReEdit2").attr("disabled",false);
				$("#selReEdit2").attr("checked",false);
			}
			if($("#selCompanySpecific2").attr("disabled") == true)
			{
				$("#selCompanySpecific2").attr("disabled",false);
				$("#selCompanySpecific2").attr("checked",false);
			}
			if($("#CheckAll2").attr("disabled") == true)
			{
				$("#CheckAll2").attr("disabled",false);
				$("#CheckAll2").attr("checked",false);
			}


			//for supplier data-rte
			if($("#selAccess3").attr("disabled") == true)
			{
				$("#selAccess3").attr("disabled",false);
				$("#selAccess3").attr("checked",false);
			}
			if($("#selAdd3").attr("disabled") == true)
			{
				$("#selAdd3").attr("disabled",false);
				$("#selAdd3").attr("checked",false);
			}
			if($("#selEdit3").attr("disabled") == true)
			{
				$("#selEdit3").attr("disabled",false);
				$("#selEdit3").attr("checked",false);
			}
			if($("#selDelete3").attr("disabled") == true)
			{
				$("#selDelete3").attr("disabled",false);
				$("#selDelete3").attr("checked",false);
			}
			if($("#selPrint3").attr("disabled") == true)
			{
				$("#selPrint3").attr("disabled",false);
				$("#selPrint3").attr("checked",false);
			}
			if($("#selConfirm3").attr("disabled") == true)
			{
				$("#selConfirm3").attr("disabled",false);
				$("#selConfirm3").attr("checked",false);
			}
			if($("#selReEdit3").attr("disabled") == true)
			{
				$("#selReEdit3").attr("disabled",false);
				$("#selReEdit3").attr("checked",false);
			}
			if($("#selCompanySpecific3").attr("disabled") == true)
			{
				$("#selCompanySpecific3").attr("disabled",false);
				$("#selCompanySpecific3").attr("checked",false);
			}
			if($("#CheckAll3").attr("disabled") == true)
			{
				$("#CheckAll3").attr("disabled",false);
				$("#CheckAll3").attr("checked",false);
			}


			//for stock entry data-frozen
			if($("#selInvAccess1").attr("disabled") == true)
			{
				$("#selInvAccess1").attr("disabled",false);
				$("#selInvAccess1").attr("checked",false);
			}
			if($("#selInvAdd1").attr("disabled") == true)
			{
				$("#selInvAdd1").attr("disabled",false);
				$("#selInvAdd1").attr("checked",false);
			}
			if($("#selInvEdit1").attr("disabled") == true)
			{
				$("#selInvEdit1").attr("disabled",false);
				$("#selInvEdit1").attr("checked",false);
			}
			if($("#selInvDelete1").attr("disabled") == true)
			{
				$("#selInvDelete1").attr("disabled",false);
				$("#selInvDelete1").attr("checked",false);
			}
			if($("#selInvPrint1").attr("disabled") == true)
			{
				$("#selInvPrint1").attr("disabled",false);
				$("#selInvPrint1").attr("checked",false);
			}
			if($("#selInvConfirm1").attr("disabled") == true)
			{
				$("#selInvConfirm1").attr("disabled",false);
				$("#selInvConfirm1").attr("checked",false);
			}
			if($("#selInvReEdit1").attr("disabled") == true)
			{
				$("#selInvReEdit1").attr("disabled",false);
				$("#selInvReEdit1").attr("checked",false);
			}
			if($("#selInvCompanySpecific1").attr("disabled") == true)
			{
				$("#selInvCompanySpecific1").attr("disabled",false);
				$("#selInvCompanySpecific1").attr("checked",false);
			}
			if($("#CheckAllInv1").attr("disabled") == true)
			{
				$("#CheckAllInv1").attr("disabled",false);
				$("#CheckAllInv1").attr("checked",false);
			}
			

			//for stock entry data-rte
			if($("#selInvAccess2").attr("disabled") == true)
			{
				$("#selInvAccess2").attr("disabled",false);
				$("#selInvAccess2").attr("checked",false);
			}
			if($("#selInvAdd2").attr("disabled") == true)
			{
				$("#selInvAdd2").attr("disabled",false);
				$("#selInvAdd2").attr("checked",false);
			}
			if($("#selInvEdit2").attr("disabled") == true)
			{
				$("#selInvEdit2").attr("disabled",false);
				$("#selInvEdit2").attr("checked",false);
			}
			if($("#selInvDelete2").attr("disabled") == true)
			{
				$("#selInvDelete2").attr("disabled",false);
				$("#selInvDelete2").attr("checked",false);
			}
			if($("#selInvPrint2").attr("disabled") == true)
			{
				$("#selInvPrint2").attr("disabled",false);
				$("#selInvPrint2").attr("checked",false);
			}
			if($("#selInvConfirm2").attr("disabled") == true)
			{
				$("#selInvConfirm2").attr("disabled",false);
				$("#selInvConfirm2").attr("checked",false);
			}
			if($("#selInvReEdit2").attr("disabled") == true)
			{
				$("#selInvReEdit2").attr("disabled",false);
				$("#selInvReEdit2").attr("checked",false);
			}
			if($("#selInvCompanySpecific2").attr("disabled") == true)
			{
				$("#selInvCompanySpecific2").attr("disabled",false);
				$("#selInvCompanySpecific2").attr("checked",false);
			}
			if($("#CheckAllInv2").attr("disabled") == true)
			{
				$("#CheckAllInv2").attr("disabled",false);
				$("#CheckAllInv2").attr("checked",false);
			}
		i++;
		});
		
	}

}

function selInvAll1(i,j)
{
	//alert("Inv");
	var CheckAll	=	"CheckAllInv1";
	var selAccess	=	"selInvAccess1";
	var selAdd	=	"selInvAdd1";
	var selEdit	=	"selInvEdit1";
	var selDelete	=	"selInvDelete1";
	var selPrint	=	"selInvPrint1";
	var selConfirm	=	"selInvConfirm1";
	var selReEdit	=	"selInvReEdit1";
	var selCpnySpeci=	"selInvCompanySpecific1";
	//var selActive	=	"selActive_"+i+"_"+j;
				
	if (document.getElementById(CheckAll).checked) {
			//alert("asaS");
		document.getElementById(selAccess).checked = true;
		document.getElementById(selAdd).checked = true;
		document.getElementById(selEdit).checked = true;
		document.getElementById(selDelete).checked = true;
		document.getElementById(selPrint).checked = true;
		document.getElementById(selConfirm).checked = true;
		document.getElementById(selReEdit).checked = true;
		document.getElementById(selCpnySpeci).checked = true;
			//document.getElementById(selActive).checked = true;
	} 
	else 
	{

		if (document.getElementById(selAccess).checked)
			document.getElementById(selAccess).checked = false;
		if (document.getElementById(selAdd).checked)
			document.getElementById(selAdd).checked = false;
		if (document.getElementById(selEdit).checked) 
			document.getElementById(selEdit).checked = false;
		if (document.getElementById(selDelete).checked)
			document.getElementById(selDelete).checked = false;
		if (document.getElementById(selPrint).checked)
			document.getElementById(selPrint).checked = false;
		if (document.getElementById(selConfirm).checked)
			document.getElementById(selConfirm).checked = false;
		if (document.getElementById(selReEdit).checked) 
			document.getElementById(selReEdit).checked = false;
		if (document.getElementById(selCpnySpeci).checked) 
			document.getElementById(selCpnySpeci).checked = false;
			
	}

		/*
		if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		*/

		// selected check
		//checkSel(i,j);
		//checkAllData();
}


	function selInvAll2(i,j)
	{
		//alert("Inv");
		var CheckAll	=	"CheckAllInv2";
		var selAccess	=	"selInvAccess2";
		var selAdd	=	"selInvAdd2";
		var selEdit	=	"selInvEdit2";
		var selDelete	=	"selInvDelete2";
		var selPrint	=	"selInvPrint2";
		var selConfirm	=	"selInvConfirm2";
		var selReEdit	=	"selInvReEdit2";
		var selCpnySpeci=	"selInvCompanySpecific2";
		//var selActive	=	"selActive_"+i+"_"+j;
				
		if (document.getElementById(CheckAll).checked) {
			//alert("asaS");
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

			if (document.getElementById(selAccess).checked)
				document.getElementById(selAccess).checked = false;
			if (document.getElementById(selAdd).checked)
				document.getElementById(selAdd).checked = false;
			if (document.getElementById(selEdit).checked) 
				document.getElementById(selEdit).checked = false;
			if (document.getElementById(selDelete).checked)
				document.getElementById(selDelete).checked = false;
			if (document.getElementById(selPrint).checked)
				document.getElementById(selPrint).checked = false;
			if (document.getElementById(selConfirm).checked)
				document.getElementById(selConfirm).checked = false;
			if (document.getElementById(selReEdit).checked) 
				document.getElementById(selReEdit).checked = false;
			if (document.getElementById(selCpnySpeci).checked) 
				document.getElementById(selCpnySpeci).checked = false;
			
		}

		/*if (!document.getElementById(selAdd).checked || !document.getElementById(selEdit).checked || !document.getElementById(selDelete).checked || !document.getElementById(selPrint).checked || !document.getElementById(selConfirm).checked || !document.getElementById(selReEdit).checked || !document.getElementById(selCpnySpeci).checked) {
			document.getElementById(CheckAll).checked = false;
		}
		*/
		// selected check
		//checkSel(i,j);
		//checkAllData();
	}



