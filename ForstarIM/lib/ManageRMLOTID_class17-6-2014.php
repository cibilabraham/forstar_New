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
				where a.created_on ='".$date."' ";
			if($rmLotOD != '')
		{
			$qry.= " and a.id=".$rmLotOD;
		}
		return $this->databaseConnect->getRecord($qry);
			
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
			$style = 'style="padding-left:10px; padding-right:10px;" class="listing-head"';
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
		$qry = "UPDATE t_unittransfer SET active=1 WHERE id=".$id;		
		$updateStatus	= $this->databaseConnect->updateRecord($qry);
		
		if ($updateStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();	
		
		return $updateStatus;
	}
}