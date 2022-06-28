function validateAddFreezerCapacity(form)
{
	
	var freezerName		=	form.freezerName.value;
	var capacity		=	form.capacity.value;
	var freezingTime	=	form.freezingTime.value;
	
	if (freezerName=="") {
		alert("Please enter a Freezer name.");
		form.freezerName.focus();
		return false;
	}
	
	if (capacity=="") {
		alert("Please enter a Freezer Capacity.");
		form.capacity.focus();
		return false;
	}

	if (!isADigit(capacity)) {
		alert("Please enter a number.");
		form.capacity.focus();
		return false;
	}

	if (freezingTime=="") {
		alert("Please enter a Freezing Time.");
		form.freezingTime.focus();
		return false;
	}

	if (!isADigit(freezingTime)) {
		alert("Please enter a number.");
		form.freezingTime.focus();
		return false;
	}

	if (!confirmSave()) {
		return false;
	} else {
		return true;
	}
}


//Checking Digit From 0- 
function isADigit (str) 
{
    if (str == null) {
        return (false);
    }

    if (isNaN(str)) {
	return (false);
    } else if(str<0) {
	return (false);
    }
    return (true);
}