<?
	Class CompetitorsCatch
	{

		/****************************************************************
		This class deals with all the operations relating to Competitors Catch 
		*****************************************************************/
		var $databaseConnect;


		//Constructor, which will create a db instance for this class
		function CompetitorsCatch(&$databaseConnect)
		{
			$this->databaseConnect =&$databaseConnect;
		}

#Insert a blank Record
		function addTempMaster()
		{

		$qry	=	"insert into t_competitorscatch (date,flag) values(Now(),'0')";
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		if ($insertStatus)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

#Delete last inserted Id		
function delLastInsertId($entryId){

		$qry	=	" delete from t_competitorscatch where id=$entryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
}
		
		
		# Add Competitors Catch
	function addCompetitorsCatch($landingCenterId,$lastId)
		{
			
			
			$qry	=	" update t_competitorscatch set landingcenter_id=$landingCenterId, flag=1 where id=$lastId";
			//echo $qry;
			$result	=	$this->databaseConnect->updateRecord($qry);
			if ($result)
			{
				$this->databaseConnect->commit();
			}
			else
			{
				 $this->databaseConnect->rollback();
			}
			return $result;	
		}


		# Returns all fishs 
		function fetchAllRecords()
		{
			$qry	=	"select distinct a.id, a.landingcenter_id,a.date,a.flag from t_competitorscatch a left join t_competitorscatch_list c on a.id=c.compcatch_id";
			
			//$qry	=	"select a.id, a.landingcenter_id,a.competitor_id,a.fish_id,a.quantity,a.date,b.id,b.code,b.name,c.id,c.name,c.code,d.id,d.code,d.name from t_competitorscatch a, m_landingcenter b,m_fish c, m_competitor d where a.landingcenter_id=b.id and a.fish_id = c.id and a.competitor_id = d.id";
			
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}

# Filter table by Date 
	function competitorsCatchRecFilter($enterDate)
		{
			
			$qry	=	"select distinct a.id, a.landingcenter_id,a.date,a.flag from t_competitorscatch a left join t_competitorscatch_list c on a.id=c.compcatch_id where a.date='$enterDate'";
		//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}


# Filter table using id
		function find($competitorsCatchId)
		{
			$qry	=	"select distinct a.id, a.landingcenter_id,a.date,a.flag from t_competitorscatch a left join t_competitorscatch_list c on a.id=c.compcatch_id where a.id=$competitorsCatchId";
		
			$result	=	array();
			$result	=	$this->databaseConnect->getRecord($qry);
			return $result;
		}


		# Delete Competitors Catch
		 
		function deleteCompetitorsCatch($compCatchId)
		{
			$qry	=	" delete from t_competitorscatch where id=$compCatchId";
			
			$result	=	$this->databaseConnect->delRecord($qry);
			if ($result)
			{
				$this->databaseConnect->commit();
			}
			else
			{
				 $this->databaseConnect->rollback();
			}
			return $result;

		}

		# update Competitors Catch  record 
function updateCompetitorsCatch($catchId,$landingCenterId)
		{
			$qry	=	" update t_competitorscatch set landingcenter_id=$landingCenterId where id=$catchId";
			//echo $qry;
			$result	=	$this->databaseConnect->updateRecord($qry);
			if ($result)
			{
				$this->databaseConnect->commit();
			}
			else
			{
				 $this->databaseConnect->rollback();
			}
			return $result;	
		}
	

//<------------------- Frame list -------------------------------------->

function addCompetitor($fishId,$quantity,$catchId,$competitorId){

			$qry			=	" insert into t_competitorscatch_list(compcatch_id,competitor_id, fish_id,quantity) values($catchId,$competitorId, $fishId,$quantity)";
				
			//echo $qry;
			$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
			if ($insertStatus)
			{
				$this->databaseConnect->commit();
			}
			else
			{
				 $this->databaseConnect->rollback();
			}
			return $insertStatus;

}


#Delete last Competitors List
		
function delCompLastInsertId($entryId){

		$qry	=	" delete from t_competitorscatch_list where compcatch_id=$entryId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
}

function fetchAllCompetitorsCatchListRecords($lastId,$competitorId){

	$qry	=	"select  a.id, a.compcatch_id, a.fish_id, a.competitor_id,a.quantity,sum(a.quantity), b.id, b.name, b.code,c.id,c.code,c.name from t_competitorscatch_list a, m_fish b, m_competitor c where a.fish_id=b.id and a.competitor_id = c.id and a.compcatch_id='$lastId' group by a.competitor_id";
			
			//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}


# Filter t_competitorscatchlist table using copetitor Id
		
		function findCompetitorId($competitorEditId,$catchId)
		{
			$qry	=	"select  a.id, a.compcatch_id, a.fish_id, a.competitor_id,a.quantity, b.id, b.name, b.code,c.id,c.code,c.name from t_competitorscatch_list a, m_fish b, m_competitor c where a.fish_id=b.id and a.competitor_id = c.id and a.competitor_id=$competitorEditId and a.compcatch_id=$catchId";
		//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}

# Delete added competitor from the list

function deleteCompetitorFromList($competitorId){

		$qry	=	" delete from t_competitorscatch_list where competitor_id=$competitorId";

		$result	=	$this->databaseConnect->delRecord($qry);
		if ($result)
		{
			$this->databaseConnect->commit();
		}
		else
		{
			 $this->databaseConnect->rollback();
		}
		return $result;
}

function updateCompetitorList($compListId,$quantity){

			$qry	=	" update t_competitorscatch_list set quantity='$quantity' where id=$compListId";
			//echo $qry;
			$result	=	$this->databaseConnect->updateRecord($qry);
			if ($result)
			{
				$this->databaseConnect->commit();
			}
			else
			{
				 $this->databaseConnect->rollback();
			}
			return $result;	
}

}	
?>