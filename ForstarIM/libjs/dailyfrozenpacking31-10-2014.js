function validateAddDailyFrozenPacking(form)
{	
	var selMode		= document.getElementById("hidMode").value;

	if (selMode==1) {
		var selectDate		=	form.selectDate.value;
		var rm_lot_id		=	form.rm_lot_id.value;
		var selUnit		=	form.unit.value;
		var selCompany		=	form.company.value;
		var processor		=	form.processor.value;
		//var lotId		=	form.lotId.value;
		var fish		=	form.fish.value;
		var processCode		=	form.processCode.value;
		var freezingStage	=	form.freezingStage.value;
		var eUCode		=	form.eUCode.value;
		var brand		=	form.brand.value;
		var frozenCode		=	form.frozenCode.value;
		var mCPacking		=	form.mCPacking.value;
		var exportLotId		=	form.exportLotId.value;
		var allocateMode	=	form.allocateMode.value;
		var selQuality		=	form.selQuality.value;
	
		var selQuickEntryList = document.getElementById("hidSelQuickEntryList").value;
		
	
		var entrySel = document.getElementById("hidEntrySel").value;	
			
		if (selectDate=="") {
			alert("Please Select a Date.");
			form.selectDate.focus();
			return false;
		}
		
		// if (rm_lot_id=="") {
			// alert("Please Select a RM LOT ID.");
			// form.rm_lot_id.focus();
			// return false;
		// }
		
		if (selUnit=="") {
			alert("Please select a Unit.");
			form.unit.focus();
			return false;
		}
		if (selCompany=="") {
			alert("Please select a Company.");
			form.company.focus();
			return false;
		}
		
		if (processor=="") {
			alert("Please select a Processor.");
			form.processor.focus();
			return false;
		}
	
		if (entrySel=='QE' && selQuickEntryList=="") {
			alert("Please select a Quick Entry List.");
			return false;
		}
	
		if (selQuickEntryList!="") {
			var gradeRowCount = document.getElementById("hidGradeRowCount").value;
			var pcRowCount    = document.getElementById("hidPCRowCount").value;
			var displayQE	  = document.getElementById("displayQE").value;
			
			var packingEntered = false;
			var qelPCExist = false;
			for (i=1; i<=pcRowCount; i++) {
				var qelRecExist = document.getElementById("recExist_"+i).value;
				var totalMCPack  = 0;
				var totLooseSlab = 0;
				var nMC = 0;
				var nLS = 0;
				for (j=1; j<=gradeRowCount; j++) {
					if (displayQE=='DMCLS' || displayQE=='DMC') nMC 	= document.getElementById("numMC_"+i+"_"+j).value;
					if (displayQE=='DMCLS' || displayQE=='DLS') nLS 	= document.getElementById("numLooseSlab_"+i+"_"+j).value;
					var numMC 	 = (nMC=="")?0:nMC;
					var numLSlab = (nLS=="")?0:nLS;
					totalMCPack += parseInt(numMC);
					totLooseSlab += parseInt(numLSlab);				
				}
				if ((totalMCPack!=0 || totLooseSlab!=0)) {
					packingEntered = true;
				}
				
				if (qelRecExist==1) qelPCExist = true;
			}
	
			if (qelPCExist) {
				alert("Process code is already in database.");
				return false;
			}
	
			/*
			if ((totalMCPack==0 && totLooseSlab==0) || (totalMCPack=="" && totLooseSlab=="")) {
				alert("Please enter Number of Packing Details.");
				return false;
			}
			*/
			
			if (!packingEntered) {
				alert("Please enter Packing Details.");
				return false;
			}
		}
		
		if (fish=="" && entrySel=="DE") {
			alert("Please select a fish.");
			form.fish.focus();
			return false;
		}
		
		if (processCode=="" && entrySel=="DE") {
			alert("Please select a Process Code.");
			form.processCode.focus();
			return false;
		}

		if (freezingStage==0 &&  entrySel=="DE") {
			alert("Please select a Freezing Stage.");
			form.freezingStage.focus();
			return false;
		}
	
		if (freezingStage==0 &&  allocateMode!="") {
			alert("Please select a Freezing Stage.");
			form.freezingStage.focus();
			return false;
		}
	
		if (selQuality==0 &&  allocateMode!="") {
			alert("Please select a Quality.");
			form.selQuality.focus();
			return false;
		}
	
		if (eUCode==0 && allocateMode!="") {
			alert("Please select a EU Code.");
			form.eUCode.focus();
			return false;
		}
		
		if (brand==0 && allocateMode!="") {
			alert("Please select a Brand.");
			form.brand.focus();
			return false;
		}
		
		if (frozenCode=="" && entrySel=="DE") {
			alert("Please select a Frozen Code.");
			form.frozenCode.focus();
			return false;
		}
		
		if (mCPacking==0 && allocateMode!="") {
			alert("Please select a MC Packing.");
			form.mCPacking.focus();
			return false;
		}
		
		if (lotId=="" && allocateMode!="") {
			alert("Please enter a Frozen Lot Id.");
			form.lotId.focus();
			return false;
		}
		
		if (exportLotId==0 && allocateMode!="") {
			alert("Please select a PO Id.");
			form.exportLotId.focus();
			return false;
		}	
		//Checking Grade entered or not
		if (fish!="" && processCode!="" && entrySel=="DE") {
			isPackEntered 		= false;
			var entryModified 	= false;
			var rowCount		= parent.iFrame1.document.frmDailyFrozenPackingGrade.hidRowCount.value;
				
			for (i=1;i<=rowCount;i++) {
				var numMC	 = parent.iFrame1.document.getElementById("numMC_"+i).value;
				var numLooseSlab = parent.iFrame1.document.getElementById("numLooseSlab_"+i).value;
				
				var hidNumMC	    = parent.iFrame1.document.getElementById("hidNumMC_"+i).value;
				var hidNumLooseSlab = parent.iFrame1.document.getElementById("hidNumLooseSlab_"+i).value;
	
				if (numMC!="" || numLooseSlab!="") {
					isPackEntered = true;
				}
	
				if (numMC!=hidNumMC || numLooseSlab!=hidNumLooseSlab) {
					entryModified = true;
				}
			}
			if (isPackEntered ==false) {
				alert("Please enter Number of Packing Details.");
				return false;
			}	
			if (entryModified) {
				alert(" Please save the number of packing Details. ");
				return false;
			}	
		}
		// End Here checking grade
	}

	// Edit Mode
	if (selMode==0) {
		var prodnRowCount 	= document.getElementById("hidProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidGradeRowCount").value;			

		for (var i=1; i<=prodnRowCount; i++) {
			var mcPacking = document.getElementById("mcPackingId_"+i);
			if (mcPacking.value==0) {
				alert("Please select a MC Pkg");
				mcPacking.focus();
				return false;
			}

			var packEntered = false;
			for (var j=1; j<=gradeRowCount; j++) {
				var numMC = document.getElementById("numMC_"+j+"_"+i).value;
				var numLS = document.getElementById("numLS_"+j+"_"+i).value;
				if(numMC!=0 || numLS!=0){
					packEntered = true;
				}				
			} // grade Row Count Ends here

 			if (!packEntered) {
				alert("Please enter Number of Packing Details.");
				mcPacking.focus();
				return false;
			}
			
		} // Product Row count Ends here
	}

	if (!confirmSave()) return false;
	else return true;	
}

function validateDailyFrozenPackingSearch(form)
{
	var frozenPackingFrom	=	form.frozenPackingFrom.value;
	var frozenPackingTill	=	form.frozenPackingTill.value;
	
	if (frozenPackingFrom=="") {
		alert("Please select From Date.");
		form.frozenPackingFrom.focus();
		return false;
	}
	
	if (frozenPackingTill=="") {
		alert("Please select Till Date.");
		form.frozenPackingTill.focus();
		return false;
	}

return true;
}

function validateFrozenpackingGrade(form)
{
	isPackEntered 		=	false;
	var rowCount		=	document.getElementById("hidRowCount").value;
	
	for(i=1;i<=rowCount;i++){
		var numMC		=	document.getElementById("numMC_"+i).value;
		var numLooseSlab	=	document.getElementById("numLooseSlab_"+i).value;
		if(numMC!="" || numLooseSlab!=""){
			isPackEntered = true;
		}
	}
	if( isPackEntered ==false ) {
		alert("Please enter Number of Packing Details.");
		return false;
	}	
}


// left /right /up/down moving

function focusNextGradeEntry(e,form,name)
{
	var ecode = getKeyCode(e);	
	
	var rowCount	=	document.getElementById("hidRowCount").value;
      
	var fName = name.split("_");

	for(i=1;i<=rowCount;i++)
	{	
		var numMC		=	"numMC_"+i;
		var numLooseSlab	=	"numLooseSlab_"+i;
		// Down Arrow and enter key
		if ((ecode==13) || (ecode == 0) || (ecode==40))
		{			
			nextTextBoxName = fName[0]+"_"+(parseInt(fName[1])+1);
			var nextControl = eval(form+"."+nextTextBoxName);
			if ( nextControl ) { nextControl.focus();}			
			return false;
    		}
		//uP aRROW
		if ((ecode==38))
		{
			nextTextBoxName = fName[0]+"_"+(parseInt(fName[1])-1);
			var nextControl = eval(form+"."+nextTextBoxName);
			if ( nextControl ) { nextControl.focus(); }
			return false;
    		}
		//Right Arrow
		if ((ecode==39))
		{
			rightArrow	=  "numLooseSlab_"+(parseInt(fName[1]));
			var nextControl = eval(form+"."+rightArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
	    	}
		//Left Arrow
		if ((ecode==37))
		{		
			leftArrow 	=	"numMC_" + (parseInt(fName[1]));	
			var nextControl = eval(form+"."+leftArrow);
			if ( nextControl ) { nextControl.focus(); }
			return false;
    		}
		
	}
}
	// Passing value from Master to child form
	function passMCPkgValue()
	{		
		mCPacking		=	document.getElementById("mCPacking").value;
		parent.iFrame1.document.frmDailyFrozenPackingGrade.hidMcPkg.value=mCPacking;
	}

	// Calculate QE Total
	function calcQETotal(displayQE)
	{
		var LSToMCConversionType   = document.getElementById("hidLS2MCType").value;
		//alert( LSToMCConversionType);
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

				if ((numMC==0 || numMC!=0) && numLooseSlab!=0 && numPacks!="" && LSToMCConversionType=="AC") {		
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

	// left /right /up/down moving (Focus Next)
	function fNGradeTxtBox(e, form, fldName, displayQE)
	{
		var ecode = getKeyCode(e);	

		var gradeRowCount = document.getElementById("hidGradeRowCount").value;
		var pcRowCount    = document.getElementById("hidPCRowCount").value;
	
		var fName = fldName.split("_");
		
			// Down Arrow and enter key
			if ((ecode==13) || (ecode == 0) || (ecode==40))
			{			
				nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])+1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus();}			
				return false;
			}
			//uP aRROW
			if ((ecode==38))
			{
				nextTextBoxName = fName[0]+"_"+fName[1]+"_"+(parseInt(fName[2])-1);
				var nextControl = eval(form+"."+nextTextBoxName);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
			//Right Arrow
			rightArrow = "";
			if ((ecode==39))
			{
				if (fName[0]!="numLooseSlab" && displayQE=='DMCLS') rightArrow =   "numLooseSlab_"+fName[1]+"_"+(parseInt(fName[2]));
				else if (displayQE=='DLS') rightArrow =   "numLooseSlab_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				else rightArrow =   "numMC_"+(parseInt(fName[1])+1)+"_"+(parseInt(fName[2]));
				var nextControl = eval(form+"."+rightArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
			//Left Arrow
			if ((ecode==37))
			{	
				if (fName[0]!="numMC" && displayQE=='DMCLS') leftArrow = "numMC_"+fName[1]+"_"+(parseInt(fName[2]));	
				else if (displayQE=='DMC') leftArrow = "numMC_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));	
				else if (displayQE=='DLS') leftArrow =   "numLooseSlab_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));
				else leftArrow =	"numLooseSlab_"+(parseInt(fName[1])-1)+"_"+(parseInt(fName[2]));	
				
				var nextControl = eval(form+"."+leftArrow);
				if ( nextControl ) { nextControl.focus(); }
				return false;
			}
	}

	function hidRows()
	{
		var selQuickEntryList = document.getElementById("hidSelQuickEntryList").value;
		var entrySel = document.getElementById("hidEntrySel").value;
		if (entrySel=='QE') {
			document.getElementById("fishRow").style.display="none";
			document.getElementById("pcRow").style.display="none";
			document.getElementById("fsRow").style.display="none";
			document.getElementById("qltyRow").style.display="none";
			document.getElementById("eucRow").style.display="none";
			document.getElementById("brndRow").style.display="none";
			document.getElementById("fcRow").style.display="none";
			document.getElementById("mcpRow").style.display="none";
			document.getElementById("fliRow").style.display="none";
			document.getElementById("eliRow").style.display="none";
			document.getElementById("gradeRow").style.display="none";
			document.getElementById("buyerRow").style.display="none";			
			
		} else {
			document.getElementById("fishRow").style.display="";
			document.getElementById("pcRow").style.display="";
			document.getElementById("fsRow").style.display="";
			document.getElementById("qltyRow").style.display="";
			document.getElementById("eucRow").style.display="";
			document.getElementById("brndRow").style.display="";
			document.getElementById("fcRow").style.display="";
			document.getElementById("mcpRow").style.display="";
			document.getElementById("fliRow").style.display="";
			document.getElementById("eliRow").style.display="";
			document.getElementById("gradeRow").style.display="";
			document.getElementById("buyerRow").style.display="";
		}
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

	function chkQePcExist()
	{
		/* $qeFreezingStageId, $qeEUCodeId, $qeBrandId, $qeFrozenCodeId, $qeMCPackingId, $qeFrozenLotId, $qeExportLotId, $qeQualityId, $qeCustomerId */
		var selectDate 		= document.getElementById("selectDate").value;
		var processor  		= document.getElementById("processor").value;
		var qeFrozenCodeId	= document.getElementById("qeFrozenCodeId").value;
		var qeMCPackingId	= document.getElementById("qeMCPackingId").value;
		qeMCPackingId		= (qeMCPackingId!=0)?qeMCPackingId:document.getElementById("qeMCPacking").value;
		var qeQualityId		= document.getElementById("qeQualityId").value;

		var pcRowCount	= document.getElementById("hidPCRowCount").value;		
		/* Hide on 24-02-10
		for (i=1; i<=pcRowCount; i++) {
			var fishId	= document.getElementById("hFishId_"+i).value;
			var pCodeId	= document.getElementById("hProcesscodeId_"+i).value;
			xajax_chkQERecExist(selectDate, processor, fishId, pCodeId, qeFrozenCodeId, qeMCPackingId, qeQualityId, i);
			
		}
		*/
	}

	function calcMCPack(displayQE)
	{
		setTimeout("calcQETotal('"+displayQE+"')",1000);
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
		displaySummary(); // Display summary
	}

	function callProdnCalc()
	{
		setTimeout("calcProdnQty()",500);
	}

	// left /right /up/down moving (Focus Next)
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

	// Production details summary row
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
		lsSummary(lsPkgGroupArr);
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
			} else document.getElementById("LS_"+gId).innerHTML = "&nbsp;";
		}
	}

	function cfmDelete(form, prefix, rowcount)
	{			
		var rowCount	=	rowcount;
		var fieldPrefix	=	prefix;
		var frozenPackingFrom = document.getElementById("frozenPackingFrom").value;
		var frozenPackingTill = document.getElementById("frozenPackingTill").value;
		//var conDelMsg	=	"Do you wish to delete the selected items?";
		var conDelMsg = "The date range you have selected is "+frozenPackingFrom+" and "+frozenPackingTill+". \n\n Do you wish to delete the selected items?";
			
		
		if (!isAnyChecked(rowCount,fieldPrefix)) {
			alert("Please select a record to delete.");
			return false;
		}
		
		if (confirm(conDelMsg)) return true;
		return false;	
	}

	/* Allocate Starts Here */
	function addAllocateRow()
	{
		var atRow = document.getElementById("hidAllocateProdnRowCount").value;
		atRow++;

		var table = "#prodnAllocateTble";
		var $tr = $(table).find("tbody tr.tr_clone:last");
		var $clone = $tr.clone().attr("id", "allocateRow_"+atRow).show();
	
		$clone.find("input,select,a").each(function()
		{
			var oldIDName = $(this).attr("id");
			var fieldPart = oldIDName.substring(0, oldIDName.lastIndexOf("_") + 1);
			//var last = fieldIdName.substring(fieldIdName.lastIndexOf("_") + 1, fieldIdName.length);
			var newIDName = fieldPart + atRow;
			
			$(this).attr({
			  'id': newIDName,
			  'name': newIDName
			});
		});	
		$tr.after($clone);

		// Reset
		$('input[id^=numMC_][id $='+atRow+']').val('');
		$('input[id^=numLS_][id $='+atRow+']').val('');
		$('input[id^=totalSlabs_][id $='+atRow+']').val('');
		$('input[id^=totalQty_][id $='+atRow+']').val('');
		$('select[id^=POId_][id $='+atRow+']').val('');
		$('input[id^=POEntryId_][id $='+atRow+']').val('');
		$('input[id^=status_][id $='+atRow+']').val('');
		
		$('a[id^=viewPOForAllocation_][id $='+atRow+']').attr('onclick','').unbind('click');
		

		document.getElementById("hidAllocateProdnRowCount").value = atRow;	
		//calcAllocateProdnQty();
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
					var nLS = document.getElementById("numLS_"+j+"_"+i);
					var numMC = (nMC.value!="")?parseInt(nMC.value):0;
					var numLS = (nLS.value!="")?parseInt(nLS.value):0;
					// Convert LS to MC
					var eMC   = Math.floor(numLS/numMcPack);
					if (!isNaN(eMC)) var numMC = parseInt(numMC) + parseInt(eMC);
					var numLS = parseInt(numLS)%parseInt(numMcPack);	
					if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+i).value = numMC;
					if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+i).value = numLS;
					
					totNumMC += numMC;
					totNumLS += numLS;

				} // Grade loop Ends here
				
				// Total Slabs
				var totalSlabs 	= (totNumMC*numMcPack)+totNumLS;
				// total Qty	
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

		for (var i=1; i<=prodnRowCount; i++) {
			   var status = document.getElementById("status_"+i).value;	
			   if (status!='N')
			   {
				   var POId = document.getElementById("POId_"+i).value;	
					allocatedMCArr[i] = {};

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
							if (numMC!=0) {
								pkgGroupArr[arrIndex] = parseInt(numMC);
								allocatedMCArr[i][gradeId] = numMC;
							}
						}
						// LS
						if (numLS!=0 && numLS!="" && !isNaN(numLS)) {
							if (typeof(lsPkgGroupArr[gradeId])!="undefined" && numLS!=0) {
								numLS = parseInt(numLS) + parseInt(lsPkgGroupArr[gradeId]);
							}
							if (numLS!=0) lsPkgGroupArr[gradeId] = parseInt(numLS);
						}						
					} // Grade Loop

					

			   }
		} // Product Loop Ends 

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

	function allocatePrdnSumryTblRow(mcPkgCode, pkgArr)
	{	
		var gradeCount  = document.getElementById("hidAllocateGradeRowCount").value;		
		var tbl		= document.getElementById("prodnAllocateTble");
		var lastRow	= tbl.rows.length-1;
		var row		= tbl.insertRow(lastRow);

		row.height	= "28";
		//row.className 	= "whiteRow";
		row.align 	= "center";

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
		//cell3.className	= "listing-item"; cell3.align	= "center"; cell3.style.borderRight=bdr; cell3.style.borderBottom=bdr;	
		cell3.className	= "listing-item"; cell3.align	= "center"; cell3.style.borderRight=bdr; cell3.style.borderBottom=bdr;	
		nextCell1.className = "listing-item"; nextCell1.align	= "center"; nextCell1.noWrap = "true";
		nextCell2.className = "listing-item"; nextCell2.align	= "center"; nextCell2.noWrap = "true";

		cell1.innerHTML	= "";
		cell2.innerHTML	= "MC PKG";
		//cell3.innerHTML	= mcPkgCode;
		cell3.innerHTML	= "&nbsp;";
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
		document.getElementById("hidAllocateSummaryTblRowCount").value = fldId;
		//document.getElementById("hidAllocateTblRowCount").value = fieldId;
	}

	function allocateLSSummary(lsPkgArr)
	{
		var gradeCount  = document.getElementById("hidAllocateGradeRowCount").value;
		for (var i=1; i<=gradeCount;i++) {
			var gId = document.getElementById("gId_"+i).value;
			if (typeof(lsPkgArr[gId])!="undefined") {				
				document.getElementById("LS_"+gId).innerHTML = "<strong>"+lsPkgArr[gId]+"</strong>";
			} else document.getElementById("LS_"+gId).innerHTML = "&nbsp;";
		}
	}

	// left /right /up/down moving (Focus Next)
	function nTxtBoxAL(e, form, obj)
	{
		var fldName = obj.id;
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


function validateDFPAllocation(form)
{		
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;			
		var poSelected = false;
	
		for (var i=1; i<=prodnRowCount; i++) {
				var status = document.getElementById("status_"+i).value;	
			   if (status!='N')
			   {					
					var POId = document.getElementById("POId_"+i);
					if (POId.value=="" || POId.value==0) {
						alert("Please select a purchase order");
						POId.focus();
						return false;
					}

					var packEntered = false;
					for (var j=1; j<=gradeRowCount; j++) {
						var numMC = document.getElementById("numMC_"+j+"_"+i).value;
						var numLS = document.getElementById("numLS_"+j+"_"+i).value;
						if(numMC!=0 || numLS!=0){
							packEntered = true;
						}				
					} // grade Row Count Ends here

					if (!packEntered) {
						alert("Please enter Number of Packing Details.");
						POId.focus();
						return false;
					}

					if (POId.value!="") {	
						poSelected = true;
					}
			   } 
		} // Product Row count Ends here

		if (!poSelected)
		{
			alert("Please add atleast one allocation");
			return false;
		}

		if (!checkPOQty())
		{
			if (!confirm("Do your wish to continue?"))
			{
				return false;
			}
		}

		if (!validatePORepeat()) {
			return false;
		}

	if (!confirmSave()) return false;
	else return true;	
}

var removedAllocationArr = new Array();
function setAllocateRowStatus(obj)
{
	var fldName = obj.id;
	var fName = fldName.split("_");

	if (confirmRemoveItem()) {
		
		var selRowId = fName[1];
		var POEntryId = document.getElementById("POEntryId_"+selRowId).value;
		if (selRowId!=0)
		{
			removedAllocationArr.push(POEntryId);
		}		
		
		$("#status_"+selRowId).val("N");
		$("#allocateRow_"+selRowId).hide();

		//document.getElementById("hidAllocateProdnRowCount").value = parseInt(document.getElementById("hidAllocateProdnRowCount").value)-1;
		calcAllocateProdnQty();
	}

	delArrStr = removedAllocationArr.join(",");
	document.getElementById("hidDelAllocationArr").value = delArrStr;

	return false;
}


function changePO(obj)
{
	var fldName = obj.id;	
	var fName = fldName.split("_");	
	var selRowId = fName[1];
	validatePORepeat(); // Validate Repeat
	
	var selPOId = obj.value;
	
	if (selPOId>0)
	{
		getPOItems(selPOId, selRowId); 

		$("#viewPOForAllocation_"+selRowId).click( function () { viewPO(selPOId); });
	}
	else $("#viewPOForAllocation_"+selRowId).attr('onclick','').unbind('click');
}

function viewPO(POId)
{
	if (POId!="")
	{
			printWindow('ViewPO.php?selPOId='+POId,700,600);
	}

}

function getPOItems(poId, selRowId)
{
	xajax_getPOItems(poId, selRowId);
}

var poMCArr = {};
function SetPOGrades(selRowId, poGradeVal)
{	
	poMCArr[selRowId] = {};
	var gradeSplitArr = poGradeVal.split(",");	
	for (var i=0;i<gradeSplitArr.length;i++ )
	{
		var gradeVal = gradeSplitArr[i];
		var gradeValArr = gradeVal.split(":");
		var gradeId = gradeValArr[0];
		var numMc	= gradeValArr[1];
		
		poMCArr[selRowId][gradeId] = numMc;
		//poMCArr[poId].push(gradeId+":"+numMc);
		//poMCArr[poId][gradeId] = numMc;
	}

	/*
	for (var rId in poMCArr)
	{
		var gArr = poMCArr[rId];
		for (var gId in gArr)
		{
		}
	}
	*/

	checkPOQty();
}

function validatePORepeat()
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
	
    var rc = document.getElementById("hidAllocateProdnRowCount").value;
    var prevOrder = 0;
    var arr = new Array();
    var arri=0;

    for( j=1; j<=rc; j++ )    {
		var status = document.getElementById("status_"+j).value;	
       if (status!='N')
       {
        var rv = document.getElementById("POId_"+j).value;
        if ( arr.indexOf(rv) != -1 )    {
            alert("Please make sure the selected purchase order is not duplicate.");
            document.getElementById("POId_"+j).focus();
            return false;
        }
        arr[arri++]=rv;
     }
    }

	
    return true;
}

function checkPOQty()
{
	var productAllocation = false; 
	for (var aRId in allocatedMCArr )
	{
		var aArr = allocatedMCArr[aRId];
		for (var gId in aArr )
		{
			var allocatedNumMC = aArr[gId];
			var poMC = 0
			if (typeof(poMCArr[aRId][gId])!="undefined") {
				poMC = poMCArr[aRId][gId];
			}

			if (allocatedNumMC>poMC)
			{
				productAllocation = true;
			}

		}
	}// Loop Ends here
	
	if (productAllocation)
	{
		alert("Purchase order MC and allocated MC is different");
		return false;
	}

	return true;
}

/* Allocation ends here */

// Conversion starts here
function convertLS2MC()
{
	var prodnRowCount 	= document.getElementById("hidProdnRowCount").value;
	var gradeRowCount = document.getElementById("hidGradeRowCount").value;
	var differentPkg = false;

	// Check different Packing exist
	var prevMCPKgId = "";
	var numMCPack = "";
	
	for (var i=1; i<=prodnRowCount; i++) {
		var mcPkgId = document.getElementById("mcPackingId_"+i).value;
		numMCPack = document.getElementById("numMcPack_"+i).value;

		if (prevMCPKgId!=mcPkgId && i>1)
		{
			differentPkg = true;
		}
		prevMCPKgId = mcPkgId;
	}
	if (differentPkg)
	{
		alert("Different packing exist in the selected combination. Please correct the packing selection.");
		return false;
	}

	var LSMisMatch = false;
	var LSNotExistCount = 0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{
		var ls = document.getElementById("numLSC_"+i).value;
		var numLS = (!isNaN(ls) && ls!="")?ls:0;
		if (numLS==0)
		{
			LSNotExistCount++;
		}

		var balLS = parseInt(numLS)%parseInt(numMCPack);
		if (balLS>0)
		{
			LSMisMatch = true;
		}		
	}

	if (LSMisMatch)
	{
		alert("LS cannot convert to MC. Please check the Total LS against each grade.");
		return false;
	}

	if (gradeRowCount==LSNotExistCount)
	{	
		alert("LS is not existing for conversion.");
		return false;
	}

	// Convert each grade LS to MC , set to first row
	var converted = false;
	for (var i=1;i<=gradeRowCount ;i++ )
	{
		var ls = document.getElementById("numLSC_"+i).value;
		var numLS = (!isNaN(ls) && ls!="")?ls:0;
		if (numLS>0)
		{
			var mcPack = Math.floor(numLS/numMCPack); // Convert to MC
			if (mcPack>0)
			{
				var existingMC = document.getElementById("numMC_"+i+"_1").value;
				var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
				document.getElementById("numMC_"+i+"_1").value = parseInt(existMC)+parseInt(mcPack);
				document.getElementById("numLSC_"+i).value = "";
				converted = true;
			}			
		}			
	}

	if (converted)
	{
		$('input[name^="numLS_"]').val('');
		calcProdnQty();
	}

	if (!confirmSave()) return false;
	else return true;
}

function convert2MC()
{
	//alert("hii"); 
	var prodnRowCount 	= document.getElementById("hidProdnRowCountCvt").value;
	var gradeRowCount = document.getElementById("hidGradeRowCountCvt").value;
	var differentPkg = false;

	// Check different Packing exist
	var prevMCPKgId = "";
	var numMCPack = "";
	
	for (var i=1; i<=prodnRowCount; i++) {
		var mcPkgId = document.getElementById("mcPackingIdCvt_"+i).value;
		if (prevMCPKgId!=mcPkgId && i>1)
		{
			differentPkg = true;
		}
		prevMCPKgId = mcPkgId;
	}
	if (differentPkg)
	{
		alert("Different packing exist in the selected combination. Please correct the packing selection.");
		return false;
	}

	var LSMisMatch = false;
	var LSNotExistCount = 0;
	for (var i=1; i<=gradeRowCount; i++ )
	{	
		var ls = document.getElementById("numLSCCvt_"+i).value;
		var numLS = (!isNaN(ls) && ls!="")?ls:0;
		if (numLS==0)
		{
			LSNotExistCount++;
		}
	}

	if (gradeRowCount==LSNotExistCount)
	{	
		alert("LS is not existing for conversion.");
		return false;
	}
		
	if(!repeatRMlotId()){
		return false;
	}
		
	
	
	// Convert each grade LS to MC , set to first row
	var converted = false;
	
	for (var i=1;i<=gradeRowCount ;i++ )
	{
		var ls = document.getElementById("numLSCCvt_"+i).value;
		var numLS = (!isNaN(ls) && ls!="")?ls:0;
		if (numLS>0)
		{
			converted = true;			
		}			
	}

	if (converted)
	{
		calcProdnQtyLS();
	}

}

function calcProdnQtyLS()
	{
		var slabTotal=0; var qtyTotal=0; var gradeId=""; var lsPkgGroupArr 	= new Array(); var EntryId = new Array(); var mc=''; var ls=''; var lsCnt='';
		//alert("hii");
		var LSToMCConversionType   = document.getElementById("hidLS2MCType").value;
		var prodnRowCount 	= document.getElementById("hidProdnRowCountCvt").value;
		var gradeRowCount = document.getElementById("hidGradeRowCountCvt").value;
		//alert("hii");
		var filledWt		= parseFloat(document.getElementById("filledWt").value);
		
		for (var i=1; i<=prodnRowCount; i++) {
		//alert(i);
			var totNumLS = 0;
			//var mcPackingIdCvt=document.getElementById("mcPackingIdCvt_"+i).value;
			//var numMcPackCvt=document.getElementById("numMcPackCvt_"+i).value;
			var fishIdCvt=document.getElementById("fishIdCvt_"+i).value;
			var processIdCvt=document.getElementById("processIdCvt_"+i).value;
			var freezingIdCvt=document.getElementById("freezingIdCvt_"+i).value;
			var frozenCodeIdCvt=document.getElementById("frozenCodeIdCvt_"+i).value;
			var companyId=document.getElementById("companyId_"+i).value;
			var unitId=document.getElementById("unitId_"+i).value;
			var dFrznPkgEntryIdCvt=document.getElementById("dFrznPkgEntryIdCvt_"+i).value;
			
			if(EntryId=="")
			{
				EntryId=dFrznPkgEntryIdCvt;
			}
			else
			{
				EntryId+=','+dFrznPkgEntryIdCvt;
			}
			
			
			/*for (var j=1; j<=gradeRowCount; j++) {
				var numLS = document.getElementById("numLSCvt_"+j+"_"+i).value;
				var gradeId = document.getElementById("sGradeIdCvt_"+j+"_"+i).value;
				//alert(numLS);
				// Convert LS to MC
				if (LSToMCConversionType=='AC')
				{				
					if (numLS!=0 && numLS!="" && !isNaN(numLS)) {
					if (typeof(lsPkgGroupArr[gradeId])!="undefined" && numLS!=0) {
						numLS = parseInt(numLS) + parseInt(lsPkgGroupArr[gradeId]);
					}
					if (numLS!=0) lsPkgGroupArr[gradeId] = parseInt(numLS);
				}

				}
				//totNumLS += numLS;
			} */
			
				
		} // Prodn Row count Ends here
		
		var hidLSRowCount=document.getElementById("hidLSRowCount").value;
		//alert(hidLSRowCount);
		for(j=0; j<hidLSRowCount; j++)
		{
			var mcPackingIdCvt=document.getElementById("mcPackingIdCnvrtLs_0").value;
			var numMcPackCvt=document.getElementById("numMcPackCnvrtLs_0").value;
			for(k=0; k<gradeRowCount; k++)
			{	//alert(k+'--'+j);
				var gIdrm=document.getElementById("gIdrm_"+k).value;
				var gradeQty = document.getElementById("gradeQty_"+k+"_"+j).value;
				//alert(gradeQty);
				if (gradeQty!=0 && gradeQty!="" && !isNaN(gradeQty)) {
					if (typeof(lsPkgGroupArr[gIdrm])!="undefined" && gradeQty!=0) {
						gradeQty = parseInt(gradeQty) + parseInt(lsPkgGroupArr[gIdrm]);
					}
					if (gradeQty!=0) lsPkgGroupArr[gIdrm] = parseInt(gradeQty);
				}
			}
		}
		
		
		
		for (var k=1; k<=gradeRowCount; k++) {
		var gId = document.getElementById("gId_"+k).value;
		var gradeIDValue=lsPkgGroupArr[gId];
		//alert(gradeIDValue);
		//alert("LSCvt_"+gId);
			if(gradeIDValue!=undefined)
			{	
				var ls=gradeIDValue % numMcPackCvt;
				var str=(gradeIDValue / numMcPackCvt);
				mc=parseInt(str); 
				slabTotal+=gradeIDValue;
				qtyTotal+=gradeIDValue*filledWt;
				
			}
			//alert(ls);
			if(ls!=0 || ls!='')
			{
				var lsCnt=1;
				alert("cannot convert LS to MC");
				return false;
			}
			mc=''; ls='';
			//document.getElementById("numMCCvt_"+k).value=mc;
			/*document.getElementById("numLSCvt_"+k+"_1").value=ls;
			document.getElementById("numLSCCvt_"+k).value=ls;
			//document.getElementById("LSCvt_"+gId).value=ls;
			document.getElementById("LSCvt_"+gId).innerHTML = "<strong>"+ls+"</strong>";
			mc=''; ls='';*/
		}
			/*document.getElementById("totalSlabsCvt_1").value=slabTotal;
			document.getElementById("totalQtyCvt_1").value=qtyTotal;*/
		
		
	
	// for (var l=2; l<=prodnRowCount; l++) {	
		// for (var m=1; m<=gradeRowCount; m++) {
	
		// document.getElementById("numLSCvt_"+m+"_"+l).value="";
		
		// }
		// document.getElementById("totalSlabsCvt_"+l).value="";
		// document.getElementById("totalQtyCvt_"+l).value="";
	// }
		//displaySave();
		if(lsCnt==1)	
		{
			return false;
		}
		else{
			lsSummaryLot(lsPkgGroupArr,mcPackingIdCvt,fishIdCvt,processIdCvt,freezingIdCvt,frozenCodeIdCvt,companyId,unitId,filledWt,EntryId);
			return true;
		}			
	}




function calcProdnQtyLS_old()
	{
		var totalSlabs=0; var totalQty=0; var gradeId=""; var lsPkgGroupArr 	= new Array(); var EntryId = new Array();
		//alert("hii");
		var LSToMCConversionType   = document.getElementById("hidLS2MCType").value;
		var prodnRowCount 	= document.getElementById("hidProdnRowCountCvt").value;
		var gradeRowCount = document.getElementById("hidGradeRowCountCvt").value;
		//alert("hii");
		var filledWt		= parseFloat(document.getElementById("filledWt").value);
		
		for (var i=1; i<=prodnRowCount; i++) {
		//alert(i);
			var totNumLS = 0;
			var mcPackingIdCvt=document.getElementById("mcPackingIdCvt_"+i).value;
			var fishIdCvt=document.getElementById("fishIdCvt_"+i).value;
			var processIdCvt=document.getElementById("processIdCvt_"+i).value;
			var freezingIdCvt=document.getElementById("freezingIdCvt_"+i).value;
			var frozenCodeIdCvt=document.getElementById("frozenCodeIdCvt_"+i).value;
			var companyId=document.getElementById("companyId_"+i).value;
			var unitId=document.getElementById("unitId_"+i).value;
			var dFrznPkgEntryIdCvt=document.getElementById("dFrznPkgEntryIdCvt_"+i).value;
			
			if(EntryId=="")
			{
				EntryId=dFrznPkgEntryIdCvt;
			}
			else
			{
				EntryId+=','+dFrznPkgEntryIdCvt;
			}
			
			
			for (var j=1; j<=gradeRowCount; j++) {
				var numLS = document.getElementById("numLSCvt_"+j+"_"+i).value;
				var gradeId = document.getElementById("sGradeIdCvt_"+j+"_"+i).value;
				//alert(numLS);
				// Convert LS to MC
				if (LSToMCConversionType=='AC')
				{				
					if (numLS!=0 && numLS!="" && !isNaN(numLS)) {
					if (typeof(lsPkgGroupArr[gradeId])!="undefined" && numLS!=0) {
						numLS = parseInt(numLS) + parseInt(lsPkgGroupArr[gradeId]);
					}
					if (numLS!=0) lsPkgGroupArr[gradeId] = parseInt(numLS);
				}

				}
				//totNumLS += numLS;
			} 
			
				
		} 
		
		
			
		//lsSummaryLot(lsPkgGroupArr,mcPackingIdCvt,fishIdCvt,processIdCvt,freezingIdCvt,frozenCodeIdCvt,companyId,unitId,filledWt,EntryId);	
			
			
	}

function lsSummaryLot(lsPkgArr,mcPackingIdCvt,fishIdCvt,processIdCvt,freezingIdCvt,frozenCodeIdCvt,companyId,unitId,filledWt,EntryId)
	{ var gradeId=new Array(); var realValue="";
		var gradeCount  = document.getElementById("hidGradeRowCountCvt").value;
		for (var i=1; i<=gradeCount;i++) {
			var gId = document.getElementById("gId_"+i).value;
			
			if(gradeId=="")
			{
				gradeId=gId;
			}
			else
			{
				gradeId+=','+gId;
			}
			
			//alert(gId);
			//alert(lsPkgArr[gId]);
			if (typeof(lsPkgArr[gId])!="undefined") {	
				realValue=1;
				// document.getElementById("LS_"+gId).innerHTML = "<strong>"+lsPkgArr[gId]+"</strong>";
			// } else document.getElementById("LS_"+gId).innerHTML = "&nbsp;";
			}
			
			
		}
		//alert(lsPkgArr+'/'+mcPackingIdCvt+'/'+gradeId);
		if(realValue==1)
		{
			xajax_generateRMlotId(mcPackingIdCvt,fishIdCvt,processIdCvt,freezingIdCvt,frozenCodeIdCvt,companyId,unitId,lsPkgArr,gradeId,filledWt,EntryId);
			return true;
		}
		else
		{
			alert("cannot convert to LS");
			return false;
		}
		
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
		alert(existMC);

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



function cfmConvertLS(form,prefix,rowcount)
{
   
	//showFnLoading();
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	//alert(rowCount);
	var conDelMsg	=	"Do you wish to delete the selected items?";
	
	if(!isAnyChecked(rowCount,fieldPrefix))
	{
		alert("Please select a record to delete.");
		return false;
	}
	
	if(!validateRepeatIssuance()){
	
		return false;
	}
		
	return true;

}

function isAnyChecked(rowCount,fieldPrefix)
{
	for ( i=1; i<=rowCount; i++ )
	{
		if(document.getElementById(fieldPrefix+i).checked)
		{
			return true;
		}		
	}
	return false;
}

function repeatRMlotId()
{	//alert("hii");
	if (Array.indexOf != 'function') {  
	Array.prototype.indexOf = function(f, s) {
		if (typeof s == 'undefined') s = 0;
		for (var i = s; i < this.length; i++) {   
		if (f === this[i]) return i; 
		}    
		return -1;  
		}
	}
		
	var arra = new Array();
	var arrk=0;
	var hidLSRowCount=document.getElementById('hidLSRowCount').value;
	for( j=0; j<hidLSRowCount; j++ )	{
	  
		var totalRM_id=document.getElementById('rmlotId_CnvrtLs_'+j).value;
		if ( arra.indexOf(totalRM_id) != -1 )	{
			alert("RM lot id Cannot be duplicate.");
			document.getElementById("rmlotId_CnvrtLs_"+j).focus();
			return false;
		}
		arra[arrk++]=totalRM_id;
           
	}
	
	return true;	
	
	
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

	var sc = document.getElementById("hidRowCount").value;
	var arrGP = new Array();
	var arra = new Array();
	var freez = new Array();
	var frozen = new Array();
	var company = new Array();
	var unit = new Array();
	var arr=""; var LS="";
	var arrk=0;
	for(j=1; j<=sc; j++ )	
	{
		if(document.getElementById("delGId_"+j).checked)
		{
			var sg = document.getElementById("rmLotID_"+j).value;
			//alert(sg);
			if(sg=="0")
			{
				arr=1;
			}
			
			var numLS = document.getElementById("numLS_"+j).value;
			//alert(sg);
			if(numLS=="0.00")
			{
				LS=1;
			}
			
			var val=document.getElementById("delGId_"+j).value;
			var process=val.split(",");
			//alert(process[0]);
			var processid=process[0];
			var freezing=process[1];
			var frozenCode=process[2];
			var packUnit=process[4];
			//alert(packUnit);
			arra[arrk] = processid;
			arrGP[arrk] = packUnit;
			freez[arrk] = freezing;
			frozen[arrk] = frozenCode;
			
			var company_id=document.getElementById("companyId_"+j).value;
			company[arrk] = company_id;
			
			var unit_id=document.getElementById("unitId_"+j).value;
			unit[arrk] = unit_id;
			
			if ( arra.indexOf(processid) > 0 )	{
				var repeat=1;
				alert("RM lot id with same process code can only be used.");
			}
			if ( arrGP.indexOf(packUnit) > 0 )	{
				var repeat=1;
				alert("RM lot id with same MC PKG can only be used.");
			}
			if ( freez.indexOf(freezing) > 0 )	{
				var repeat=1;
				alert("RM lot id with same Freezing Stage can only be used.");
			}
			if ( frozen.indexOf(frozenCode) > 0 )	{
				var repeat=1;
				alert("RM lot id with same Frozen code can only be used.");
			}
			if ( company.indexOf(company_id) > 0 )	{
				var repeat=1;
				alert("RM lot id with same company can only be used.");
			}
			if ( unit.indexOf(unit_id) > 0 )	{
				var repeat=1;
				alert("RM lot id with same unit can only be used.");
			}
			arrk++;	
		}
    }
	if(arr==1)
	{
		alert("Cannot convert data with out RM lot id .");
		return false;
	}
	if(LS==1)
	{
		alert("Cannot use data with out LS value.");
		return false;
	}
	if(repeat!=1)
	{
		//alert("hii");
		return true;
	}
	else
	{
		//alert("hui");
		return false;
	}
	//return true;
}

function displaySave()
{
$('#cmdConvert2MC').hide();
$('#cmdConvertLSSave').show();
$('#cmdConvert2MC1').hide();
$('#cmdConvertLSSave1').show();
}


function addMCForConvert(tableId,rmId,mcPkgId,numPackId,gradeIdAll,gradeAll,mode)
{
	//alert("hii--jii");
	//alert(gradeIdAll);
	//alert(gradeAll);
	var grdAr=gradeIdAll.split("/"); 
	//alert(grdAr);
	var grdlen=grdAr.length;
	//alert(grdlen);
	/*for(l=0; l<grdlen; l++)
	{
	alert(grdAr[l]);
	}*/	var m=5;
		var tbl		= document.getElementById(tableId);
			var lastRow	= tbl.rows.length;
			var row		= tbl.insertRow(lastRow);
			
			row.height	= "28";
			row.className 	= "whiteRow";
			row.align 	= "center";
			row.id 		= "dRow_"+fdId;	
			
			var cell1	= row.insertCell(0);
			var cell2	= row.insertCell(1);
			var cell3	= row.insertCell(2);
			var cell4	= row.insertCell(3);
			
			/*var cell5	= row.insertCell(4);
			var cell6	= row.insertCell(5);
			var cell7	= row.insertCell(6);
			var cell8	= row.insertCell(7);
			var cell9	= row.insertCell(8);
			var cell10	= row.insertCell(9);
			var cell11	= row.insertCell(10);
			var cell12	= row.insertCell(11);
			var cell13	= row.insertCell(12);
			var cell14	= row.insertCell(13);
			var cell15	= row.insertCell(14);
			var cell16	= row.insertCell(15);
			var cell17	= row.insertCell(16);*/
			
			//var cell"+h+"= row.insertCell(17);
			
			//alert("cell"+m);
			/*for(l=0; l<grdlen; l++)
			{
				var cellvalue="cell"+m;
				
				cellvalue= row.insertCell(parseInt(m)-1);
				m++;
			}*/
			for(l=0; l<grdlen; l++)
			{	var cellvalue ="cell"+m;
				window[cellvalue]  = row.insertCell(parseInt(m)-1);
			m++;
			}
			cell1.id = "srNo_"+fdId;		
			cell1.className	= "listing-item"; cell1.align	= "center";
			cell2.className	= "listing-item"; cell2.align	= "center";
			cell3.className	= "listing-item"; cell3.align	= "center";
			cell4.className	= "listing-item"; cell4.align	= "center";
			var n=5;
			for(l=0; l<grdlen; l++)
			{	var cellvalues ="cell"+n;
				var cellalign ="cell"+n;
				window[cellvalues].className  = "listing-item"; window[cellalign].align  = "center"; 
			n++;
			}
			
			/*cell5.className	= "listing-item"; cell5.align	= "center";
			cell6.className	= "listing-item"; cell6.align	= "center";
			cell7.className	= "listing-item"; cell7.align	= "center";
			cell8.className	= "listing-item"; cell8.align	= "center";
			cell9.className	= "listing-item"; cell9.align	= "center";
			cell10.className	= "listing-item"; cell10.align	= "center";
			cell11.className	= "listing-item"; cell11.align	= "center";
			cell12.className	= "listing-item"; cell12.align	= "center";
			cell13.className	= "listing-item"; cell13.align	= "center";
			cell14.className	= "listing-item"; cell14.align	= "center";
			cell15.className	= "listing-item"; cell15.align	= "center";
			cell16.className	= "listing-item"; cell16.align	= "center";
			cell17.className	= "listing-item"; cell17.align	= "center";
			*/
		
			/*cell5.className	= "listing-item"; cell5.align	= "center";
			for(l=0; l<grdlen; l++)
			{
				cell+n+.className	= "listing-item"; cell+n+.align	= "center";
			n++;
			}*/
			
			var sl=parseInt(fdId)+1;
		
			var rmlot	= "<select name='rmlotId_CnvrtLs_"+fdId+"' id='rmlotId_CnvrtLs_"+fdId+"' onchange=\"checkLsValueLot(document.getElementById('rmlotId_CnvrtLs_"+fdId+"').value,"+fdId+");\"  ><option value='0'>--Select--</option>";
						<?php
				if (sizeof($rmlotRecs)>0) {	
					foreach($rmlotRecs as $rm) {
								$rmIds = $rm[0];
								$rmName	= stripSlash($rm[1]);
								
			?>	
			
				if (rmId=="<?=$rmIds?>")  var sel = "Selected";
				else var sel = "";

			rmlot += "<option value=\"<?=$rmIds?>\" "+sel+"><?=$rmName?></option>";	
			<?php
					}
				}
				
			?>	
			rmlot += "</select>";
			if(fdId>=1)
			{
			//alert("hii");
			mcPkgId=document.getElementById('mcPackingIdCnvrtLs_0').value;
			//document.getElementById('mcPackingIdCnvrtLs_'+fdId).disabled=true;
			}
			var mcPack	= "<select name='mcPackingIdCnvrtLs_"+fdId+"' id='mcPackingIdCnvrtLs_"+fdId+"' onchange=\"xajax_getNumPK(document.getElementById('mcPackingIdCnvrtLs_"+fdId+"').value,"+fdId+");\"  ><option value='0'>--Select--</option>";//changeAllMC
						   <?php
						   if (sizeof($mcpackingRecords)>0) {
						  foreach($mcpackingRecords as $mcp) {
							$mcPkgIds		= $mcp[0];
							$mcpackingCode		= stripSlash($mcp[1]);
			?>
				if (mcPkgId=="<?=$mcPkgIds?>")  var sel = "Selected";
				else var sel = "";

			mcPack += "<option value=\"<?=$mcPkgIds?>\" "+sel+"><?=$mcpackingCode?></option>";	
			<?php
					}
				}
				
			?>	
			mcPack += "</select>";
				
			
			var ds = "N";	
			//if( fieldId >= 1) 
			//var imageButton = "<a href='###' onClick=\"setTestRowVehicleAndDriverStatus('"+fdId+"');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			
			//var hiddenFields = "<input name='dStatus_"+fdId+"' type='hidden' id='dStatus_"+fdId+"' value=''><input name='dIsFromDB_"+fdId+"' type='hidden' id='dIsFromDB_"+fdId+"' value='"+ds+"'>
			var hiddenFields = "<input name='numMcPackCnvrtLs_"+fdId+"' type='hidden' id='numMcPackCnvrtLs_"+fdId+"' value='"+numPackId+"'>";

			//cell1.innerHTML	= "<input name='test_"+fldId+"' type='text' id='test_"+fldId+"' value=\""+unescape(vehicleType)+"\" size='24'>";
			cell1.innerHTML	= sl;
			cell2.innerHTML	= rmlot;
			cell3.innerHTML	= mcPack+hiddenFields;
			//cell2.innerHTML	= Qty;	
			cell4.innerHTML	='LS';
			var p=5;
			for(l=0; l<grdlen; l++)
			{	var cellhtml ="cell"+p;
				
				window[cellhtml].innerHTML  = "<input name='gradeQty_"+l+"_"+fdId+"' type='text' id='gradeQty_"+l+"_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value='' onkeyup=\"return checkValid(document.getElementById('gradeQty_"+l+"_"+fdId+"').value,document.getElementById('rmlotId_CnvrtLs_"+fdId+"').value,"+l+","+fdId+");\">";
			p++;
			}
			//var lst=	"cell"+p;
			//window[lst].innerHTML = hiddenFields;
			
			
			/*cell5.innerHTML	= "<input name='gradeQty_1_"+fdId+"' type='text' id='gradeQty_1_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell6.innerHTML	= "<input name='gradeQty_2_"+fdId+"' type='text' id='gradeQty_2_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell7.innerHTML	= "<input name='gradeQty_3_"+fdId+"' type='text' id='gradeQty_3_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell8.innerHTML	= "<input name='gradeQty_4_"+fdId+"' type='text' id='gradeQty_4_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell9.innerHTML	= "<input name='gradeQty_5_"+fdId+"' type='text' id='gradeQty_5_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell10.innerHTML	= "<input name='gradeQty_6_"+fdId+"' type='text' id='gradeQty_6_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell11.innerHTML	= "<input name='gradeQty_7_"+fdId+"' type='text' id='gradeQty_7_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell12.innerHTML	= "<input name='gradeQty_8_"+fdId+"' type='text' id='gradeQty_8_"+fdId+"' size='5'  style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell13.innerHTML	= "<input name='gradeQty_9_"+fdId+"' type='text' id='gradeQty_9_"+fdId+"' size='5' style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell14.innerHTML	= "<input name='gradeQty_10_"+fdId+"' type='text' id='gradeQty_10_"+fdId+"' size='5'  style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell15.innerHTML	= "<input name='gradeQty_11_"+fdId+"' type='text' id='gradeQty_11_"+fdId+"' size='5'  style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			cell16.innerHTML	= "<input name='gradeQty_12_"+fdId+"' type='text' id='gradeQty_12_"+fdId+"' size='5'  style='text-align:right; border:none;' tabindex="+fdId+"  value=''>";
			
			cell17.innerHTML = imageButton+hiddenFields;*/
			
			if(fdId>=1)
			{
			document.getElementById('mcPackingIdCnvrtLs_'+fdId).disabled=true;
			}
		
			fdId		= parseInt(fdId)+1;	
			
			document.getElementById("hidLSRowCount").value = fdId;	
			
}
function changeAllMC(number)
{
	var mcpack=document.getElementById('mcPackingIdCnvrtLs_0').value;
	//alert(mcpack);
	var hidLSRowCount=document.getElementById('hidLSRowCount').value;
	for(i=0; i<=hidLSRowCount; i++)
	{
		document.getElementById('mcPackingIdCnvrtLs_'+i).value=mcpack;
		document.getElementById('numMcPackCnvrtLs_'+i).value=number;
	}
}
function setTestRowVehicleAndDriverStatus(id)
{
	if (confirmRemoveItem()) {
	
		document.getElementById("dStatus_"+id).value = document.getElementById("dIsFromDB_"+id).value;
		
		document.getElementById("dRow_"+id).style.display = 'none';
//document.getElementById("bRow_"+id).style.display = 'block';			
	}
	return false;
}
function checkLsValueLot(rmlotId,fld)
{
	var rmSz=document.getElementById('rmsz').value;
	for(i=0; i<rmSz; i++)
	{
		var totalRM=document.getElementById('totalRM_'+i).value;
		if(rmlotId==totalRM)
		{
			var gradeRowCount = document.getElementById("gIdRMSz").value;
			for(j=0; j<gradeRowCount; j++)
			{
				var gIdrm=document.getElementById('gIdrm_'+j).value;
					//alert(gIdrm);
				var gradeQty=document.getElementById('gradeQty_'+j+'_'+fld).value;
					//alert(gradeQty);
				var lstotalRM=document.getElementById('lstotalRM_'+j+'_'+i).value;
					//alert(lstotalRM);
				if(gradeQty>lstotalRM)
				{
					alert("LS value exceed the maximum value");
					document.getElementById('gradeQty_'+j+'_'+fld).focus();
					return false;
				}
			}
		}
	}
}
function checkValid(grdVal,rmlot,grdrw,fld)
{
	//alert(grdVal+"-->"+rmlot);
	if(rmlot=='0')
	{
		alert("You need to select an rmlotid");
		return false;
	}
	if(grdVal!='' || grdVal!='0')
	{
		//alert(grdrw);
		//alert(fld);
		rmSz=document.getElementById('rmsz').value;
		for(i=0; i<rmSz; i++)
		{
			var totalRM=document.getElementById('totalRM_'+i).value;
			if(rmlot==totalRM)
			{	var grdRow=parseInt(grdrw)+1;
				//alert(grdrw+'_'+i);
				var	lstotalRM=document.getElementById('lstotalRM_'+grdrw+'_'+i).value;
				//alert(lstotalRM);
				if(grdVal>lstotalRM)
				{
					alert("LS value exceed the maximum value");
					document.getElementById('lstotalRM_'+grdrw+'_'+i).focus();
					return false;
				}
				
				
				
				
			}
		}
		//alert(rmSz);
		
	}
}

function dailyFrozenpackingLoad(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}

	function changeQuickEntryList(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();

	}

	function changeDisplayMCLS(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();

	}

	function quickEntryOption(formObj)
	{
		showFnLoading(); 
		formObj.form.submit();
	}