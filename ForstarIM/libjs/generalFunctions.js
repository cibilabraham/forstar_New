var selectedf = false;
var selectedf = false;
function checkAll(field)
{
	if (!selectedf) {
		for (i = 0; i < field.length; i++)
		field[i].checked = true ;
		selectedf = true;
	} else {
		for (i = 0; i < field.length; i++)
		field[i].checked = false ;
		selectedf = false;
	}
}


function checkAll(field,prefix,sel)
{
		for (i = 0; i < field.length; i++)
		{
			if ( field[i].name.indexOf(prefix) == 0 )
			{
				field[i].checked = sel;
			}
		}
}

function checkAll(field,prefix)
{
	//alert(field+","+prefix);
	if (!selectedf)
	{
		for (i = 0; i < field.length; i++)
		{
			if ( field[i].name && field[i].name.indexOf(prefix) == 0 )
			{
				field[i].checked = true ;
				
			}
		}
		selectedf = true;
	}
	else
	{
		for (i = 0; i < field.length; i++)
		{			
			if ( field[i].name && field[i].name.indexOf(prefix) == 0 )
			{
				field[i].checked = false ;
			}
		}
		selectedf = false;
	}
}

function chkAll(field,prefix)
{
	//alert(field+","+prefix);
	if (!selectedf) {
		for (i = 0; i < field.length; i++) {
			if ( field[i].id && field[i].id.indexOf(prefix) == 0 ) {
				field[i].checked = true ;
				
			}
		}
		selectedf = true;
	} else {
		for (i = 0; i < field.length; i++) {			
			if ( field[i].id && field[i].id.indexOf(prefix) == 0 ) {
				field[i].checked = false ;
			}
		}
		selectedf = false;
	}
}


function isWhitespace (c) 
{
    var whitespace = " \t\r\n\f\'";
    return (whitespace.indexOf (c) != -1);
}

function isDigit (str) 
{
    if (str == null) 
	{
        return (false);
    }
    if (isNaN(str))
    {
	return (false);
    }
	else if(str<=0)
	{
		return (false);
	}
    return (true);
}

function isBlank (str) 
{
    if (str == null) {
        return (true);
        }
    for (var i = 0; i < str.length; i++) {        
        var c = str.charAt (i);
        if (!isWhitespace (c)) {
           return (false);
           }
        }
    return (true);
}


function isValidEmail (str) 
{
    if (str == null) {
        return (false);
        }
    str = trim (str);                        // Start by trimming off whitespace at both ends
    for (var i = 0; i < str.length; i++) {   // Check that the address does not contain whitespace
        var c = str.charAt (i);
        if (isWhitespace (c)) {
           return (false);
           }
        }
    if (window.RegExp) {
        var tempStr = "a";  // First check that regular expression support is present
        var tempReg = new RegExp (tempStr);
        if (tempReg.test (tempStr)) {
            var r1 = new RegExp ("(@.*@)|(@\\.)|(^\\.)");
            var r2 = new RegExp ("^[a-zA-Z0-9\\!\\#\\$\\%\\&\\'\\*\\+\\-\\.\\/\\=\\?\\^\\_\\`\\{\\|\\}\\~]+\\@(\\[?)[a-zA-Z0-9\\-\\.]+\\.([a-zA-Z]{2,4}|[0-9]{1,3})(\\]?)$"); // Filter according to RFC822 rules
            return (!r1.test(str) && r2.test(str));
            }
        }
    return (str.indexOf (".") > 2) && (str.indexOf ("@") > 0);
}

function isValidZIP (str) 
{
    if (str == null) {
        return (false);
        }
    str = trim (str);                        // Start by trimming off whitespace at both ends
    if ((str.length != 5) && (str.length != 10)) {
       return (false);
       }
    for (var i = 0; i < str.length; i++) {   // Check that the address does not contain whitespace
        var c = str.charAt (i);
        if (i == 5) {
           if (c != '-') {
              return (false);
              }
           }
        else {
           if (!isDigit (c)) {
              return (false);
              }
           }
        }
    return (true);
}

function checkemail(str) 
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


	function checkemailUsername(str) 
{

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   alert("Please enter a valid username.")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert("Please enter a valid username.")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert("Please enter a valid username.")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    alert("Please enter a valid username.")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert("Please enter a valid username.")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    alert("Please enter a valid username.")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Please enter a valid username.")
		    return false
		 }

 		 return true					
	}

function assignValue(form,val,prefix)
{
	//alert(prefix);
	
	//showFnLoading();
 	if (val!="") {
		
		eval("form."+prefix+".value="+"'"+val+"'");
		//alert("hii");
		//eval( "form."+prefix+".value ="+val);
	}

	}

function printWindow(url,width,height)
{
		var winl = (screen.width - width) / 2;
     	var wint = (screen.height - height) / 2;
	eval("page = window.open(url, 'Forstar_Foods', 'top="+ wint +", left="+ winl +",  status=1,scrollbars=1,location=0,resizable=1,width="+ width +",height="+ height +"');");

	/*
	//removed on 16-07-07
	var newwindow	=	window.open(url,'',"width="+width+", height="+height+", top=300, left=500,  status=1, scrollbars=1, resizable=1");
	//alert(newwindow);
	return false;*/ 
}

function confirmSave()
{
	var saveMsg	=	"Do you wish to save the changes?";
	if(confirm(saveMsg))
	{
		return true;
	}
	return false;
	
}

function cancel(url)
{
	var cancelMsg	=	"Do you wish to cancel?";
	if(confirm(cancelMsg))
	{
		window.location.href =	url;
		//window.location =	url;
	}
	else
	{
		return false;
	}
	return true;
}

/* Confirm delete when using checkbox **/
function confirmDelete(form,prefix,rowcount)
{
	//alert("kii");
	//showFnLoading();
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;
	var conDelMsg	=	"Do you wish to delete the selected items?";
	//alert(rowCount+","+fieldPrefix);
	
	if(!isAnyChecked(rowCount,fieldPrefix))
	{
		alert("Please select a record to delete.");
		return false;
	}
	
	if(confirm(conDelMsg))
	{
		return true;
	}
		
	return false;

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



function isAnyCheckedgenerateOld(rowCount,fieldPrefix)
{
	for ( i=1; i<=rowCount; i++ )
	{
		if(document.getElementById(fieldPrefix+i).checked)
		{
		var checkVal=document.getElementById(fieldPrefix+i).value;
		//input = Base64.encode('erg');
		//alert(input);
			if(procurementId=="0")
				{
				procurementId=Base64.encode(checkVal);
				}
				else
				{
				procurementId+=','+Base64.encode(checkVal);
				}
				//return true;
		}
		//return true;
		
		
	}
	if(procurementId=="0")
	{
		return false;
	}
	else
	{
		window.location='RMProcurmentGatePass.php?procurementId='+procurementId;
	}
	//alert(procurementId);
		return true;
	
}




function formatNumber(num,dec,thou,pnt,curr1,curr2,n1,n2) 
{
	var x = Math.round(num * Math.pow(10,dec));
	if (x >= 0) n1=n2='';
	var y = (''+Math.abs(x)).split('');
	var z = y.length - dec;
	y.splice(z, 0, pnt);
	while (z > 3) {
		z-=3;
		y.splice(z,0,thou);
	}
	var r = curr1+n1+y.join('')+n2+curr2;
	if (parseFloat(r)==0)	{
		return "0.00";
	}
	return r;
}

//Get KeyCode
function getKeyCode(e)
{
	if(window.event!=undefined) return window.event.keyCode;
	return e.which;
}


// Days calculation from the current date
function findDaysDiff(selectDate)
{	
	var deltaD = (servertimeOBJ.getTime())-(new Date().getTime());
	var clientDate = new Date();
	var today = new Date(clientDate.getTime()+deltaD);	
	var days = 0;
	var difference = 0;
	SDate 	= selectDate.split("/");	
	//var entryDate = new Date(SDate[2],SDate[1]-1,SDate[0]);
	var entryDate = new Date(SDate[2],SDate[1]-1,SDate[0]);	
	difference = parseInt(entryDate.getTime())-parseInt(today.getTime()) ;
	days = Math.ceil(difference/(1000*60*60*24));
	//alert("today="+today+"entry="+entryDate+"Days="+days);	
	return days;
}
//Selected From and To date check
function checkDateSelected(fromDate,toDate)
{
	var days = 0;
	var difference = 0;
	fDate 	=	fromDate.split("/");
	var selFDate = new Date(fDate[2],fDate[1],fDate[0]);
	tDate 	=	toDate.split("/");
	var selTDate = new Date(tDate[2],tDate[1],tDate[0]);
	difference = selFDate - selTDate;
	days = Math.ceil(difference/(1000*60*60*24));
	return days;
}

function confirmCancelChallan(form, prefix, rowcount)
{
	
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;

	var conDelMsg	=	"Do you wish to cancel the selected Challan?";
	
	if (!isAnyChecked(rowCount,fieldPrefix)) {
		alert("Please select a record.");
		return false;
	}
	
	if (confirm(conDelMsg)) {
		return true;
	}		
	return false;
}


/* Confirm   when using checkbox -- edited on 09-2-08* */
function confirmSelRow(form, prefix, rowcount)
{
	
	var rowCount	=	rowcount;
	var fieldPrefix	=	prefix;

	var conDelMsg	=	"Do you wish to Continue?";
	
	if (!isAnyChecked(rowCount,fieldPrefix)) {
		alert("Please select a record.");
		return false;
	}
	
	if (confirm(conDelMsg)) {
		return true;
	}
		
	return false;
}

function confirmContinue()
{
	var saveMsg	=	"Do you wish to Continue?";
	if (confirm(saveMsg)) {
		return true;
	}
	return false;
}
//Number Format
function number_format( number, decimals, dec_point, thousands_sep ) 
{
    // From : http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_number_format/
    // *     example 1: number_format(1234.5678, 2, '.', '');
    // *     returns 1: 1234.57
 
    var i, j, kw, kd, km;
 
    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ",";
    }
    if (thousands_sep == undefined) {
        thousands_sep = ".";
    }
 
    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";
 
    if ((j = i.length) > 3) {
        j = j % 3;
    } else{
        j = 0;
    }
 
    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
 
    return km + kw + kd;
}
/**************** Date Format checking starts here *****************/
/**
 * DHTML date validation script for dd/mm/yyyy. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr)
{
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}
/**************** Date Format checking Ends here *****************/

function confirmRemoveItem()
{
	if( confirm("Do you wish to remove this item?") ) return true;
	return false;
}

/* common Confirm delete */
function confirmMsgDel()
{	
	var conDelMsg	=	"Do you wish to delete the selected items?";
	if (confirm(conDelMsg)) return true;		
	return false;
}

// Checking the number is integer/ float
function checkNumber(x)
{	
	var anum=/(^\d+$)|(^\d+\.\d+$)/
	if (anum.test(x)) {
		testresult=true
	} else {
		alert("Please enter a valid number!")
		testresult=false
	}
	return (testresult)
}

	/******************** Check Phone Number  Starts Here **/
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
		for (i = 0; i < s.length; i++) {   
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
	/*********************************** Phone Number Check Ends Here **/

	/*** Email Check **/
	function echeck(str) 
	{
		var at="@";
		var dot=".";
		var lat=str.indexOf(at);
		var lstr=str.length;
		var ldot=str.indexOf(dot);
		if (str.indexOf(at)==-1){
		   alert("Invalid E-mail ID");
		   return false;
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert("Invalid E-mail ID");
		   return false;
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert("Invalid E-mail ID");
		    return false;
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    alert("Invalid E-mail ID");
		    return false;
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert("Invalid E-mail ID");
		    return false;
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    alert("Invalid E-mail ID");
		    return false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Invalid E-mail ID");
		    return false;
		 }
 		 return true;					
	}
	/** Email Chk Ends Here**/

	/* Chk Pincode Integer*/
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
	/* Chk Pincode Integer Ends*/

	/* Logout Function*/
	function confirmLogout()
	{
		var confirmMsg	=	confirm("Do you wish to logout?")	
		if (confirmMsg) {
			window.location='Logout.php';
		}	
	}

	/* Camera Window*/
	var newwindow;
	function mynewwindow(url)
	{
		newwindow=window.open(url,'IM','status=0, scrollbars=1, resizable=1, titlebar=no'); 
	}

	/*
	* For submitting the Form
	*/
	function submitForm(field1,field2,frmName)
	{
		if (document.getElementById(field1).value!="" && document.getElementById(field2).value!="")
		{
			frmName.submit();
		}
		return false;
	} 

	/*
	* Implode function like PHP function
	* + original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)	
	*/
	function implode (glue, pieces) 
	{
		return ( ( pieces instanceof Array ) ? pieces.join ( glue ) : pieces );
	}

	/*
	* Check digit is float/ int
	*/
	function checkDigit(x)
	{	
		var anum=/(^\d+$)|(^\d+\.\d+$)/
		if (anum.test(x)) testresult=true
		else testresult=false		
		return (testresult)
	}

	/*
	* Check Date With In
	* This will alert 'false'
		alert(dateWithin('12/20/2007 12:00:00 AM','12/20/2007 1:00:00 AM','12/19/2007 12:00:00 AM'));
	* Begin date, end Date, Check date
	*/
	function dateWithin(beginDate, endDate, checkDate) 
	{
		var b,e,c;
		//b = Date.parse(beginDate); //Format m/d/y
		//e = Date.parse(endDate);
		//c = Date.parse(checkDate);		
		b = convertTime(beginDate); //Format d/m/y
		e = convertTime(endDate);
		c = convertTime(checkDate);
		if((c <= e && c >= b)) {
			return true;
		}
		return false;
	}

	/*
	* Parse Date to Time (Format: d/m/y)
	*/
	function convertTime(selDate)
	{
		LDT		= selDate.split("/");
		var LDTime	= new Date(LDT[2], LDT[1], LDT[0]);
		return LDTime.getTime();
	}

	String.prototype.trim = function() {
		a = this.replace(/^\s+/, '');
		return a.replace(/\s+$/, '');
	};

	// Trim function starts here
	// Removes leading whitespaces
	function LTrim( value ) 
	{		
		var re = /\s*((\S+\s*)*)/;
		return value.replace(re, "$1");
		
	}
	
	// Removes ending whitespaces
	function RTrim( value ) 
	{
		var re = /((\s*\S+)*)\s*/;
		return value.replace(re, "$1");		
	}
	
	// Removes leading and ending whitespaces
	function trim (value)
	{
		return LTrim(RTrim(value));
	}
	// Ttrim function ends here ----------------------------

	function init_fields()
	{
		// Readonly change style  starts here
		var el, els, e, f = 0, form, forms = document.getElementsByTagName('form');
		while (form = forms.item(f++)) {
			e = 0; els = form.getElementsByTagName('input');
			while (el = els.item(e++))
				if (el.readOnly || el.readonly) el.className = 'readonly';
		}
		// Ends here
	}

	// Chk login
	function chkLoginStatus()
	{
		//xajax_chkLoginStatus();
		//setTimeout("chkLoginS()",1000);
	}
	
	function doLogout()
	{	
		window.location='Logout.php';
	}	
	// Login status ends here

	// Checking the number is integer/ float return msg
	function chkValidNumber(x)
	{	
		var anum=/(^\d+$)|(^\d+\.\d+$)/;

		if (x<=0) testresult=false;
		else if (anum.test(x)) testresult=true;
		else testresult=false;

		if (!testresult) alert("Please enter a valid number!");
		return testresult;
	}

	// Return without Msg
	function validNumber(x)
	{	
		var anum=/(^\d+$)|(^\d+\.\d+$)/;

		if (x<=0) testresult=false;
		else if (anum.test(x)) testresult=true;
		else testresult=false;
		
		return testresult;
	}


	function isFloat(value)
	{
   	      if (/\./.test(value)) return true;
	      else return false;
    }

	function in_array (needle, haystack, argStrict) 
	{
		// Checks if the given value exists in the array  
		// 
		// version: 1109.2015
		// discuss at: http://phpjs.org/functions/in_array    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// +   improved by: vlado houba
		// +   input by: Billy
		// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
		// *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);    // *     returns 1: true
		// *     example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
		// *     returns 2: false
		// *     example 3: in_array(1, ['1', '2', '3']);
		// *     returns 3: true    // *     example 3: in_array(1, ['1', '2', '3'], false);
		// *     returns 3: true
		// *     example 4: in_array(1, ['1', '2', '3'], true);
		// *     returns 4: false
		var key = '',        strict = !! argStrict;
	 
		if (strict) {
			for (key in haystack) {
				if (haystack[key] === needle) {                return true;
				}
			}
		} else {
			for (key in haystack) {            if (haystack[key] == needle) {
					return true;
				}
			}
		} 
		return false;
	}
	
	
	
	