<?php
class StockWastageReport
{  
	var $databaseConnect;

	function StockWastageReport(&$databaseConnect)
	{
        	$this->databaseConnect =&$databaseConnect;
	}

	function getStockRecForStockSummery($fromDate, $tillDate)
	{
		$qry = " SELECT a.id, a.department_id, b.stock_id, c.name, sum(b.quantity) FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_stock c ON c.id = b.stock_id where a.created>='$fromDate' and a.created<='$tillDate' GROUP BY b.stock_id ";
		//$qry = " SELECT a.id, a.department_id, b.stock_id, '', sum( b.quantity ), sum( b.scrap_value ) ,sum(b.total_amount), b.reason_type FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id  where a.created>='$fromDate' and a.created<='$tillDate' GROUP BY b.stock_id ";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getWastageDetialsofStock($stkId,$fromDate, $tillDate,$mode, $dipId)
	{

		if( $mode=='S')
		{
			$qry = " SELECT sum( b.quantity ), sum( b.scrap_value ) ,sum(b.total_amount), b.reason_type FROM stock_return a JOIN  stock_return_entry b ON b.return_main_id = a.id AND b.stock_id =$stkId where a.created>='$fromDate' and a.created<='$tillDate'  GROUP BY b.reason_type  ";
		}
		else  if( $mode=='D')
		{
			$qry = " SELECT sum( b.quantity ), sum( b.scrap_value ) ,sum(b.total_amount), b.reason_type ,a.department_id ,d.name, b.stock_id FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_department d on d.id = a.department_id  where a.created>='$fromDate' and a.created<='$tillDate' AND b.stock_id =$stkId and  a.department_id=$dipId group by  b.reason_type ";
		}
		
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}


	function getWastageDetialsofDept($depId,$fromDate, $tillDate,$mode, $dipId)
	{

		if( $mode=='S')
		{
			//$qry = " SELECT sum( b.quantity ), sum( b.scrap_value ),sum(b.total_amount), b.reason_type,b.stock_id  FROM stock_return a JOIN  stock_return_entry b ON b.return_main_id = a.id AND a.department_id =$depId where a.created>='$fromDate' and a.created<='$tillDate'  GROUP BY  b.stock_id,b.reason_type ";
			$qry = "SELECT sum( b.quantity ), sum( b.scrap_value ),sum(b.total_amount), b.reason_type,b.stock_id , d.name FROM stock_return a JOIN  stock_return_entry b ON b.return_main_id = a.id AND a.department_id =$depId left join m_stock d on d.id = b.stock_id where a.created>='$fromDate' and a.created<='$tillDate'  GROUP BY  b.stock_id,b.reason_type";
		}
		else  if( $mode=='D')
		{
			$qry = " SELECT sum( b.quantity ), sum( b.scrap_value ) ,sum(b.total_amount), b.reason_type ,a.department_id ,d.name, b.stock_id FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_department d on d.id = a.department_id  where a.created>='$fromDate' and a.created<='$tillDate' AND b.stock_id =$stkId and  a.department_id=$dipId group by  b.reason_type ";
		}
		
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

	function getWastageDepartmentRecs($stkId,$fromDate, $tillDate)
	{

		$qry = " SELECT sum( b.quantity ), a.department_id ,d.name,b.stock_id  FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_department d on d.id = a.department_id  where a.created>='$fromDate' and a.created<='$tillDate' AND b.stock_id =$stkId group by   a.department_id";
		
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}
	
	function getWastageStockRecords($deptId,$fromDate, $tillDate)
	{
		$sql = "SELECT sum( b.quantity ) , a.department_id, d.name, b.stock_id FROM stock_return a JOIN stock_return_entry b ON b.return_main_id  = a.id LEFT JOIN m_stock d ON d.id = b.stock_id WHERE a.created >= '$fromDate' AND a.created <= '$tillDate' AND  a.department_id =$deptId GROUP BY b.stock_id";
		$result	=	$this->databaseConnect->getRecords($sql);
		return $result;
	}


	function getUnitPriceOfStock($stkId)
	{
		$unitPrice = 0;

		$qry1 = "select a.stock_id, d.stock_id, d.unit_price 
		from goods_receipt_entries a, goods_receipt b, m_purchaseorder c, purchaseorder_entry d  where a.goods_receipt_id=b.id and b.po_id=c.id and c.id=d.po_id and a.stock_id=d.stock_id and  a.stock_id=$stkId  order by b.created desc limit 1 ";
		$qry2 = "SELECT min( a.nego_price ) FROM supplier_stock a, supplier b WHERE a.supplier_id = b.id AND stock_id=$stkId";


		

		$lsunitPriceRec = $this->databaseConnect->getRecords($qry1);
		if( sizeof( $lsunitPriceRec ) > 0 ) 	
		{
			//echo "::: $qry1 <br><br>";
			$unitPrice = $lsunitPriceRec[0][2]; // find the last supplier price 
		}
		else  
		{
			//echo "::: $qry2 <br><br>";
			$minUnitPriceRec = $this->databaseConnect->getRecords($qry2);
			if( sizeof($minUnitPriceRec) > 0 ) $unitPrice = $minUnitPriceRec[0][0]; // get the lowst price of this stock
		}
		return $unitPrice;
	}


	function getUnitPriceOfDept($depId, $fromDate, $tillDate)
	{
		$getWastageStockRecs = $this->getWastageStockRecords($depId, $fromDate, $tillDate);
	
		if( sizeof( $getWastageStockRecs ) > 0 )
		{
			while( list(, $gr ) = each($getWastageStockRecs) )
			{
				$stkId = $gr[3];
				$up += $this->getUnitPriceOfStock($stkId);
			}
		}
		return $up;
	}

	function getSortedStockRecs($fromDate, $tillDate)
	{

		$amtArray = array();
		$stockRecords = $this->getStockRecForStockSummery($fromDate, $tillDate);
		if( sizeof($stockRecords) > 0 ) 
		{
			$i=0;
			while( list(, $stk ) = each ( $stockRecords ) )
			{
				$stkId = $stk[2];
				$stkName = $stk[3];
				$totalQty = $stk[4];
				$up = $this->getUnitPriceOfStock($stkId);
				$amtArray[$i] = $up*$totalQty;
				$i++;
			}
		}
		array_multisort( $amtArray, SORT_DESC, $stockRecords);
		return $stockRecords;
	}

	function getDepRecForDepSummery($fromDate, $tillDate, $mode)
	{
		if( $mode == 'D')
		{
			$sql = " SELECT a.id, a.department_id, b.stock_id, c.name,sum(b.quantity) , d.name,b.reason_type FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_department c ON c.id = a.department_id join m_stock d on d.id = b.stock_id  where a.created>='$fromDate' and a.created<='$tillDate' GROUP BY  a.department_id ";
		}
		else 
		{
			$sql = " SELECT a.id, a.department_id, b.stock_id, c.name, b.quantity , d.name,b.reason_type FROM stock_return a JOIN stock_return_entry b ON b.return_main_id = a.id LEFT JOIN m_department c ON c.id = a.department_id join m_stock d on d.id = b.stock_id  where a.created>='$fromDate' and a.created<='$tillDate' GROUP BY  b.stock_id,a.department_id order by a.department_id desc ";
		}
		$result	=	$this->databaseConnect->getRecords($sql);
		return $result;
	}

	
	function getDepartmentWiseReportDetails($fromDate, $tillDate)
	{
		$sql="
			SELECT 
				sum(lostQty), sum(stolenQty), sum(dmgdQty),sum(deterioQty),sum(scapValue), stockId,depId,stkName  
			FROM
			(
				select sum(stock_return_entry.quantity) as lostQty, 0 as stolenQty,0 as dmgdQty,0 as deterioQty, sum(stock_return_entry.scrap_value) as scapValue, stock_return_entry.stock_id as stockId, stock_return.department_id as depId , m_stock.name as stkName from stock_return_entry JOIN stock_return ON  stock_return_entry .return_main_id = stock_return.id AND stock_return_entry .reason_type = 'L' LEFT JOIN m_stock ON m_stock.id = stock_return_entry.stock_id  where stock_return.created>='$fromDate' and stock_return.created<='$tillDate' group by stock_return_entry.stock_id, stock_return.department_id

				UNION

				select 0 as lostQty, sum(stock_return_entry.quantity) as stolenQty,0 as dmgdQty,0 as deterioQty, sum(stock_return_entry.scrap_value) as scapValue, stock_return_entry.stock_id as stockId, stock_return.department_id as depId, m_stock.name as stkName from stock_return_entry JOIN stock_return ON  stock_return_entry .return_main_id = stock_return.id AND stock_return_entry .reason_type = 'S' LEFT JOIN m_stock ON m_stock.id = stock_return_entry.stock_id  where stock_return.created>='$fromDate' and stock_return.created<='$tillDate' group by stock_return_entry.stock_id, stock_return.department_id

				UNION
				
				select 0 as lostQty, 0 as stolenQty,sum(stock_return_entry.quantity) as  dmgdQty,0 as deterioQty, sum(stock_return_entry.scrap_value) as scapValue, stock_return_entry.stock_id as stockId, stock_return.department_id as depId, m_stock.name as stkName from stock_return_entry JOIN stock_return ON  stock_return_entry .return_main_id = stock_return.id AND stock_return_entry .reason_type = 'D' LEFT JOIN m_stock ON m_stock.id = stock_return_entry.stock_id  where stock_return.created>='$fromDate' and stock_return.created<='$tillDate' group by stock_return_entry.stock_id, stock_return.department_id

				UNION
				
				select 0 as lostQty, 0 as stolenQty,0 as dmgdQty,sum(stock_return_entry.quantity) as deterioQty, sum(stock_return_entry.scrap_value) as scapValue, stock_return_entry.stock_id as stockId, stock_return.department_id as depId, m_stock.name as stkName from stock_return_entry JOIN stock_return ON  stock_return_entry .return_main_id = stock_return.id AND stock_return_entry .reason_type = 'DR' LEFT JOIN m_stock ON m_stock.id = stock_return_entry.stock_id where stock_return.created>='$fromDate' and stock_return.created<='$tillDate' group by stock_return_entry.stock_id, stock_return.department_id 

			) 
			AS stockDetials group by stockDetials.stockId,stockDetials.depId ORDER BY stockDetials.depId asc ";
			$result	=	$this->databaseConnect->getRecords($sql);
			return $result;
	}


	function getSortedDeptRecs($fromDate, $tillDate)
	{

		$deptWiseRecs = $this->getDepartmentWiseReportDetails($fromDate, $tillDate);
		$stkArray = array();
	
		if( sizeof($deptWiseRecs) > 0 )
		{
			$preStkId = 0;
			$prevDipId = 0;
			$i=0;
			while( list(, $rec) = each ($deptWiseRecs) )
			{
				$stkName =  $rec[7];
				$stkId = $rec[5];	
				$dipId = $rec[6];	
				$lostQty = $rec[0];
				$stolenQty = $rec[1];
				$dmgdQty = $rec[2];
				$detertdQty = $rec[3];
				$scrapVal = $rec[4];
		
				
				$totalQty = $lostQty+$stolenQty+$dmgdQty+$detertdQty;
				$unitPrice = $this->getUnitPriceOfStock($stkId);
				$totalAmount = $totalQty*$unitPrice;
						
				$stkArray[$dipId][$stkId] = array($lostQty,$stolenQty,$dmgdQty,$detertdQty,$unitPrice,$totalAmount,$stkName,$scrapVal);
			}
		}
		return $stkArray;
		
		/*$amtArray = array();
		$deptRecs = $this->getDepRecForDepSummery($fromDate, $tillDate,'D');
	
		if( sizeof($deptRecs) > 0 ) 
		{
			$i=0;
			while( list(, $stk ) = each ( $deptRecs ) )
			{
				$depId = $stk[1];
				$depName = $stk[3];
				$totalQty = $stk[4];
				$up = 0;

				$up += $this->getUnitPriceOfDept($depId, $fromDate, $tillDate);
				$amtArray[$i] = $up*$totalQty;
				$i++;	
			}

		}
		array_multisort( $amtArray, SORT_DESC, $deptRecs);
		return $deptRecs;*/
		//echo "<br><pre>";
		//print_r($departmentStockArray);
		//echo "</pre>";
	//	return $deptWiseRecs;
	}
}
?>