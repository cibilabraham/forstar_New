function validateAddDailyThawing(form)
{
	var selectDate		=	form.selectDate.value;
	var fish		=	form.fish.value;
	var processCode		=	form.processCode.value;
	var freezingStage	=	form.freezingStage.value;
	var eUCode		=	form.eUCode.value;
	var brand		=	form.brand.value;
	var frozenCode		=	form.frozenCode.value;
	var mCPacking		=	form.mCPacking.value;
	return true;

	/*if (selectDate=="") {
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
	}*/

	/*Checking Number of Packing details entered*/
	/*if (fish!="" && processCode!="") {
		isPackEntered 	= false;
		var rowCount	= parent.iFrame1.document.frmDailyThawingGrade.hidRowCount.value;
	
		for (i=1;i<=rowCount;i++) {
			var numMC		=	parent.iFrame1.document.getElementById("numMCThawing_"+i).value;
			var numLooseSlab	=	parent.iFrame1.document.getElementById("numLooseSlabThawing_"+i).value;
			if (numMC!="" && numLooseSlab!="") {
				isPackEntered = true;
			}
		}

		if (!isPackEntered) {
			alert("Please enter Number of Packing used for Thawing.");
			return false;
		}	
	}	


	if (!confirmSave()) return false;
	else return true;*/
}

	/* Validating IFrame values*/
	function validateFrozenPackingThawingGrade(form)
	{
		isPackEntered 		=	false;
		var rowCount		=	document.getElementById("hidRowCount").value;
		
		for (i=1;i<=rowCount;i++) {
			var numMC		=	document.getElementById("numMCThawing_"+i).value;
			var numLooseSlab	=	document.getElementById("numLooseSlabThawing_"+i).value;
			if (numMC!="" && numLooseSlab!="") {
				isPackEntered = true;
			}
		}
		if (!isPackEntered) {
			alert("Please enter Number of Packing used for Thawing.");
			return false;
		}	
	}

	// Calculate QE Total
	function calcQETotal(displayQE)
	{
		//alert(displayQE);
		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		var pcRowCount    = document.getElementById("hidPCRowCount").value;
		var numPacks	  = document.getElementById("hidNumPack").value;			
		
		
		for (i=1; i<=pcRowCount; i++) {
			var totalMCPack  = 0;
			var totLooseSlab = 0;
			var nMC = 0;
			var nLS = 0;
			var totSlab = 0;
			for (j=1; j<=gradeRowCount; j++) {
				if (displayQE=='DMCLS' || displayQE=='DMC') nMC = document.getElementById("numMC_"+i+"_"+j).value;
				if (displayQE=='DMCLS' || displayQE=='DLS') nLS 	= document.getElementById("numLooseSlab_"+i+"_"+j).value;
				var numMC 	 = (nMC=="")?0:nMC;
				var numLooseSlab = (nLS=="")?0:nLS;
				//alert("MC="+numMC+"LS="+numLooseSlab);

				if ((numMC==0 || numMC!=0) && numLooseSlab!=0 && numPacks!="") {		
					if (displayQE=='DMCLS') var totalMcPacks = Math.floor(numLooseSlab/numPacks);
					numMC	= parseInt(numMC) + parseInt(totalMcPacks);
					if (displayQE=='DMCLS') var numLSlab = parseInt(numLooseSlab)%parseInt(numPacks);
					else var numLSlab = parseInt(numLooseSlab);
					if (displayQE=='DMCLS' || displayQE=='DMC') document.getElementById("numMC_"+i+"_"+j).value = numMC;
					if (displayQE=='DMCLS' || displayQE=='DLS') document.getElementById("numLooseSlab_"+i+"_"+j).value = numLSlab;
				} else {
					var numMC 	 = (nMC=="")?0:nMC;
					var numLSlab = (nLS=="")?0:nLS;
				}

				totalMCPack += parseInt(numMC);
				totLooseSlab += parseInt(numLSlab);
				var calcTotSlab = (parseInt(numMC)*parseInt(numPacks))+parseInt(numLSlab);
				totSlab += parseInt(calcTotSlab);	
				
			} // Grade Loop Ends here
			if (displayQE=='DMCLS' || displayQE=='DMC') document.getElementById("totalMCPack_"+i).value = totalMCPack;
			if (displayQE=='DMCLS' || displayQE=='DLS') document.getElementById("totLooseSlab_"+i).value = totLooseSlab;
			if (totalMCPack!=0 || totLooseSlab!=0) document.getElementById("packEntered_"+i).value = 1;
			else document.getElementById("packEntered_"+i).value = "";

			if (totSlab && numPacks!="" && displayQE=='DMCLS') {
				document.getElementById("totSlabs_"+i).innerHTML = "["+totSlab+"]";
			}
		} // Pc Row Loop Ends here		
	}

	// High light Row And Col
	function hLightRNC(rowsId, colsId)
	{
		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		var pcRowCount    = document.getElementById("hidPCRowCount").value;
		for (j=1; j<=gradeRowCount; j++) {
			document.getElementById("gradeRow_"+j).className='clearRowhLTxt';
		}
		for (i=1; i<=pcRowCount; i++) {
			document.getElementById("processCodeCol_"+i).className = 'clearColhLTxt';
		}
		// Highlight Col & Row
		document.getElementById("gradeRow_"+rowsId).className = 'highlightTxt';
		document.getElementById("processCodeCol_"+colsId).className = 'highlightTxt';
	}

	// left /right /up/down moving (Focus Next)
	function fNGradeTxtBox(e, form, fldName, displayQE)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);
		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		var pcRowCount    = document.getElementById("hidPCRowCount").value;
	
		var fName = fldName.split("_");
		
			// Down Arrow and enter key
			if ((ecode==13) || (ecode == 0) || (ecode==40)) {			
				nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])+1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus();}			
				return false;
			}

			//uP aRROW
			if ((ecode==38)) {
				nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])-1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}

			//Right Arrow
			rightArrow = "";
			if ((ecode==39)) {
				if (fName[0]!="numLooseSlab" && displayQE=='DMCLS') rightArrow =   "numLooseSlab_"+fName[1]+"_"+(parseInt(fName[2]));
				else if (displayQE=='DLS') rightArrow =   "numLooseSlab_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				else rightArrow =   "numMC_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				var nextControl = eval(form+"."+rightArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}

			//Left Arrow
			if ((ecode==37)) {	
				if (fName[0]!="numMC" && displayQE=='DMCLS') leftArrow = "numMC_"+fName[1]+"_"+(parseInt(fName[2]));	
				else if (displayQE=='DMC') leftArrow = "numMC_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));	
				else if (displayQE=='DLS') leftArrow =   "numLooseSlab_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));
				else leftArrow =	"numLooseSlab_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));	
				
				var nextControl = eval(form+"."+leftArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
	}

	function calcMCPack(displayQE)
	{
		setTimeout("calcQETotal('"+displayQE+"')",1000);
	}

		// calc Allocation Prodn Qty
	function calcAllocateProdnQty()
	{
	
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;			
		var filledWt		= parseFloat(document.getElementById("filledWt").value);
		var numMcPack	= parseInt(document.getElementById("hidNumPack").value);
		for (var i=1; i<=prodnRowCount; i++) {
		   var status = document.getElementById("status_"+i).value;	
		   if (status!='N')
		   {
				var totNumMC = 0;
				var totNumLS = 0;
				for (var j=1; j<=gradeRowCount; j++) {
					var nMC = document.getElementById("numMC_"+j+"_"+i);
					var nLS=0;
					var numMC = (nMC.value!="")?parseInt(nMC.value):0;
					var numLS = (nLS.value!="")?parseInt(nLS.value):0;
					// Convert LS to MC
					var eMC   = Math.floor(numLS/numMcPack);
					if (!isNaN(eMC)) var numMC = parseInt(numMC) + parseInt(eMC);
					var numLS = parseInt(numLS)%parseInt(numMcPack);	
					if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+i).value = numMC;
					//if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+i).value = numLS;
					
					totNumMC += numMC;
					totNumLS += numLS;

				} // Grade loop Ends here
				
				// Total Slabs
				totNumLS=0;
				//alert(totNumLS);
				var totalSlabs 	= (totNumMC*numMcPack)+totNumLS;
				var totalQty	= totalSlabs*filledWt;
				
				if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+i).value = totalSlabs;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+i).value = number_format(totalQty,2,'.','');
			
		   }
		} // Prodn Row count Ends here
		
		displayAllocateSummary(); // Display summary
	}

	var allocatedMCArr = {};
	function displayAllocateSummary()
	{
		removeAllocateRow();
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	

		var pkgGroupArr 	= new Array();
		var lsPkgGroupArr 	= new Array();
			
		
		var mcPkgCode = document.getElementById("hidMCPkgCode").value;
		var numMcPack	= parseInt(document.getElementById("hidNumPack").value);
	
		var totAllocatedMC = 0;
		var totAllocatedLS = 0;

		for (var i=1; i<=prodnRowCount; i++) {
			   var status = document.getElementById("status_"+i).value;	
			   if (status!='N')
			   {
				   //var POId = document.getElementById("POId_"+i).value;				   
					allocatedMCArr[i] = {};

					for (var j=1; j<=gradeRowCount; j++) {
						var numMC = document.getElementById("numMC_"+j+"_"+i).value;
						//var numLS = document.getElementById("numLS_"+j+"_"+i).value;
						var numLS=0;
						var gradeId = document.getElementById("sGradeId_"+j+"_"+i).value;
						// MC
						var arrIndex = mcPkgCode+"_"+gradeId;
						if (numMC!=0 && numMC!="" && !isNaN(numMC)) {
							totAllocatedMC = totAllocatedMC + parseInt(numMC);
							//alert(totAllocatedMC);

							if (typeof(pkgGroupArr[arrIndex])!="undefined" && numMC!=0) {
								numMC = parseInt(numMC) + parseInt(pkgGroupArr[arrIndex]);
							}
							if (numMC!=0) {
								pkgGroupArr[arrIndex] = parseInt(numMC);
								allocatedMCArr[i][gradeId] = numMC;								
							}
						}
						// LS
						if (numLS!=0 && numLS!="" && !isNaN(numLS)) {
							totAllocatedLS = totAllocatedLS + parseInt(numLS);

							if (typeof(lsPkgGroupArr[gradeId])!="undefined" && numLS!=0) {
								numLS = parseInt(numLS) + parseInt(lsPkgGroupArr[gradeId]);
							}
							if (numLS!=0) {
								lsPkgGroupArr[gradeId] = parseInt(numLS);								
							}
						}						
					} // Grade Loop					

			   }
		} // Product Loop Ends
		
		totAllocatedLS=0;
		var totAllocatedSlabs 	= (totAllocatedMC*numMcPack)+totAllocatedLS;
		if (!isNaN(totAllocatedSlabs))
		{
			document.getElementById("totAllocatedSlabs").value = totAllocatedSlabs;
		}

		var prevPkgCode = "";
		for (var pga in pkgGroupArr)
		{
			var splitArr = pga.split("_");
			var mcPkgCode 	= splitArr[0];
			var gradeId	= splitArr[1];
			if (prevPkgCode!=mcPkgCode) {
				allocatePrdnSumryTblRow(mcPkgCode, pkgGroupArr);
			}
			prevPkgCode = mcPkgCode;
		}
		allocateLSSummary(lsPkgGroupArr);
	}

	function removeAllocateRow()
	{
		var tRowCount = fieldId;
		
		for (var i=1; i<=tRowCount; i++) {
				if (tRowCount>0) {
					if(document.getElementById("tRow_"+i)!=null) {
						var tRIndex = document.getElementById("tRow_"+i).rowIndex;	
						document.getElementById('prodnAllocateTble').deleteRow(tRIndex);	
					}					
				}
		}
	}
function validateThawing(form)
{
	

var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
var i=1;
var flag=0;
for (var j=1; j<=gradeRowCount; j++) {						 
						var numthawMC = document.getElementById("tothidAvailableSlabs_"+j).value;
						var numthawMCol=document.getElementById("numMC_"+j+"_"+i).value;
						if (parseInt(numthawMC)<parseInt(numthawMCol))
						{
							alert("Available Quantity less than thawing quantity.Please reenter");
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
				alert("Please enter Number of Thawing Details.");
				return false;
			}


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


var previousCheckId;

    function toggle(chkBox) {
         if (chkBox.checked) {
              if (previousCheckId) {
                   document.getElementById(previousCheckId).checked = false;
              }
              previousCheckId = chkBox.getAttribute('id');
         }
    }
