function validateAddDailyFrozenRePacking(form)
{
	
	var selectDate		=	form.selectDate.value;
	var fish		=	form.fish.value;
	var processCode		=	form.processCode.value;
	var freezingStage	=	form.freezingStage.value;
	var eUCode		=	form.eUCode.value;
	var brand		=	form.brand.value;
	var frozenCode		=	form.frozenCode.value;
	var mCPacking		=	form.mCPacking.value;

	var reasonRePack	=	form.reasonRePack.value;

	var numNewInnerPack	=	form.numNewInnerPack.value;
	var numLabelCard	= 	form.numLabelCard.value;
	var numNewMC		=	form.numNewMC.value;

	var rePackEUCode	= 	form.rePackEUCode.value;
	var rePackBrand		=	form.rePackBrand.value;
	var rePackFrozenCode	=	form.rePackFrozenCode.value;
	var rePackMCPacking	=	form.rePackMCPacking.value;

	
	if (selectDate=="") {
		alert("Please Select a Date.");
		form.selectDate.focus();
		return false;
	}

	if (fish=="") {
		alert("Please select a fish.");
		form.fish.focus();
		return false;
	}
	
	if (processCode=="") {
		alert("Please select a Process Code.");
		form.processCode.focus();
		return false;
	}

	if (freezingStage=="") {
		alert("Please select a Freezing Stage.");
		form.freezingStage.focus();
		return false;
	}
	
	if (eUCode=="") {
		alert("Please select a EU Code.");
		form.eUCode.focus();
		return false;
	}
	
	if (brand=="") {
		alert("Please select a Brand.");
		form.brand.focus();
		return false;
	}
	
	if (frozenCode=="") {
		alert("Please select a Frozen Code.");
		form.frozenCode.focus();
		return false;
	}
	
	if (mCPacking=="") {
		alert("Please select a MC Packing.");
		form.mCPacking.focus();
		return false;
	}

	//Checking Number of Packing details entered
	if (fish!="" && processCode!="") {
		isPackEntered 		=	false;

		var rowCount		=	parent.iFrame1.document.frmDailyFrozenRePackingGrade.hidRowCount.value;
	
		for(i=1;i<=rowCount;i++)
		{
			var numMC		=	parent.iFrame1.document.getElementById("numMCRePack_"+i).value;
			var numLooseSlab	=	parent.iFrame1.document.getElementById("numLooseSlabRePack_"+i).value;
			if (numMC!="" && numLooseSlab!="") {
				isPackEntered = true;
			}
		}
		if (isPackEntered==false) {
			alert("Please enter Number of Packing Details.");
			return false;
		}	
	}	

	if (reasonRePack=="") {
		alert("Please select Reason for Re-Packing.");
		form.reasonRePack.focus();
		return false;
	}

	if (numNewInnerPack=="") {
		alert("Please enter No of New Inner Packs used.");
		form.numNewInnerPack.focus();
		return false;
	}

	if (numLabelCard=="") {
		alert("Please enter No of Labels / Header Cards Used.");
		form.numLabelCard.focus();
		return false;
	}

	if (numNewMC=="") {
		alert("Please enter No of New MC Used.");
		form.numNewMC.focus();
		return false;
	}

	if (rePackEUCode=="") {
		alert("Please select Repacked EU Code.");
		form.rePackEUCode.focus();
		return false;
	}

	if (rePackBrand=="") {
		alert("Please select Repacked Brand.");
		form.rePackBrand.focus();
		return false;
	}

	if (rePackFrozenCode=="") {
		alert("Please select Repacked Frozen Code.");
		form.rePackFrozenCode.focus();
		return false;
	}

	if (rePackMCPacking=="") {
		alert("Please select Repacked MC Pkg.");
		form.rePackMCPacking.focus();
		return false;
	}


		
	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}

function validateDailyFrozenPackingSearch(form) {
	var frozenPackingFrom	=	form.frozenPackingFrom.value;
	var frozenPackingTill	=	form.frozenPackingTill.value;
	
	if( frozenPackingFrom=="" )
	{
		alert("Please select From Date.");
		form.frozenPackingFrom.focus();
		return false;
	}
	
	if( frozenPackingTill=="" )
	{
		alert("Please select Till Date.");
		form.frozenPackingTill.focus();
		return false;
	}
return true;
}

function validateFrozenRepackingGrade(form)
{
	isPackEntered 		=	false;

	var rowCount		=	document.getElementById("hidRowCount").value;
	
	for(i=1;i<=rowCount;i++)
	{
		var numMC		=	document.getElementById("numMCRePack_"+i).value;
		var numLooseSlab	=	document.getElementById("numLooseSlabRePack_"+i).value;
		if (numMC!="" && numLooseSlab!="") {
			isPackEntered = true;
		}
	}
	if (isPackEntered==false) {
		alert("Please enter Number of Packing Details.");
		return false;
	}	
}



	




/*##########################################*/
function passEuCodeValue()
{
	document.getElementById("rePackEUCode").value = document.getElementById("eUCode").value;  
}

function passBrandValue()
{
	document.getElementById("rePackBrand").value = document.getElementById("brand").value;  	
}

function passFrozenValue()
{
	document.getElementById("rePackFrozenCode").value = document.getElementById("frozenCode").value;  	
}

function passMCPackingValue()
{
	document.getElementById("rePackMCPacking").value = document.getElementById("mCPacking").value;  	
}
/*##########################################*/


function callProdnCalc()
	{
		setTimeout("calcProdnQty()",500);
	}

function callPkgChange(rowId)
{
	setTimeout("calcPkgChange("+rowId+")",500);
}

function calcPkgChange(rowId)
{
	
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	// After get the prev mc pack change to current
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value;
	
	

	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		var existingMC = document.getElementById("numMC_"+i+"_"+rowId).value;
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;

		var existingLS = document.getElementById("numLS_"+i+"_"+rowId).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;

		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		
		if (totalSlabs>0)
		{			
			var totMC	 = Math.floor(totalSlabs/numMCPack);
			var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;	
		}
	}
	

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	
	calcProdnQty();
	
	recalcLSForConversion();
}

function recalcLSForConversion()
{
	var prodnRowCount 	= document.getElementById("hidProdnRowCount").value;
	var gradeRowCount = document.getElementById("hidGradeRowCount").value;

	var lsConversionArr 	= new Array();
	
	for (var i=1; i<=prodnRowCount; i++) 
	{
		for (var j=1;j<=gradeRowCount ;j++ )
		{
			var gradeId = document.getElementById("sGradeId_"+j+"_"+i).value;
			var existingLS = document.getElementById("numLS_"+j+"_"+i).value;
			var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
			
			if (existLS!=0 && existLS!="" && !isNaN(existLS)) {
				if (typeof(lsConversionArr[gradeId])!="undefined" && existLS!=0) {
					existLS = parseInt(existLS) + parseInt(lsConversionArr[gradeId]);
				}
				if (existLS!=0) lsConversionArr[gradeId] = parseInt(existLS);
			}
		}
	}

		// Assign to conversion section
		for (var i=1; i<=gradeRowCount;i++) {
			var cgId = document.getElementById("cGradeId_"+i).value;
			if (typeof(lsConversionArr[cgId])!="undefined") {				
				document.getElementById("numLSC_"+i).value = lsConversionArr[cgId];
			} else document.getElementById("numLSC_"+i).value = "";
		}

}

// calc Prodn Qty
	function calcProdnQty()
	{
		
		var LSToMCConversionType   = document.getElementById("hidLS2MCType").value;
		
		var prodnRowCount 	= document.getElementById("hidProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidGradeRowCount").value;

		var filledWt		= parseFloat(document.getElementById("filledWt").value);
			
		for (var i=1; i<=prodnRowCount; i++) {

			var numMcPack	= parseInt(document.getElementById("numMcPack_"+i).value);
			
			var totNumMC = 0;
			var totNumLS = 0;
			for (var j=1; j<=gradeRowCount; j++) {
				var nMC = document.getElementById("numMC_"+j+"_"+i);
				var nLS = document.getElementById("numLS_"+j+"_"+i);
				var numMC = (nMC.value!="")?parseInt(nMC.value):0;
				var numLS = (nLS.value!="")?parseInt(nLS.value):0;
				// Convert LS to MC
				if (LSToMCConversionType=='AC')
				{				
					var eMC   = Math.floor(numLS/numMcPack);
					if (!isNaN(eMC)) var numMC = parseInt(numMC) + parseInt(eMC);
					var numLS = parseInt(numLS)%parseInt(numMcPack);	
					if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+i).value = numMC;
					if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+i).value = numLS;
				}
				totNumMC += numMC;
				totNumLS += numLS;

			} // Grade loop Ends here
			
			// Total Slabs
			var totalSlabs 	= (totNumMC*numMcPack)+totNumLS;
			// total Qty	
			var totalQty	= totalSlabs*filledWt;
			if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+i).value = totalSlabs;
			if (!isNaN(totalQty)) document.getElementById("totalQty_"+i).value = number_format(totalQty,2,'.','');

			/*
			//Find MC Qty
			var mcQty = filledWt*totNumMC*numMcPack;
			//Find LS Qty
			var lsQty = filledWt*totNumLS;
			if (!isNaN(totNumMC)) document.getElementById("totNumMC_"+i).value = totNumMC;
			if (!isNaN(totNumLS)) document.getElementById("totNumLS_"+i).value = totNumLS;
			if (!isNaN(mcQty)) document.getElementById("MCQty_"+i).value = number_format(mcQty,2,'.','');
			if (!isNaN(lsQty)) document.getElementById("LSQty_"+i).value = number_format(lsQty,2,'.','');
			*/			
		} // Prodn Row count Ends here
		/*
		var pkgGroupArr 	= new Array();
		var lsPkgGroupArr 	= new Array();
		*/
		
		//displaySummary(); // Display summary
	}

	function displaySummary()
	{
		removeRow();
		
		var prodnRowCount 	= document.getElementById("hidProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidGradeRowCount").value;	

		var pkgGroupArr 	= new Array();
		var lsPkgGroupArr 	= new Array();
		
		for (var i=1; i<=prodnRowCount; i++) {
			
			if (document.getElementById("hidflag_"+i).value=='c')
			{
			var mcPkgCode = document.getElementById("mcPackingId_"+i).options[document.getElementById("mcPackingId_"+i).selectedIndex].text;
			}
			else 
			{
			var mcPkgCode = document.getElementById("mcPackingId_"+i).value;
			}
			
			//$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
			//$lsPkgGroupArr[$sGradeId] += $numLS;
			var numMcPack	= parseInt(document.getElementById("numMcPack_"+i).value);
			for (var j=1; j<=gradeRowCount; j++) {
				var numMC = document.getElementById("numMC_"+j+"_"+i).value;
				var numLS = document.getElementById("numLS_"+j+"_"+i).value;
				var gradeId = document.getElementById("sGradeId_"+j+"_"+i).value;
				// MC
				
				var arrIndex = mcPkgCode+"_"+gradeId;
				if (numMC!=0 && numMC!="" && !isNaN(numMC)) {
					if (typeof(pkgGroupArr[arrIndex])!="undefined" && numMC!=0) {
						numMC = parseInt(numMC) + parseInt(pkgGroupArr[arrIndex]);
					}
					if (numMC!=0) pkgGroupArr[arrIndex] = parseInt(numMC);
				}
				// LS
				if (numLS!=0 && numLS!="" && !isNaN(numLS)) {
					if (typeof(lsPkgGroupArr[gradeId])!="undefined" && numLS!=0) {
						numLS = parseInt(numLS) + parseInt(lsPkgGroupArr[gradeId]);
					}
					if (numLS!=0) lsPkgGroupArr[gradeId] = parseInt(numLS);
				}
					
			} // Grade Loop
		} // Product Loop Ends 
		
		var prevPkgCode = "";
		for (var pga in pkgGroupArr)
		{
			var splitArr = pga.split("_");
			var mcPkgCode 	= splitArr[0];
			var gradeId	= splitArr[1];

			if (prevPkgCode!=mcPkgCode) {
				prdnSumryTblRow(mcPkgCode, pkgGroupArr);
			}
			prevPkgCode = mcPkgCode;
		}
			
		//lsSummary(lsPkgGroupArr);
		
	
		
	}

	function removeRow()
	{
		//var mtRCount = parseInt(document.getElementById("hidTableRowCount").value)+1;
		//var tRowCount = document.getElementById("hidSummaryTblRowCount").value;
		var tRowCount = fieldId;
		
		for (var i=1; i<=tRowCount; i++) {
				if (tRowCount>0) {
					if(document.getElementById("tRow_"+i)!=null) {
						var tRIndex = document.getElementById("tRow_"+i).rowIndex;	
						document.getElementById('prodnDtlsTble').deleteRow(tRIndex);	
					}					
				}
		}
	}	
	
	// Find LS Summary
	function lsSummary(lsPkgArr)
	{
		
		var gradeCount  = document.getElementById("hidGradeRowCount").value;
		
		for (var i=1; i<=gradeCount;i++) {
			var gId = document.getElementById("gId_"+i).value;
			if (typeof(lsPkgArr[gId])!="undefined") {				
				document.getElementById("LS_"+gId).innerHTML = "<strong>"+lsPkgArr[gId]+"</strong>";
				//document.getElementById("numLS_"+gId).innerHTML = "<strong>"+lsPkgArr[gId]+"</strong>";
			} else document.getElementById("LS_"+gId).innerHTML = "&nbsp;";
			//else document.getElementById("numLS_"+gId).innerHTML = "&nbsp;";
		}
		
	}

		function prdnSumryTblRow(mcPkgCode, pkgArr)
	{	
		
		var gradeCount  = document.getElementById("hidGradeRowCount").value;		
		var tbl		= document.getElementById("prodnDtlsTble");
		
			
		var lastRow	= tbl.rows.length-3; // If table last row is not exist then set to 1
	
		var row		= tbl.insertRow(lastRow);
		
		row.height	= "28";
		//row.className 	= "whiteRow";
		row.align 	= "center";
		//+"_"+fldId
		row.id 		= "tRow_"+fieldId;	
		
		var cell1	= row.insertCell(0);
		var cell2	= row.insertCell(1);
		var cell3	= row.insertCell(2);
		//var cell4	= row.insertCell(3);
		var fCell = 3;		
		var cellArr = new Array();
		for (var i=1; i<=gradeCount;i++) {
			var cellMId = parseInt(fCell)+i;
			var insCellId = (fCell+i)-1;
			var joinCell = "cell"+cellMId;
			var joinCell	= row.insertCell(insCellId);
			cellArr[i-1] = joinCell;
		}		

		var fCellAL = parseInt(fCell)+parseInt(gradeCount);
		var nextCell1 = "cell"+(fCellAL+1);
		var nextCell2 = "cell"+(fCellAL+2);		
		var nextCell1	= row.insertCell(fCellAL);
		var nextCell2	= row.insertCell(fCellAL+1);	
		
		var bdr 	= "1px solid #999999";
		
		cell1.className	= "listing-item"; cell1.align	= "center"; 
		cell2.className	= "listing-head"; cell2.align	= "left"; cell2.style.borderLeft=bdr; cell2.style.borderRight=bdr; cell2.style.borderBottom=bdr;
		cell3.className	= "listing-item"; cell3.align	= "center"; cell3.style.borderRight=bdr; cell3.style.borderBottom=bdr;	
		//cell4.className	= "listing-item"; cell4.align	= "center"; cell4.style.borderRight=bdr; cell4.style.borderBottom=bdr;	
		nextCell1.className = "listing-item"; nextCell1.align	= "center"; nextCell1.noWrap = "true";
		nextCell2.className = "listing-item"; nextCell2.align	= "center"; nextCell2.noWrap = "true";

		cell1.innerHTML	= "";
		cell2.innerHTML	= "MC PKG";
		cell3.innerHTML	= mcPkgCode;
		//cell4.innerHTML	= "&nbsp;";
		
		for (var i=0; i<cellArr.length;i++) {	
			var gId = document.getElementById("gId_"+(i+1)).value;

			cellArr[i].className = "listing-item"; cellArr[i].align	= "right"; cellArr[i].noWrap = "true"; cellArr[i].style.borderRight=bdr; cellArr[i].style.borderBottom=bdr; cellArr[i].style.fontWeight="bold";
			if (typeof(pkgArr[mcPkgCode+"_"+gId])!="undefined") cellArr[i].innerHTML = pkgArr[mcPkgCode+"_"+gId];
			else cellArr[i].innerHTML = "&nbsp;";
		}
		nextCell1.innerHTML="";
		nextCell2.innerHTML="";		

		fieldId		= parseInt(fieldId)+1;	
		fldId		= parseInt(fldId)+1;
		document.getElementById("hidSummaryTblRowCount").value = fldId;
		document.getElementById("hidTableRowCount").value = fieldId;
		
		
	}
	
	//var pkgGroupArr 	= new Array();
	//var lsPkgGroupArr 	= new Array();

		function nTxtBox(e, form, fldName)
	{
		var ecode = getKeyCode(e);	
			
		var fName = fldName.split("_");
		
			// Down Arrow and enter key
			if ((ecode==13) || (ecode == 0) || (ecode==40)) {
				if (fName[0]=="numMC") nextTextBoxName = "numLS_"+fName[1]+"_"+(parseInt(fName[2]));
				else if (fName[0]=="numLS") nextTextBoxName =   "numMC_"+fName[1]+"_"+(parseInt(fName[2])+1);

				//nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])+1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus();}			
				return false;
			}
			//uP aRROW
			if ((ecode==38)) {
				if (fName[0]=="numMC") nextTextBoxName = "numLS_"+fName[1]+"_"+(parseInt(fName[2])-1);
				else if (fName[0]=="numLS") nextTextBoxName =   "numMC_"+fName[1]+"_"+(parseInt(fName[2]));
				
				//nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])-1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
			//Right Arrow
			rightArrow = "";
			if ((ecode==39)) {
				if (fName[0]=="numMC") rightArrow =   "numMC_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				else if (fName[0]=="numLS") rightArrow =   "numLS_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				
				var nextControl = eval(form+"."+rightArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
			//Left Arrow
			if ((ecode==37)) {				
				if (fName[0]=="numMC") leftArrow = "numMC_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));	
				else if (fName[0]=="numLS") leftArrow =   "numLS_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));
				
				var nextControl = eval(form+"."+leftArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
	}

	function validateDFPReglazing(form)
{
	alert("hai2");
var selDate		=	form.selDate.value;
var todDate		=	form.todDate.value;
var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
var frozCode= document.getElementById("hidselFrozenCode_1").value;
var comfrozCode=document.getElementById("selFrozenCode_1").value;
var i=1;
var flag=0;
if (frozCode==comfrozCode)
{
	alert("Please Change the Frozen Code");
	return false;
}
for (var j=1; j<=gradeRowCount; j++) {						 
						var numthawMC = document.getElementById("tothidAvailableSlabs_"+j).value;
						var numthawMCol=document.getElementById("numMC_"+j+"_"+i).value;
						var hidnumthawMCol=document.getElementById("numMCG_"+j+"_"+i).value;
						//if (parseInt(numthawMC)<parseInt(numthawMCol))
						if (parseInt(numthawMCol)>parseInt(hidnumthawMCol))
						{
							alert("Available Quantity less than Reglazing quantity.Please reenter");
							flag=1;
							break;
						}	
					
						
}

var packEntered = false;
			for (var j=1; j<=gradeRowCount; j++) {
				var numMC = document.getElementById("numMC_"+j+"_"+i).value;				
				if(numMC!=0){
					packEntered = true;
				}				
			} // grade Row Count Ends here

 			if (!packEntered) {
				alert("Please enter Number of Reglazing Details.");
				return false;
			}

/*var elem = todDate.split('/');  
		day = elem[0];
		month = elem[1];
		month=parseInt(month)-1;
		year = elem[2];
		var aelem = selDate.split('/');  
		aday = aelem[0];  
		amonth = aelem[1]; 
		amonth=parseInt(amonth)-1;
		ayear = aelem[2];	
		var dt = new Date(ayear,amonth,aday);
		var tod = new Date(year,month,day);			
			
			
		if (dt<tod)
		{
		alert("This Date is not allowed");
		return false;
		}*/
		if (flag==1)
		{
		return false;
		}
		//else
		//{
		//return true;
		//}
		if (confirm("Do you wish to continue?"))
				{
				return true;
				} else
	{
					return false;
	}




}
