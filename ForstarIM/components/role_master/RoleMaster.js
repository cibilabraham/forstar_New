<script>
	function validateRoleMaster(form)
	{
		var name 		= form.name.value;
		var entryExist		= document.getElementById("entryExist").value;

		if (name=="") {
			alert("Please enter a role name.");
			form.name.focus();
			return false;
		}		

		if (entryExist!="") {
			alert("Role is already exist in database.");
			form.name.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}
</script>