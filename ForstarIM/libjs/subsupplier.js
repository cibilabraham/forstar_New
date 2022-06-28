function displaySupplier()
{
	if(document.getElementById("mainSupplier").value !="" ) {
		document.getElementById("subSupplierMainSupplier").value	=	document.getElementById("mainSupplier").value;
	}
	if(document.getElementById("mainSupplier").value =="" ) {
		document.getElementById("subSupplierMainSupplier").value	=	"";
	}
}

function validateAddSubSupplier(form)
{	
	var subSupplierCode	=	form.subSupplierCode.value;
	var subSupplierName	=	form.subSupplierName.value;
	var Phone				=	form.subSupplierTelNo.value;
	var emailID				=	form.subSupplierEmail.value;
	var Pincode				=	form.subSupplierPincode.value;
	var place				=	form.subSupplierPlace.value;
	
	if ( subSupplierCode=="" ) {
		alert("Please enter a Sub Supplier code.");
		form.subSupplierCode.focus();
		return false;
	}
	
	if ( subSupplierName=="" ) {
		alert("Please enter a Sub Supplier name.");
		form.subSupplierName.focus();
		return false;
	}

	if (place=="") {
		alert("Please select a Landing Center.");
		form.subSupplierPlace.focus();
		return false;
	}

	if (Phone=="") {
		alert("Please enter a phone number.");
		form.subSupplierTelNo.focus();
		return false;
	}
		
	if (checkInternationalPhone(Phone)==false){
		alert("Please Enter a Valid Phone Number");
		form.subSupplierTelNo.value="";
		form.subSupplierTelNo.focus();
		return false;
	}

	if (emailID!="") {
		if (echeck(emailID)==false){
			form.subSupplierEmail.value="";
			form.subSupplierEmail.focus();
			return false;
		}
	}

	if (isPositiveInteger(Pincode)==false) {
		form.subSupplierPincode.value="";
		form.subSupplierPincode.focus();
		return false;
	}
	
	if (!confirmSave()) return false;	
	return true;
}

var digits = "0123456789";
var phoneNumberDelimiters = "()- ";
var validWorldPhoneChars = phoneNumberDelimiters + "+";
var minDigitsInIPhoneNumber = 10;
function isInteger(s)
{   
	var i;
    	for (i = 0; i < s.length; i++) {
		var c = s.charAt(i);
		if (((c < "0") || (c > "9")))
			return false;
    	}
    	return true;
}

function stripCharsInBag(s, bag)
{   
	var i;
    	var returnString = "";
   	for (i = 0; i < s.length; i++)  {   
		var c = s.charAt(i);
		if (bag.indexOf(c) == -1) returnString += c;
    	}
    	return returnString;
}

function checkInternationalPhone(strPhone)
{
	s=stripCharsInBag(strPhone,validWorldPhoneChars);
	return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}

function echeck(str) 
{
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert("Invalid E-mail ID")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    alert("Invalid E-mail ID")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Invalid E-mail ID")
		    return false
		 }

 		 return true					
	}

function isPositiveInteger(val)
{
      for (var i = 0; i < val.length; i++) {
            var ch = val.charAt(i);
            if (ch < "0" || ch > "9") {
			alert("Please enter correct Pincode");
            return false;
            }
      }
      return true;
}