function validateLogin(form)
{
	var uname= form.txtUsername.value;
	var pword= form.txtPwd.value;
	
	if (!checkemailUsername(uname)) {
		form.txtUsername.focus();
		return false;
	} else if (pword=="") {
		alert("Please enter valid password.");
		form.txtPwd.focus();
		return false;
	}	
	return true;		
}