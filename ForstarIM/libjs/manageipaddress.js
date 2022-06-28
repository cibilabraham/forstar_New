function validateAddIPAddress(form)
{
	
	var selFixedIP		=	form.selIP[0].checked;
	var selRangeIP		=	form.selIP[1].checked;
	var ipAddress		=	form.ipAddress.value;
	var ipAddressFrom	=	form.ipAddressFrom.value;
	var ipAddressTo		=	form.ipAddressTo.value;
	
	if(selFixedIP == "" && selRangeIP==""){
		alert("Please select any one IP Address type.");
		return false;
	}
	
	if(selFixedIP!=""){
		if(ipAddress=="" )
			{
				alert("Please enter an IP Address.");
				form.ipAddress.focus();
				return false;
			}
			if (!validateIPAddress(ipAddress)){
   				alert("Please enter Valid IP Address.");
				form.ipAddress.focus();
				return false;
 			}
			
	}
	
	if(selRangeIP!=""){
		if(ipAddressFrom=="" )
			{
				alert("Please enter IP Address From.");
				form.ipAddressFrom.focus();
				return false;
			}
			
			if (!validateIPAddress(ipAddressFrom)){
   				alert("Please enter Valid IP Address.");
				form.ipAddressFrom.focus();
				return false;
 			}
			
			if(ipAddressTo=="" )
			{
				alert("Please enter IP Address To.");
				form.ipAddressTo.focus();
				return false;
			}
			if (!validateIPAddress(ipAddressTo)){
   				alert("Please enter Valid IP Address.");
				form.ipAddressTo.focus();
				return false;
 			}
			if(!checkIPRange(ipAddressFrom,ipAddressTo)){
				alert("Please enter Valid IP Address Range. First three bytes should be equal and the Last byte of To IP Address should be greater than From IP Address");
				return false;				
			}
	}
		
		
	if(!confirmSave())
	{
		return false;
	}
	else
	{
		return true;
	}
}

function ShowFixedIP(){
	document.getElementById( "fixedIP" ).style.display = "block";
	document.getElementById( "rangeIP" ).style.display = "none";
}

function ShowRangeIP(){
	document.getElementById( "fixedIP" ).style.display = "none";
	document.getElementById( "rangeIP" ).style.display = "block";
}

function IPAddressSelHide()
{
	document.getElementById( "fixedIP" ).style.display = "none";
	document.getElementById( "rangeIP" ).style.display = "none";
}

function HideRangeIP(){
	document.getElementById( "rangeIP" ).style.display = "none";	
}

function HideFixedIP(){
	document.getElementById( "fixedIP" ).style.display = "none";
}


function validateIPAddress(inputString) {

 //create reqular expression to validate that the
 //format of the string is at least correct
 var re = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;

 //test the input string against the regular expression
 if (re.test(inputString)) {

   //now, validate the separate parts
   var parts = inputString.split(".");
   if (parseInt(parseFloat(parts[0])) == 0) {
     return false;
   }
   for (var i=0; i<parts.length; i++) {
     if (parseInt(parseFloat(parts[i])) > 255) {
       return false;
     }
   }
   return true;
 }
 else {
   return false;
 }
}

function checkIPRange(ipAddressFrom,ipAddressTo){
	var IPFrom	=	ipAddressFrom.split(".");
	var IPTo	=	ipAddressTo.split(".");
	//alert(IPFrom[3]);
	if(IPFrom[0]==IPTo[0] && IPFrom[1]==IPTo[1] && IPFrom[2]==IPTo[2] && IPFrom[3]<=IPTo[3]){
		return true;
	}
	else {
		return false;
	}	
	
}