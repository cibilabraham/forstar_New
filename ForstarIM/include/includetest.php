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
  require("lib/anishiyatesting_class.php");
    
    
   
   $databaseConnect		=	new DatabaseConnect();
 $sessObj				=	new Session($databaseConnect);
 $userObj				=	new User($databaseConnect,$sessObj);
  $modulemanagerObj		=	new ModuleManager($databaseConnect);
   $accesscontrolObj		=	new AccessControl($databaseConnect,$roleId);
  $manageroleObj			=	new ManageRole($databaseConnect);
 $testing    =	new testing($databaseConnect);
  
  ?>
  