function isNumber(evt)
{
    var iKeyCode = (evt.which) ? evt.which : evt.keyCode
    if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
      return false;

	 return true;
} 

function copyData()
{
	 var waterProcessingRatePerUnit=$("#waterProcessingRatePerUnit").val();
	 if(waterProcessingRatePerUnit!="")
	{
		 $("#waterGeneralRatePerUnit").val(waterProcessingRatePerUnit);
	}
}