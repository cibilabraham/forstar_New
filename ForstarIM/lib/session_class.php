<?
class Session
{  
/************************************************************************************************
   This class deals with all the operations relating to Session Management. 
 ************************************************************************************************/

	var $databaseConnect;

	/*Constructor, which will create a db instance for this class*/
	function Session(&$databaseConnect)
    	{
       		$this->databaseConnect =&$databaseConnect;
    	}

	/*Creates new session variable*/
	function createSession($varName, $value)
	{
		
		$_SESSION[$varName] = $value;
	}

	/*Checkes whther the session is existing or not, If existing returns userid. If session/login is incorrect it redirects the screen to the login page.*/
	function chkLogin($insideIFrame=null)
	{		
		if ($insideIFrame && $this->getValue("userId")=="") {
			echo "<script language='JavaScript' type='text/javascript'>parent.chkLogin();</script>";
		} else if ( $this->getValue("userId") == "" )	{
			header ("Location: Login.php");
			exit;
		}
		return true;
	}

	/*Checkes wheter the session is present/empty or not*/
	function isExisting($varName)
	{
		if (strlen($this->getValue($varName))>0) {
			return true;
		} else {
			return false;
		}
	}


	/*Returns value from a session variable.*/
	function getValue($varName)
	{
		return $_SESSION[$varName];
	}


	/*Returns sessionId*/
	function getSessId()
	{
		return session_id();
	}

	/*Updates the Session value*/
	function updateSession($varName, $value)
	{
		$_SESSION[$varName] = $value;
	}

	/*Puts new the Session value*/
	function putValue($keyName, $value)
	{
		$_SESSION[$keyName] = $value;
	}

	/*This function for user logout*/
	function sessionEnd()
	{
		session_destroy();
	}
}
?>