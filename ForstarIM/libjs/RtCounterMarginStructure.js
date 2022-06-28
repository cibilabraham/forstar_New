	function validateRtCounterMarginStructure(form)
	{
		var selRetailCounter	= form.selRetailCounter.value;
		//var selProduct		= form.selProduct.value;
		var margin		= form.margin.value;
		var mode		= document.getElementById("hidMode").value;
		var hidSelection	= document.getElementById("hidSelection").value;

		if (selRetailCounter=="") {
			alert("Please select a Retail Counter.");
			form.selRetailCounter.focus();
			return false;
		}
		/*	
		if (selProduct=="") {
			alert("Please select a Product.");
			form.selProduct.focus();
			return false;
		}
		*/
		var selProduct = "";
		if (hidSelection=='I' || mode==1) {
			var selProduct		= form.selProduct.value;	
		}
		if (mode==1) {
			var selProductCategory = form.selProductCategory.value;
			var selProductState    = document.getElementById("selProductState").value;
			var selProductGroup    = document.getElementById("selProductGroup").value;
			if (selProduct=="" && selProductCategory=="" && selProductState!=0) {
				var dMsg = " Retail Counter margin will apply against all Product category.\n Do you wish to Continue? ";
				if (!confirm(dMsg)) return false;
			} else if (selProduct=="" && selProductCategory=="" && selProductState==0) {
				var dMsg = " Retail Counter margin will apply against all Product category and State.\n Do you wish to Continue? ";
				if (!confirm(dMsg)) return false;
			} else if (selProduct=="" && selProductCategory!="" && selProductState==0) {
				var dMsg = " Retail Counter margin will apply against all Product State.\n Do you wish to Continue? ";
				if (!confirm(dMsg)) return false;
			}
		} 
		else  if (selProduct=="" && hidSelection=='I') {
			alert("Please select a Product.");
			form.selProduct.focus();
			return false;
		} 
		
		if (margin=="") {
			alert("Please enter a Margin.");
			form.margin.focus();
			return false;
		}
		if (!isDigit(margin)) {
			alert("Please enter a number.");
			form.margin.focus();
			return false;
		}

		/*
		if (retCtMarginRateList=="") {
			alert("Please select a Rate list.");
			form.retCtMarginRateList.focus();
			return false;
		}	
		*/
		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	function hideProductSpex()
	{
		var selProduct = document.getElementById("selProduct").value;
		if (selProduct!="") {
			document.getElementById("selProductCategory").value = "";
			document.getElementById("selProductState").value = "";
			document.getElementById("selProductGroup").value = "";				
			document.getElementById("column0").style.display = "none";
			document.getElementById("column1").style.display = "none";	
			document.getElementById("singleProdEnabled").value = 1;		
		} else {
			document.getElementById("selProduct").value = "";
			document.getElementById("column0").style.display = "";
			document.getElementById("column1").style.display = "";					
			document.getElementById("singleProdEnabled").value = 0;	
		}
	}
	
	function enableRtCounterMgnStructBtn(mode)
	{
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = false;
			document.getElementById("cmdAdd1").disabled = false;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = false;
			document.getElementById("cmdSaveChange1").disabled = false;
		}
	}
	
	function disableRtCounterMgnStructBtn(mode)
	{		
		if (mode==1) {
			document.getElementById("cmdAdd").disabled = true;
			document.getElementById("cmdAdd1").disabled = true;
		} else if (mode==2) {
			document.getElementById("cmdSaveChange").disabled = true;
			document.getElementById("cmdSaveChange1").disabled = true;
		}
	}

	/*
		Validating Continue section
	*/
	function validateEditSelection()
	{
		var editSelection1 = document.getElementById("editSelection1").checked;
		var editSelection2 = document.getElementById("editSelection2").checked;
		if (!editSelection1 && !editSelection2) {
			alert("Please select Individual/Group");
			return false;
		}
		return true;
	}
	/* Delete single Mgn */
	function valiateDeleteMargin()
	{		
		var selProduct = document.getElementById("selProduct").value;
		
		if (selProduct=="") {
			alert("Please select a Product.");
			document.getElementById("selProduct").focus();
			return false;
		}
		var conDelMsg	=	"Do you wish to delete the selected items?";
		if (!confirm(conDelMsg)) {
			return false;
		}
		return true;		
	}

