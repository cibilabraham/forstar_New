<?
class StockSearch
{  
	/****************************************************************
	This class deals with all the operations relating to Supplier Stock
	*****************************************************************/
	var $databaseConnect;
	
    
	//Constructor, which will create a db instance for this class
	function StockSearch(&$databaseConnect)
    {
        $this->databaseConnect =&$databaseConnect;
	}

#Getting Search Records

function fetchSupplierStockRecords($supplierId){
	
		$qry	=	"select a.id,a.supplier_id,a.stock_id,a.quote_price,a.nego_price, b.name from supplier_stock a, m_stock b where supplier_id='$supplierId' and a.stock_id=b.id";
		//echo $qry;
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
}



	
}