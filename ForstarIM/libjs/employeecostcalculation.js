function calcEmployeeCost()
{	
	//var costVal=$("#costPercent_"+i).val();
	var totCostPercent=0; 
	for(i=0; i<7; i++)
	{
		var costPercent=$("#costPercent_"+i).val();
		//alert(costPercent);
		if(costPercent!="")
		{
			totCostPercent+=parseFloat(costPercent);
		}
	}
	if(i==7)
	{	
		//alert(totCostPercent);
		var total=number_format(totCostPercent,2,'.','');	
		$("#costPercent_"+i).val(total);
	}
}

	function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }    