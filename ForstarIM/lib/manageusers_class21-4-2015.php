<?php
class ManageUsers
{  
	/****************************************************************
	This class deals with all the operations relating to Manage Users
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageUsers(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Add 
	function addUser($addUserName,$PWord,$defaultCompany,$selRole)
	{
		$qry	= "insert into user (username,password,default_company,role_id) values('".$addUserName."','".$PWord."','".$defaultCompany."',  '".$selRole."')";

		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	##add allocated  company,unit and department for user
	function addUserDetail($lastId,$company,$unit,$department,$role)
	{
		$qry	= "insert into user_details (user_id,company_id,unit_id,department_id,role) values('".$lastId."','".$company."','".$unit."','".$department."','".$role."')";

		//echo $qry; 
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Users
	function fetchAllRecords()
	{
		$qry	= "select * from user";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get USER based on id 
	function find($userId)
	{
		$qry	= "select * from user where id=$userId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	function findDetails($userId)
	{
		$qry	= "select * from user_details where user_id=$userId";
		//echo $qry;
		return $this->databaseConnect->getRecords($qry);
	}
	

	# Update a User	
	function updateUser($upUserName,$PWord,$selRole,$defaultCompany,$userEditId)
	{
		$qry	= " update user set username='$upUserName',password='$PWord',default_company='$defaultCompany',role_id='$selRole' where id=$userEditId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	###UPDATE USER DETAILS
	function updateUserDetail($userEditId,$company,$unit,$department,$role,$editUserDetail)
	{
		$qry	= " update user_details set user_id='$userEditId',company_id='$company',unit_id='$unit',department_id='$department',role='$role' where id=$editUserDetail";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a User	 Detail row
	function delUserDetailRec($userId)
	{
		$qry	=	" delete from user_details where id=$userId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete all User details of an user
	function delAllUserDetail($userId)
	{
		$qry	=	" delete from user_details where user_id=$userId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	
	# Delete a User	
	function deleteUser($userId)
	{
		$qry	=	" delete from user where id=$userId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function getUsername($userId)
	{
		$qry = " select username from user where id='$userId'";
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	# -----------------------------------------------------
	# Checking User Id is in use (Manage Dashboard, Daily catch entry);
	# -----------------------------------------------------
	function userRecInUse($userId)
	{		
		$qry = " select id from (
				select a.id as id from assign_dashboard a where a.user_id='$userId'
			union
				select a1.id as id from t_dailycatch_main a1 where a1.createdby='$userId'			
			) as X group by id ";

		//echo "<br>$qry<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	###get all units for a company for xajax
	function getUnit($company)
	{		
		$qry="select id,name from m_plant where  company_id='$company' and active='1' order by name asc";
		//echo $qry;
		
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>=1) $resultArr = array(''=>'-- Select All--');
		else if (sizeof($result)==1) $resultArr = array();
		else $resultArr = array(''=>'-- Select --');

		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;	
	}


	###get all units for a company
	function getUserCompany($companyId)
	{		
		$qry="select id,name from m_plant where  company_id='$companyId' and active='1' order by name asc";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		return $result;	
	}

	###get company, unit and department alloted for this user
	function getFunctionDetail($userId)
	{
		$companyVal=array(); 	$unitVal=array();  	$departmentVal=array(); 
		$qry="select company_id,unit_id,department_id from user_details where  user_id='$userId'";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		foreach($result as $res)
		{	
			$companyId=$res[0];
			$unitId=$res[1];
			$departmentId=$res[2];
			if($companyId=="0")
			{
				$query = "select id,display_name  from m_billing_company where active='1'";
				$rest	= $this->databaseConnect->getRecords($query);
				foreach($rest as $rt)
				{
					$id=$rt[0];
					$name=$rt[1];
					$companyVal[$id]=$name;
					if($unitId=="0")
					{
						$unitRecs= "select id,name from m_plant where company_id='$id' and active='1'";
						$unit	= $this->databaseConnect->getRecords($unitRecs);
						foreach($unit as $units)
						{
							$uid=$units[0];
							$uname=$units[1];
							$unitVal[$id][$uid]=$uname;
						}
					}
					else
					{
						$unitRecs= "select id,name from m_plant where company_id='$id' and id='$res[1]' and active='1'";
						$unit	= $this->databaseConnect->getRecords($unitRecs);
						foreach($unit as $units)
						{
							$uid=$units[0];
							$uname=$units[1];
							$unitVal[$id][$uid]=$uname;
						}
					}

				}
			}
			else
			{
				$query = "select id,display_name  from m_billing_company where id='".$res[0]."'";
				$rests	= $this->databaseConnect->getRecords($query);
				foreach($rests as $rts)
				{
					$id=$rts[0];
					$name=$rts[1];
					//echo $id.','.$name;
					$companyVal[$id]=$name;
					if($unitId=="0")
					{
						$unitRecs= "select id,name from m_plant where company_id='$id' and active='1'";
						$unit	= $this->databaseConnect->getRecords($unitRecs);
						foreach($unit as $units)
						{
							$uid=$units[0];
							$uname=$units[1];
							$unitVal[$id][$uid]=$uname;
						}
					}
					else
					{
						$unitRecs= "select id,name from m_plant where company_id='$id' and id='$res[1]' and active='1'";
						$unit	= $this->databaseConnect->getRecords($unitRecs);
						foreach($unit as $units)
						{
							$uid=$units[0];
							$uname=$units[1];
							$unitVal[$id][$uid]=$uname;
						}
					}

				}
			}
			###department 	
			if($res[2]=='0')
			{
				$departmentRecs= "select id,name from m_department where active='1'";
				$department	= $this->databaseConnect->getRecords($departmentRecs);
				foreach($department as $departments)
				{
					$dpid=$departments[0];
					$dpname=$departments[1];
					$departmentVal[$dpid]=$dpname;
				}
			}
			else
			{
				$departmentRecs= "select id,name from m_department where id='$res[2]' and active='1'";
				$department	= $this->databaseConnect->getRecords($departmentRecs);
				foreach($department as $departments)
				{
					$dpid=$departments[0];
					$dpname=$departments[1];
					$departmentVal[$dpid]=$dpname;
				}
			}
		}
		$resultArr=array($companyVal,$unitVal,$departmentVal);
		return $resultArr;
	}



	###get company, unit and department alloted for this user
	function getUserReferenceSet($userId)
	{
		$companyVal=array(); 	$unitVal=array();  	$departmentVal=array(); 
		$qry="select mbc.id,mbc.display_name,mbc.default_row,mp.id,mp.name,md.id,md.name from user_details ud left join m_billing_company mbc on (ud.company_id=mbc.id or ud.company_id=0) left join m_plant mp on( ud.unit_id=mp.id or (ud.unit_id=0 and mbc.id=mp.company_id) ) left join m_department md on (ud.department_id=md.id or ud.department_id=0) where ud.user_id='$userId' and mbc.active='1' and mp.active='1' and md.active='1' order by mbc.name,mp.name,md.name";
		//echo $qry;
		$result = array();
		$result = $this->databaseConnect->getRecords($qry);
		$j=0;
		foreach($result as $res)
		{
			$companyId=$res[0];
			$companyName=$res[1];
			$defaultCom=$res[2];
			if($defaultCom=="Y")
			{
				$defaultCompany=$companyId;
			}
			$unitId=$res[3];
			$unitName=$res[4];
			$departmentId=$res[5];
			$departmentName=$res[6];
			if($j==0)
			{
				$companyVal[$companyId]=$companyName;
				$unitVal[$companyId][$unitId]=$unitName;
				$departmentVal[$departmentId]=$departmentName;
			}
			else
			{
				if($companyNameOld!=$companyName)
				{
					$companyVal[$companyId]=$companyName;
				}
				if($unitNameOld!=$unitName)
				{
					$unitVal[$companyId][$unitId]=$unitName;
				}
				if($departmentNameOld!=$departmentName)
				{
					$departmentVal[$departmentId]=$departmentName;
				}
			}

			$companyNameOld=$companyName;
			$unitNameOld=$unitName;
			$departmentNameOld=$departmentName;
		}
		$defaultUserCompany=$this->getDefaultUserCompany($userId);
		($defaultUserCompany!="")?$defaultCompany=$defaultUserCompany:$defaultCompany=$defaultCompany;

		$resultArr=array($companyVal,$unitVal,$departmentVal,$defaultCompany);
		//printr($resultArr);
		return $resultArr;
	}
	
	###get default company for user
	function getDefaultUserCompany($userId)
	{
		$qry="select default_company from user where id='$userId' and default_company!='0' and default_company is not null";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result[0]:"";
		
	}



}
