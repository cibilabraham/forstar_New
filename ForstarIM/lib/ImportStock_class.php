<?php
class ImportStock 
{
	function ImportStock()
	{			

	}
		# For Ordinary Stock
		function readOrdinaryStock($fileName, $incHead)
		{
			$stockList = array();
			if ($fileName !="" ) {
				$fp		= fopen ($fileName,"r");
				$i=0; 
				while ($data = fgetcsv ($fp, 1000, ",")) {
					$i++;
					if( $incHead=='Y' && $i == 1 ) continue;
					$code = trim($data[0]);
					$stockName 	= trim($data[1]);
					$description 	= trim($data[2]);
					$reorderPoint 	= trim($data[3]);
					$qtyInStock   	= trim($data[4]);	
					$addHoldingPcent = trim($data[5]);
					$stockingPeriod =  trim($data[6]);
					$basicUnitQty  	= trim($data[7]);
					$minimumOrderUnit = trim($data[8]);
					$minOrderQtyPerUnit = trim($data[9]);
					$brand = trim($data[10]);
					$type	= trim($data[11]);
					$modelNo = trim($data[12]);
					$size = trim($data[13]);
					$dLength = trim($data[14]);
					$dBreadth = trim($data[15]);
					$dHeight = trim($data[16]);	
					$diameter = trim($data[17]);	
					$radious  = trim($data[18]);					
					$weight = trim($data[19]);
					$color = trim($data[20]);
					$madeOf = trim($data[21]);
					$particularsDesc = trim($data[22]);
					
					$stockList[] = array(0=>$code, 1=>$stockName, 2=>$description, 3=>$reorderPoint, 4=>$qtyInStock, 5=>$addHoldingPcent, 6=>$stockingPeriod, 7=>$basicUnitQty, 8=>$minimumOrderUnit, 9=>$minOrderQtyPerUnit, 10=>$brand, 11=>$type, 12=>$modelNo, 13=>$size, 14=>$dLength, 15=>$dBreadth, 16=>$dHeight, 17=>$diameter, 18=>$radious, 19=>$weight, 20=>$color, 21=>$madeOf, 22=>$particularsDesc);
				}
				
			}
			return $stockList;
		}

		# For packing type stock
		function readPackingStock($fileName, $incHead)
		{
			$stockList = array();
			if ($fileName !="" ) {
				$fp		= fopen ($fileName,"r");
				$i=0; 
				while ($data = fgetcsv ($fp, 1000, ",")) {
					$i++;
					if( $incHead=='Y' && $i == 1 ) continue;
					$code = trim($data[0]);
					$stockName 	= trim($data[1]);
					$description 	= trim($data[2]);
					$reorderPoint 	= trim($data[3]);
					$qtyInStock   	= trim($data[4]);	
					$addHoldingPcent = trim($data[5]);
					$stockingPeriod =  trim($data[6]);
					$noOfLayers 	= trim($data[7]);
					$color		= trim($data[8]);
					$packingWeight	= trim($data[9]);
					$packingKg	= trim($data[10]);
					$noOfColors	= trim($data[11]);
					$dimension	= trim($data[12]);
					$cartonWeight	= trim($data[13]);
					
					$stockList[] = array(0=>$code, 1=>$stockName, 2=>$description, 3=>$reorderPoint, 4=>$qtyInStock, 5=>$addHoldingPcent, 6=>$stockingPeriod, 7=>$noOfLayers, 8=>$color, 9=>$packingWeight, 10=>$packingKg, 11=>$noOfColors, 12=>$dimension, 13=>$cartonWeight);
				}				
			}
			return $stockList;
		}


	function generateStockCSVFormat($csvFolder, $databaseConnect, $csv, $stockGroupObj, $inventoryType, $categoryFilterId, $categoryName)
	{
		$stockCSVFolder = realpath('.')."/".$csvFolder."/";
		
		if ($categoryName!="") $directoryName = $categoryName."_StockFormat_".date("dmY") ;
		else $directoryName = "StockFormat_".date("dmY") ;

		$outputFileDirectory = $stockCSVFolder.$directoryName."/";	
		$this->deleteDir($outputFileDirectory);
		$zipFilename = "";

		$whr = "a.category_id=b.id and a.active=1";
		if ($categoryFilterId) $whr .= " and a.category_id='$categoryFilterId' ";
		if ($inventoryType=='P') $whr .= " and a.carton='Y' ";
		else if ($inventoryType=='N') $whr .= " and a.carton='N' ";

		$orderBy	= " a.name asc ";				
		$qry	= "select a.id, a.category_id, a.name, a.description, b.name, a.unitgroup_id, a.check_point, a.carton, b.id from stock_subcategory a, stock_category b ";	
		if ($whr) 	$qry .= " where ".$whr;
		if ($orderBy)	$qry .= " order by ".$orderBy;
		//echo $qry;
		$stockCatNSubCatRecs	= $databaseConnect->getRecords($qry);
		//echo "stockCatNSubCatRecs=".sizeof($stockCatNSubCatRecs);

		if (mkdir($outputFileDirectory, 0, true)) {

			//$zip = new ZipArchive();
			$zipFilename = $directoryName.".zip";
			$zipFileDir = $stockCSVFolder.$zipFilename;
			if (file_exists($zipFileDir)) {
				unlink($zipFileDir);
			}
			$zip = new ZipStream($zipFilename);

			/*
			if ($zip->open($zipFileDir, ZIPARCHIVE::CREATE)!==TRUE)
			{
				//echo "cannot open <$zipFilename>\n";
			}
			*/
			$commonField = array("Name", "Description", "Re-order Required (Yes/No)", "Reorder Point", "Quantity in Stock", "Additional Holding Percent", "Stocking Period (Month)");	
			foreach ($stockCatNSubCatRecs as $scr) {
				$selCategoryId = $scr[8];
				$selSubCategoryId = $scr[0] ;
				$subCategoryName	= stripSlash($scr[2]);			
				$categoryName		= $scr[4];
				$chkCarton		= $scr[7];
				
				$stockGroupRecs = $stockGroupObj->getStockGroupRecs($selCategoryId, $selSubCategoryId);

				$newFieldArr = array();
				if ($chkCarton=='Y') {
					//"Packing(Kg x Nos)","Suitable For (Frozen Code)"
					$newFieldArr = array("No of Layers","Type of Carton","Brand","Color","Packing Weight","No.of Colors","Dimension","Carton Weight");			
				} else {
					$newFieldArr = array("Basic Unit","Basic Qty","Packed Qty","Min Order/Package");
				}

				if (sizeof($stockGroupRecs)>0) {
					foreach($stockGroupRecs as $sgr) {
						$stkLabelName	= $sgr[7];
						$stkFieldType	= $sgr[8];
						$stkFieldName	= $sgr[9];
						$stkFieldDefaultValue = $sgr[10];
						$stkFieldVDation = $sgr[12];
						$disSymbol = ($stkFieldVDation=='Y')?"*":"";
						$stkGroupDisplayName = $disSymbol.$stkLabelName;
						$stkGroupField = ($stkFieldType!="T")?$stkGroupDisplayName."($stkFieldDefaultValue)":$stkGroupDisplayName;
						array_push($newFieldArr,$stkGroupField);
					}
				}

				$fieldArr = array_merge($commonField, $newFieldArr);
				$csv->addRow($fieldArr);
				
				$filename = $categoryName."_".$subCategoryName.".csv";
				$csvContent = $csv->getAsCSV();		
				$dest_filename = $outputFileDirectory.$filename;				
				$handle = fopen($dest_filename, "w");
				fwrite($handle, $csvContent);
				fclose($handle);
				# Add File
				//$zip->addFile($dest_filename, $filename);
				$data = file_get_contents($dest_filename);				
				$zip->add_file($filename, $data);

			}
			if (file_exists($zipFileDir)) {
				$this->deleteDir($outputFileDirectory);
			}
			$zip->finish();
			//$zip->close();		
		}
		return $zipFilename;
	}

	function deleteDir($dirPath) 
	{
		if (is_dir($dirPath)) {
			if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
				$dirPath .= '/';
			}
			$files = glob($dirPath . '*', GLOB_MARK);
			foreach ($files as $file) {
				if (is_dir($file)) {
					self::deleteDir($file);
				} else {
					unlink($file);
				}
			}
			rmdir($dirPath);
			return true;
		} else return false;
	}



		# For Stock
		function readStockFromCSV($fileName, $incHead, $categoryId, $subCategoryId, $databaseConnect, $stockGroupObj)
		{
			$whr = " a.category_id=b.id and a.category_id='$categoryId' and a.id='$subCategoryId'";
			$orderBy	= " a.name asc ";				
			$qry	= "select a.id, a.category_id, a.name, a.description, b.name, a.unitgroup_id, a.check_point, a.carton, b.id from stock_subcategory a, stock_category b ";	
			if ($whr) 	$qry .= " where ".$whr;
			if ($orderBy)	$qry .= " order by ".$orderBy;
			//echo $qry;
			$stockCatNSubCatRecs	= $databaseConnect->getRecords($qry);

			$commonField = array("Name", "Description", "Re-order Required (Yes/No)", "Reorder Point", "Quantity in Stock", "Additional Holding Percent", "Stocking Period (Month)");	
			foreach ($stockCatNSubCatRecs as $scr) {
				$selCategoryId = $scr[8];
				$selSubCategoryId = $scr[0] ;
				$subCategoryName	= stripSlash($scr[2]);			
				$categoryName		= $scr[4];
				$chkCarton		= $scr[7];
				$stockType	= ($chkCarton=='Y')?"P":"O";

				$stockGroupRecs = $stockGroupObj->getStockGroupRecs($selCategoryId, $selSubCategoryId);

				$newFieldArr = array();
				if ($chkCarton=='Y') {
					//"Packing(Kg x Nos)","Suitable For (Frozen Code)"
					$newFieldArr = array("No of Layers","Type of Carton","Brand","Color","Packing Weight","No.of Colors","Dimension","Carton Weight");			
				} else {
					$newFieldArr = array("Basic Unit","Basic Qty","Packed Qty","Min Order/Package");					
				}

				// Group Recs
				if (sizeof($stockGroupRecs)>0) {
					foreach($stockGroupRecs as $sgr) {
						$stkGroupEntryId = $sgr[5];
						$stkLabelName	= $sgr[7];
						$stkFieldType	= $sgr[8];
						$stkFieldName	= $sgr[9];
						$stkFieldDefaultValue = $sgr[10];
						$stkFieldVDation = $sgr[12];
						$disSymbol = ($stkFieldVDation=='Y')?"*":"";
						$stkGroupDisplayName = $disSymbol.$stkLabelName;
						$stkGroupField = ($stkFieldType!="T")?$stkGroupDisplayName."($stkFieldDefaultValue)":$stkGroupDisplayName;
						$stkGroupField = array($stkGroupField,$stkFieldName,$stkGroupEntryId,$stkFieldType,$stkFieldVDation);
						array_push($newFieldArr,$stkGroupField);
					}
				}


				$fieldArr = array_merge($commonField, $newFieldArr);
			}


				


			$stockList = array();
			if ($fileName !="" ) {
				$fp		= fopen ($fileName,"r");
				$i=0; 
				while ($data = fgetcsv ($fp, 1000, ",")) {
					$i++;
					if( $incHead=='Y' && $i == 1 ) continue;
					$stockList[] = $data;
				}
				
			}
			return array($stockList, $fieldArr, $stockType);
		}

		function startsWith($haystack,$needle,$case=true) {
				if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
				return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
		}


}
?>