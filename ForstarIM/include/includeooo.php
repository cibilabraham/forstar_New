<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
 session_start();
 
  require("lib/errHandler.php");
 require("lib/databaseConnect.php");
 require("lib/ResultSetIterator.php");
 require("lib/constants.php");
 require("lib/user_class.php");
 require("lib/session_class.php");
 require("lib/config.php");
 require("lib/managerole_class.php");
 require("lib/modulemanager_class.php");
  require("lib/accesscontrol_class.php");
  require("lib/UnitTransfer_class.php");
   require("lib/plantsandunits_class.php");
  
   $databaseConnect		=	new DatabaseConnect();
 $sessObj				=	new Session($databaseConnect);
 $userObj				=	new User($databaseConnect,$sessObj);
  $modulemanagerObj		=	new ModuleManager($databaseConnect);
   $accesscontrolObj		=	new AccessControl($databaseConnect,$roleId);
  $manageroleObj			=	new ManageRole($databaseConnect);
  $unitTransferObj   =	new UnitTransfer($databaseConnect);
  $plantandunitObj		=	new PlantMaster($databaseConnect);
  
  
 //$sessObj->chkLogin($insideIFrame);
 
  #Getting Curret URL
  $currentFile = $_SERVER["SCRIPT_NAME"];
  $parts = Explode('/', $currentFile);
  $currentUrl = $parts[count($parts) - 1];
  #USD VALUE
  //$oneUSD	=	$usdvalueObj->findUSDValue();
  #NUM. ROWS TO BE DISPLAYED
//  $limit 	=	$displayrecordObj->findDisplayRecord();

  # Get Logged User Id
 // $userId	= $sessObj->getValue("userId");

  # Create Log file	
  //$logManagerObj->createLogFile($currentUrl);
  ?>