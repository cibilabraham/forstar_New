function validateEmployeeDetails(form)
{
	var name = form.name.value;
	var designation = form.designation.value;
	var department = form.department.value;
	
	if(name == "")
	{
		alert("Please Enter Name");
		form.name.focus();
		return false;
	}
	
	if(designation == "")
	{
		alert("Please Select Designation");
		form.designation.focus();
		return false;
	}
	
	if(department == "")
	{
		alert("Please Select Department");
		form.department.focus();
		return false;
	}
	
	return true;
	
}