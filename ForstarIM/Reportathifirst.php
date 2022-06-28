<?
	require("include/include.php");
	require_once('lib/Report_ajaxathi.php');
	
	
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	$genReqNumber	= "";

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	// if (!$accesscontrolObj->canAccess()) {
		////echo "ACCESS DENIED";
		// header("Location: ErrorPage.php");
		// die();
	// }
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
		if ($p["search"]!="") {

		$companyName		=$p["companyName"];
		$reportName		    =$p["reportName"];
		$transcationName	=$p["transcationName"];
		$reportField		=$p["reportField"];
		$cnt=count($reportField);
		for($i=0; $i<$cnt; $i++)
		{
		if($i==0)
		{
		$value=$reportField[$i];
		}
		else
		{
		$value=$value.','.$reportField[$i];
		}
		$fld=$value;
		}
		
		switch($transcationName)
		{
		case 1:
			{
			$query="SELECT $fld
			FROM t_phtcertificate a
			LEFT JOIN m_fishcategory b ON ( a.species = b.id ) 
			LEFT JOIN m_supplier_group c ON ( a.supplier_group = c.id ) 
			LEFT JOIN supplier d ON ( a.supplier = d.id ) 
			LEFT JOIN m_pond_master e ON ( a.pond_Name = e.id ) ";
			//$query="select $fld from t_phtCertificate";
			$result	=$reportathiObj->fetchAllReportRecords($query);
			$fieldName=Array('a.PHTCertificateNo'=>'PHT Certificate No','b.category'=>'Species','c.supplier_group_name'=>'Supplier group','d.name'=>'Supplier','e.pond_name'=>'Pond Name','e.pond_qty'=>'PHT Quantity',' a.date_of_issue'=>'Date of Issue','a.date_of_expiry'=>'Date of expiry','a.received_date'=>'Received date');
			}
		case 2:
			{
				$tableName="t_phtmonitoring";
				break;
			}
		case 3:
			{
				$tableName="t_rmprocurmentorder";
				break;
			}
		case 4:
			{
				//$tableName="t_rmtestdata";
				$fieldName=Array('unit'=>'Unit Name','lot'=>'Lot id','test_name'=>'Test name','test_method'=>'Test Method','date_of_testing'=>'Date of Testing','result'=>'Result');
				break;
			}
		case 5:
			{
				$tableName="t_rmreceiptgatepass";
				break;
			}
		case 6:
			{
				$tableName="t_soaking";
				break;
			}
		case 7:
			{
				$tableName="t_unittransfer";
				break;
			}
		case 8:
			{
				$tableName="weighment_data_sheet";
				break;
			}
		case 9:
			{
				$tableName="t_rmweightaftergrading";
				break;
			}
		case 10:
			{
				$tableName="t_dailypreprocess_entries";
				break;
			}
		case 11:
			{
				$tableName="t_dailyfrozenpacking_main";
				break;
			}
			
		}
		
		// if ($PHTCertificateNo!="") {
			// $phtCertificateRecIns	=	$phtCertificateObj->addPHTCertificate($PHTCertificateNo,$species,$supplierGroup,$supplier,$pondName,$phtQuantity,$dateOfIssue,$dateOfExpiry,$receivedDate, $userId);

	
		// }
	}
	
	# List all records
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	//$rmLotId	= $unitTransferObj->fetchAllRecords();
	$comapanyDetails	= $billingCompanyObj->fetchAllRecords();
	$transcationNameRecords	= Array('1'=>'PHT Certificate','2'=>'PHT Monitoring','3'=>'RM Procurment order','4'=>'RM TestData','5'=>'RM Receipt Gate Pass','6'=>'Soaking','7'=>'Unit Transfer','8'=>'Weightment Data Sheet','9'=>'RM Weightment After Grading','10'=>'Pre Processing',
	'11'=>'Frozen Packing','12'=>'','13'=>'','14'=>'','15'=>'','16'=>'','17'=>'','18'=>'','19'=>'','20'=>'',
	'21'=>'','22'=>'','23'=>'','24'=>'','25'=>'','26'=>'','27'=>'','28'=>'','29'=>'','30'=>'',
	'31'=>'','32'=>'','33'=>'','34'=>'','35'=>'','36'=>'','37'=>'','38'=>'','39'=>'','40'=>'');
	
	
	//if ($editMode) $heading	=	$label_editSoaking;
	//else $heading	=	$label_addSoaking;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/Soaking.js"; // For Printing JS in Head section

	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmReport" action="" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											
											<input type="hidden" name="hidSoakingId" value="<?=$editsoakngDataId;?>">
											
										
											<tr>
											  <td colspan="2" nowrap class="fieldName" >
			
				<table width="200" align="center">
					
					
							<tr>
								   <td class="fieldName" nowrap>*Company Name:</td>
								 
												<td  height="10" ><select name="companyName" id="companyName" >
											  <option value="">--select--</option>
											  <?
												foreach($comapanyDetails as $un)
													{
														$companyId		=	$un[0];
														$comapanyName	=	stripSlash($un[1]);
															$selected = ($companyName==$companyId)?"selected":""
														
											?>
											  <option value="<?=$companyId?>" <?=$selected?>><?=$comapanyName?></option>
											  <? }?>
										        </select>										      </td>
								</tr>
								
								<tr>
                                	<td class="fieldName" nowrap>*Report Name:&nbsp;</td>
                                     <td><INPUT TYPE="text" NAME="reportName" id="reportName" size="15" value="<?=$reportName?>"></td>
										
							   </tr>
							
							<tr>
							   <td class="fieldName" nowrap>*Transaction Name:</td>
							   <td  height="10" ><select name="transcationName" id="transcationName" onchange="xajax_getField(document.getElementById('transcationName').value,'');">
											  <option value="">--select--</option>
											  <?
												foreach($transcationNameRecords as $transcationId => $transactionName)
													{
														// $transcationId		=	$un[0];
														// $transactionName	=	stripSlash($un[1]);
															//$selected = ($transcationName==$transcationId)?"selected":""
														
											?>
											  <option value="<?=$transcationId?>" <?=$selected?>><?=$transactionName?></option>
											  <? }?>
										        </select>									      </td>
							   
							</tr>
												
						  
							  <tr>
                                	<td class="fieldName" align='right'>*Report  Field:&nbsp;</td>
                                         <td  height="10" ><select name="reportField[]"  id="reportField" multiple style="width:150px" >
											 
											  
										        </select>										      </td>
                                                </tr>
												
							 
												
							
                </table>
								  
											  
											  
											  </td>
					  </tr>
					<tr>
					  <td colspan="2">&nbsp;</td>
					</tr>					
	

	
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
	

												<td colspan="2" align="center">
												<input type="submit" name="search" class="button" value=" Search">&nbsp;&nbsp;
																						</td>
												
												
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	

		
				
		
		<tr>
			<td height="10"></td>
		</tr>
		<?php if(isset($_POST['search']))
		{
		?>
		<tr>
			<td align="center"> <h1><?php echo $reportName;?></h1> </td>
		</tr>
		<tr>
			<td align="center"> <h2><?php $company=$reportObj->findCompany($companyName);
											echo $company[0];
										?></h2> </td>
		</tr>
		<tr>
			<td>
			<table width="99%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="0" class="print">
				<tbody>
				<tr bgcolor="#f2f2f2" align="center">
				
				
				<?php 
				
				foreach($reportField as $rId)
				{
					if(isset($fieldName[$rId]))
					{
					?>
					<th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head"><?php echo $fieldName[$rId];?> </th>
					<?php
					}
					else
					{
					?>
					 <th width="20%" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" class="listing-head">&nbsp;</th>
					<?php
					}
					?>
				<?php
				}
				?>
				</tr>
				<?php 	foreach($result as $selrecords)
				{?>
				<tr bgcolor="#FFFFFF"> 
				<?php
				for($i=0; $i<$cnt; $i++)
				{
				?>
                <td nowrap="" height="25" style="padding-left:5px; padding-right:5px;" class="listing-item"><?php echo $selrecords[$i];?></td>
               <?php
			   } $selrecords[$i]="";
			   ?>
				</tr>
				<?php
				}
				?>				
					<!--<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="center">
				TOTAL<br/>
				<span class="listing-item" style="line-height:normal;font-size:7px;">()</span>
			</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>0.00</strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>0.00</strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			0.00</strong></td>
		</tr>-->
				
				</tbody>
			</table>
			</td>
		</tr>
		<?php
		}
		?>
	</table>


	
	
	
	

	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>