<?php
class ProductionMarketingCost
{  
	/****************************************************************
	This class deals with all the operations relating to Production Matrix Master
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function ProductionMarketingCost(&$databaseConnect)
    {	
        	$this->databaseConnect =&$databaseConnect;
	}


	function fetchAllFixedProduction()
	{
		//$qry = "select a.id,a.name from m_rte_department a  left join m_staff_master b on  a.id=b.department  where a.active='1' and a.type='marketing' and b.type='fixed' and b.active='1' group by a.id";
		//echo $qry;
		$qry = "select a.id, a.new_total_cost, a.total_cost, b.name from m_production_fixed_marketing a left join m_rte_department b on a.department_id=b.id";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}

	function getFixedTot($productnId)
	{
		$qry = "select sum(actual_cost) as cost from m_staff_master where department='$productnId' and type='fixed' and end_date='0000-00-00'  and active='1'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		//printr($result);
		return $result[0];
	}

	function getFixedNew($fixedPrdnId)
	{
		$qry = "select new_total_cost as newtotalcost from m_production_fixed_marketing where department_id='$fixedPrdnId'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		//printr($result);
		return $result[0];
	}


	function fetchAllVariableProduction()
	{
		//$qry = "select a.id,a.name from m_rte_department a  left join m_staff_master b on  a.id=b.department  where a.active='1' and a.type='marketing' and b.type='variable' and b.active='1'  group by a.id";
		//echo $qry;
		$qry = "select a.id, a.new_total_cost, a.total_cost, b.name from m_production_variable_marketing a left join m_rte_department b on a.department_id=b.id";
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result:"";
	}

	function getVariableTot($productnId)
	{
		$qry = "select sum(actual_cost) as cost from m_staff_master where department='$productnId' and type='variable'  and end_date='0000-00-00' and active='1'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		//printr($result);
		return $result[0];
	}

	function getVariableNew($varPrdnId)
	{
		$qry = "select new_total_cost as newtotalcost from m_production_variable_marketing where department_id='$varPrdnId'";
		//echo $qry;
		$result=$this->databaseConnect->getRecord($qry);
		//printr($result);
		return $result[0];
	}


	function addProductionFixedMarketing($fixedProductionId,$newFixedCost,$fixedCost,$userId)
	{
		$qry="insert into m_production_fixed_marketing (department_id,new_total_cost,total_cost,createdon,createdby) values('$fixedProductionId','$newFixedCost','$fixedCost',Now(),'$userId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	function addProductionVariableMarketing($varProductionId,$newVarCost,$varCost,$userId)
	{
		$qry="insert into m_production_variable_marketing (department_id,new_total_cost,total_cost,createdon,createdby) values('$varProductionId','$newVarCost','$varCost',Now(),'$userId')";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Delete Production Fixed Power Rec
	function deleteProductionFixedMarketingRec()
	{
		$qry = " delete from m_production_fixed_marketing";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}

	# Delete Production Variable Power Rec
	function deleteProductionVariableMarketing()
	{
		$qry = " delete from m_production_variable_marketing";
		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	
	#Checking for Fixed Marketing Cost for a particular Department
	function checkFixedMktngEntry($department)
	{
		$qry = "select id, new_total_cost, total_cost from m_production_fixed_marketing where department_id='$department'";
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update Actual Cost and New Total Cost in Fixed Production Marketing Cost
	function updateFixedMktngCost($fixedId,$updateNewTotalCost,$updateTotalCost)
	{
		$qry = "update m_production_fixed_marketing set new_total_cost='$updateNewTotalCost', total_cost='$updateTotalCost' where id='$fixedId'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	#Checking for Variable Marketing Cost for a particular Department
	function checkVariableMktngEntry($department)
	{
		$qry = "select id, new_total_cost, total_cost from m_production_variable_marketing where department_id='$department'";
		$result=$this->databaseConnect->getRecord($qry);
		return (sizeof($result)>0)?$result:"";
	}
	
	#Update Actual Cost and New Total Cost in Variable Production Marketing Cost
	function updateVariableMktngCost($variableId,$updateNewTotalCost,$updateTotalCost)
	{
		$qry = "update m_production_variable_marketing set new_total_cost='$updateNewTotalCost', total_cost='$updateTotalCost' where id='$fixedId'";
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
}
?>