<?php
class Container
{  
	/****************************************************************
	This class deals with all the operations relating to Container
	*****************************************************************/
	var $databaseConnect;
	
	//Constructor, which will create a db instance for this class
	function Container(&$databaseConnect)
  	{
        	$this->databaseConnect =&$databaseConnect;
	}

/*
#Check Blank Record Exist

function checkBlankRecord(){

		$qry = "select a.id, b.id from t_container_main a left join t_container_entry b on a.id=b.main_id where (a.container_id is null  or a.container_id=0 ) order by a.id desc";
		//echo $qry;
		$result	=	$this->databaseConnect->getRecord($qry);
		
		return 	(sizeof($result)>0)?array( $result[0], $result[1]):false;	
}

#Indert blank record
function addTempDataMainTable()
	{
				
		$qry	=	"insert into t_container_main (select_date) values(Now())";
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
#Insert blank record
function addTempDataEntryTable($mainId)
	{
				
		$qry	=	"insert into t_container_entry (main_id) values('$mainId')";
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

function maxValuePO(){

		$qry	=	"select max(container_id) from t_container_main";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return $rec[0];
}







#Get Records For Selected Date Range
	
	function getContainerRecords($fromDate,$tillDate)
	{
	
		$whr		=	"a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.container_id!=0" ;
		
						
		$orderBy	=	"a.container_id asc";
		
		$qry		=	"select a.id, a.container_id, a.shipping_line_id, a.container_no, a.seal_no, a.vessal_details, a.sailing_on, a.expected_date, a.select_date, b.id, b.invoice_id from t_container_main a left join t_container_entry b on a.id=b.main_id";
		
		if ($whr!="")
			$qry   .=" where ".$whr;
		if ($orderBy!="")
		 	$qry   .=" order by ".$orderBy;
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	
	

//------------------------ Delete From Main Table------------------------------



function checkRecordsExist($containerMainId){

		$qry	=	"select b.main_id from t_container_main a, t_container_entry b  where  a.id=b.main_id and b.main_id='$containerMainId' ";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
}


# Delete a  Main Rec

	function deleteContainerMainRec($containerMainId)
	{
		$qry	=	" delete from t_container_main where id=$containerMainId";

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
//------------------------ Delete End------------------------------


function filterPurchaseOrderRecs($selPOId, $mode, $containerEntryId){

	if($mode==1)
		{
			$qry = "select a.id, a.invoice_id, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, c.id, c.grade_id, c.freezingstage_id, c.frozencode_id, c.mcpacking_id, c.number_mc, c.priceperkg, c.value_usd, c.value_inr, a.extended, a.logstatus, a.logstatusdescr, d.id, d.po_gradeentry_id from t_purchaseorder_main a join t_purchaseorder_rm_entry b join t_purchaseorder_grade_entry c left join t_container_rm_entry d on c.id=d.po_gradeentry_id and d.containerentry_id='$containerEntryId' where a.id=b.main_id and b.id=c.rmentry_id and a.invoice_id!=0 and a.id='$selPOId' order by a.invoice_id asc";
			
		}
		else
		{
			$qry = "select a.id, a.invoice_id, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, c.id, c.grade_id, c.freezingstage_id, c.frozencode_id, c.mcpacking_id, c.number_mc, c.priceperkg, c.value_usd, c.value_inr, a.extended, a.logstatus, a.logstatusdescr from t_purchaseorder_main a, t_purchaseorder_rm_entry b, t_purchaseorder_grade_entry c where a.id=b.main_id and b.id=c.rmentry_id and a.invoice_id!=0 and a.id='$selPOId' order by a.invoice_id asc";			
		}

		//$qry = "select a.id, a.invoice_id, a.customer_id, a.payment_term, a.lastdate, a.select_date, b.id, b.fish_id, b.processcode_id, b.eucode_id, b.brand_id, c.id, c.grade_id, c.freezingstage_id, c.frozencode_id, c.mcpacking_id, c.number_mc, c.priceperkg, c.value_usd, c.value_inr, a.extended, a.logstatus, a.logstatusdescr from t_purchaseorder_main a, t_purchaseorder_rm_entry b, t_purchaseorder_grade_entry c where a.id=b.main_id and b.id=c.rmentry_id and a.invoice_id!=0 and a.id='$selPOId' order by a.invoice_id asc";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
}

	


function addRMDetails($lastId,$gradeEntryId){

	$qry	=	"insert into t_container_rm_entry (containerentry_id, po_gradeentry_id) values('$lastId','$gradeEntryId')";
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


#Delete Container RM Entry Record
function deleteContainerRMEntryRec($containerEntryId){

		$qry	=	" delete from t_container_rm_entry where containerentry_id='$containerEntryId' ";
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

#find Container, (Generated No)
function findContainerNo($coId){
	$qry = "select container_id from t_container_main where id='$coId'";
	$result	=	$this->databaseConnect->getRecord($qry);
	return 	(sizeof($result)>0)?$result[0]:false;
}
*/
# -------------------------------------------------------------------------------------------------------------------------------

/*	# Insert
	function insertContainerMainRec($containerId, $selectDate, $shippingLine, $containerNo, $sealNo, $vessalDetails, $sailingDate, $expectedDate, $containerYear, $containerType, $userId)
	{
		$qry	= "insert into t_container_main (container_id, select_date, shipping_line_id, container_no, seal_no, vessal_details, sailing_on, expected_date, cid_year, created, created_by, container_type) values('$containerId', '$selectDate', '$shippingLine', '$containerNo', '$sealNo', '$vessalDetails', '$sailingDate', '$expectedDate', '$containerYear', Now(), '$userId', '$containerType')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}
*/
	
	# Insert
	function insertContainerMainRec($containerId, $selectDate, $shippingLine, $containerNo, $sealNo, $vessalDetails, $sailingDate, $expectedDate, $containerYear, $containerType, $userId,$containerAlpha,$containerNumgen)
	{
		$qry	= "insert into t_container_main (container_id, select_date, shipping_line_id, container_no, seal_no, vessal_details, sailing_on, expected_date, cid_year, created, created_by, container_type,alpha_code,num_gen_id) values('$containerId', '$selectDate', '$shippingLine', '$containerNo', '$sealNo', '$vessalDetails', '$sailingDate', '$expectedDate', '$containerYear', Now(), '$userId', '$containerType','$containerAlpha','$containerNumgen')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}

	# Insert PO
	function insertContainerPO($containerId, $invoiceId)
	{		
		$qry	= "insert into t_container_entry (main_id, invoice_id) values('$containerId', '$invoiceId')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $insertStatus;
	}	


	# Get Packing  based on id
	function find($containerMainId)
	{
		$qry	= "select a.id, a.container_id, a.shipping_line_id, a.container_no, a.seal_no, a.vessal_details, a.sailing_on, a.expected_date, a.select_date, a.container_type,a.alpha_code,a.num_gen_id from t_container_main a where a.id='$containerMainId'";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}

	# Returns all Completed Records
	function getInvoiceRecs()
	{		
		$qry = "select tim.id, tim.invoice_type, tim.invoice_no, tim.proforma_no, tim.sample_invoice_no, mc.customer_name, if (tim.invoice_no!=0 and tim.invoice_type='T', tim.invoice_date, tim.entry_date) as selDate, me.alpha_code  
				from t_invoice_main tim left join m_customer mc on  tim.customer_id=mc.id 
				left join m_exporter me on me.id=tim.exporter_id
				order by tim.invoice_date desc, tim.invoice_no desc";

		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);

		$resultArr = array(''=>'--Select--');
		if (sizeof($result)>0) {
			foreach ($result as $v) {
				$invoiceId 	= $v[0];
				$invType 	= $v[1];
				$sInvoiceNo 	= $v[2];
				$pfNo 	= $v[3];
				$saNo	= $v[4];
				$alphaCode = $v[7];

				$invoiceNo = "";
				// E - Export, T-Taxable, S-Sample
				if ($sInvoiceNo!=0) $invoiceNo=$sInvoiceNo;
				else if ($invType=='T' || $invType=='E') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "S$saNo";
				$distName = $v[5];
				$displayTxt = $invoiceNo." (".$distName.")";
				if ($alphaCode!="") $displayTxt = $alphaCode."/".$invoiceNo." (".$distName.")";

				$resultArr[$invoiceId] = $displayTxt;
			}
		}
		return $resultArr;
	}

	function getSelPORecsEdit($containerId)
	{	
		$resultArr='';
		$qry = "select tce.id, tce.invoice_id,tim.invoice_type, tim.invoice_no, tim.proforma_no, tim.sample_invoice_no, mc.customer_name, if (tim.invoice_no!=0 and tim.invoice_type='T', tim.invoice_date, tim.entry_date) as selDate, me.alpha_code from t_container_entry tce left join t_invoice_main tim on tce.invoice_id=tim.id left join m_exporter me on me.id=tim.exporter_id join m_customer mc on tim.customer_id=mc.id where tce.main_id='$containerId' order by tim.invoice_no desc, tim.proforma_no desc  ";
		//echo "<br>$qry<br>";
		$result= $this->databaseConnect->getRecords($qry);
		if (sizeof($result)>0) {
			foreach ($result as $v) {
				$invoiceId 	= $v[0];
				$invType 	= $v[2];
				$sInvoiceNo 	= $v[3];
				$pfNo 	= $v[4];
				$saNo	= $v[5];
				$alphaCode = $v[8];

				$invoiceNo = "";
				// E - Export, T-Taxable, S-Sample
				if ($sInvoiceNo!=0) $invoiceNo=$sInvoiceNo;
				else if ($invType=='T' || $invType=='E') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "S$saNo";
				$distName = $v[6];
				$displayTxt = $invoiceNo." (".$distName.")";
				if ($alphaCode!="") $displayTxt = $alphaCode."/".$invoiceNo." (".$distName.")";

				if($displayTxt=="")
				{
					$resultArr=$displayTxt;
				}
				else
				{
					$resultArr.='<br/>'.$displayTxt;
				}
			}
		}
		//printr($resultArr);
		return $resultArr;
	}
	
	function getSelPORecs($containerId)
	{	
		$qry = " select tce.id, tce.invoice_id,
				(if (tim.invoice_no!=0, CONCAT(me.alpha_code,'/',tim.invoice_no), if(tim.invoice_type='T' or tim.invoice_type='E', CONCAT(me.alpha_code,'/P',tim.proforma_no), CONCAT(me.alpha_code,'/S',tim.sample_invoice_no)))) as invNo 
				from t_container_entry tce left join t_invoice_main tim on tce.invoice_id=tim.id 
				left join m_exporter me on me.id=tim.exporter_id
				where tce.main_id='$containerId' order by tim.invoice_no desc, tim.proforma_no desc ";
		//echo "<br>$qry<br>";
		return $this->databaseConnect->getRecords($qry);
	}
	
	# Update
	function updateContainerMainRec($mainId, $containerId, $selectDate, $shippingLine, $containerNo, $sealNo, $vessalDetails, $sailingDate, $expectedDate, $containerYear, $containerConfirmed, $confirmedUser, $containerType)
	{
		$qry	= "update t_container_main  set  container_id='$containerId', shipping_line_id='$shippingLine', container_no='$containerNo', seal_no='$sealNo', vessal_details='$vessalDetails', sailing_on='$sailingDate', expected_date='$expectedDate', select_date='$selectDate', cid_year='$containerYear', confirmed='$containerConfirmed', confirmed_by='$confirmedUser', container_type='$containerType' where id='$mainId' ";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateContainerEntry($selPOId,$entryId)
	{
		$qry	=	"update t_container_entry  set invoice_id='$selPOId' where id='$entryId'";
		//echo $qry;
		$result	=	$this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}

	# Delete a Entry Rec
	function deleteContainerEntryRec($containerEntryId)
	{
		$qry	= " delete from t_container_entry where id=$containerEntryId";
 		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}
	

	function fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit)
	{	
		$whr		= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.container_id!=0" ;		
						
		$orderBy	= "a.container_id desc";

		$limit		= " $offset, $limit ";
		
		$qry		=	"select a.id, a.container_id, a.shipping_line_id, a.container_no, a.seal_no, a.vessal_details, a.sailing_on, a.expected_date, a.select_date, a.confirmed from t_container_main a";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		if ($limit!="") $qry .= " limit ".$limit;	
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	function fetchAllRecords($fromDate, $tillDate)
	{		
		$whr		= "a.select_date>='".$fromDate."' and a.select_date<='".$tillDate."'  and a.container_id!=0" ;		
						
		$orderBy	= "a.container_id desc";
		
		$qry		=	"select a.id, a.container_id, a.shipping_line_id, a.container_no, a.seal_no, a.vessal_details, a.sailing_on, a.expected_date, a.select_date, a.confirmed from t_container_main a";
		
		if ($whr!="") 		$qry   .= " where ".$whr;
		if ($orderBy!="") 	$qry   .= " order by ".$orderBy;
		//echo $qry;		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Delete a Entry Rec
	function deleteContainerIinvoiceEntries($containerId)
	{
		$qry	= " delete from t_container_entry where main_id=$containerId";
 		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	# --------- Proforma invoice Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextProformaInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		list($soNum,$numGenId,$alphaCode) = $this->getMaxProformaNum($soYear);
		$validSONum = $this->getValidProformaNum($soNum,$numGenId,$alphaCode);		
		if ($validSONum) $rec= array($soNum+1,$numGenId,$alphaCode);
		else $rec= $this->getCurrentProformaNum($selDate);
		//printr($rec);
		return $rec;
	}

	function getMaxProformaNum($soYear)
	{
		//$qry = " select max(container_id),num_gen_id,alpha_code from t_container_main where container_id!=0 and cid_year='$soYear' group by id order by container_id desc, select_date desc";
		$qry = " select max(container_id),num_gen_id,alpha_code from t_container_main where container_id!=0 group by id order by container_id desc, select_date desc";
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1],$rec[2],$rec[3]);
	}

	function getValidProformaNum($soNum,$numGenId,$alphaCode)
	{	$selDate=date("Y-m-d");	
		$qry	= "select start_no,id,alpha_code from number_gen where type='SHPC' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and start_no<='$soNum' and end_no>='$soNum' and alpha_code='$alphaCode' and id='$numGenId' ";
	//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentProformaNum($selDate)
	{
		$qry	= "select start_no,id,alpha_code  from number_gen where type='SHPC' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecord($qry);
		//return (sizeof($result)>0)?$result[0][0]:"";
		return array($result[0],$result[1],$result[2],$result[3]);
	}

/*	# --------- Proforma invoice Number starts ----
	# Get Next Proforma Invoice Number	
	function getNextProformaInvoiceNo()
	{	
		$selDate = date("Y-m-d");
		$soYear	 = date("Y", strtotime($selDate));
		list($soNum, $invoiceDate) = $this->getMaxProformaNum($soYear);
		$validSONum = $this->getValidProformaNum($soNum, $invoiceDate);		
		if ($validSONum) return $soNum+1;
		else return $this->getCurrentProformaNum($selDate);
	}

	function getMaxProformaNum($soYear)
	{
		$qry = " select max(container_id), select_date from t_container_main where container_id!=0 and cid_year='$soYear' group by id order by container_id desc, select_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return array($rec[0],$rec[1]);
	}

	function getValidProformaNum($soNum, $selDate)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SHPC' and  date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' and start_no<='$soNum' and end_no>='$soNum' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function getCurrentProformaNum($selDate)
	{
		$qry	= "select start_no, end_no from number_gen where type='SHPC' and date_format(start_date,'%Y-%m-%d')<='$selDate' and date_format(end_date,'%Y-%m-%d')>='$selDate' ";
		//echo $qry;
		$result = $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?$result[0][0]:"";
	}
*/


	# ----- Proforma Ends Here -----------------------------

	# ----------------------------
	# Check Proforma Number Exist
	# ----------------------------
	# Check valid PF num
	function chkValidProformaNum($selDate, $invoiceNum)
	{		
		$qry	= "select start_no, end_no from number_gen where type='SHPC' and date_format(start_date,'%Y-%m-%d')<='$selDate' and (date_format(end_date,'%Y-%m-%d')>='$selDate' or (end_date is null || end_date=0)) and start_no<='$invoiceNum' and end_no>='$invoiceNum'";	
		//echo $qry;
		$rec = $this->databaseConnect->getRecords($qry);
		return (sizeof($rec)>0)?true:false;
	}	

	function checkProformaNumExist($soId, $cSOId)
	{
		if ($cSOId!="") $uptdQry = " and id!=$cSOId";
		else $uptdQry = "";
		$qry = " select id from t_container_main where container_id='$soId' $uptdQry";
		$rec = $this->databaseConnect->getRecords($qry);
		//echo sizeof($rec);
		return (sizeof($rec)>0)?true:false;	
	}
	// Ends Here
	
	function deleteContainerMainRec($containerMainId)
	{
		$qry	=	" delete from t_container_main where id=$containerMainId";

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
}