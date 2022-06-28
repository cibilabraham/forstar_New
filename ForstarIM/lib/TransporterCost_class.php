<?php 
class TransporterCost
{
	/****************************************************************
	This class deals with all the operations relating to Transporter Coat
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function TransporterCost(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}
	
	function updateTransporterCost($transportCostId, $transportCostNSWE, $transportCostNE, $transportCostFRZ)
	{
		$qry = "update m_transporter_cost set transport_cost_NSWE='$transportCostNSWE', transport_cost_NE='$transportCostNE', transport_cost_FRZ='$transportCostFRZ' where id='$transportCostId'";
		
		$confirmResult	=	$this->databaseConnect->updateRecord($qry);
		if ($confirmResult) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $confirmResult;
	}
	
	function addTransporterCost($transportCostNSWE, $transportCostNE, $transportCostFRZ)
	{
		$qry = "insert into m_transporter_cost (transport_cost_NSWE,transport_cost_NE,transport_cost_FRZ) values('$transportCostNSWE','$transportCostNE','$transportCostFRZ')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
	
	function getTransportCostDetails()
	{
		$qry = "select id, transport_cost_NSWE, transport_cost_NE, transport_cost_FRZ from m_transporter_cost";
		$result = $this->databaseConnect->getRecord($qry);
		return (sizeof($result>0)?$result:"");
	}
}
?>