var Checkcount=''; var receipt_lot_id=''; var generateCount='';
function GenerateRmLotId(form,prefix,rowcount)
{
//alert(prefix);
//showFnLoading();
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	//var conDelMsg	=	"Do you wish to delete the selected items?";
	
	if(!isAnyChecked(rowCount,fieldPrefix))
	{
		alert("Please select a record to generate.");
		return false;
	}
	
	if(!validateRepeatIssuance()){
	
	
		return false;
	}
		
	return false;

}

function isAnyChecked(rowCount,fieldPrefix)
{
	for ( i=0; i<rowCount; i++ )
	{
			if(document.getElementById(fieldPrefix+i).checked)
		{
			
			return true;
		}		
	}
	return false;
}

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

	var sc = document.getElementById("supplierSize").value;
	
	var arrGP = new Array();
	var arra = new Array();
	
	var arrk=0;
	for( j=0; j<sc; j++ )	
	{
	    
		if(document.getElementById("rm_lot_"+j).checked)
		{
			//Checkcount++;
			//document.getElementById("rm_lot_"+j).style.display = 'none';
			
			var sg = document.getElementById("Company_Name_"+j).value;
			//alert(sg);
			arrGP[arrk] = sg;
			//alert(arrGP);
			//alert( >0);
			
		if ( arrGP.indexOf(sg) > 0 )	{
			alert("Company Name must be same.");
			document.getElementById("Company_Name_"+j).focus();
			return false;
		}
				
			var unt = document.getElementById("unit_"+j).value;
				arra[arrk] = unt;
			//alert( arrGP.indexOf(sg);
			if ( arra.indexOf(unt) > 0 )	{
				alert("Unit  must be same.");
				document.getElementById("unit_"+j).focus();
				return false;
			}	
				
			arrk++;	
		}
		
		//if(document.getElementById("rm_lot_"+j).checked == true)
		//{
		//alert("hii");
			
		//}
            
	}
	for( j=0; j<sc; j++ )	
	{
		if(document.getElementById("rm_lot_"+j).checked)
		{
			Checkcount++;
			
			document.getElementById("rm_lot_"+j).style.display = 'none';
			
			var procurementAvailable=document.getElementById("procurementAvailable").value;
			var receiptID=document.getElementById("rm_lot_"+j).value;
			
				if(receipt_lot_id=='')
				{
					receipt_lot_id=receiptID;
				}
				else
				{
					receipt_lot_id+=','+receiptID;
				}
			
			
		document.getElementById("rm_lot_"+j).checked = false;
		var companyName=document.getElementById("Company_Name_"+j).value;
		var unit=document.getElementById("unit_"+j).value;
		}
	
	document.getElementById("hidcheck").value=Checkcount;
	
	}
	
	//alert(companyName+'---'+unit);
	xajax_getRMlotId(receipt_lot_id,Checkcount,procurementAvailable,companyName,unit);
	var sizeSuplr=document.getElementById("supplierSize").value;
	
	xajax_saveChange(Checkcount,sizeSuplr);
	generateCount++;
	//alert(generateCount);
	document.getElementById("rowcnt").value=generateCount;
	receipt_lot_id='';
	//return true;
}


function ReloadPage()
{
	location.reload();
	// var generateLotid=document.getElementById("generateLotid").value;
////alert(generateLotid);
	// window.location='ManageRMLOTID.php?generateLotID='+generatelotid;
}

function CheckUniqueUnit()
{
	var companyOld=document.getElementById("company_old_id").value;
	var unit_old_id=document.getElementById("unit_old_id").value;
	var Company_Name=document.getElementById("Company_Name").value;
	var unit=document.getElementById("unit").value;
	if((companyOld==Company_Name) && (unit_old_id==unit))
	{
	 alert("Cannot Transfer same unit for the same company");
	 return false;
	}
	else{
			var alreadyExist=document.getElementById("alreadyExist").value;
			if(alreadyExist==1)
			{
			alert("Already transfered from this unit");
			 return false;
			}
			else{
			return true;
			}

		//return true;
	}
		
}
function UnitAlreadyTransfer()
{
var rm_lot_id=document.getElementById("rm_lot_id").value;
xajax_unitexist(rm_lot_id);
}

