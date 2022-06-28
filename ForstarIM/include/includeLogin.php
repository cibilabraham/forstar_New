<?php 
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
 session_start();

 require("lib/databaseConnect.php");
 require("lib/constants.php");
 require("lib/session_class.php");
 require("lib/user_class.php");
 require("lib/config.php");
 require("lib/manageipaddress_class.php");
 require("lib/LogManager_class.php");	

 $databaseConnect	= new databaseConnect();
 $sessObj			= new Session($databaseConnect);
 $userObj			= new User($databaseConnect,$sessObj); 
 $manageipaddressObj	= new ManageIPAddress($databaseConnect);
 $logManagerObj		= new LogManager($databaseConnect, $sessObj);
?>