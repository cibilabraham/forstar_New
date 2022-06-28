<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
  /*------The below is a set of Global Variable to use------*/
    	var fieldId		=	0;
	var fldId		=	0; // Another variable using in Multiple row adding (Sales Order)
	var fdId		= 	0;

  	function setfieldId(id)
   	{		
	 	fieldId		= id;
	}	


	// time ticker
	//to store timeout ID
	var tID;
	function tickTimer(t, editingId, frm)
	{		
		//if time is in range
		if (t>=0) {
			//var timeCalc = Math.floor(t);
			//alert(timeCalc/60);
			document.getElementById("timeTickerRow").innerHTML= "Time Remaining "+Math.floor(t/60) + ":" + (t%60)+" seconds.";
			t=t-1;
			tID=setTimeout("tickTimer('"+t+"', '"+editingId+"', '"+frm+"')",1000);
		}
		//stop the timeout event
		else
		{
			//Update Rec in Eah functionality JS
			updateEditedMainRec(editingId);
			setTimeout("killTimer('"+tID+"', '"+frm+"')",1000);
			document.getElementById("timeTickerRow").innerHTML = "Edit Lock Released.";
		}
	}
	
	//function to stop the timeout event
	function killTimer(id, frm)
	{		
		clearTimeout(id);
		document.getElementById(frm).submit();
	}
	// time ticker Ends Here

	// Refresh Starts here
	var t =refreshTimeLimit;	
	var sTime = Math.floor(t/60)+":"+(t%60);	
	var limit= sTime;		
	
	if (document.images){	
		var parselimit=limit.split(":");
		parselimit=parselimit[0]*60+parselimit[1]*1;
	}
	var curtime = 0;
	function beginrefresh(frm)
	{		
		if (!document.images) return;
		if (parselimit==1) {
			document.getElementById(frm).submit();
		} else { 			
			parselimit = parselimit-1 ;
			var curmin=Math.floor(parselimit/60);
			var cursec=parselimit%60;
			if (curmin!=0)  curtime=curmin+" minutes and "+cursec+" seconds left until page refresh!";
			else curtime=cursec+" seconds left until page refresh!";			
			document.getElementById("refreshMsgRow").innerHTML = curtime;
			setTimeout("beginrefresh('"+frm+"')",1000);
		}
	}

	
</SCRIPT>
