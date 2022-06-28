<?php
class ManageIPAddress
{  
	/****************************************************************
	This class deals with all the operations relating to Manage IP Address
	*****************************************************************/
	var $databaseConnect;	
    
	//Constructor, which will create a db instance for this class
	function ManageIPAddress(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect;
	}

	function addIPAddress($ipAddressFrom,$ipAddressTo,$description, $selIP)
	{		
		$qry	=	"insert into ipaddress (ipaddressfrom,ipaddressto,descr,iptype) values('".$ipAddressFrom."','".$ipAddressTo."', '".$description."', '".$selIP."')";
		
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	

	# Returns all Users
	
	function fetchAllRecords()
	{
		$qry	=	"select id,ipaddressfrom,ipaddressto,descr,iptype from ipaddress";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get USER based on id 
	
	function find($ipAddressId)
	{
		$qry	=	"select id,ipaddressfrom , ipaddressto, descr, iptype from ipaddress where id=$ipAddressId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Update a IP Address	
	function updateIPAddress($ipAddressId,$ipAddressFrom,$ipAddressTo,$description, $selIP)
	{
		$qry	=	" update ipaddress set ipaddressfrom='$ipAddressFrom', ipaddressto='$ipAddressTo' , descr='$description', iptype='$selIP' where id=$ipAddressId";

		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete a IP Address	
	function deleteIPAddress($ipAddressId)
	{
		$qry	=	" delete from ipaddress where id=$ipAddressId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	
	#Checking Client IP Address is exisiting Used	In Login Session
	function searchIPAddress($clientIP)
	{	
		$qry	=	"select id, ipaddressfrom, ipaddressto from ipaddress where ipaddressfrom='$clientIP'";	
		$rec	=	$this->databaseConnect->getRecord($qry);
		return sizeof($rec) > 0 ? true : false;
	}


	function searchIPAddressRange($searchIPKey)
	{
		$qry	=	"select id, ipaddressfrom, ipaddressto from ipaddress where ipaddressfrom like '$splitIP%'" ;

		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#IP Address Privilege Checking
	function isIPAddressAllowed($clientIP)
	{	
		list ($C1, $C2, $C3, $C4) = explode('.', $clientIP); //$C - Client
		$searchIPKey	= trim($C1.".".$C2.".".$C3);

		$ipExists = $this->searchIPAddress($clientIP);
		
		if(!$ipExists)
		{
			$checkIPAddressRangeRecs	=	$this->searchIPAddressRange($searchIPKey);
			
			foreach($checkIPAddressRangeRecs as $IPR)
			{
				 $ipFrom	=	 $IPR[1];
				 $ipTo		=	 $IPR[2];
		
				list ($F1, $F2, $F3, $F4) = explode('.', $ipFrom); //$F - IP From
				list ($T1, $T2, $T3, $T4) = explode('.', $ipTo);	//$T - IP To
				if($C4>=$F4 && $C4<=$T4) return true;						
			}
		}
		return $ipExists;
	}

	#Checking IP ADDRESS Enabled/Disabled
	#--------------------------------------
	function isIPEnabled()
	{
		$qry	=	"select id, ip_enable from c_system where ip_enable=1";
		$rec	=	$this->databaseConnect->getRecord($qry);
		return sizeof($rec) > 0 ? true : false;
	}

	function updateIPAddressPrivilege($ipEnabled)
	{
		$qry	=	" update c_system set ip_enable='$ipEnabled' where id=1";
 	
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	#--------------------------------------
}

?>