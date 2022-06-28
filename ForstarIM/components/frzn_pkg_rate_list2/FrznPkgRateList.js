<script language="javascript">
	function validateFrznPkgRateList(form)
	{
		var name 	= form.name.value;
		var entryExist	= document.getElementById("entryExist").value;

		if (name=="") {
			alert("Please enter a rate list name.");
			form.name.focus();
			return false;
		}		

		if (entryExist!="") {
			alert("Rate List is already exist in database.");
			form.name.focus();
			return false;
		}

		if (!confirmSave()) return false;
		else return true;
	}
</script>