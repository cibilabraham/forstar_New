var poMCArr = {};
var poMCCount = 0;

function validateAddDailyFrozenPacking(form)
{	
	var selMode		= document.getElementById("hidMode").value;

	if (selMode==1) {
		var selectDate		=	form.selectDate.value;
		var selUnit		=	form.unit.value;
		var processor		=	form.processor.value;
		var lotId		=	form.lotId.value;
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
		
		if (selUnit=="") {
			alert("Please select a Unit.");
			form.unit.focus();
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

function validateFrozenStkAllocationSearch()
{
return true;
	var processCodeId = document.getElementById("filterProcessCode");
	if (processCodeId.value=="")
	{
		alert("Please select a process code.");
        processCodeId.focus();
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
	//alert(ecode);
	
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

	// left /right /up/down moving (Focus Next)
	function fNGradeTxtBox(e, form, fldName, displayQE)
	{
		var ecode = getKeyCode(e);	
		//alert(ecode);
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
		//alert(fldName);
			
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
		var lastRow	= tbl.rows.length-1;
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
			//alert(gId);
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
		//alert("hai5");
		var pkgGroupArr 	= new Array();
		var lsPkgGroupArr 	= new Array();
		
		for (var i=1; i<=prodnRowCount; i++) {
			var mcPkgCode = document.getElementById("mcPackingId_"+i).options[document.getElementById("mcPackingId_"+i).selectedIndex].text;
			//alert(mcPkgCode+"==P::"+prodnRowCount);
			//$pkgGroupArr[$mcPkgCode][$sGradeId] += $numMC;
			//$lsPkgGroupArr[$sGradeId] += $numLS;
			var numMcPack	= parseInt(document.getElementById("numMcPack_"+i).value);
			//alert("hai55");
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
		//alert(pkgGroupArr["X2_8"]);
	
		var prevPkgCode = "";
		for (var pga in pkgGroupArr)
		{
			var splitArr = pga.split("_");
			var mcPkgCode 	= splitArr[0];
			var gradeId	= splitArr[1];
			//alert(mcPkgCode+"="+gradeId);
				//alert("hai6");
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
					//alert(document.getElementById("tRow_"+i)+"=="+i);
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
		//alert("jjj");
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
					//var nLS = document.getElementById("numLS_"+j+"_"+i);
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
				//alert(totalSlabs);
				// total Qty	
				var totalQty	= totalSlabs*filledWt;
				//alert(filledWt);
				if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+i).value = totalSlabs;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+i).value = number_format(totalQty,2,'.','');
		   }
		} // Prodn Row count Ends here
		
		displayAllocateSummary(); // Display summary
	}



function calcAllocateProdnQtyreg()
	{
		//alert("hai1");
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;
		document.getElementById("selFrozenCode_2").value=0;
		document.getElementById("mcPackingId_2").value=0;
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
					//var nLS=0;
					var numMC = (nMC.value!="")?parseInt(nMC.value):0;
					var numLS = (nLS.value!="")?parseInt(nLS.value):0;
					// Convert LS to MC
					var eMC   = Math.floor(numLS/numMcPack);
					if (!isNaN(eMC)) var numMC = parseInt(numMC) + parseInt(eMC);
					var numLS = parseInt(numLS)%parseInt(numMcPack);	
					if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+i).value = numMC;
					//alert(numMC);
					if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+i).value = numLS;
					//if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+2).value = numMC;
					//if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+2).value = numLS;
					
					totNumMC += numMC;
					totNumLS += numLS;

				} // Grade loop Ends here
				
				// Total Slabs
				//totNumLS=0;
				//alert(totNumLS);
				var totalSlabs 	= (totNumMC*numMcPack)+totNumLS;
				//alert(totalSlabs);
				// total Qty	
				var totalQty	= totalSlabs*filledWt;
				//alert(filledWt);
				if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+i).value = totalSlabs;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+i).value = number_format(totalQty,2,'.','');
		   }
		} // Prodn Row count Ends here
		
		for (var j=1; j<=gradeRowCount; j++) {
		document.getElementById("numMC_"+j+"_"+2).value =0;
		document.getElementById("numLS_"+j+"_"+2).value =0;
		}

		displayAllocateSummary(); // Display summary
	}




function calcAllocateProdnQtyrep()
	{
		//alert("hai11");
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;
		//document.getElementById("selFrozenCode_2").value=0;
		document.getElementById("repselFrozenCode_2").value=0;
		document.getElementById("mcPackingId_2").value=0;
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
					//var nLS=0;
					var numMC = (nMC.value!="")?parseInt(nMC.value):0;
					var numLS = (nLS.value!="")?parseInt(nLS.value):0;
					// Convert LS to MC
					var eMC   = Math.floor(numLS/numMcPack);
					if (!isNaN(eMC)) var numMC = parseInt(numMC) + parseInt(eMC);
					var numLS = parseInt(numLS)%parseInt(numMcPack);	
					if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+i).value = numMC;
					if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+i).value = numLS;
					//if (!isNaN(numMC)) document.getElementById("numMC_"+j+"_"+2).value = numMC;
					//if (!isNaN(numLS)) document.getElementById("numLS_"+j+"_"+2).value = numLS;
					
					totNumMC += numMC;
					totNumLS += numLS;

				} // Grade loop Ends here
				
				// Total Slabs
				//totNumLS=0;
				//alert(totNumLS);
				var totalSlabs 	= (totNumMC*numMcPack)+totNumLS;
				//alert(totalSlabs);
				// total Qty	
				var totalQty	= totalSlabs*filledWt;
				//alert(filledWt);
				if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+i).value = totalSlabs;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+i).value = number_format(totalQty,2,'.','');
		   }
		} // Prodn Row count Ends here

		for (var j=1; j<=gradeRowCount; j++) {
		document.getElementById("numMC_"+j+"_"+2).value =0;
		document.getElementById("numLS_"+j+"_"+2).value =0;
		}
		
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
				  // ###commented on 17-11-2014 by athira //cannot add reglazing second record entry with rmlotid.
				   var POId = document.getElementById("POId_"+i).value;	
				
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
		//alert(totAllocatedSlabs);
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
		cell2.className	= "listing-head"; cell2.align	= "left"; cell2.style.borderLeft=bdr; cell2.style.borderRight=bdr; cell2.style.borderBottom=bdr;cell2.style.borderTop=bdr;
		//cell3.className	= "listing-item"; cell3.align	= "center"; cell3.style.borderRight=bdr; cell3.style.borderBottom=bdr;	
		cell3.className	= "listing-item"; cell3.align	= "center"; cell3.style.borderRight=bdr; cell3.style.borderBottom=bdr;cell3.style.borderTop=bdr;	
		nextCell1.className = "listing-item"; nextCell1.align	= "center"; nextCell1.noWrap = "true";
		nextCell2.className = "listing-item"; nextCell2.align	= "center"; nextCell2.noWrap = "true";

		cell1.innerHTML	= "";
		cell2.innerHTML	= "MC PKG";
		//cell3.innerHTML	= mcPkgCode;
		cell3.innerHTML	= "&nbsp;";
		for (var i=0; i<cellArr.length;i++) {	
			var gId = document.getElementById("gId_"+(i+1)).value;
			cellArr[i].className = "listing-item"; cellArr[i].align	= "right"; cellArr[i].noWrap = "true"; cellArr[i].style.borderRight=bdr;cellArr[i].style.borderTop=bdr; cellArr[i].style.borderBottom=bdr; cellArr[i].style.fontWeight="bold";
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
			/*if (typeof(lsPkgArr[gId])!="undefined") {				
				document.getElementById("LS_"+gId).innerHTML = "<strong>"+lsPkgArr[gId]+"</strong>";
			} else document.getElementById("LS_"+gId).innerHTML = "&nbsp;";*/
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

/*function validateQty(totval,enteredvalue)
{
if (enteredvalue>totval)
{
alert("Please renter the quantity");
alert(totval);
alert(enteredvalue);
return false;
}
else {
return true;
}
}*/

function validateDFPThawing(form)
{
	//alert("hai");
var selDate		=	form.selDate.value;
var todDate		=	form.todDate.value;
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

var elem = todDate.split('/');  
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


function validateDFPPacking(form)
{
	//alert("hai");
var selDate		=	form.selDate.value;
var todDate		=	form.todDate.value;
var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
var i=1;
var flag=0;
for (var j=1; j<=gradeRowCount; j++) {						 
						var numthawMC = document.getElementById("tothidAvailableSlabs_"+j).value;
						var numthawMCol=document.getElementById("numMC_"+j+"_"+i).value;
						var hidnumthawMCol=document.getElementById("numMCG_"+j+"_"+i).value;
						//if (parseInt(numthawMC)<parseInt(numthawMCol))
						if (parseInt(numthawMCol)>parseInt(hidnumthawMCol))
						{
							alert("Available Quantity less than Repacking quantity.Please reenter");
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
				alert("Please enter Number of Repacking Details.");
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
if (document.getElementById("repselFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			return false;
		}
		else if (document.getElementById("mcPackingId_2").value==0)
		{
			alert("Please select the MC Packing");
			return false;
		}

		if (confirm("Do you wish to continue?"))
				{

				return true;
				} else
	{
					return false;
	}




}

function validateDFPReglazing(form)
{
	//alert("hai2");
var selDate		=	form.selDate.value;
var todDate		=	form.todDate.value;
var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
//var frozCode= document.getElementById("hidselFrozenCode_1").value;
//var comfrozCode=trim(document.getElementById("selFrozenCode_1").value);
//alert("f-"+frozCode+"co-"+comfrozCode);
/*if (frozCode==comfrozCode)
{
	alert("Please Change the Frozen Code");
	return false;
}*/
var i=1;
var flag=0;
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
		if (document.getElementById("selFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			return false;
		}
		else if (document.getElementById("mcPackingId_2").value==0)
		{
			alert("Please select the MC Packing");
			return false;
		}
		if (confirm("Do you wish to continue?"))
		//if (confirm("Do you wish to Repack?"))
				{
			document.getElementById("RpYes").value=1;
				return true;
				} else
	{
					document.getElementById("RpYes").value=0;
					return false;
	}




}

function go(form)
{
	
	if ((form.optionrpkrgz[1].checked==false) && (form.optionrpkrgz[0].checked==false))
	{
		alert("Please click the Option");
		return false;
	}


}





function validateDFPAllocation(form)
{		
	//alert("hai1");
		var prodnRowCount 	= document.getElementById("hidAllocateProdnRowCount").value;
		var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;			
		var poSelected = false;
		var flag=0;
		var totAvailableSlabs = document.getElementById("totAvailableSlabs").value;	
		var totAllocatedSlabs = document.getElementById("totAllocatedSlabs").value;	
		//alert("tavs"+totAvailableSlabs);
		//alert("tas"+totAllocatedSlabs);
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
					var colgradeTotal=0;
					var numMCol=0;
					for (var j=1; j<=gradeRowCount; j++) {
						 
						var numMC = document.getElementById("numMC_"+j+"_"+i).value;
						
						//numMCol=document.getElementById("numMC_"+i+"_"+j).value;
						var numLS=0;
						
						//var numLS = document.getElementById("numLS_"+j+"_"+i).value;
						if(numMC!=0 || numLS!=0){
							packEntered = true;
						}	

						colgradeTotal=colgradeTotal+numMCol;
					
						
						
					} // grade Row Count Ends here
						
						//alert(colgradeTotal);


							

						
						
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


								for (var j=1; j<=gradeRowCount; j++) {
								var sum=0;
								var numAvailableMC = document.getElementById("tothidAvailableallocSlabs_"+j).value;
								for (var i=1; i<=prodnRowCount; i++) {
									
									var numMCG = document.getElementById("numMC_"+j+"_"+i).value;									
									sum=parseInt(numMCG)+parseInt(sum);

								}
								if (parseInt(numAvailableMC) < parseInt(sum))
								{
									
									flag=1;
									break;
								}
								//alert("sum is "+sum);
								//alert(numAvailableMC);
								
								
								}



		if (!poSelected)
		{
			alert("Please add atleast one allocation");
			return false;
		}

		if (!validatePORepeat()) {
			return false;
		}

		if (flag==1)
		{
			alert("Available Quantity less than Allocated quantity.Please reenter");
			return false;
		}

		if (parseInt(totAllocatedSlabs)>parseInt(totAvailableSlabs))
		{
			alert("Allocated quantity is not available in this allocation. So please adjust the allocated slabs");
			return false;
		}

		if (poMCCount>0)
		{
			if (!checkPOQty(1))
			{
				if (confirm("Do you wish to continue?"))
				{
					return true;
				} else return false;
			}		
		}		

	if (!confirmSave()) return false;
	else return true;	
}

var removedAllocationArr = new Array();
function setAllocateRowStatus(obj)
{
	var fldName = obj.id;
	var fName = fldName.split("_");
	//alert("hii");
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
		checkPOQty(0);
	}

	delArrStr = removedAllocationArr.join(",");
	document.getElementById("hidDelAllocationArr").value = delArrStr;

	displayPurchaseQnty();

	return false;
}


function changePO(obj,processId)
{
	var fldName = obj.id;	
	var fName = fldName.split("_");	
	var selRowId = fName[1];
	validatePORepeat(); // Validate Repeat
	displayPurchaseQnty(processId);// purchase order Quantity
	var selPOId = obj.value;
	showFnLoading(); 
	if (selPOId>0)
	{	
		getPOItems(selPOId, selRowId); 

		$("#viewPOForAllocation_"+selRowId).click( function () { viewPO(selPOId); });
		hideFnLoading();
	}
	else { $("#viewPOForAllocation_"+selRowId).attr('onclick','').unbind('click');
	hideFnLoading();
	}
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
function displayPurchaseQnty(processId)
{
	//alert("hii");
	gradeId=new Array(); ProdnId=new Array();
	var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;
	for (i=1; i<=gradeRowCount; i++)
	{
		gId=document.getElementById("gId_"+i).value;
		if(gradeId=="")
		{
			gradeId=gId;
		}
		else
		{
			gradeId+=','+gId;
		}
	}
	//alert(obj.value);
	var ProdnRowCount	=document.getElementById("hidAllocateProdnRowCount").value;
	for(j=1; j<=ProdnRowCount; j++)
	{
		var POId=document.getElementById("POId_"+j).value;
		var	statusPOId=document.getElementById("status_"+j).value;
		if(statusPOId!="N")
		{
			if(ProdnId=="")
			{
				ProdnId=POId;
			}
			else
			{
				ProdnId+=','+POId;
			}
		}
	}
	//alert(ProdnRowCount);
	xajax_getPOAvailableItems(gradeId,ProdnId,processId);
	//alert(gradeId);	

}
function SetPOGrades(selRowId, poGradeVal)
{	
	poMCArr[selRowId] = {};
	var gradeSplitArr = poGradeVal.split(",");	
	for (var i=0;i<gradeSplitArr.length;i++ )
	{
		var gradeVal = gradeSplitArr[i];
		var gradeValArr = gradeVal.split(":");
		var gradeId = gradeValArr[0];
		//var numMc	= gradeValArr[1];
		//alert(numMc);
		poMCArr[selRowId][gradeId] = numMc;
		poMCCount++;
		//poMCArr[poId].push(gradeId+":"+numMc);
		//poMCArr[poId][gradeId] = numMc;
		//alert(gradeId+"="+numMc);
	}

	/*
	alert("sds");
	for (var rId in poMCArr)
	{
		//alert(gId+":::"+poMCArr[rId]);
		var gArr = poMCArr[rId];
		for (var gId in gArr)
		{
			alert(gId+":::"+gArr[gId]);

		}
	}
	*/

	checkPOQty(0);
	//alert(poGradeArr);
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

function checkPOQty(msg)
{
	var productAllocation = false; 
	for (var aRId in allocatedMCArr )
	{
		var status = document.getElementById("status_"+aRId).value;	
	   if (status!='N')
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
					//productAllocation=false;
				}

				//alert(allocatedNumMC+"==="+poMC);
			}
		} // Status check ends
	}// Loop Ends here
	
	if (productAllocation && msg==1)
	{
		alert("Purchase order MC and allocated MC is different.");
		return false;
	}

	return true;
}

function checkboxSel()
{
var atLeastOneIsChecked = false;
 $('input.fsaChkbx:checkbox').each(function () {
  if ($(this).is(':checked')) {
   atLeastOneIsChecked = true;
      // Stop .each from processing any more items
      return false;
    }
  });

  	if (!atLeastOneIsChecked){
		alert("Please select a Record");
		return false;
	}
	return true;
}




function callPkgChange(rowId)
{
	//alert("hai1");
	setTimeout("calcPkgChange("+rowId+")",500);
	
}



function calcPkgChange(rowId)
{
	//alert("hai2");
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert("hai4");
	// After get the prev mc pack change to current
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	//alert(numMCPackUsed);
	//alert("hai5");
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
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;	
		}
	}
//alert("hai6");

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}

function callPkgChangerprg(rowId)
{
	//alert("hai1");
	setTimeout("calcPkgChangerprg("+rowId+")",500);
	
}
function callPkgChangerep(rowId)
{
	//alert("hai1");
	setTimeout("calcPkgChangerep("+rowId+")",500);
	
	
}


/*function calcPkgChangerep(rowId)
{
alert("hii");
	if (document.getElementById("repselFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			document.getElementById("repselFrozenCode_2").value=0;
			return false;
		}
	alert("hai2");
	//alert(rowId);
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert(numMCPack);
	//var numMCPack= document.getElementById("selFrozenCode_2").value;
	//alert("hai4"+numMCPack);

		var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
	// After get the prev mc pack change to current
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+2).value; 
	
	//alert("hai5-------------"+numMCPackUsed);
	//alert("hai5");
	var totNumMC=0;
	var totNumLS=0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		//alert(gradeRowCount);
		var existingMC = document.getElementById("numMC_"+i+"_"+rowId).value;
		//alert("1");
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		var existingLS = document.getElementById("numLS_"+i+"_"+rowId).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//alert("2");
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		
		if (totalSlabs>0)
		{			
			//alert("first"+totalSlabs);
			var totMC	 = Math.floor(totalSlabs/numMCPack);
			var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;
			document.getElementById("numLSG_"+i+"_"+rowId).value = totLS;	
			totNumMC += totMC;
			totNumLS += totLS;

		}
		
	}
//alert("hai6");
//alert(totNumMC);
//alert(totNumLS);
var totalSlabs1 	= (totNumMC*numMCPack)+totNumLS;
				//alert("ts"+totalSlabs1);
				// total Qty	
				var totalQty	= totalSlabs1*ffilledWt;
				//alert(ffilledWt);
				if (!isNaN(totalSlabs1)) document.getElementById("totalSlabs_"+2).value = totalSlabs1;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}*/


function calcPkgChangerprg(rowId)
{

	//alert("first--reg");
	if (document.getElementById("selFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			document.getElementById("mcPackingId_2").value=0;
			return false;
		}
	//alert("hai2");
	//alert(rowId);
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert(numMCPack);
	//var numMCPack= document.getElementById("mcPackingId_2").value;
	//alert("Second Row x value"+numMCPack);
		//var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		var filledWt=parseFloat(document.getElementById("filledWt").value);
		var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		//alert("First and Second filled Wt"+filledWt+" "+ffilledWt);
	// After get the prev mc pack change to current
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+1).value; 
	//alert("First Row x value"+numMCPackUsed);
	//alert("hai5-------------"+numMCPackUsed);
	//alert("hai5");
	var totNumMC=0;
	var totNumLS=0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		//alert(gradeRowCount);
		var existingMC = document.getElementById("numMC_"+i+"_"+1).value;
		//alert(existingMC);
		//alert("1");
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		var existingLS = document.getElementById("numLS_"+i+"_"+1).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//alert("2");
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		//alert("First Row Total Slabs"+totalSlabs);
		if (totalSlabs>0)
		{			
			//alert("first"+totalSlabs);
			var iwet=totalSlabs*filledWt;			
			//alert("iwet with filled Wt"+iwet+" "+filledWt);
			//var fwet=totalSlabsi*parseFloat(ffilledWt);
			//var fwet=parseFloat(ffilledWt);
			var fwet=ffilledWt;
			//alert("fwet with filled Wt"+fwet+" "+ffilledWt);
			var totMC=Math.floor(iwet/(numMCPack*fwet));
			//alert("total MC"+totMC);
			var totMCN=(iwet/(numMCPack*fwet)-totMC);
			//alert(" totMCN"+ totMCN);
			var totLSr=(totMCN*numMCPack*ffilledWt)/ffilledWt;
			//alert("totLSr"+totLSr);
			var num=(totMCN*numMCPack*ffilledWt)/ffilledWt;
			var totLS=precise_round(num,2);
			//alert("totLS"+totLS);
			//var totMC	 = Math.floor(totalSlabs/numMCPack);
			//var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			//alert(totMC);
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;
			document.getElementById("numLSG_"+i+"_"+rowId).value = totLS;	
			totNumMC += totMC;
			totNumLS += totLS;

		}
		
	}
//alert("hai6");
//alert(totNumMC);
//alert(totNumLS);
			var totalSlabs1 	= (totNumMC*numMCPack)+totNumLS;
				//alert("ts"+totalSlabs1);
				// total Qty	
				var totalQty	= totalSlabs1*ffilledWt;
				//alert("ff"+ffilledWt);
				//alert("tq"+totalQty);
				if (!isNaN(totalSlabs1)) document.getElementById("totalSlabs_"+2).value = totalSlabs1;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}

function precise_round(num,decimals){
return Math.round(num*Math.pow(10,decimals))/Math.pow(10,decimals);
}


function calcPkgChangerep(rowId)
{

	//alert("first--rep");
	if (document.getElementById("repselFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			document.getElementById("mcPackingId_2").value=0;
			return false;
		}
	//alert("hai2");
	//alert(rowId);
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert(numMCPack);
	//var numMCPack= document.getElementById("mcPackingId_2").value;
	
		//var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		var filledWt=parseFloat(document.getElementById("filledWt").value);
		var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		//alert("First and Second filled Wt"+filledWt+" "+ffilledWt);
	// After get the prev mc pack change to current
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+1).value; 
	//alert("Second Row x value"+numMCPack);
	//alert("First Row x value"+numMCPackUsed);
	//alert("hai5-------------"+numMCPackUsed);
	//alert("hai5");
	var totNumMC=0;
	var totNumLS=0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		//alert(gradeRowCount);
		var existingMC = document.getElementById("numMC_"+i+"_"+1).value;
		//alert("1");
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		var existingLS = document.getElementById("numLS_"+i+"_"+1).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//alert("2");
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		//alert("First Row Total Slabs"+totalSlabs);
		if (totalSlabs>0)
		{			
			//alert("first"+totalSlabs);
			var iwet=totalSlabs*filledWt;			
			
			//var fwet=totalSlabsi*parseFloat(ffilledWt);
			//var fwet=parseFloat(ffilledWt);
			var fwet=ffilledWt;
			
			var totMC=Math.floor(iwet/(numMCPack*fwet));
			//alert(filledWt);
			var totMCN=(iwet/(numMCPack*fwet)-totMC);
			//alert("Total Mc"+totMC);
			//alert("iwet with filled Wt"+iwet+" "+filledWt);
			//alert("fwet with filled Wt"+fwet+" "+ffilledWt);
			//alert(" totMCN"+ totMCN);
			var num=(totMCN*numMCPack*ffilledWt)/ffilledWt;
			var totLS=precise_round(num,2);
			//var totLS=precise_round((totMCN*numMCPack*ffilledWt)/ffilledWt);
			//alert(" totLS"+ totLS);
			//var totMC	 = Math.floor(totalSlabs/numMCPack);
			//var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;
			document.getElementById("numLSG_"+i+"_"+rowId).value = totLS;	
			totNumMC += totMC;
			totNumLS += totLS;

		}
		
	}
//alert("hai6");
//alert(totNumMC);
//alert(totNumLS);
			var totalSlabs1 	= (totNumMC*numMCPack)+totNumLS;
				
				// total Qty	
				var totalQty	= totalSlabs1*ffilledWt;
				//alert("ts"+totalSlabs1);
				//alert("final filledWt"+ffilledWt);
				//alert("tq"+totalQty);
				if (!isNaN(totalSlabs1)) document.getElementById("totalSlabs_"+2).value = totalSlabs1;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}

/*function calcPkgChangerep(rowId)
{

	//alert("first--rep");
	if (document.getElementById("repselFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			document.getElementById("mcPackingId_2").value=0;
			return false;
		}
	//alert("hai2");
	//alert(rowId);
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert(numMCPack);
	//var numMCPack= document.getElementById("mcPackingId_2").value;
	
		//var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		var filledWt=parseFloat(document.getElementById("filledWt").value);
		var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
		//alert("First and Second filled Wt"+filledWt+" "+ffilledWt);
	// After get the prev mc pack change to current
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+1).value; 
	//alert("Second Row x value"+numMCPack);
	//alert("First Row x value"+numMCPackUsed);
	//alert("hai5-------------"+numMCPackUsed);
	//alert("hai5");
	var totNumMC=0;
	var totNumLS=0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		//alert(gradeRowCount);
		var existingMC = document.getElementById("numMC_"+i+"_"+1).value;
		//alert("1");
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		var existingLS = document.getElementById("numLS_"+i+"_"+1).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//alert("2");
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		//alert("First Row Total Slabs"+totalSlabs);
		if (totalSlabs>0)
		{			
			//alert("first"+totalSlabs);
			var iwet=totalSlabs*filledWt;			
			
			//var fwet=totalSlabsi*parseFloat(ffilledWt);
			//var fwet=parseFloat(ffilledWt);
			var fwet=ffilledWt;
			
			var totMC=Math.floor(iwet/(numMCPack*fwet));
			var totMCN=(iwet/(numMCPack*fwet)-totMC);
			//alert("Total Mc"+totMC);
			//alert("iwet with filled Wt"+iwet+" "+filledWt);
			//alert("fwet with filled Wt"+fwet+" "+ffilledWt);
			//alert(" totMCN"+ totMCN);
			var num=(totMCN*numMCPack*ffilledWt)/ffilledWt;
			var totLS=precise_round(num,2);
			//var totLS=precise_round((totMCN*numMCPack*ffilledWt)/ffilledWt);
			//alert(" totLS"+ totLS);
			//var totMC	 = Math.floor(totalSlabs/numMCPack);
			//var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;
			document.getElementById("numLSG_"+i+"_"+rowId).value = totLS;	
			totNumMC += totMC;
			totNumLS += totLS;

		}
		
	}
//alert("hai6");
//alert(totNumMC);
//alert(totNumLS);
			var totalSlabs1 	= (totNumMC*numMCPack)+totNumLS;
				
				// total Qty	
				var totalQty	= totalSlabs1*ffilledWt;
				//alert("ts"+totalSlabs1);
				//alert("final filledWt"+ffilledWt);
				//alert("tq"+totalQty);
				if (!isNaN(totalSlabs1)) document.getElementById("totalSlabs_"+2).value = totalSlabs1;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}*/






function calcPkgChangerprg_1(rowId)
{

	if (document.getElementById("selFrozenCode_2").value==0)
		{
			alert("Please select the Frozen Code");
			document.getElementById("selFrozenCode_2").value=0;
			return false;
		}
//	alert("hai2");
	//alert(rowId);
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;
	//alert("hai3");
	var numMCPack		= document.getElementById("numMcPack_"+rowId).value;
	//alert(numMCPack);
	//var numMCPack= document.getElementById("selFrozenCode_2").value;
	//alert("hai4"+numMCPack);
		var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
	// After get the prev mc pack change to current
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value; 
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+2).value; 
	
	//alert("hai5-------------"+numMCPackUsed);
	//alert("hai5");
	var totNumMC=0;
	var totNumLS=0;
	for (var i=1;i<=gradeRowCount ;i++ )
	{	
		//alert(gradeRowCount);
		var existingMC = document.getElementById("numMC_"+i+"_"+rowId).value;
		//alert("1");
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		var existingLS = document.getElementById("numLS_"+i+"_"+rowId).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//alert("2");
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		
		if (totalSlabs>0)
		{			
			//alert("first"+totalSlabs);
			var totMC	 = Math.floor(totalSlabs/numMCPack);
			var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

			document.getElementById("numMC_"+i+"_"+rowId).value = totMC;
			document.getElementById("numMCG_"+i+"_"+rowId).value = totMC;
			document.getElementById("numLS_"+i+"_"+rowId).value = totLS;
			document.getElementById("numLSG_"+i+"_"+rowId).value = totLS;	
			totNumMC += totMC;
			totNumLS += totLS;

		}
		
	}
//alert("hai6");
//alert(totNumMC);
//alert(totNumLS);
var totalSlabs1 	= (totNumMC*numMCPack)+totNumLS;
				//alert("ts"+totalSlabs1);
				// total Qty	
				var totalQty	= totalSlabs1*ffilledWt;
				//alert(ffilledWt);
				if (!isNaN(totalSlabs1)) document.getElementById("totalSlabs_"+2).value = totalSlabs1;
				if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+rowId).value = numMCPack;

	// Enable calculation after
	//calcProdnQty();
	//recalcLSForConversion();
}



function callPkgChangeFr(rowId)
{
//alert("hai1");
	setTimeout("calcPkgChangeFr("+rowId+")",500);
	
}

function callPkgChangeFrrep(rowId)
{
//alert("hai1");
	setTimeout("calcPkgChangeFrrep("+rowId+")",500);
	
}



function calcPkgChangeFrrep(rowId)
{
	var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
	var packEntered = false;
			for (var j=1; j<=gradeRowCount; j++) {
				var numMC = document.getElementById("numMC_"+j+"_"+1).value;				
				if(numMC!=0){
					packEntered = true;
				}				
			} // grade Row Count Ends here

 			if (!packEntered) {
				alert("Please enter Number of Reglazing Details.");
				document.getElementById("repselFrozenCode_2").value=0;
				return false;
			}
document.getElementById("mcPackingId_2").value=0;
}

function calcPkgChangeFr(rowId)
{
	var gradeRowCount	= document.getElementById("hidAllocateGradeRowCount").value;	
	var packEntered = false;
			for (var j=1; j<=gradeRowCount; j++) {
				var numMC = document.getElementById("numMC_"+j+"_"+1).value;				
				if(numMC!=0){
					packEntered = true;
				}				
			} // grade Row Count Ends here

 			if (!packEntered) {
				alert("Please enter Number of Reglazing Details.");
				document.getElementById("selFrozenCode_2").value=0;
				return false;
			}
document.getElementById("mcPackingId_2").value=0;
}
function calcPkgChangeFr_old(rowId)
{
	//alert("fr");
	//alert(rowId);
	document.getElementById("mcPackingId_2").value=0;
	var ifrozId=parseFloat(document.getElementById("finfrozenCode").value);
	var filledWt=parseFloat(document.getElementById("filledWt").value);
	var ffilledWt=parseFloat(document.getElementById("hidffilledWt").value);
	//alert("First Product---filledWt"+filledWt);
	//alert("Second Product--ffilledWt"+ffilledWt);
	//alert("ffrozId"+ffrozId);
	//var rowId=1;
	var totNumMC=0;
	var totNumLS=0; 
	var gradeRowCount	= document.getElementById("hidGradeRowCount").value;	
	//var numMCPack		= document.getElementById("numMcPack_"+rowId).value;	
	//var numMCPackUsed	= document.getElementById("hidNumMcPack_"+rowId).value;
	//var numMCPackUsedi	= document.getElementById("hidNumMcPackPrev_"+rowId).value;
	var numMCPack		= document.getElementById("numMcPack_"+1).value;
	var numMCPackUsed	= document.getElementById("hidNumMcPack_"+1).value;
	var numMCPackUsedi	= document.getElementById("hidNumMcPackPrev_"+1).value;
	//alert("numMCPack"+numMCPack);
	//alert("numMCPackUsed"+numMCPackUsed);
	//alert("numMCPackUsedi"+numMCPackUsedi);
	//alert(gradeRowCount);
	for (var i=1;i<=gradeRowCount;i++)
	{	//alert(existMC);
		var existingMC = document.getElementById("numMC_"+i+"_"+1).value;
		//var existingMC = document.getElementById("hidnumMC_"+i+"_"+rowId).value;
		//var existingMC=parseInt(document.getElementById("inumMC_"+i+"_"+rowId).value);
		var existMC = (!isNaN(existingMC) && existingMC!="")?existingMC:0;
		

		//var existingMCi = document.getElementById("hidnumMC_"+i+"_"+rowId).value;
		//var existingMCi=parseInt(document.getElementById("inumMC_"+i+"_"+rowId).value)
		//var existMCi = (!isNaN(existingMCi) && existingMCi!="")?existingMCi:0;
		//alert(existMCi);
		//document.getElementById("numMC_"+i+"_"+rowId).value=existMCi;
		var existingLS = document.getElementById("numLS_"+i+"_"+1).value;
		//var existingLS = document.getElementById("inumLS_"+i+"_"+rowId).value;
		var existLS = (!isNaN(existingLS) && existingLS!="")?existingLS:0;
		//var existingLSi = document.getElementById("hidnumLS_"+i+"_"+rowId).value;
		//var existingLSi = document.getElementById("inumLS_"+i+"_"+rowId).value;
		//var existLSi = (!isNaN(existingLSi) && existingLSi!="")?existingLSi:0;
		//var totalSlabsi = (parseInt(existMCi)*parseInt(numMCPackUsedi))+parseInt(existLSi);
		//alert("totalSlabsi"+totalSlabsi);
		var totalSlabs = (parseInt(existMC)*parseInt(numMCPackUsed))+parseInt(existLS);
		//alert("Total Slabs"+totalSlabsi);
		if (totalSlabs>0)
		{	
			//alert("em"+existMC);
	//	alert("Intial Total Slabs"+totalSlabs);
		//alert("filledWt"+filledWt);
		//alert("ffilledWt"+ffilledWt);
			var iwet=Math.round(totalSlabs*filledWt);
			
			//alert("iwet with filled Wt"+iwet+" "+filledWt);
			//var fwet=totalSlabsi*parseFloat(ffilledWt);
			//var fwet=parseFloat(ffilledWt);
			var fwet=ffilledWt;
		//alert("fwet with filled Wt"+fwet+" "+ffilledWt);
			/*	if ((ifrozId==ffrozId) && (numMCPackUsedi==numMCPackUsed))
				{
					alert("first");

			var totMC=existingMCi;
			var totLS=existingLSi;
				}
				else if (ifrozId==ffrozId)
				{
					alert("second");*/
			//var totMC=parseInt(Math.floor(existingMCi*numMCPackUsedi)/numMCPackUsed);
			//var totLS=Math.floor(existingMCi*numMCPackUsedi)%numMCPackUsed;
			/*var totMC=Math.floor(totalSlabsi/numMCPackUsed);
			var totLS=parseInt(totalSlabsi)%parseInt(numMCPackUsed);
				}
				else {*/
					
			//var totMC=parseInt(Math.floor(iwet/fwet));
			//alert("third"+totMC);
			/*alert(numMCPackUsedi+" "+numMCPackUsed);
			var totMCCon=parseInt(totMC*numMCPackUsedi/numMCPackUsed);
			totLS=parseInt(totMC*numMCPackUsedi)%numMCPackUsed;
			//var totLS=parseInt(iwet)%parseInt(fwet);
			totMC=totMCCon;
			alert(totLS);*/
			//var totMC=parseInt(Math.floor(iwet/fwet));
			var totMC=parseInt(iwet/fwet);
			//alert("before conversion"+totMC);
			//alert(numMCPackUsedi+" "+numMCPackUsed);
			//totMC=parseInt(totMC*numMCPackUsedi/numMCPackUsed);
			var numMCPackUsedInt=1;
			document.getElementById("mcPackingId_2").value=numMCPackUsedInt;
			document.getElementById("hidNumMcPack_"+2).value=numMCPackUsedInt;
			//alert("after conversion"+totMC);
			//var totLS=parseInt(iwet)%parseInt(fwet);
			var totLS=parseInt(Math.floor(iwet-(totMC*ffilledWt)));
			if (isNaN(totLS))
			{
				totLS=0;
			}
			//alert(totLS);
			//}



			//var totMC	 = Math.floor(totalSlabs/numMCPack);
			//var totLS	 = parseInt(totalSlabs)%parseInt(numMCPack);

//New Comment
			document.getElementById("numMC_"+i+"_"+2).value = totMC;
			document.getElementById("numMCG_"+i+"_"+2).value = totMC;
			document.getElementById("numLS_"+i+"_"+2).value = totLS;
			document.getElementById("numLSG_"+i+"_"+2).value = totLS;
			totNumMC += totMC;
			totNumLS += totLS;
			
		}
	}

//var totalSlabs 	= (totMC*numMCPack)+totLS;
//var totalSlabs 	= (totMC*numMCPackInt)+totLS;
//var totalSlabs 	= (totNumMC*numMCPackInt)+totNumLS;
var totalSlabs=totNumMC+totNumLS;
//alert("Final new Product"+totalSlabs);
			// total Qty	
			var totalQty	= totalSlabs*ffilledWt;
			//alert("new Product--ffilledWt---totalQty"+totalSlabs+" "+ffilledWt+" "+totalQty);
			if (!isNaN(totalSlabs)) document.getElementById("totalSlabs_"+2).value = totalSlabs;
			if (!isNaN(totalQty)) document.getElementById("totalQty_"+2).value = number_format(totalQty,2,'.','');

	document.getElementById("hidNumMcPack_"+1).value = numMCPack;

	
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

	function generateNewLot()
	{
		//alert("hii");
		var company=document.getElementById('hidComId').value;
		var unit=document.getElementById('hidUntId').value;
		var newRmLot=document.getElementById('newRmLot').checked;
		if(newRmLot!="")
		{
			document.getElementById('rmlotDetails').style.display="block";
			xajax_getNewLot(company,unit);
			
		}
		else
		{
			document.getElementById('rmlotDetails').style.display="none";
		}
		
	}
	