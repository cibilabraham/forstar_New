<?php
	require("lib/helper/zipstream.php");
	require("include/include.php");
	require("lib/helper/csv.php");
	$csvObj = new csvHelper();

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/


	

	# Upload file
	$errMsg = "";
	if ($p['cmdUpload']!='') {
	
		$tmpFile = $_FILES['uploadFile']['tmp_name'];
		$fileName = $_FILES['uploadFile']['name'];
		$fileNameWithoutExt = preg_replace("/\\.[^.\\s]{3,4}$/", "", $fileName);
		$fileSize = $_FILES['uploadFile']['size'];
		$fileType = $_FILES['uploadFile']['type'];
		$fileType = substr($fileName,strrpos($fileName,"."),strlen($fileName));
		$fileIncludesHeading = ( $p["incHeading"] == "" ) ? "N" : "Y";
		$stockType	= $p["stockType"];

		$sessObj->putValue("CategoryId","");
		$sessObj->putValue("SubCategoryId","");

		if ($fileNameWithoutExt!="") {
			$fileArr = explode("_",$fileNameWithoutExt);
			
			$categoryName = $subCategoryName = "";
			if (sizeof($fileArr)>0) {
				$categoryName	 = $fileArr[0];
				$subCategoryName = $fileArr[1];
				
				$categoryId		= $stockObj->getCategoryIdByName(trim($categoryName));
				$subCategoryId	= $stockObj->getSubCategoryIdByName(trim($subCategoryName));
				$sessObj->putValue("CategoryId",$categoryId);
				$sessObj->putValue("SubCategoryId",$subCategoryId);
			}
		}


		if( strtolower($fileType) == ".csv" )
		{
			$dt = Date('Ymd');
			$saveFileName = "StockList_$dt.csv";

			$folderName	= "stock_xls";
			$sessObj->putValue("fileName",$folderName."/".$saveFileName);	
			$sessObj->putValue("incHeading",$fileIncludesHeading);
			$sessObj->putValue("stockType",$stockType);			
			
			$uploadCheck = $fileManageObj->uploadFile($folderName, $tmpFile, $saveFileName);
			if( $uploadCheck ) header("Location:AssignStockCategories.php");
		}
		else $errMsg = "Invalid file type. Please select a valid csv file to upload." ;
	}


	// cmdGenerateCSV
	$csvMsg = "";
	if ($p['cmdGenerateCSV']!="") {
		$inventoryType	= $p["inventoryType"];
		$csvFolder		= "stock_xls";
		$categoryId		= $p["categoryId"];
		$categoryName	= "";
		if ($categoryId>0) {
			$categoryRec		=	$categoryObj->find($categoryId);
			$categoryName		=	stripSlash($categoryRec[1]);
		}

		$genCSVZipFileName = $importStockObj->generateStockCSVFormat($csvFolder, $databaseConnect, $csvObj, $stockGroupObj, $inventoryType, $categoryId, $categoryName);
	
		$downloadFilename = base64_encode($csvFolder."/".$genCSVZipFileName);

		if($genCSVZipFileName!=""){		
			$csvMsg = "<div style=\"border:1px solid green; padding: 10px; text-align: center;\"><span class=\"listing-item\" >Successfully created all the CSV Format.<br><br>Click <a href=\"DownloadFile.php?file=$downloadFilename&ext=zip\" title=\"Click here to download file.\">here</a> to download. </br></span></div>";
		}
			else $csvMsg = "<span class=\"listing-item\" ><font color='red'>Failed to create CSV Format. </font></span>";
	}


	# List all Category ;
	//$categoryRecords	=	$categoryObj->fetchAllRecords();

	$categoryRecords	=	$categoryObj->fetchAllRecordsActivecategory();


	$ON_LOAD_PRINT_JS	= "libjs/ImportStock.js";
	require("template/topLeftNav.php");
?>
<form name="FrmImportStock" action="ImportStock.php" method="post"  ENCTYPE="multipart/form-data">
<table cellspacing="0"  align="center" cellpadding="0" width="80%">
	<tr>
		<td height="40" >&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Step 1: Upload File</td>
							</tr>
							
							<tr>
								<td class="fieldName"  align='center' colspan="3" >
								<input type="submit" class='button'  name="cmdUpload" value="Upload" onClick="return validateStockImport();">
								</td>
							</tr>
							<? if($errMsg!="" ) {?>
							<tr>
								<td height="20" align="center" colspan="3" class="err1" ><?=$errMsg; ?></td>
							</tr>
							<?}?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" Style="padding-top:10px;padding-bottom:10px;" >
									<table cellpadding="0"  width="99%" cellspacing="1" border="0" align="center">
										<tr>
											<td nowrap class='fieldName' align='right'>*&nbsp;Select csv file:&nbsp;</td>
											<td nowrap ><input type="file" class='input-text' name='uploadFile' id='uploadFile' value='Browse' size=75 ></td>
										</tr>
		<tr>
			<td nowrap class='fieldName' align='right'></td>
			<td nowrap class='fieldName'>
				<input type="checkbox" id="incHeading" name="incHeading" value='Y' checked style="vertical-align:middle;" class="chkBox">&nbsp;File includes heading<!--&nbsp;&nbsp;&nbsp;<span class="listing-item">Stock Type:</span>&nbsp;
				<input name="stockType" id="stockType1" type="radio" value="P" <? if($stockType=='P') echo "Checked";?> class="chkBox" style="vertical-align:middle;">Packing&nbsp;
				<input name="stockType" id="stockType2" type="radio" value="O" <? if($stockType=='O') echo "Checked";?> class="chkBox" style="vertical-align:middle;">Ordinary-->
			</td>
		</tr>
	</table>
								</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" >
								<input type="submit" class='button'  name="cmdUpload" value="Upload" onClick="return validateStockImport();">
								</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" height='10'>
								
								</td>
							</tr>
					  </table>
					</td>
				</tr>
				
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<tr>
				<td height="20" align="center" ></td>
	</tr>
<!--  -->
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Download/Generate CSV File Format</td>
							</tr>
							
							<tr>
								<td class="fieldName"  align='center' colspan="3" >
								
								</td>
							</tr>
							<? if($errMsg!="" ) {?>
							<tr>
								<td height="20" align="center" colspan="3" class="err1" ><?=$errMsg; ?></td>
							</tr>
							<?}?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" Style="padding-top:10px;padding-bottom:10px;" >
									<table cellpadding="0"  width="99%" cellspacing="1" border="0" align="center">
										
		<tr>
			<td nowrap>
			<table align="center">
				<!--TR>
					<TD class="fieldName">
						<span class="listing-item">1.</span>&nbsp;<a href="#" onClick="return printWindow('DownloadStockFileFormat.php?stockType=P',700,600);" class="link1">Packing Stock Format</a> &nbsp;&nbsp;&nbsp;&nbsp;<span class="listing-item">2.</span>&nbsp; <a href="#" onClick="return printWindow('DownloadStockFileFormat.php?stockType=O',700,600);" class="link1">Ordinary Stock Format</a>
					</td>
				</TR-->
				<tr>
					<td>
						<table>
								<tr>									
									<td>
										<table border=0>
											<tr>
												<td>
													<table>
														<tr>
															<td class="fieldName">Category</td>
															<td nowrap>
																<select name="categoryId" id="categoryId" style="width:160px;" onchange="invTypeChange(1);">				
																	<option value="">-- Select --</option>
																	<?php
																	foreach ($categoryRecords as $cr) {
																		$categoryId	= $cr[0];
																		$categoryName	= stripSlash($cr[1]);
																		$selected = ($selCategoryId==$categoryId)?"Selected":"";
																	?>
																		<option value="<?=$categoryId?>" <?=$selected;?>><?=$categoryName;?></option>
																	<? }?>
																</select>
															</td>
														</tr>
													</table>													
												</td>
											</tr>
											<tr>
												<td class="fieldName" style="text-align:center;">[OR]</td>
											</tr>
											<tr>
												<td>
													<table>
														<tr>
															<td class="fieldName">Inventory Type</td>
															<td nowrap>
																<select name="inventoryType" id="inventoryType" onchange="invTypeChange(2);">
																		<option value="">--Select--</option>	
																		<option value="P">Carton</option>
																		<option value="N">Normal</option>
																		<option value="B">Both</option>
																</select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
									<td nowrap>
										<input type="submit" class='button'  name="cmdGenerateCSV" value="Generate" onClick="return validateGenerateCSV();">
									</td>
								</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td nowrap><?=$csvMsg?></td>
				</tr>
			</table>			
		</tr>
	</table>
								</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" >
								
								</td>
							</tr>
							<tr>
								<td class="fieldName"  align='center' colspan="3" height='10'>
								
								</td>
							</tr>
					  </table>
					</td>
				</tr>
				
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>
<!--  -->
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
						
					
				
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>	
  </table>
</form>
<?
require("template/bottomRightNav.php");
?>
