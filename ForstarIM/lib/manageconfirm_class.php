<?php
class ManageConfirm
{

	/****************************************************************
	This class deals with all the operations relating to ManageConfirm
	*****************************************************************/
	var $databaseConnect;
	

	//Constructor, which will create a db instance for this class
	function ManageConfirm(&$databaseConnect)
    	{
       		 $this->databaseConnect =&$databaseConnect;
	}

	# Get Record
	function find()
	{
		$qry	= "select id, rm_confirm, ac_confirm, dpp_confirm, ddate_confirm, pkg_details_confirm, pkg_valid_pc_confirm, dpp_valid_pc, adv_amt_restriction, ls_mc_conversion_type,pro_confirm,weightment_data_confirm,receipt_gate_confirm from c_system where id is not null ";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Update 
	function updateConfirmRecord($confirmId, $rmConfirm, $acConfirm, $dppConfirm, $deliveryDateConfirm, $pkgDetailsConfirm, $validPCConfirm, $dppValidPrePCConfirm, $advAmtRestriction, $convertLSToMC,$proConfirm,$weightDataConfirm,$receiptGateConfirm)
	{
		$qry	= " update c_system set rm_confirm='$rmConfirm', ac_confirm='$acConfirm', dpp_confirm='$dppConfirm', ddate_confirm='$deliveryDateConfirm', pkg_details_confirm='$pkgDetailsConfirm', pkg_valid_pc_confirm ='$validPCConfirm', dpp_valid_pc='$dppValidPrePCConfirm', adv_amt_restriction='$advAmtRestriction', ls_mc_conversion_type='$convertLSToMC',pro_confirm='$proConfirm',weightment_data_confirm='$weightDataConfirm',receipt_gate_confirm='$receiptGateConfirm' where id=$confirmId";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	#Check RM Confirm Enabled or Not
	function isRMConfirmEnabled()
	{
		$result = $this->find();
		$rmConfirm = $result[1];
		return ($rmConfirm=='Y')?true:false;
	}
	
	#Check Ac Confirm Enabled or not
	function isACConfirmEnabled()
	{
		$result = $this->find();
		$acConfirm = $result[2];
		return ($acConfirm=='Y')?true:false;
	}

	# Check daily Pre-Process Entry Confirmed
	function isDPPConfirmEnabled()
	{
		$result = $this->find();
		$dppConfirm = $result[3];
		return ($dppConfirm=='Y')?true:false;
	}

	# Delivery Date Confirm enabled check 
	function deliveryDateConfirmEnabled()
	{
		$result = $this->find();
		$dppConfirm = $result[4];
		return ($dppConfirm=='Y')?true:false;
	}

	# Delivery Date Confirm enabled check 
	function pkgConfirmEnabled()
	{
		$result = $this->find();
		$pkgConfirm = $result[5];
		return ($pkgConfirm=='Y')?true:false;
	}

	function pkgValidPCEnabled()
	{
		$result = $this->find();
		$pkgValidPCConfirm = $result[6];
		return ($pkgValidPCConfirm=='Y')?true:false;
	}

	# Daily Pre-Process valid process code
	function dppValidPrePCEnabled()
	{
		$result = $this->find();
		$pkgValidPCConfirm = $result[7];
		return ($pkgValidPCConfirm=='Y')?true:false;
	}

	/**
	* Restriction for entering advance amt when overdue amt exist
	*/
	function advAmtRestrictionEnabled()
	{
		$result = $this->find();
		$confirmed = $result[8];
		return ($confirmed=='Y')?true:false;
	}

	/**
	* Get LS 2 MC conversion type
	* AC - Auto convert/ MC - Manually Convert
	*/
	function getLS2MCConversionType()
	{
		$result = $this->find();
		return $result[9];
	}
	
	# Procurment order Confirm enabled check 
	function procumentOdrConfirmEnabled()
	{
		$result = $this->find();
		$procurmentConfirm = $result[10];
		return ($procurmentConfirm=='Y')?true:false;
	}
	
	# weightment data sheet Confirm enabled check
	function weightmentDataConfirmEnabled()
	{
		$result = $this->find();
		$weightmentConfirm = $result[11];
		return ($weightmentConfirm=='Y')?true:false;
	}
	
	#receipt gate Confirm enabled check
	function receiptGateConfirmEnabled()
	{
		$result = $this->find();
		$receiptConfirm = $result[12];
		return ($receiptConfirm=='Y')?true:false;
	}

}
?>