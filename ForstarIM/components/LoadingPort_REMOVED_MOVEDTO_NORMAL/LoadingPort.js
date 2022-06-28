<script language="javascript" >

	function validateLoadingPort(form)
	{
		var name 	= document.getElementById("name");
		
		if (name.value=="") {
			alert("Please enter a port of loading name.");
			form.name.focus();
			return false;
		}		

		if (!confirmSave()) return false;
		else return true;
	}

</script>
