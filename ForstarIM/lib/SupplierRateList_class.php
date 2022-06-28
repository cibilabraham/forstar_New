<?php
class SupplierRateList
{
	/****************************************************************
	This class deals with all the operations relating to Supplier Rate List
	*****************************************************************/
	var $databaseConnect;

	//Constructor, which will create a db instance for this class
	function SupplierRateList(&$databaseConnect)
    	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function addSupplierRateList($rateListName, $startDate, $copyRateList, $selSupplierId, $supplierCurrentRateListId,$negoPrice,$stockid)
	{
		$qry = "insert into m_supplier_ratelist (name, start_date, supplier_id,rate,stock_id) values('".$rateListName."', '".$startDate."', '$selSupplierId','$negoPrice','$stockid')";
//echo $qry;
		if ($supplierCurrentRateListId!="") {
			$updateRateListEndDate = $this->updateSupplierRateListRec($supplierCurrentRateListId, $startDate);
		}		
		//echo $qry;
		$insertStatus	=	$this->databaseConnect->insertRecord($qry);
		
		if ($insertStatus) {
			$this->databaseConnect->commit();
	#-----------------------------Copy Functions-------------------------------------------------
			$insertedRateListId = $this->databaseConnect->getLastInsertedId();
			
			if ($copyRateList!="") {
				$supplierStockRecords = $this->fetchAllSupplierStockRecords($copyRateList, $selSupplierId);
				foreach ($supplierStockRecords as $ssr) {
					$supplierStockRecId 	= $ssr[0];	
					$supplierId 		= $ssr[1];
					$stockId		= $ssr[2];
					$quotedPrice		= $ssr[3];
					$negotiatedPrice	= $ssr[4];
					$exciseRate		= $ssr[5];
					$cstRate		= $ssr[6];		
					$schedule		= $ssr[7];
					$layerRate		= $ssr[8];
					$layerConverRate	= $ssr[9];
					$remarks		= $ssr[10];
					$stockType		= $ssr[11];
					$created		= $ssr[12];
					$createdBy		= $ssr[13];	
					$unitPricePerQty	= $ssr[14];	
					$unitPricePerEachItem	= $ssr[15];	
					
					# Supplier Stock entry
					$supplierStockInsertStatus = $this->addSupplierStock($supplierId, $stockId, $quotedPrice, $negotiatedPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerRate, $layerConverRate, $createdBy, $insertedRateListId, $unitPricePerQty, $unitPricePerEachItem);
							
					if ($supplierStockInsertStatus) {
						$newSupplierStockId = $this->databaseConnect->getLastInsertedId();
					}
							
					$supplierStockLayerRecords = $this->fetchAllSupplierStockLayerRecs($supplierStockRecId);
					if (sizeof($supplierStockLayerRecords)>0) {
						while (list(,$v) = each($supplierStockLayerRecords)) {
							$paperQuality	= $v[1]; 
							$layerBrand	= $v[2]; 
							$layerGsm	= $v[3];
							$layerBf	= $v[4];
							$layerCobb	= $v[5];
							$layerNo	= $v[6];
							# Insert layer Rec
							$this->addLayer($newSupplierStockId, $paperQuality, $layerBrand, $layerGsm, $layerBf, $layerCobb, $layerNo);
						}
					}
				}
			}
	#----------------------------Copy Functions End   --------------------------------------------		
		
		} else {
			$this->databaseConnect->rollback();
		}
		return $insertStatus;
	}

	# Returns all Recs

	function fetchAllPagingRecords($offset, $limit,$supplierFilterId)
	{
		$whr 	= " a.supplier_id=b.id";

		if ($supplierFilterId!="") {
			$whr .= " and a.supplier_id=".$supplierFilterId;
			$orderBy  = " a.start_date desc";	
		} else {
			$whr .= "";
			$orderBy  = " a.name asc";
		}
		$limit = " $offset, $limit ";
		$qry	=	"select a.id, a.name, a.start_date, b.name,a.active,(select count(a1.id) from supplier_stock a1 where rate_list_id=a.id) as tot from m_supplier_ratelist a, supplier b";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}

	/*function fetchAllPagingRecords($offset, $limit, $supplierFilterId)
	{
		$whr 	= " a.supplier_id=b.id";

		if ($supplierFilterId!="") {
			$whr .= " and a.supplier_id=".$supplierFilterId;
			$orderBy  = " a.start_date desc";	
		} else {
			$whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
			$orderBy  = " a.name asc";
		}

		$limit = " $offset, $limit ";
				
		$qry	= "select a.id, a.name, a.start_date, b.name,a.active,(select count(a1.id) from supplier_stock a1 where rate_list_id=a.id) as tot from m_supplier_ratelist a, supplier b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		if ($limit!="") 	$qry .= " limit ".$limit;
		
		$result	= $this->databaseConnect->getRecords($qry);
		echo "$qry";
		return $result;
	}*/


	# Returns all Recs
	function fetchAllRecords($supplierFilterId)
	{
		$whr 	= " a.supplier_id=b.id";

		if ($supplierFilterId!="") {
			$whr .= " and a.supplier_id=".$supplierFilterId;
			$orderBy  = " a.start_date desc";	
		} else {
			$whr .= "";
			$orderBy  = " a.name asc";
		}
		
		$qry	=	"select a.id, a.name, a.start_date, b.name,a.active,(select count(a1.id) from supplier_stock a1 where rate_list_id=a.id) as tot from m_supplier_ratelist a, supplier b";
		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;
		$result	=	$this->databaseConnect->getRecords($qry);
		//echo $qry;
		return $result;
	}


	/*function fetchAllRecords($supplierFilterId)
	{
		$whr 	= " a.supplier_id=b.id";

		if ($supplierFilterId!="") {
			$whr .= " and a.supplier_id=".$supplierFilterId;
			$orderBy  = " a.start_date desc";
		} else {
			$whr .= " and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date))";
			$orderBy  = " a.name asc";
		}		
		
		$qry	= "select a.id, a.name, a.start_date, b.name,a.active,(select count(a1.id) from supplier_stock a1 where rate_list_id=a.id) as tot from m_supplier_ratelist a, supplier b";

		if ($whr!="") 		$qry .= " where ".$whr;
		if ($orderBy!="") 	$qry .= " order by ".$orderBy;		
		
		$result	= $this->databaseConnect->getRecords($qry);
		//echo "$qry";
		return $result;
	}*/


	# Returns all Supplier Rate List Recs
	function fetchAllSupplierRateListRecords($supplierId)
	{
		$qry	= "select id, name, start_date from m_supplier_ratelist where supplier_id='$supplierId' order by id desc";
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	# Get a Rec based on id 
	
	function find($categoryId)
	{
		$qry	=	"select id, name, start_date from m_supplier_ratelist where id=$categoryId";
		//echo $qry;
		return $this->databaseConnect->getRecord($qry);
	}


	# Update a Rec
	function updateSupplierRateList($rateListName, $startDate, $supplierRateListId, $hidStartDate, $latestRateListId)
	{
		$qry = " update m_supplier_ratelist set name='$rateListName', start_date='$startDate' where id=$supplierRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;	
	}
	
	
	# Delete a Rec
	function deleteSupplierRateList($supplierRateListId)
	{
		$qry	= " delete from m_supplier_ratelist where id=$supplierRateListId";

		$result	= $this->databaseConnect->delRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $result;
	}

	#Checking Rate List Id used
	function checkRateListUse($supplierRateListId)
	{
		$qry	= "select id from supplier_stock where rate_list_id='$supplierRateListId'";
		$result	= array();
		$result	= $this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?true:false;
	}

	function latestRateListUnit($supplierId,$stock_id)
	{
		$cDate = date("Y-m-d");
	
		//$qry = "select a.id from m_supplier_ratelist a where supplier_id=$supplierId and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";

		$qry = "select a.id from m_supplier_ratelist a where supplier_id=$supplierId and stock_id='$stock_id' and ((CURDATE()>=a.start_date && (a.end_date is null || a.end_date=0)) or (CURDATE()>=a.start_date and CURDATE()<=a.end_date)) order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}


	#Find the Latest Rate List  Id (using in Other screen )
	function latestRateList($supplierId,$stock_id)
	{
		$cDate = date("Y-m-d");
	
		//$qry = "select a.id from m_supplier_ratelist a where supplier_id=$supplierId and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";

		$qry = "select a.id from m_supplier_ratelist a where supplier_id=$supplierId and stock_id='$stock_id' and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	#Find Latest rate List Date
	function findRateList($supplierId)
	{
		$cDate = date("Y-m-d");
		$qry	= "select a.id, name, start_date from m_supplier_ratelist a where supplier_id=$supplierId and '$cDate'>=date_format(a.start_date,'%Y--%m-%d') order by a.start_date desc";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		if (sizeof($rec)>0) {
			$array			=	explode("-", $rec[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList =  $rec[1]."&nbsp;(".$startDate.")";
		}
		return (sizeof($rec)>0)?$displayRateList:"";
	}

#---------------------------------Copy Functions---------------------------------------------
	#Fetch All Supplier Stock Records
	function fetchAllSupplierStockRecords($selRateList, $selSupplierId)
	{
		$qry = "select  id, supplier_id, stock_id, quote_price, nego_price, excise_rate, cst, schedule, packing_rate, packing_conv_rate, remark, stock_type, created, createdby, unit_price_per_qty, unit_price_per_each_item from supplier_stock where rate_list_id='$selRateList' and supplier_id='$selSupplierId'";
		//echo $qry;
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	#Insert Supplier Stock Record
	function addSupplierStock($supplierId, $stockId, $quotePrice, $negoPrice, $exciseRate, $cstRate, $schedule, $remarks, $stockType, $layerRate, $layerConverRate, $userId, $insertedRateListId, $unitPricePerQty, $unitPricePerEachItem)
	{
		$qry = "insert into supplier_stock (supplier_id, stock_id, quote_price, nego_price, excise_rate, cst,schedule, remark, packing_rate, packing_conv_rate, stock_type, created, createdby, rate_list_id, unit_price_per_qty, unit_price_per_each_item) values('".$supplierId."', '".$stockId."', '".$quotePrice."', '".$negoPrice."', '$exciseRate', '$cstRate', '$schedule', '$remarks', '$layerRate', '$layerConverRate', '$stockType', Now(), '$userId', '$insertedRateListId', '$unitPricePerQty', '$unitPricePerEachItem')";
		//echo $qry."<br>";
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		return $insertStatus;
	}
	
	#Fech all Supplier Stock Layer Recs
	function fetchAllSupplierStockLayerRecs($supplierStockRecId)
	{
		$qry = "select id, quality, brand, gsm, bf, cobb, layer_no from supplier_stock_layer where supplierstock_id='$supplierStockRecId'";
		
		$result	= $this->databaseConnect->getRecords($qry);
		return $result;
	}

	#Add stock layer Recs
	function addLayer($newSupplierStockId, $paperQuality, $layerBrand, $layerGsm, $layerBf, $layerCobb, $layerNo)
	{
		$qry = "insert into supplier_stock_layer (supplierstock_id, quality, brand, gsm, bf, cobb, layer_no) values('$newSupplierStockId', '$paperQuality', '$layerBrand', '$layerGsm', '$layerBf', '$layerCobb', '$layerNo')";
		//echo $qry;
		$insertStatus	= $this->databaseConnect->insertRecord($qry);		
		if ($insertStatus) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();
		
		return $insertStatus;
	}
#------------------------------Copy Functions End------------------------------------------------

	# update Supplier Rate List Rec
	function updateSupplierRateListRec($supplierCurrentRateListId, $startDate)
	{
		$sDate		=	explode("-",$startDate); //
		//$selectDate	=	$sDate[2]."/".$sDate[1]."/".$sDate[0];
		$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
		$qry = " update m_supplier_ratelist set end_date='$endDate' where id=$supplierCurrentRateListId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;	
	}

	function updateSupplierRateListconfirm($supplierId){
		$qry	= "update m_supplier_ratelist set active='1' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	


	}

	function updateSupplierRateListReleaseconfirm($supplierId){
	$qry	= "update m_supplier_ratelist set active='0' where id=$supplierId";
 		//echo $qry;
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;

	}


	#Find the Latest Rate List  Id (using in Other screen )
	function getStartDate($rateListId)
	{
		$cDate = date("Y-m-d");
	
		$qry = "select start_date from m_supplier_ratelist a where id='$rateListId'";
		//echo $qry;
		$rec = $this->databaseConnect->getRecord($qry);
		return (sizeof($rec)>0)?$rec[0]:"";
	}

	###CHECK ENTRY FOR THIS DATE FOR THE SAME SUPPLIER EXIST
	function chkValidDateEntry($seldate, $cId,$supplierId)
	{
		$uptdQry ="";
		if ($cId!="") $uptdQry = " and id!=$cId";
		$qry	= "select a.id, a.name, a.start_date from m_supplier_ratelist a where '$seldate'<=date_format(a.start_date,'%Y-%m-%d') $uptdQry and supplier_id='$supplierId' order by a.start_date desc";
		//echo $qry."<br>";
		//die();
		$result	=	$this->databaseConnect->getRecords($qry);
		return (sizeof($result)>0)?false:true;
	}

	# Date Wise Rate list
	function getSupplierRateList($selDate,$supplierId)
	{	
		$qry	= "select id as ratelistid from m_supplier_ratelist where date_format(start_date,'%Y-%m-%d')<='$selDate' and supplier_id='$supplierId' order by id desc";
		//echo $qry; 
		//echo die();
		$result	=	$this->databaseConnect->getRecord($qry);
		return $result[0];
	}
	
	# update Rec
	function updateRateListRec($pageCurrentRateListId, $endDate)
	{
		$qry = " update m_supplier_ratelist set end_date='$endDate' where id=$pageCurrentRateListId";
 		//echo $qry; die();
		$result	= $this->databaseConnect->updateRecord($qry);
		if ($result) $this->databaseConnect->commit();
		else $this->databaseConnect->rollback();		
		return $result;
	}
	

}
?>