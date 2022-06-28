<?php
	require("include/includeLogin.php");
	$err			=	"";	
	
	#-------------------------------------------
 	#Getting Clent & Server IP Address
	$clientIP	=	$_SERVER['REMOTE_ADDR'];
	
	$serverIP	=	$_SERVER['SERVER_ADDR']; 
	#IsIPAddressAlloed	(manageipaddress_class.php)
	if(($serverIP!=$clientIP) && !($manageipaddressObj->isIPAddressAllowed($clientIP)) && ($manageipaddressObj->isIPEnabled()!=""))
	{
		die();
	}
	#-------------------------------------------
	# Check Already Logged
	$cUserId = $sessObj->getValue("userId");
	$cUserRoleId 	= $sessObj->getValue("userRole");
	if ($cUserId && $cUserRoleId) {
		header("Location:".$urlAfterLogin);
	}
	if ($p["cmdLogin"]!="") {
		$username		=	$p["txtUsername"];
		$password		=	$userObj->getEncodedString($p["txtPwd"]);

		$checkLogin		=	$userObj->chkLogin($username,$password);
		
	#Checking Client IP Address
	
		if ((sizeof($checkLogin) > 0)) {
			$userId		=	$checkLogin[0];
			$userName	=	$checkLogin[1];
			$userRoleId	=	$checkLogin[3];
			$userRoleName = 	$checkLogin[4];
			$lastLogin = date('j M Y, H:i a ',strtotime($checkLogin[5]));
					
			#Update Login Time	
			$updateLoginTime = $userObj->UpdateLoginTime($userId);
				
			$sessObj->createSession("userId",$userId);
			$sessObj->createSession("userName",$userName);
			$sessObj->createSession("userRole",$userRoleId);
			$sessObj->createSession("userRoleName",$userRoleName);
			$sessObj->createSession("lastLogin",$lastLogin);
			header("Location:".$urlAfterLogin);
			exit;
		} else {
			$err		=	$errLogin;
		}
	}
	

	$ON_LOAD_PRINT_JS	= "libjs/user.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle'>
<tr>
	<Td height='100'></td>
</tr>
<tr><td>
	<!-- Login Box Start -->
	<form name="loginFrm" method='post' action="Login.php" onLoad='document.loginFrm.txtUser.focus();' >
	<table width="330" height="170" border="0" align="center" cellpadding="0" cellspacing="0" valign='middle'>
        <tr> 
          <td rowspan="2" bgcolor="#bfbfbf" width="1"></td>
          <td  height="21" align="left" valign="middle" background="images/box_02.gif"><table width="98%" border="0" align="center" cellpadding="1" cellspacing="1">
              <tr>
                <td width="95%" class="pageName">Login</td>
                <td width="5%" align="right"><div align="left"><img src="images/bullet1.gif" width="8" height="9"></div></td>
              </tr>
            </table></td>
          <td colspan="2" rowspan="4" background="images/box_03.gif" >&nbsp; 
          </td>
        </tr>
        <tr> 
          <td  width="319" height="142" align="center" valign="top" >
		  <table width="300" border="0" align="center" cellpadding="1" cellspacing="1">
              <tr> 
                <td colspan="2" height="13"></td>
              </tr>
					<?
						if ( strlen($err) !=0  ){
					?>		
							<tr> 
								<td class="err1" colspan='3' align='center'><?=$errLogin;?></td>
							</tr>
					<?
						}
					?>
					
              <tr> 
                <td class="caption1">* User Name :</td>
                <td align="center"><input name="txtUsername" type="text" id="txtUsername" size="25"></td>
              </tr>
             
              <tr> 
                <td colspan="2" height="8"></td>
              </tr>
              <tr> 
                <td class="caption1">* Password :</td>
                <td align="center"><input name="txtPwd" type="password" id="txtPwd" size="25"></td>
              </tr>
              <tr> 
                <td colspan="2" height="8"></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
                <td align="center">
					<input type="submit" class="button" name="cmdLogin"  id="cmdLogin"  value=" Login "  onClick=" return validateLogin(document.loginFrm);">
				</td>
              </tr>
              <tr> 
                <td colspan="2">
				<!-- <a href="forgotPassword.php" class="link1">Forgot Password?</a> -->
				</td>
              </tr>
			  <tr> 
                <td colspan="2" height="6"></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr> 
          <td height="6" colspan="3" background="images/box_05.gif"></td>
          <td width="4" height="6"><img src="images/box_06.gif" width="4" height="6" alt=""></td>
        </tr>
        <tr> 
          <td> <img src="images/spacer.gif" width="1" height="1" alt=""></td>
          <td> <img src="images/spacer.gif" width="267" height="1" alt=""></td>
          <td width="1"> <img src="images/spacer.gif" width="1" height="1" alt=""></td>
          <td> <img src="images/spacer.gif" width="4" height="1" alt=""></td>
        </tr>
      </table>
	  </form>
	  <!-- Login Box End -->
	</td>
   </tr>
  </table>
  <SCRIPT LANGUAGE="JavaScript">
  <!--
  document.loginFrm.txtUsername.focus();
  //-->
  </SCRIPT>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>