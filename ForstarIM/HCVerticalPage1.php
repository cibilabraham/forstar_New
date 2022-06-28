<?php
require("include/include.php");
include ("jpgraph-1.26/src/jpgraph.php");
include ("jpgraph-1.26/src/jpgraph_canvas.php");
	$selHCId = $g["selHCId"];
	require("lib/LanguageResource.php");

	$langResObj	=	new LanguageResource("resource_bundle/","hc_");
	//$langResHandle	=	$langResObj->loadBundle("eng");
	# Find  Records
	$healthCertificateRec	= $healthCertificateObj->find($selHCId);
		$editHealthCertificateId = $healthCertificateRec[0];
		$selLanguage		= $healthCertificateRec[1];
		$langResHandle	=	$langResObj->loadBundle($selLanguage);

		$txt = $langResHandle["P.I.LH"];	

	if ($selHCId) {
		
	    // Setup a basic canvas we can work 
		$g = new CanvasGraph(30,350,'auto');
		$g->SetMargin(0,0,0,0);
		//$g->SetShadow();
		$g->SetColor('white'); 
		$g->SetMarginColor("white");
		// We need to stroke the plotarea and margin before we add the
		// text since we otherwise would overwrite the text.
		$g->InitFrame();

		// Draw a text box in the middle
		//$txt=$selHCId;
		$t = new Text($txt,10,350);
		$t->SetFont(FF_ARIAL,FS_BOLD,11);		
		// How should the text box interpret the coordinates?
		$t->Align('center','bottom');
		$t->SetOrientation("v");
		
		// How should the paragraph be aligned?
		$t->ParagraphAlign('center');

		// Add a box around the text, white fill, black border and gray shadow
		//$t->SetBox("white","black","gray");

		// Stroke the text
		$t->Stroke($g->img);		
		// Stroke the graph
		$g->Stroke();
	}
?>