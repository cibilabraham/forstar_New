function releaseSeal(form,val,prefix)
{
   
	//showFnLoading();
	// var form= form;
	// var confirm	=	confirm;
	// var sealid	=	sealid;
	var conDelMsg	=	"Do you wish to Release Seal?";
	
	
	if(confirm(conDelMsg))
	{
		if (val!="") {
		eval("form."+prefix+".value ="+"'"+val+"'");
		//eval( "form."+prefix+".value =	"+val);
		}

		return true;
	}
		
	return false;

}