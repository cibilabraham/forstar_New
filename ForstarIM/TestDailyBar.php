<?php
require("include/include.php");
require("lib/bar_class.php"); //for uploading
$barObj			=	new Bar($databaseConnect); //for uploading
DEFINE("TTF_DIR","c:/windows/fonts/");
include ("jpgraph-1.19/jpgraph.php");
include ("jpgraph-1.19/jpgraph_bar.php");
include ("jpgraph-1.19/jpgraph_log.php"); 


	//$selDate = $g["selDate"];
	
	$dateFrom = mysqlDateFormat(date("d/m/Y", mktime(0, 0, 0, date("m"), date("d")-21, date("Y")))); 
	$dateTill = mysqlDateFormat(date("d/m/Y"));

	$barRecords		=	$barObj->fetchFishProcessSummaryRecords($dateFrom,$dateTill);


	if(sizeof($barRecords)>0)
		{
			$i	=	0;
			$months		=	array();
			$databary	=	array();	
			while(list(,$vals)=each($barRecords))
			{

			$fishRec			=	$fishmasterObj->find($vals[1]);
			$fishName			=	$fishRec[2];
			$processCodeRec		=	$processcodeObj->find($vals[2]);
			$processCode		=	$processCodeRec[2];
			$months[$i]			=	$fishName."-".$processCode; //Months from Database
			
			$totalQty		=	$vals[4];
			$databary[$i]	=	$totalQty; // Quantity from database
			
			$i++;
			}
		}

// New graph with a drop shadow
$graph = new Graph(400,400,'auto');
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($months);
$graph->xaxis->SetLabelAngle(90);


// Color the two Y-axis to make them easier to associate
// to the corresponding plot (we keep the axis black though)
$graph->yaxis->SetColor("black","red");


// Set title and subtitle
$graph->title->Set("Analysis of Daily Catch Entry");
$graph->subtitle->Set("Y-Scale: 'Quantity'");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Create the bar plot
$b1 = new BarPlot($databary);
$b1->SetLegend("Quantity");
//$b1->SetFillColor("orange");


// The order the plots are added determines who's ontop
$graph->Add($b1);

// Finally output the  image
$graph->Stroke();

?>





<?
/*
require ("include/include.php");

#-----------------------------
# Hard Coded data for testing 
#-----------------------------


DEFINE("TTF_DIR","c:/windows/fonts/");

include ("../jpgraph-1.19/jpgraph.php"); 
include ("../jpgraph-1.19/jpgraph_log.php"); 
include ("../jpgraph-1.19/jpgraph_bar.php"); 
	$show=true;

	$monthFrom		=	$g['fmonth'];
	$ins_catId			=	$g['catid'];
	$selMonth	=	$g['selMonth'];
//	echo "icid=$ins_catId<br>";
	
	if (!$ins_catId) return;

	$currentMonth	=	$nowY."-".$nowM."-".$nowD;
	$ydata			=	array();	
	$totalMonths	=	$g['selN'];

	$month2	=	substr($currentMonth,5,2);
	$LM		=	$month2-$totalMonths;
	$year2	=	substr($currentMonth,0,4);
	$datax	=	array();
	$mamt = 0;

	// Create the graph. These two calls are always required 

	$graph = new Graph(600,300,"auto");     
	$graph->img->SetMargin(40,110,20,40); 
	
	//$graph->SetY2Scale("log");
	
	
	$color=array("blue","green","red","lightblue","yellow","orange","lightgreen","pink");
	
	$cia = split(',',$ins_catId);
	for ( $i=0; $i<=$selMonth;$i++ )
	{
		$datax[$i]	=	date("M y", mktime(0, 0, 0, $LM, $month2, $year2));	
		$LM++;
	}
	$tsz=$i;
//print_r($datax);
//exit;
	$lps = array();
	$lpi=0;
	// we have to get data for each month by queries and add to array
	for ($x=0; $x < sizeof($cia); $x++)
	{
		if ( $cia[$x] == "" ) continue;
		$catId = $cia[$x];
		$getAmounts		=	$exTrackerObj->getPlotAmount($SessUserId,$monthFrom,$catId,$currentMonth);
		$ln=$exTrackerObj->getExpenseType($SessUserId,$catId);
		if(sizeof($getAmounts)>0)
		{
			$i	=	0;
			$tdata = array();
			while(list(,$vals)=each($getAmounts))
			{
				$hasdata=1;
				$amt = $vals[0];
				$date = $vals[1];
				$tdata[$date]=$amt;
				if ($amt > $mamt )  $mamt = $amt;
				$i++;
			}
		}
		$ydata = array();
		for ( $z=0; $z<$tsz;$z++ )
		{
			$v = $tdata[ $datax[$z] ];
			if ( $v != "" )	$ydata[$z]=$v;
			else $ydata[$z]=0;
		}
//		print_r($ydata);
		$lp = new BarPlot($ydata);
		$lp->SetFillColor($color[$x]); 
		$lp->SetWeight(2); 
		$lp->SetLegend($ln);
		$lps[$lpi++] = $lp;		
	}

  $gbplot = new GroupBarPlot( $lps);
  $graph->Add($gbplot); 


	$graph->SetScale("textlin",0,$mamt);
	$graph->SetShadow(); 
	$graph->ygrid->Show(true,true); 
	$graph->xgrid->Show(true,false); 
	$graph->title->Set("Historical View"); 
	$graph->xaxis->title->Set(""); 
	$graph->yaxis->title->Set("Amount->"); 

	$graph->title->SetFont(FF_FONT1,FS_BOLD); 
	$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD); 
	$graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 8); 
//	$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD); 
	$graph->yaxis->SetColor("blue");

$graph->legend->Pos(0.01,0.5,"right","center"); 

$graph->xaxis->SetTickLabels($datax);
//$graph->xaxis->SetTextTickInterval(2); 
$graph->SetColor("white");
$graph->SetMarginColor("white");
$graph->xaxis->SetLabelAngle(30); 
// Display the graph 
//$show=false;
if ( $show ) $graph->Stroke();
*/
?> 