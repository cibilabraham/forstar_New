
//sum of fixed cost 
function getTotalFixed()
{
	var sumTotal=0; var i=0;
	$(".newFixedCost").each(function()
	{
		var fixedVal=$("#newFixedCost_"+i).val();
		if(fixedVal!="")
		{
			sumTotal+=parseFloat(fixedVal);
		}
		i++;
	})
	var sum= parseFloat(sumTotal).toFixed(2);	
	$("#newTotalFix").val(sum);
	//alert(sumTotal);
}

//sum of variable cost 
function getTotalVariable()
{
	var sumTotal=0; var j=0;
	$(".newVariableCost").each(function()
	{	
		var variableVal=$("#newVarCost_"+j).val();
		if(variableVal!="")
		{
			sumTotal+=parseFloat(variableVal);
		}
		j++;
	})
	var sum= parseFloat(sumTotal).toFixed(2);	
	$("#newTotalVar").val(sum);
	//alert(sumTotal);
}

function isNumber(evt) {
        var iKeyCode = (evt.which) ? evt.which : evt.keyCode
        if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
            return false;

        return true;
    }   