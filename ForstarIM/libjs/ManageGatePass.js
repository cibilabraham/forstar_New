	function validateGatePass(form, gatePassConfirm)
	{
		var company = form.company.value;

		if (company=="") {
			alert("Please select company.");
			form.company.focus();
			return false;
		}
		var unit = form.unit.value;

		if (unit=="0") {
			alert("Please select unit.");
			form.unit.focus();
			return false;
		}
		
		
		var consignmentDetails = form.consignmentDetails.value;

		if (consignmentDetails=="") {
			alert("Please enter consignment details.");
			form.consignmentDetails.focus();
			return false;
		}

		if (!confirmSave()) {
			return false;
		}
		return true;
	}

	// time ticker
	//to store timeout ID
	var tID;
	function tickTimer(t, gatePassId)
	{		
		//if time is in range
		if (t>=0) {
			var timeCalc = Math.floor(t);
			//alert(timeCalc/60);
			document.getElementById("timeTickerRow").innerHTML= "Time Remaining "+Math.floor(t/60) + ":" + (t%60)+" seconds.";
			t=t-1;
			tID=setTimeout("tickTimer('"+t+"','"+gatePassId+"')",1000);
		}
		//stop the timeout event
		else {			
			setTimeout("killTimer('"+tID+"')",1000);
			document.getElementById("editingGatePassId").value = gatePassId;			
			document.getElementById("timeTickerRow").innerHTML = "Edit Lock Released.";
		}
		//alert(tID+","+gatePassId);
	}	
	//function to stop the timeout event
	function killTimer(id)
	{		
		clearTimeout(id);
		document.getElementById("frmManageGatePass").submit();
	}
	// time ticker Ends Here

	var t ='<?=$refreshTimeLimit?>';	
	var sTime = Math.floor(t/60)+":"+(t%60);	
	var limit= sTime;		
	
	if (document.images){	
		var parselimit=limit.split(":");
		parselimit=parselimit[0]*60+parselimit[1]*1;
	}
	var curtime = 0;
	function beginrefresh()
	{		
		if (!document.images) return;
		if (parselimit==1) {
			document.getElementById("frmManageGatePass").submit();
		}
		else { 			
			parselimit = parselimit-1 ;
			var curmin=Math.floor(parselimit/60);
			var cursec=parselimit%60;
			if (curmin!=0)  curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
			else curtime=cursec+" seconds left until page refresh!";
			
			document.getElementById("refreshMsgRow").innerHTML = curtime;
			setTimeout("beginrefresh()",1000);
		}
	}