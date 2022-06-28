	require("ftemplate/include.php");

	require_once 'components/{modelName}/{modelName}_ajax.php';
	require_once("components/{modelName}/{modelName}_controller.php");
	${modelShortName}_c = new {modelName}_controller(array_merge($p,$g), $xajax);
	
	if ($p["cmdAddNew"]!="") 	${modelShortName}_c->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="") 	${modelShortName}_c->save();
	else if ($p["editId"]!="")	${modelShortName}_c->edit();	
	else if ($p["cmdDelete"]!="")	${modelShortName}_c->deleteRecs();
	else if ($g["print"]!="")	${modelShortName}_c->printList();

	// Display page
	${modelShortName}_c->index();
