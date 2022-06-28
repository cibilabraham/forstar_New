<?php
	require("include/include.php");
	require_once("lib/invoice_ajax.php");	

	ob_start();
	
	$ON_LOAD_SAJAX 		= "Y"; 

	$offset = 0;
	// $limit  = 25;
	
	$sealNoList   = $ManageProcurementGatePassObj->getAllSealNos();
	$employeeList = $ManageProcurementGatePassObj->getAllEmployee();
	
	$getPassList = $ManageProcurementGatePassObj->getAllPassList();
	
	require("template/topLeftNav.php");
?>
<form method="post" action="Managelotid.php" name="frmManagelotid">
	
    <tr> 
      <td> <table width="80%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName">&nbsp;Procurement Gate Pass  </td>
                 
                </tr>
		<tr> 
                  <td height="10" colspan="3"></td>
                </tr>
		
                <tr> 
                  <td height="10" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td><input type="submit" onclick="return confirmDelete(this.form,'delId_',3);" name="cmdDelete" class="button" value=" Delete ">                           &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                          &nbsp;
						  <!--<input type="button" onclick="return printWindow('PrintManageChallan.php?filterFunctionType=',700,600);" class="button" name="btnPrint" value=" Print ">-->
						  </td>
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                                <tr> 
                  <td width="1"></td>
                  <td style="padding-left:10px;padding-right:10px;" colspan="2"> 
			<table width="90%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="1">
                                            <tbody><tr bgcolor="#f2f2f2" align="center"> 
                        <td width="20" rowspan="2">
				<input type="checkbox" class="chkBox" onclick="checkAll(this.form,'delId_'); " id="CheckAll" name="CheckAll">
			</td>
                        <td nowrap="" rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Gate Pass ID</td>

						  <td nowrap="" rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Process Type</td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Date</td>
                        <td colspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Number</td>
			 <td rowspan="2" style="padding-left:10px; padding-right:10px;" class="listing-head">Updated Date</td>
				                        <td width="50" rowspan="2" class="listing-head"></td>
			                      </tr>
			<tr bgcolor="#f2f2f2" align="center">
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">From</td>
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">To</td>	
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">From</td>
				<td style="padding-left:10px; padding-right:10px;" class="listing-head">To</td>
			</tr>
			<?php
				if($getPassList > 0)
				{
					$i = 1;
					foreach($getPassList as $row)
					{
			?>
            <tr bgcolor="WHITE"> 
                <td width="20" height="25">
				<input type="checkbox" class="chkBox" value="<?php echo $row[0];?>" id="delId_<?php echo $i;?>" name="delId_<?php echo $i;?>">
				</td>
                <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[1];?>
				</td>
				<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[9];?>
				</td>
				<td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[2];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[3];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[4];?>
				</td>
			    <td nowrap="" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[5];?>
				</td>
				<td nowrap="" align="center" style="padding-left:10px; padding-right:10px;" class="listing-item">
				<?php echo $row[8];?>
				</td>
			    <td width="40" align="center" class="listing-item">
				<input type="submit" onclick="assignValue(this.form,<?php echo $row[0];?>,'editId'); assignValue(this.form,'<?php echo $i;?>','editSelectionChange');this.form.action='Managelotid.php';" name="cmdEdit" value="Edit">
				</td>
			</tr>
			<?php
					$i++;
					}
				}
				 else {
			?>
                      <tr bgcolor="white"> 
                        <td colspan="9"  class="err1" height="10" align="center"> 
						No records found
                         </td>
                      </tr>
                      	<?php
				}
			?>
                      	                      
                <input type="hidden" value="<?php echo $idGenRecordsSize;?>" id="hidRowCount" name="hidRowCount">
                <input type="hidden" value="" name="editId">
				<input type="hidden" value="0" name="editSelectionChange">
                 </tbody>
				 </table>
				 </td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td><input type="submit" onclick="return confirmDelete(this.form,'delId_',3);" name="cmdDelete" class="button" value=" Delete ">                           &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                          &nbsp;
						  <!--
						  <input type="button" onclick="return printWindow('PrintManageChallan.php?filterFunctionType=',700,600);" class="button" name="btnPrint" value=" Print ">-->
						  </td>
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3">
			<input type="hidden" value="" name="hidRecId">
			<input type="hidden" value="" name="hidFilterFunctionType">
		  </td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
	</form>