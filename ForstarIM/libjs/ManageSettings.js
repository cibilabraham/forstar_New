function validate()
{
	var username=$("#username").val();
	var pwd=$("#pwd").val();
	if(username=="")
	{
		alert("Please enter username");
		return false
	}
	if(pwd=="")
	{
		alert("Please enter password");
		return false
	}
		return true
	

}