<?php
class PondMaster
{
	/****************************************************************
	This class deals with all the operations relating to Pond Master
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PondMaster(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	#Add a Pond Master
	function addPondMaster($pondName, $supplier, $alloteeName,$address,$state,$district,$taluk,$village,$location,$pondSize,$pondSizeUnit,$pondQty,$returnDays,$userId)
	{
		// $qry	=	"insert into m_pond_master (pond_name, supplier, allotee_name, address, state,district,taluk,village,location,registration_type,registration_no,registration_date,registration_expiry_date,pond_size,pond_size_unit,pond_qty,created_on,created_by) values('".$pondName."','$supplier','".$alloteeName."','".$address."','$state','".$district."','".$taluk."','".$village."','".$location."','$registrationType','".$registrationNo."','".$registrationDate."','".$registrationExpiryDate."','".$pondSize."','$pondSizeUnit', '".$pondQty."',  Now(), '$userId')";
		$qry	=	"insert into m_pond_master (pond_name, supplier, allotee_name, address, state,district,taluk,village,location,pond_size,pond_size_unit,pond_qty,return_days,created_on,created_by) values('".$pondName."','$supplier','".$alloteeName."','".$address."','$state','".$district."','".$taluk."','".$village."','".$location."','".$pondSize."','$pondSizeUnit', '".$pondQty."','".$returnDays."',  Now(), '$userId')";
		// echo $qry;die;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Add a Pond Master Registration details registrationIds
	function addPondRegistration($pond_id,$registrationTypeArr,$registrationNoArr,$registrationDateArr,$registrationExpiryDateArr)
	{
		$insertStatus	=  '';
		// echo '<pre>';
		// print_r($registrationTypeArr);
		// print_r($registrationNoArr);
		// print_r($registrationDateArr);
		// print_r($registrationExpiryDateArr);
		// echo '</pre>';
		// die;
		if(sizeof($registrationTypeArr)>0)
		{
			for($i=0;$i<sizeof($registrationTypeArr);$i++)
			{
				$registrationType       = $registrationTypeArr[$i];
				$registrationNo         = $registrationNoArr[$i];
				$registrationDate       = mysqlDateFormat($registrationDateArr[$i]);
				$registrationExpiryDate = mysqlDateFormat($registrationExpiryDateArr[$i]);
				$qry	=	"insert into m_pond_registration (pond_id,registration_type,registration_no,registration_date,registration_expiry_date) values('".$pond_id."','".$registrationType."','".$registrationNo."','".$registrationDate."','".$registrationExpiryDate."')";
				$insertStatus	= $this->databaseConnect->insertRecord($qry);	
			}
		}
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Update Pond Master Registration details 
	function updatePondRegistration($registrationIds,$pond_id,$registrationTypeArr,$registrationNoArr,$registrationDateArr,$registrationExpiryDateArr)
	{
		$insertStatus	=  '';
		// echo '<pre>';
		// print_r($registrationIds);
		// print_r($registrationNoArr);
		// print_r($registrationDateArr);
		// print_r($registrationExpiryDateArr);
		// echo '</pre>';
		// die;
		if(sizeof($registrationTypeArr)>0)
		{
			$insertUpdateIds = '';
			for($i=0;$i<sizeof($registrationTypeArr);$i++)
			{
				$registrationType       = $registrationTypeArr[$i];
				$registrationNo         = $registrationNoArr[$i];
				$registrationDate       = mysqlDateFormat($registrationDateArr[$i]);
				$registrationExpiryDate = mysqlDateFormat($registrationExpiryDateArr[$i]);
				if(isset($registrationIds[$i]))
				{
					$qry	=	"update m_pond_registration set pond_id = '".$pond_id."',registration_type = '".$registrationType."',
								 registration_no = '".$registrationNo."',registration_date = '".$registrationDate."',
								 registration_expiry_date = '".$registrationExpiryDate."' WHERE id = '".$registrationIds[$i]."' ";
					$insertStatus	= $this->databaseConnect->updateRecord($qry);
					if($insertUpdateIds == '')
					{
						$insertUpdateIds = "'".$registrationIds[$i]."'";
					}
					else
					{
						$insertUpdateIds.= ",'".$registrationIds[$i]."'";
					}
				}
				else
				{
					$qry	=	"insert into m_pond_registration (pond_id,registration_type,registration_no,registration_date,registration_expiry_date) values('".$pond_id."','".$registrationType."','".$registrationNo."','".$registrationDate."','".$registrationExpiryDate."')";
					$insertStatus	= $this->databaseConnect->insertRecord($qry);
					$lastId 		= $this->databaseConnect->getLastInsertedId();			
					if($insertUpdateIds == '')
					{
						$insertUpdateIds = "'".$lastId."'";
					}
					else
					{
						$insertUpdateIds.= ",'".$lastId."'";
					}
				}
			}
			$qry	= " delete from m_pond_registration where id not in(".$insertUpdateIds.") and pond_id = '".$pond_id."' ";
			$this->databaseConnect->delRecord($qry);
		}
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}

	# Returns all Paging Records 
	function fetchAllPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, registration_type, display_code, description,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name limit $offset,$limit";
		 $qry	=	"select id, pond_name, supplier, allotee_name, address, state,district,taluk,village,location,
					 pond_size,pond_size_unit,pond_qty,active,
					(SELECT GROUP_CONCAT(b.registration_type,'$$',a.registration_no,'$$',a.registration_date,'$$',a.registration_expiry_date,'$$',a.id) 
					 FROM m_pond_registration a 
					 LEFT JOIN m_registration_type b ON a.registration_type=b.id 
					 WHERE a.pond_id = m_pond_master.id) as registrations 
					 FROM m_pond_master order by pond_name limit $offset,$limit";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}
	
	# Returns all  Pond Master
	function fetchAllRecords()
	{
		//$qry	= "select id, name, description, incharge,active,(select count(a1.id) FROM stock_return a1 where a1.department_id=a.id)as tot from m_department a order by name";
		 $qry	=	"select id, pond_name, supplier, allotee_name, address, state,district,taluk,village,location,registration_type,registration_no,registration_date,registration_expiry_date,pond_size,pond_size_unit,pond_qty  from m_pond_master order by pond_name";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	

	# Get Pond Master based on id 
	function find($pondMasterId)
	{
		$qry	= "select id, pond_name, supplier, allotee_name, address, state,district,taluk,village,location,
				   pond_size,pond_size_unit,pond_qty,
				   (SELECT GROUP_CONCAT(registration_type,'$$',registration_no,'$$',registration_date,'$$',registration_expiry_date,'$$',id) FROM m_pond_registration 
				   WHERE pond_id = $pondMasterId) as registrations,return_days
				   from m_pond_master where id=$pondMasterId";
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Pond Master
	function deletePondMaster($pondMasterId)
	{
		$qry	= " delete from m_pond_master where id=$pondMasterId";
		$result	= $this->databaseConnect->delRecord($qry);
		
		$qry	= " delete from m_pond_registration where pond_id in('".$pondMasterId."') ";
		$this->databaseConnect->delRecord($qry);
			
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update  a  Pond Master
	function updatePondMaster($pondMasterId,$pondName, $supplier, $alloteeName, $address,$state,$district,$taluk,$village,$location,$pondSize,$pondSizeUnit,$pondQty,$returnDays)
	{
		$qry	= " update m_pond_master set pond_name='$pondName', supplier='$supplier', allotee_name='$alloteeName', address='$alloteeName', state='$state',district='$district',taluk='$taluk',village='$village',location='$location',pond_size='$pondSize',pond_size_unit='$pondSizeUnit',pond_qty='$pondQty',return_days='$returnDays' where id=$pondMasterId";

		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	function updatepondMasterconfirm($pondMasterId){
		$qry	= "update m_pond_master set active='1' where id=$pondMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updatePondMasterReleaseconfirm($pondMasterId){
	$qry	= "update m_pond_master set active='0' where id=$pondMasterId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}
	
	function checkPondUsed($pond_id)
	{
		$qry = "SELECT count(*) as total FROM t_rmprocurmentsupplier WHERE pond_id = '".$pond_id."' ";
		$result = $this->databaseConnect->getRecord($qry);
		return $result[0];
	}	
	function CheckDuplicate($pondName)
	{
		$qry	= "select  id from m_pond_master where pond_name='$pondName'";
		return $this->databaseConnect->getRecord($qry);
	}
}

?>