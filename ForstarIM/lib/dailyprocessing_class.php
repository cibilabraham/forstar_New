<?
	Class DailyProcessing
	{

		/****************************************************************
		This class deals with all the operations relating to Daily Rate 
		*****************************************************************/
		var $databaseConnect;


		//Constructor, which will create a db instance for this class
		function DailyProcessing(&$databaseConnect)
		{
			$this->databaseConnect =&$databaseConnect;
		}
		
		
	function addTempMaster()
		{

		$qry	=	"insert into t_dailyprocessing (date,flag) values(Now(),'0')";
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
		
		
		
		
#Add Daily Processing

function addDailyProcessing($unit,$dailyLotNo,$lastId){
			//$qry			=	" insert into t_dailyprocessing (lot_no,unit,date) values($dailyLotNo,$unit,Now())";
				$qry	=	"update t_dailyprocessing set lot_no = '$dailyLotNo', unit='$unit',date=Now(),flag=1 where id='$lastId'";
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
		
		# Filter table using Processing Id
		
		function find($dailyProcessingId)
		{
			$qry = "select distinct a.id,a.lot_no,a.unit,a.date  from t_dailyprocessing a left join t_dailyprocessing_grade b on a.id = b.lot_id where a.id=$dailyProcessingId";
			
			//$qry	=	"select a.id, a.lot_no, a.unit, a.date, b.id, b.name, c.id, c.lot_id, c.fish_id, c.packing_id, c.processcode_id, c.grade_id from t_dailyprocessing a, m_plant b, t_dailyprocessing_grade c where a.unit = b.id and a.id=$dailyProcessingId";
		//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecord($qry);
			return $result;
		}
		
		
		
		
		# Returns all Daily Processing Records
		 
		function fetchAllRecords()
		{
			$qry="select distinct a.id,a.lot_no,a.unit,a.date,a.flag  from t_dailyprocessing a left join t_dailyprocessing_grade b on a.id = b.lot_id"; 
			//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}
		
		

		# Filter Daily Processing using Date 
		function dailyProcessingRecFilter($recordsDate)
		{
			$qry	=	"select distinct a.id,a.lot_no,a.unit,a.date,a.flag  from t_dailyprocessing a where  a.date='$recordsDate'";
			//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}

# Delete the Id when press cancel	

function delLastInsertId($entryId){

		$qry	=	" delete from t_dailyprocessing where id=$entryId";

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



		# Delete Daily Processing Records
		
		function deleteDailyProcessing($dailyProcessingId)
		{
					
			$qry	=	"delete  from t_dailyprocessing where id='$dailyProcessingId'";
			//echo $qry;
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
		 
	#from Main page Delete Daily Processing GradeRecords
		
		function deleteDailyProcessingAllGrade($dailyProcessingId)
		{
					
			
			$qry	=	"delete  from t_dailyprocessing_grade where lot_id='$dailyProcessingId'";
			//echo $qry;
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
	
	
	
		# update Daily Pre Process  record 
		
	function updateDailyProcessing($dailyLotNo,$unit,$dailyProcessingId)
		{
		
			$qry	=	" update t_dailyprocessing set lot_no='$dailyLotNo',unit='$unit' where id=$dailyProcessingId ";
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
	
	
####### Daily Processing Grade Insertion is starting herre
	
	
	
	# Add Daily Processing Grade
		
	function addProcessingGrade($gradeId,$quantity,$fishId,$lotId,$packingId,$codeId)
		{
			
			
			$qry			=	" insert into t_dailyprocessing_grade (lot_id,fish_id,packing_id,processcode_id,grade_id,quantity,totalqty) values($lotId,$fishId,$packingId,$codeId,$gradeId,$quantity,$quantity)";
				
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


#Fetch all Processing Grade Records

function fetchAllProcessingGradeRecords($fish_Id,$lotNo,$packing_Id,$code_Id){

	
			$qry	=	"select  a.id, a.lot_id, a.fish_id, a.packing_id, a.processcode_id, a.grade_id, a.quantity, sum(a.totalqty), b.id, b.name, b.code,c.id,c.code from t_dailyprocessing_grade a, m_fish b, m_processcode c where a.fish_id=b.id and a.processcode_id = c.id and a.lot_id='$lotNo' group by a.processcode_id";
			
			//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;

}

# Filter table using Dailyprocessing Grade - Processcode Id
		
		function findCodeId($codeEditId,$lotEditId)
		{
			$qry	=	"select a.id, a.lot_id, a.fish_id, a.packing_id, a.processcode_id, a.grade_id, a.quantity, b.id, b.name, b.code,c.id,c.code, d.id,d.code from t_dailyprocessing_grade a, m_fish b, m_processcode c, m_grade d where a.fish_id=b.id and a.processcode_id = c.id and  a.grade_id=d.id and a.processcode_id=$codeEditId and a.lot_id=$lotEditId";
		//echo $qry;
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
		}

#Update Grade Quantity 

function updateProcessingGrade($gradeId,$quantity){
			
			$qry	=	" update t_dailyprocessing_grade set quantity='$quantity', totalqty='$quantity' where id=$gradeId";
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

#Delete Processing Grade Code

function deleteDailyProcessingGrade($processingCodeId,$lotNo){

			$qry	=	" delete from t_dailyprocessing_grade where processcode_id=$processingCodeId and lot_id=$lotNo";
			//echo $qry;
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



	
//---------- ends here ---------------------	
	
/*function fetchAllDateRecords(){
//Date wise listing
	#for selecting Date
	//$dailyRateDateRecords	=	$dailyprocessingObj->fetchAllDateRecords();
	
		/*$recordsDate					=	$p["selDate"];
	
	if($recordsDate!=0){	
	
	$dailyProcessingRecords		=	$dailyprocessingObj->dailyProcessingRecFilter($recordsDate);
	}
	else{
	$dailyProcessingRecords	=	$dailyprocessingObj->fetchAllRecords();
	}
	
		$qry	=	"select distinct date from t_dailyprocessing";
				//echo $qry;		
			$result	=	array();
			$result	=	$this->databaseConnect->getRecords($qry);
			return $result;
	}*/
}	
?>