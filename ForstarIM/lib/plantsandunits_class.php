<?php
class PlantMaster
{  
	/****************************************************************
	This class deals with all the operations relating to Plants Master 
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function PlantMaster(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	# Insert
	function addPlant($company,$no, $name,$stdProduction,$basedOn,$address,$plant_code)
	{
		$qry	=	"insert into m_plant (no,company_id,name,standard_production,based_on,address,unit_alphacode) values('".$no."','".$company."','".$name."','".$stdProduction."','".$basedOn."','".$address."','".$plant_code."')";
		//echo $qry;
		//die();
		$insertStatus	= $this->databaseConnect->insertRecord($qry);
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Returns all Plant 
	function fetchAllRecords()
	{
		//$qry	=	"select id, no, name,standard_production,based_on,active from m_plant order by name asc";
		
		$qry	=	"select mp.id, no, mp.name,b.name as company_name,standard_production,based_on,mp.active from m_plant as mp INNER JOIN m_billing_company b ON b.id = mp.company_id order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function fetchAllRecordsPlantsActive()
	{
		$qry	=	"select id, no, name,standard_production,based_on,active from m_plant where active='1'  order by name asc";
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	# Returns all Plant (PAGING)
	function fetchPagingRecords($offset, $limit)
	{
		//$qry	=	"select id, no, name,company_id,standard_production,based_on,active,((select COUNT(a.id) from m_preprocessor2plant a where a.plant_id = mp.id)+(select COUNT(a1.id) from t_dailycatch_main a1 where a1.unit=mp.id)) as tot from m_plant mp order by name asc limit $offset, $limit";
		//Rekha updated code 
		//company_id
		
		//old query 
		//$qry ="SELECT mp.id, no, mp.name,b.name as company_name, standard_production, based_on, mp.active,((select COUNT(a.id) from m_preprocessor2plant a where a.plant_id = mp.id)+(select COUNT(a1.id) from t_dailycatch_main a1 where a1.unit=mp.id))as tot FROM m_plant mp INNER JOIN m_billing_company b ON b.id = mp.company_id order by company_name,name asc limit $offset, $limit";
		//rekha modify dated on 20 june 2018
		 $qry ="SELECT mp.id, no, mp.name,b.name as company_name, standard_production, based_on, mp.active,mp.unit_alphacode,((select COUNT(a.id) from m_preprocessor2plant a where a.plant_id = mp.id)+(select COUNT(a1.id) from t_dailycatch_main a1 where a1.unit=mp.id))as tot FROM m_plant mp INNER JOIN m_billing_company b ON b.id = mp.company_id where b.active=1 order by company_name,name asc limit $offset, $limit";
	    //echo($qry);
		//exit;
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	#filter plant based on Id
	function filterAllPlantRecords($plantId)
	{
		$qry	=	"select id, no, name,standard_production,based_on from m_plant where id=$plantId";
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		//die();
		return $result;
	}

	# Get Plant based on id 
	function find($plantId)
	{
		//Rekha updated code here dated on 8 june 2018
		//"select mp.id, no, mp.name,b.name as company_name,standard_production,based_on,mp.active from m_plant as mp INNER JOIN m_billing_company b ON b.id = mp.company_id order by name asc"
// old query 
		$qry = "select id, no,company_id, name,standard_production,based_on,address,unit_alphacode from m_plant where id=$plantId";
		
		//$qry	=	"select mp.id, no,mp.company_id, mp.name,standard_production,based_on,mp.address,b.name as company_name from m_plant as mp INNER JOIN m_billing_company b ON b.id = mp.company_id where mp.id=$plantId";
		//echo($qry);
		//die();
		return $this->databaseConnect->getRecord($qry);
	}

	# Delete a Plant
	function deletePlant($plantId)
	{
		$qry	=	" delete from m_plant where id=$plantId";
		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# Update Plant
	function updatePlant($plantId,$company, $no, $name,$stdProduction,$basedOn,$address,$plant_code)
	{
		$qry	=	" update m_plant set company_id='$company',no='$no', name='$name',standard_production='$stdProduction',based_on='$basedOn',address='$address',unit_alphacode='$plant_code' where id=$plantId";
		//echo $qry;
		//die();
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# -----------------------------------------------------
	# Checking Plant Id is in use ( Pre Process Maste, Daily Catch Entry);
	# -----------------------------------------------------
	function plantNUnitRecInUse($plantId)
	{		
		$qry = " select id from (
				select a.id as id from m_preprocessor2plant a where a.plant_id='$plantId'
			union
				select a1.id as id from t_dailycatch_main a1 where a1.unit='$plantId'		
			) as X group by id ";
		//echo $qry."<br>";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;		
	}

	function updatePlantconfirm($plantId)
	{
		$qry	= "update m_plant set active='1' where id=$plantId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	function updatePlantReleaseconfirm($plantId)
	{
		$qry	= "update m_plant set active='0' where id=$plantId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	###get all unit assigned for user in manageuser
	function getCompanyUser($userId)
	{	$arrayVal=array();
		$qry = "select company_id from user_details where user_id='$userId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		if(sizeof($result>0))
		{
			foreach($result as $res)
			{
				if($res[0]=='0')
				{
					$query = "select id,display_name  from m_billing_company where active='1'";
					$rest	= $this->databaseConnect->getRecords($query);
					foreach($rest as $rt)
					{
						$id=$rt[0];
						$name=$rt[1];
						$arrayVal[$id]=$name;
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
						$arrayVal[$id]=$name;
					}
					
				}
				
			}
			
		}
		return $arrayVal;
		//return $result;
	}


	###check this units exist
	function checkDuplicate($name,$plantId)
	{
		$qry = "select id from m_plant where name='$name'";
		if($plantId) 
		{
			$qry.= " and id!=$plantId";
		}
		//echo $qry;		
		$result	= $this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?false:true;
	}
/* rekha added code */
	# Get plant alphacode  
	function getunit_alphacode($plantId)
	{
		$qry	=	"select unit_alphacode from m_plant where id=$plantId";
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";		
	}


/*end code */
	
	
}
?>