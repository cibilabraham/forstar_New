<?
class User
{  
/************************************************************************************************
   This class deals with all the operations relating to User&Login Management. 
 ************************************************************************************************/
	var $databaseConnect;
	var $sessionHandle;
    

	//Constructor, which will create a db instance for this class
	function User(&$databaseConnect, &$sessionHandle)
    {
        $this->databaseConnect =&$databaseConnect;
		$this->sessionHandle   =&$sessionHandle;
    }
	
	/*Returns encoded password*/
	function getEncodedString($pwd)
	{
		$pwd	=	base64_encode($pwd);
		return $pwd;
	}

	/*Returns decoded password*/
	function getDecodedString($pwd)
	{
		$pwd	=	base64_decode($pwd);
		return $pwd;
	}

	/*This function for user login and returns user info if success*/
	function chkLogin($username,$password)
	{
		//$qry	= " select id, username, password, role_id, last_login_time from user where username='$username' and password='$password'";
		$qry = " select a.id, a.username, a.password, a.role_id, b.name, a.last_login_time from user a , role b where a.username='$username' and a.password='$password' and a.role_id = b.id ";	
		//echo $qry;	
		$rs		=	array();
		$rs		=	$this->databaseConnect->getRecord($qry);
		return $rs;
	}

	

	/*This function is be used to get the lastinserted id(userid) from the user table.*/ 
	function insertedId()
    {
	  $rs	=	$this->databaseConnect->lastInserted();
      if(sizeof($rs)>0)
	  {
		 while(list(,$lastId)=each($rs))
		 {
		    $mLastIns=trim($lastId[0]);
		 }
	  }
      return $lastId;
    }

	function UpdateLoginTime($userId)
	{
		$qry	=	" update user set last_login_time=Now() where id=$userId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();		
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	function getUserName($userId)
	{
		$qry = " select username from user  where id='$userId'";	
		//echo $qry;	
		$rs		=	array();
		$rs		=	$this->databaseConnect->getRecord($qry);
		return (sizeof($rs)>0)?$rs[0]:"";
	}

}
?>