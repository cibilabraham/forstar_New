<?php
Class ManageProcurementGatePass
{
	function getAllPassList()
	{
		$qry	= "SELECT a.*,b.seal_number FROM procurement_gate_pas a 
				   LEFT JOIN m_seal_master b ON a.seal_no=b.id ";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllSealNos()
	{
		$qry	= "SELECT id,seal_number FROM m_seal_master WHERE change_status='Blocked'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getAllEmployee()
	{
		$qry	= "SELECT id,name FROM m_employee_master";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
}