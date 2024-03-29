<?php
Class ManageRMLOTID
{

	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function ManageRMLOTID(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect;
	}
	function getLotIdDetails($date)
	{
		$qry	= "select id,alpha_character,rm_lotid from t_manage_rm_lotid where created_on ='".$date."'";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1].$v[2];
		}
		return $resultArr;
	}
	
	
	
	function getLotIdTotalvalueHistory($rmLotID)
	{
		global $found;
		
		$qry = "select a.id,a.alpha_character,a.rm_lotid,a.supplier_id,b.name,c.process_type,d.name,a.active,a.company_id,a.unit_id,a.lot_id_origin,b.name,mbc.name from t_manage_rm_lotid a 
				left join m_plant b on a.unit_id = b.id 
				left join m_lotid_process_type c on a.processing_stage = c.id 
				left join m_plant d on a.unit_id = d.id 
                left join m_billing_company mbc on a.company_id=mbc.id
					where a.id='$rmLotID'";
		$result = $this->databaseConnect->getRecords($qry);
		
		if(sizeof($result)>0){
			$found[]=$result;
			$originId = $result[0][10];
			if($originId!='0')
			{ 
				$this->getLotIdTotalvalue($originId);
			}
			else
			{
				$ret=$found;
				$found='';
				return $ret;
			}
		}
		//printr($found);
		//return $found;
		
	}
	
	
	function getLotIdTotalvalue($rmLotID)
	{
		global $found;
		
		$qry = "select a.id,a.alpha_character,a.rm_lotid,a.supplier_id,b.name,c.process_type,d.name,a.active,a.company_id,a.unit_id,a.lot_id_origin,b.name,mbc.name from t_manage_rm_lotid a 
				left join m_plant b on a.unit_id = b.id 
				left join m_lotid_process_type c on a.processing_stage = c.id 
				left join m_plant d on a.unit_id = d.id 
                left join m_billing_company mbc on a.company_id=mbc.id
					where a.id='$rmLotID'";
		$result = $this->databaseConnect->getRecords($qry);
		
		if(sizeof($result)>0){
			$found[]=$result;
			$originId = $result[0][10];
			if($originId!='0')
			{ 
				$this->getLotIdTotalvalue($originId);
			}
			
		}
		//printr($found);
		return $found;
		
	}

	function getLotIdTotalval($date,$rmLotOD)
	{
	$qry = "select a.id,a.alpha_character,a.rm_lotid,a.supplier_id,b.name,c.process_type,d.name,a.active,a.company_id,a.unit_id,a.lot_id_origin from t_manage_rm_lotid a 
				left join m_plant b on a.unit_id = b.id 
				left join m_lotid_process_type c on a.processing_stage = c.id 
				left join m_plant d on a.unit_id = d.id 
				where a.created_on ='".$date."'  ";
			if($rmLotOD != '')
		{
			$qry.= " and a.id=".$rmLotOD;
			$qry2="select id from t_manage_rm_lotid where lot_id_origin='$rmLotOD'";
			 $res=$this->databaseConnect->getRecords($qry2);
		}
		 $result=$this->databaseConnect->getRecords($qry);
		 
		 if($res[0]>0)
		 {
		 $arrayRmLotid=array();
		 array_push($arrayRmLotid,$result);
			getLotIdTotalRmlot($res[0],$arrayRmLotid);
		 }
		
		 return $result;
			
	}
	function getLotIdTotalRmlot($lotid,&$arrayRmLotid)
	{
		$qry = "select a.id,a.alpha_character,a.rm_lotid,a.supplier_id,b.name,c.process_type,d.name,a.active,
		a.company_id,a.unit_id,a.lot_id_origin from t_manage_rm_lotid a left join m_plant b on a.unit_id = b.id 
		left join m_lotid_process_type c on a.processing_stage = c.id left join m_plant d on a.unit_id = d.id 
		where  a.id=".$lotid;
		$reslt=$this->databaseConnect->getRecords($qry);
		array_push($arrayRmLotid,$reslt);
		// return $arrayRmLotid;
	}
	
	function unitTransfered($rmLotId)
	{
	 	$qry	= "select id from  t_manage_rm_lotid where lot_id_origin='$rmLotId'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}

	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit,$company,$unit,$processingstage)
	{	
		$qry	= "select a.id,a.receipt_id,a.supplier_id,a.company_id,a.unit_id,a.rm_lotid,a.alpha_character,a.processing_stage,c.name,d.name,a.lot_id_origin,a.active from t_manage_rm_lotid a 
		left join m_billing_company c on a.company_id=c.id left join m_plant d on a.unit_id=d.id";
		$qry.=" where a.created_on>='$fromDate' and a.created_on<='$tillDate' and a.id not in  (select lot_id_origin from t_manage_rm_lotid) and a.status = '0' ";
		($company!="")? $qry.=" and a.company_id='$company'" :"";
		($unit!="")? $qry.=" and a.unit_id='$unit'": "";

		$qry.="order by  a.id desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultval=array();
		if($processingstage!="")
		{$i=0;
			foreach ($result as $rt)
			{
				$rest= $this->getRMProgressStage($rt[0]);
				//printr($rest);
				if($rest==$processingstage)
				{
					$resultval[$i][0]=$rt[0];
					$resultval[$i][1]=$rt[1];
					$resultval[$i][2]=$rt[2];
					$resultval[$i][3]=$rt[3];
					$resultval[$i][4]=$rt[4];
					$resultval[$i][5]=$rt[5];
					$resultval[$i][6]=$rt[6];
					$resultval[$i][7]=$rt[7];
					$resultval[$i][8]=$rt[8];
					$resultval[$i][9]=$rt[9];
					$resultval[$i][10]=$rt[10];
					$resultval[$i][11]=$rt[11];
				//printr($resultval);
				$i++;
				}
			}
			//printr($resultval);
			return $resultval;
		}
		else
		{
			//printr($result);	
			return $result;
		}

		//return $result;
	}
	function fetchAllPagingRecords_old($fromDate, $tillDate, $offset, $limit)
	{	
		$qry	= "select a.id,a.receipt_id,a.supplier_id,a.company_id,a.unit_id,a.rm_lotid,a.alpha_character,a.processing_stage,c.name,d.name,a.lot_id_origin,a.active from t_manage_rm_lotid a 
		left join m_billing_company c on a.company_id=c.id left join m_plant d on a.unit_id=d.id
		where a.created_on>='$fromDate' and a.created_on<='$tillDate' and a.id not in  (select lot_id_origin from t_manage_rm_lotid) and a.status = '0' order by created_on desc limit $offset, $limit";
		// $qry	= "select a.id,a.receipt_id,a.supplier_id,a.company_id,a.unit_id,a.rm_lotid,a.alpha_character,b.supplier_group_name,c.vehicle_number,d.name_of_person,$qryGenCount from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";
		
		//$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function fetchAllDateRangeRecords($fromDate, $tillDate,$company,$unit,$processingstage)
	{
		$qry	= "select a.id,a.receipt_id,a.supplier_id,a.company_id,a.unit_id,a.rm_lotid,a.alpha_character,a.processing_stage,c.name,d.name,a.lot_id_origin,a.active from t_manage_rm_lotid a 
		left join m_billing_company c on a.company_id=c.id left join m_plant d on a.unit_id=d.id";
		$qry.=" where a.created_on>='$fromDate' and a.created_on<='$tillDate' and a.id not in  (select lot_id_origin from t_manage_rm_lotid) and a.status = '0' ";
		($company!="")? $qry.=" and a.company_id='$company'" :"";
		($unit!="")? $qry.=" and a.unit_id='$unit'": "";

		$qry.="order by  a.id desc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		$resultval=array();
		if($processingstage!="")
		{$i=0;
			foreach ($result as $rt)
			{
				$rest= $this->getRMProgressStage($rt[0]);
				//printr($rest);
				if($rest==$processingstage)
				{
					$resultval[$i][0]=$rt[0];
					$resultval[$i][1]=$rt[1];
					$resultval[$i][2]=$rt[2];
					$resultval[$i][3]=$rt[3];
					$resultval[$i][4]=$rt[4];
					$resultval[$i][5]=$rt[5];
					$resultval[$i][6]=$rt[6];
					$resultval[$i][7]=$rt[7];
					$resultval[$i][8]=$rt[8];
					$resultval[$i][9]=$rt[9];
					$resultval[$i][10]=$rt[10];
					$resultval[$i][11]=$rt[11];

				//printr($resultval);
				$i++;
				}
				
			}
			//printr($resultval);
			return $resultval;
			
		}
		else
		{
			//printr($result);	
			return $result;
		}

	}
	/*function getLotIdDetails($date)
	{
		$qry	= "select id,new_lot_Id from t_unittransfer where created_on ='".$date."' ";
		$result	= $this->databaseConnect->getRecords($qry);
		$resultArr = array(''=>'-- Select --');
		
		while (list(,$v) = each($result)) {
			$resultArr[$v[0]] = $v[1];
		}
		return $resultArr;
	}
	function getLotIdTotalval($date,$rmLotOD)
	{
	$qry = "select a.id,a.new_lot_Id,a.supplier_Details,b.name,c.process_type,d.name,e.process_type,a.active from t_unittransfer a 
				left join m_plant b on a.current_Unit = b.id 
				left join m_lotid_process_type c on a.current_Stage = c.id 
				left join m_plant d on a.unit_Name = d.id 
				left join m_lotid_process_type e on a.process_Type = e.id 
				where a.created_on ='".$date."'  ";
			if($rmLotOD != '')
		{
			$qry.= " and a.id=".$rmLotOD;
		}
		return $this->databaseConnect->getRecord($qry);
			
	}*/
	function getLotIdTotalDetailsvalue($supplier_Details)
	{
		//$result = '<tr bgcolor="WHITE"><td colspan="8" align="center"> No records found</td></tr>';
		
		$qry = "select a.id,a.new_lot_Id,a.supplier_Details,b.name,c.process_type,d.name,e.process_type,a.active,f.procurment_Gate_PassId,f.supplier_Challan_Date from t_unittransfer a 
				left join m_plant b on a.current_Unit = b.id 
				left join m_lotid_process_type c on a.current_Stage = c.id 
				left join m_plant d on a.unit_Name = d.id 
				left join m_lotid_process_type e on a.process_Type = e.id 
				left join t_rmreceiptgatepass f on a.supplier_Details =f.supplier_Challan_No
				where a.supplier_Details ='".$supplier_Details."' ";
		// if($rmLotOD != '')
		// {
			// $qry.= " and a.id=".$rmLotOD;
		// }
		$resultArr	= $this->databaseConnect->getRecords($qry);
		
		
		/*else
		{
		$result.= '<table width="100%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
				<thead></thead><tbody >
				<tr bgcolor="#f2f2f2">
				<td colspan="8" align="center"> No records found</td></tr></tbody>
				</table>';
		
		}*/
		
		return $resultArr;
	}
	function find($editId)
	{
		
		global $found;
		$qry = "select a.company_id,a.unit_id,a.alpha_character,a.rm_lotid,b.id,b.receipt_id,b.receipt_gatepass_id,b.supplier_id,b.farm_id,c.name,d.pond_name,e.name,f.name,b.challan_date,b.challan_no,a.lot_id_origin from t_manage_rm_lotid a left join t_manage_rmlotid_details b on a.id=b.rmlot_main_id left join supplier c on c.id=b.supplier_id left join m_pond_master d on d.id=b.farm_id 
		left join m_billing_company e on e.id=a.company_id
		left join m_plant f on f.id=a.unit_id
		where a.id='$editId'";
		/*$qry = "select a.id,a.alpha_character,a.rm_lotid,a.supplier_id,b.name,c.process_type,d.name,a.active,a.company_id,a.unit_id,a.lot_id_origin,b.name,mbc.name from t_manage_rm_lotid a 
				left join m_plant b on a.unit_id = b.id 
				left join m_lotid_process_type c on a.processing_stage = c.id 
				left join m_plant d on a.unit_id = d.id 
                left join m_billing_company mbc on a.company_id=mbc.id
					where a.id='$rmLotID'";*/
		$result = $this->databaseConnect->getRecords($qry);
		
		if(sizeof($result)>0){
			//$found[]=$result;
			$originId = $result[0][15];
			if($originId!='0')
			{ 
				$this->find($originId);
			}
			else
			{
				$found=$result;
			}
		}
		//printr($found);
		return $found;
		/*$qry = "select a.company_id,a.unit_id,a.alpha_character,a.rm_lotid,b.id,b.receipt_id,b.receipt_gatepass_id,b.supplier_id,b.farm_id,c.name,d.pond_name,e.name,f.name,b.challan_date,b.challan_no from t_manage_rm_lotid a left join t_manage_rmlotid_details b on a.id=b.rmlot_main_id left join supplier c on c.id=b.supplier_id left join m_pond_master d on d.id=b.farm_id 
		left join m_billing_company e on e.id=a.company_id
		left join m_plant f on f.id=a.unit_id
		where a.id='$editId'";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;*/
	}

	function getRMLotIdSupplierDetailsMain($receiptGatePassId,$supplierId)
	{
	$qry = "SELECT id,supplier_id,driver,supplier_Challan_No,supplier_Challan_Date,Company_Name,unit,vehicle_Number
		      from t_rmreceiptgatepass 
			  where id='$receiptGatePassId' and  supplier_id='$supplierId'
			  ";
	$result	= $this->databaseConnect->getRecords($qry);
	return $result;
	}
	function getRMLotIdSupplierDetailsSub($receiptGatePassId,$supplierId,$farm_id)
	{
	//echo $receiptGatePassId;
	 $qry = "SELECT a.id,a.supplier_id,a.pond_id,a.challan_no,a.challan_date,a.company_id,a.unit_id,b.name,c.name from t_rm_receipt_gatepass_supplier a 
	 left join m_billing_company b on b.id=a.company_id
	 left join m_plant c on c.id=a.unit_id
	 where a.receipt_gatepass_id = '$receiptGatePassId' and  a.supplier_id in ($supplierId) and a.pond_id in ($farm_id)";
	$result	= $this->databaseConnect->getRecords($qry);
	return $result;
	}
	
	function getAllUnit()
	{
		$qry = "SELECT id,name FROM m_plant where active='1' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

	function getLotIdTotalDetails($supplier_Details)
	{
		//$result = '<tr bgcolor="WHITE"><td colspan="8" align="center"> No records found</td></tr>';
		
		$qry = "select a.id,a.new_lot_Id,a.supplier_Details,b.name,c.process_type,d.name,e.process_type,a.active,f.procurment_Gate_PassId,f.supplier_Challan_Date from t_unittransfer a 
				left join m_plant b on a.current_Unit = b.id 
				left join m_lotid_process_type c on a.current_Stage = c.id 
				left join m_plant d on a.unit_Name = d.id 
				left join m_lotid_process_type e on a.process_Type = e.id 
				left join t_rmreceiptgatepass f on a.supplier_Details =f.supplier_Challan_No
				where a.supplier_Details ='".$supplier_Details."' ";
		// if($rmLotOD != '')
		// {
			// $qry.= " and a.id=".$rmLotOD;
		// }
		$resultArr	= $this->databaseConnect->getRecords($qry);
		
		if(sizeof($resultArr) > 0)
		{
			$result.= '<table width="100%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
																		<thead><tr bgcolor="#f2f2f2">																				
			<td style="padding-left:10px; padding-right:10px;" class="listing-head">RM Lot ID</td>
			<td style="padding-left:10px; padding-right:10px;" class="listing-head">Weightment Challan Number</td>
			<td style="padding-left:10px; padding-right:10px;" class="listing-head">Current Unit</td>
			<td style="padding-left:10px; padding-right:10px;" class="listing-head">Current Processing Stage</td>
			<td style="padding-left:10px; padding-right:10px;" class="listing-head">Status</td>
			<td class="listing-head"> Action </td>
			</tr></thead>
			<tbody >';
			
			$i = 0;
			$style = 'style="padding-left:10px; padding-right:10px;" ';
			foreach($resultArr as $res)
			{
				$status = '';$edit = '<a href="javascript:void(0);" onclick="xajax_chageStatusRmLotID('.$res[0].');"> Confirm </a>';
				if($res[7] == 1) { $status = 'Confirm'; $edit = '&nbsp;'; }
				
				if($i == 0)
				{
					
				 

					//$result = '<tr bgcolor="WHITE" >';
					$result.= '<tr bgcolor="WHITE"';
					if ($res[7] == 0) {
						$result.= 'bgcolor="WHITE"  onMouseOver="ShowTip('.$disMsgInactive.');" onMouseOut="UnTip();"';
					}
					$result.= '>';
					$result.= '<td '.$style.'>'.$res[1].'</td>';
					$result.= '<td '.$style.'>'.$res[2].'</td>';
					/*$result.= '<td '.$style.'>'.$res[3].'</td>';
					$result.= '<td '.$style.'>'.$res[4].'</td>';*/
					$result.= '<td '.$style.'>'.$res[5].'</td>';
					$result.= '<td '.$style.'>'.$res[6].'</td>';
					$result.= '<td '.$style.'>'.$status.'</td>';
					$result.= '<td '.$style.'>'.$edit.'</td>';
					$result.= '</tr>';
				}
				else
				{
					//$result.= '<tr bgcolor="WHITE">';
					$result.= '<tr bgcolor="WHITE"';
					if ($res[7] == 0) {
						$result.= 'bgcolor="WHITE"  onMouseOver="ShowTip('.$disMsgInactive.');" onMouseOut="UnTip();"';
					}
					$result.= '>';
					$result.= '<td '.$style.'>'.$res[1].'</td>';
					$result.= '<td '.$style.'>'.$res[2].'</td>';
					/*$result.= '<td '.$style.'>'.$res[3].'</td>';
					$result.= '<td '.$style.'>'.$res[4].'</td>';*/
					$result.= '<td '.$style.'>'.$res[5].'</td>';
					$result.= '<td '.$style.'>'.$res[6].'</td>';
					$result.= '<td '.$style.'>'.$status.'</td>';
					$result.= '<td '.$style.'>'.$edit.'</td>';
					$result.= '</tr>';
				}
				$i++;
			}
			$result.= '</tbody>
				</table>';
			
		}
		/*else
		{
		$result.= '<table width="100%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
				<thead></thead><tbody >
				<tr bgcolor="#f2f2f2">
				<td colspan="8" align="center"> No records found</td></tr></tbody>
				</table>';
		
		}*/
		
		return $result;
	}
	function changeStatus($id)
	{
		$qry = "UPDATE t_manage_rm_lotid SET active=1 WHERE id=".$id;		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
	
	function getProcurementId($receiptgatePassId)
	{
	 	$qry="Select procurment_Gate_PassId from t_rmreceiptgatepass where id='$receiptgatePassId'";
		return $this->databaseConnect->getRecord($qry);
	}
	function getReceiptSupplierDetails($receipt_id)
	{
	  $qry = "SELECT id,supplier_id,pond_id,challan_no,challan_date,company_id,unit_id from t_rm_receipt_gatepass_supplier where receipt_gatepass_id = '".$receipt_id."' ";
	// $qry = "SELECT a.*,b.vehicle_number,c.name_of_person as driver_name FROM t_rmreceiptgatepass a 
				// LEFT JOIN m_vehicle_master b on b.id = a.vehicle_number 
				// LEFT JOIN m_driver_master c ON c.id = a.driver   
				// WHERE a.id = '".$editId."' ";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getReceiptGatePassSupplier($receiptgatePassId)
	{
		$qry = "SELECT id,supplier_id,driver,supplier_Challan_No,supplier_Challan_Date,Company_Name,unit,vehicle_Number
		      from t_rmreceiptgatepass where id='$receiptgatePassId' ";
		/*$qry	= "select a.*,b.supplier_group_name,c.vehicle_number,d.name_of_person from t_rmprocurmentorder a left join m_supplier_group b on a.suppler_group_name=b.id left join m_vehicle_master c on a.vehicle_number=c.id left join m_driver_master d on a.driver_name=d.id where date_of_entry>='$fromDate' and date_of_entry<='$tillDate' order by date_of_entry desc limit $offset, $limit";*/
		//$qry	= "select a.*,b.gate_pass_id from t_rmprocurmentorder a left join procurement_gate_pass b on b.gate_pass_id=a.gatePass where a.date_of_entry>='$fromDate' and a.date_of_entry<='$tillDate' order by a.date_of_entry desc limit $offset, $limit";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function chkValidGatePassId($selDate)
	{
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	="select id,start_no, end_no from number_gen where  date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 AND TYPE = 'LF'";

		//$qry	= "select start_no, end_no from number_gen where billing_company_id='$billingCompany' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0))";
		//$qry	="select id,number_from, number_to from manage_procrment_gate_pass where  date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate' or (date_to is null || date_to=0))";

		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return $rec;
		//return (sizeof($rec)>0)?true:false;
	}
	function getAlphaCode($selDate)
	{
		$qry = "select alpha_code from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and auto_generate=1 AND TYPE = 'LF'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		//return (sizeof($rec)>0)?$rec[0]:0;
		return $rec;
	}
	function checkGatePassDisplayExist()
	{
	
		//$qry = "select (count(*)) from t_rmreceiptgatepass where  process_type='$processType'";
		$qry = "select (count(*)) from t_manage_rm_lotid";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		//return (sizeof($rec)>0)?1:0;
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function getmaxGatePassId()
	{
		$qry = "select 	rm_lotid from  t_manage_rm_lotid order by id desc limit 1";
		//$qry = "select gate_pass_id from m_rm_gate_pass order by id desc limit 1";
		//$qry = "select lot_Id from t_rmreceiptgatepass where  process_type='$processType' order by id desc limit 1";
		//$qry = "select gatePass from t_rmprocurmentorder order by id desc limit 1";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}
	
	function getValidendnoGatePassId($selDate)
	{
		
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		$qry	= "select end_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='LF'";
		
		//$qry	= "select number_to from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	
	function getValidGatePassId($selDate)
	{
		//$billingCompany=0;
		//$selDate=Date('Y-m-d');
		//$selDate=mysqlDateFormat($selDate);
		 $qry	= "select start_no from number_gen where date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate') and type='LF'";
		
		//$qry	= "select number_from from manage_procrment_gate_pass where date_format(date_from,'%Y-%m-%d')<='$selDate' and (date_format(date_to,'%Y-%m-%d')>='$selDate')";
		//echo $selDate;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:0;
	}
	function receiptGatePassDetail($receiptIds)
	{
	 $qry	= "select b.name,c.name,d.name,a.supplier_id,a.id,c.id,d.id,a.pond_id,a.challan_date,a.challan_no from t_rm_receipt_gatepass_supplier a left join supplier b on a.supplier_id=b.id left join m_billing_company c on a.company_id=c.id left join m_plant d on a.unit_id=d.id where a.id in ($receiptIds)";
		return  $this->databaseConnect->getRecords($qry);
	}
	function receiptGatePassDetailSingle($receiptIds)
	{
		 $qry	= "select b.name,c.name,d.name,a.supplier_id,a.id,c.id,d.id,a.supplier_Challan_Date,a.supplier_Challan_No from t_rmreceiptgatepass a left join supplier b on a.supplier_id=b.id left join m_billing_company c on 	a.Company_Name=c.id left join m_plant d on a.unit=d.id where a.id in ($receiptIds)";
		return  $this->databaseConnect->getRecords($qry);
		
	}
	function getAvailableLotIdNos()
	{
	 	$qry = "SELECT id,start_no,end_no,alpha_code,current_no FROM number_gen WHERE type='LF' AND end_date >= '".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1 ";
		$result = $this->databaseConnect->fetch_array($qry);
		
		
		$seal_nos_array = array();
		if(sizeof($result) > 0)
		{
			$sql = "SELECT rm_lotid FROM t_manage_rm_lotid WHERE number_gen_id = '".$result[0]['id']."' 
					UNION 
					SELECT rmlotid FROM t_rmlotid_temporary WHERE number_gen_id = '".$result[0]['id']."'  
					";
			$existsSealNos = $this->databaseConnect->fetch_array($sql);
				// echo '<pre>';
		// print_r($existsSealNos);
		// echo '</pre>';
			$existsSealNos = array_map('current', $existsSealNos);
			$start_no = (int) $result[0]['start_no'];
			$end_no   = (int) $result[0]['end_no'];
			$k = 0;
			for($i=$start_no;$i<=$end_no;$i++)
			{
				if($k == 50)
				{
					break;
				}
				if(!in_array($i,$existsSealNos))
				{
					$seal_nos_array[] = $i;
					$k++;
				}
			}
		}
		// echo '<pre>';
		// print_r($seal_nos_array);
		// echo '</pre>';
		return $seal_nos_array;
	}
	function addLotIdTemporary($rmlotId,$number_gen)
	{
	$qry	= "insert into t_rmlotid_temporary(rmlotid,number_gen_id) values
				  ('$rmlotId','$number_gen')";	
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
		} else {
			 $this->databaseConnect->rollback();
		}
		return $insertStatus;
	}
	function addManageLotId($rmId,$alphaValue,$company_idval,$unit_idval,$number_genval,$userId)
	{
		$qry	= "insert into t_manage_rm_lotid(company_id,unit_id,rm_lotid,alpha_character,number_gen_id,created_on,created_by) values('$company_idval','$unit_idval','$rmId','$alphaValue','$number_genval',Now(),'$userId')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function addManageLotIdDetail($lastId,$receipt_idval,$receiptGatePass,$supplier_id,$farmIdVal,$supplierchellandt,$supplierchellan)
	{
		$qry	= "insert into  t_manage_rmlotid_details(rmlot_main_id,receipt_id,receipt_gatepass_id,supplier_id,farm_id,challan_date,challan_no) values('$lastId','$receipt_idval','$receiptGatePass','$supplier_id','$farmIdVal','$supplierchellandt,','$supplierchellan')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
		die();
		
	}	
	function addManageLotId_old($rmId,$alphaValue,$supplier_id,$receipt_idval,$receiptGatePass,$company_idval,$unit_idval,$farmIdVal,$number_genval,$userId)
	{
		$qry	= "insert into t_manage_rm_lotid(receipt_id,receipt_gate_pass_id,supplier_id,farm_id,company_id,unit_id,rm_lotid,alpha_character,number_gen_id,created_on,created_by) values('$receipt_idval','$receiptGatePass','$supplier_id','$farmIdVal','$company_idval','$unit_idval','$rmId','$alphaValue','$number_genval',Now(),'$userId')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	
	} 
	
	
	
	function addManageLotIdNew($rm_lot_id,$alphaValue,$generateNewLotId,$Company_Name,$unit,$number_genval,$userId)
	{
		$qry	= "insert into t_manage_rm_lotid(company_id,unit_id,rm_lotid,alpha_character,number_gen_id,lot_id_origin,created_on,created_by) values
				  ('$Company_Name','$unit','$generateNewLotId','$alphaValue','$number_genval','$rm_lot_id',Now(),'$userId')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	
	
	}
	function deleteTemporary($rmId,$number_genval)
	{
		$qry	= "delete from t_rmlotid_temporary where rmlotid='$rmId' and number_gen_id='$number_genval'";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function updateVehiclestatus($vehicleNo)
	{
	 $qry	= "update m_vehicle_master set allocated='0' ,procurement_number='' where id='$vehicleNo'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}
	function updateDriverstatus($driverName)
	{
	 $qry	= "update m_driver_master set allocated='0',procurement_number='' where id='$driverName'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	
	}
	function updateReceiptGatePass($rmAlpha,$recpt)
	{
	 	$qry	= "update t_rmreceiptgatepass set lot_Id='$rmAlpha'  where id='$recpt'";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function receiptGatePassIDFind($receiptId)
	{
	
		 $qry	= "select receipt_gatepass_id from t_rm_receipt_gatepass_supplier where id in($receiptId)";
		return  $this->databaseConnect->getRecord($qry);
	}
	function getAllRmlotIdOfSameCompanyUnit($companyId,$unitId,$editId)
	{
		$qry	= "select id,CONCAT(alpha_character,rm_lotid) from  t_manage_rm_lotid where company_id='$companyId' and unit_id='$unitId' and id!='$editId'  and id not in (SELECT rm_lot_id  FROM `weighment_data_sheet` where active='1' )  and id not in (SELECT rmLotId  FROM `t_rmweightaftergrading` ) and id not in (SELECT rm_lot_id FROM `t_dailycatch_main`) and lot_id_origin='0' and status='0' ";
		return  $this->databaseConnect->getRecords($qry);
	}
	function updateLotIdStatus($editRmlotID)
	{
		 $qry = "UPDATE t_manage_rm_lotid SET status=1 WHERE id=".$editRmlotID;		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}

	###CHECK LOTID NAME
	function getLotIDInReceipt($editRmlotIDName)
	{
		$qry	= "SELECT lot_Id,id FROM `t_rmreceiptgatepass` WHERE `lot_Id` REGEXP '(^|,)$editRmlotIDName($|,)'";
		return  $this->databaseConnect->getRecords($qry);
	}

	function removeFromString($str, $item) {
    $parts = explode(',', $str);

    while(($i = array_search($item, $parts)) !== false) {
        unset($parts[$i]);
    }

    return implode(',', $parts);
	}
	function updateLotIdReceipt($lotid,$rmLotDet)
	{
		$qry = "UPDATE t_rmreceiptgatepass SET lot_Id='$lotid' WHERE id=".$rmLotDet;		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
	function lotIdExist($lot_Id)
	{
		$qry	= "SELECT id FROM `t_manage_rm_lotid` where id ='$lot_Id' and id not in (SELECT rm_lot_id  FROM `weighment_data_sheet` where active='1' ) and  id not in (SELECT rm_lot_id FROM `t_dailycatch_main`)";
		//$qry	= "SELECT id FROM `t_manage_rm_lotid` where id ='$lot_Id' and id not in (SELECT rm_lot_id  FROM `weighment_data_sheet` where active='1' ) and id not in (SELECT rmLotId  FROM `t_rmweightaftergrading` ) and id not in (SELECT rm_lot_id FROM `t_dailycatch_main`)";
		return  $this->databaseConnect->getRecord($qry);
	}
	function getAllDetailofRmlotId($rmlotID)
	{
		$qry	= "select receipt_id,receipt_gate_pass_id,supplier_id,farm_id from t_manage_rm_lotid where id='$rmlotID'";
		$result	= $this->databaseConnect->getRecord($qry);
		return $result;
	}
	function updateDetailofRmlotId($rm_lot_id,$rmlotID_Detail,$editRmlotID,$receipt,$receiptGatePassId,$supplier_id,$pond_id)
	{	
		$qry = "UPDATE t_manage_rmlotid_details SET rmlot_main_id='$rm_lot_id' WHERE id='$rmlotID_Detail' and receipt_id='$receipt' and  receipt_gatepass_id='$receiptGatePassId' and supplier_id='$supplier_id' and farm_id='$pond_id'";		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
	function checkLotIdExist($rmlotid)
	{
		$qry	= "SELECT concat(alpha_character,rm_lotid),b.receipt_gatepass_id from t_manage_rm_lotid a left join t_manage_rmlotid_details b on a.id=b.rmlot_main_id where a.id='$rmlotid'";
		return  $this->databaseConnect->getRecords($qry);
	}
	function checkNewLotIdExist($rmlotid)
	{
		$qry	= "SELECT a.id,b.id,b.supplier_name,b.pond_name from weighment_data_sheet a left join t_weightment_data_entries b on a.id=b.weightment_data_sheet_id where a.rm_lot_id='$rmlotid'";
		return  $this->databaseConnect->getRecords($qry);
	}
	function checkOldLotIdExist($rmlotid)
	{
		$qry	= "SELECT a.id,b.id,b.supplier_name,b.pond_name from weighment_data_sheet a left join t_weightment_data_entries b on a.id=b.weightment_data_sheet_id where a.rm_lot_id='$rmlotid'";
		return  $this->databaseConnect->getRecords($qry);
	}
	function receiptgatePassCheck($rmcode,$receiptId)
	{
		 $qry	= "SELECT lot_Id FROM t_rmreceiptgatepass WHERE FIND_IN_SET('$rmcode ', CONCAT(lot_Id,' ')) > 0 and id='$receiptId'";
		return  $this->databaseConnect->getRecord($qry);
	}
	function checkReceiptLot($receiptId)
	{
	 $qry	= "SELECT lot_Id FROM t_rmreceiptgatepass WHERE  id='$receiptId'";
		return  $this->databaseConnect->getRecord($qry);
	}
	function updatermLotInReceipt($receiptid,$lotid)
	{
		$qry = "UPDATE t_rmreceiptgatepass  SET lot_Id='$lotid' WHERE id='$receiptid'";		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
	function updatermLotWeightmentOld($weightmentEntryID,$weightmentIdNew)
	{
		$qry = "UPDATE t_weightment_data_entries  SET weightment_data_sheet_id='$weightmentIdNew' WHERE id='$weightmentEntryID'";		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
	function appendingDataEntryInWeightmentData($weightment_data_sheet_id,$supplier_id,$pond_id)
	{
		$qry	= "insert into  t_weightment_data_entries(weightment_data_sheet_id,supplier_name,pond_name) values
				  ('$weightment_data_sheet_id','$supplier_id','$pond_id')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function chkLotidExtra($editRmlotID)
	{
		$qry	= "SELECT a.receipt_gatepass_id FROM t_manage_rmlotid_details a  left join t_manage_rm_lotid  b on a.rmlot_main_id=b.id WHERE  b.id='$editRmlotID'";
		return  $this->databaseConnect->getRecord($qry);
	}
	function chkWeightment($editRmlotID)
	{
		$qry	= "SELECT a.process_code_id FROM t_weightment_data_entries a  left join weighment_data_sheet  b on a.weightment_data_sheet_id=b.id WHERE  b.rm_lot_id='$editRmlotID'";
		return  $this->databaseConnect->getRecord($qry);
	}
	function deleteLotFrmWeightment($editRmlotID)
	{
		$qry	= " delete from weighment_data_sheet where rm_lot_id=$editRmlotID";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	function addLotIdGeneratedInFrozen($newRMCompanyId,$newRMUnitId,$rmId,$alphaValue,$number_genval,$userId)
	{
		$qry	= "insert into t_manage_rm_lotid(company_id,unit_id,rm_lotid,alpha_character,number_gen_id,created_on,created_by,status) values('$newRMCompanyId','$newRMUnitId','$rmId','$alphaValue','$number_genval',Now(),'$userId','2')";	
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	function getLotName($id)
	{
		$qry	= "SELECT concat(alpha_character,rm_lotid) as rm_lot_id from t_manage_rm_lotid where id='$id' ";
		$result= $this->databaseConnect->getRecord($qry);
		 return $result[0];
	}
	function getAllRMLOTIDData($id)
	{
		$qry	= "select a.id,a.receipt_id,a.supplier_id,a.company_id,a.unit_id,a.rm_lotid,a.alpha_character,a.processing_stage,c.name,d.name,a.lot_id_origin from t_manage_rm_lotid a 
		left join m_billing_company c on a.company_id=c.id left join m_plant d on a.unit_id=d.id
		where a.created_on>='$fromDate' and a.created_on<='$tillDate' and a.id not in  (select lot_id_origin from t_manage_rm_lotid) and a.status = '0' and a.id=''";
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	function getRMProgressStage($rmlotid)
	{
		$qry	= "SELECT id, 'FROZEN' as stage from t_dailyfrozenpacking_main_rmlotid where rm_lot_id='$rmlotid' union  SELECT id,'SOAKED' as stage from t_soaking  where rm_lot_id='$rmlotid' union  SELECT id,'PRE-PROCESSED' as stage from t_dailypreprocess_rmlotid where rm_lot_id='$rmlotid' union  SELECT id,'GRADED' as stage from t_rmweightaftergrading where rmLotId='$rmlotid' ";
		//echo $qry;
		 $result= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)? $result[0][1] : "RM";
		//  return $result[0][1];
	}

	function updateRMLotIDconfirm($rmlotId){
		$qry	= "update t_manage_rm_lotid set active='1' where id=$rmlotId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function updateRMLotIDReleaseconfirm($rmlotId){
		$qry	= "update t_manage_rm_lotid set active='0' where id=$rmlotId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	function getAllCompany()
	{
		$qry = "SELECT id,display_name as name FROM m_billing_company WHERE active='1' ";
		
		$result	=	array();
		$result	=	$this->databaseConnect->fetch_array($qry);
		return $result;
	}

}