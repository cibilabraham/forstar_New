<?php
	require("include/include.php");
	require("lib/config_query.php");

	$objCon          = new Config_query($databaseConnect);
	$tableName       = 'm_lotid_process_type';
	if(isset($p['cmdDelete']))
	{
		$delIds = '';
		$rowcount = $p['hidRowCount'];
		for($i = 1;$i<=$rowcount;$i++)
		{
			if(isset($p['delId_'.$i]))
			{
				if($delIds == '')
					$delIds.= $p['delId_'.$i];
				else
					$delIds.= ','.$p['delId_'.$i];
			}
		}
		if($delIds != '')
		{
			$where = 'WHERE id IN ('.$delIds.')';
			$objCon->deleteData($tableName,$where,'deleteRows');
		}
	}
	if(isset($p['cmdAdd']) && $p['cmdAdd'] == 'Add')
	{
		$where = array('process_type' => $p['process_type']);
		$processTypes = $objCon->getItems($tableName,'*',$where);
		if(sizeof($processTypes) > 0)
		{
			$errMsg = 'Process type already exists';
		}
		else
		{
			$slno = 'SL00';
			$lastRecordIds = $objCon->getItems($tableName,'MAX(id) as id');
			if(sizeof($lastRecordIds) > 0)
			{
				$slno.= $lastRecordIds[0]['id'] + 1;
			}
			$dataArray = array('slno' => $slno,'process_type' => $p['process_type']);
			$objCon->addData($tableName,$dataArray);
			$succMsg = 'Process type added successfully';
		}
	}
	if(isset($p['cmdUpdate']) && $p['cmdUpdate'] == 'Update')
	{
		$dataArray = array('process_type' => $p['process_type']);
		$where = array('id' => $p['editId']);
		$objCon->updateData($tableName,$dataArray,$where);
		$succMsg = 'Process type updated successfully';
	}
	if(isset($p['editId']) && $p['editId'] != '')
	{
		$where = array('id' => (int)$p['editId']);
		$editProcessTypes = $objCon->getItems($tableName,'*',$where);
		//print_r($editProcessTypes);
	}
	$processTypes = $objCon->getItems($tableName);
	// echo '<pre>';
	// print_r($processTypes);
	// echo '</pre>';
	require("template/topLeftNav.php");
	//print_r($p);
?>
<form name="frmManageLotidprocesstype" action="lotidprocesstype.php" method="post">
  <table width="100%" align="center" cellspacing="0" cellpadding="0">
    <tbody><tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
    
	<?php
		if(isset($succMsg))
		{
	?>
		<tr> 
		<td height="10" align="center" style="color:Maroon;" class="listing-item">
			<strong><?php echo $succMsg;?></strong>
		</td>
	</tr>
	<?php
		}
	?>
	
	<?php
		if(isset($errMsg))
		{
	?>
		<tr> 
		<td height="10" align="center" style="color:Maroon;" class="listing-item">
			<strong><?php echo $errMsg;?></strong>
		</td>
	</tr>
	<?php
		}
	?>
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	<?php
	if(isset($p['editId']) && $p['editId'] != '')
	{
	?>
		<tr> 
      <td> <table width="90%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; 
                     Update Process Type                  </td>
                </tr>
                <tr> 
                  <td width="1"></td>
                  <td colspan="2"> 
				  <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> <input type="button" onclick="return cancel('lotidprocesstype.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" onclick="return validateProcessType();" value="Update" class="button" name="cmdUpdate">                        </td>
                                              </tr>
                      <input type="hidden" value="" name="hidRoleId">
                      <tr>
                        <td nowrap="" height="10" align="center" colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td nowrap="" align="center" class="fieldName" colspan="2">
			<table width="500">
                          <tbody><tr>
                            <td nowrap="" class="fieldName">*  Process Type : </td>
			    <td>
				<input type="text" required size="23" id="process_type" name="process_type"  
				<?php 
					if(isset($_REQUEST['process_type'])) { echo 'value="'.$_REQUEST['process_type'].'"'; }
					else if(isset($editProcessTypes[0]['process_type'])) { echo 'value="'.$editProcessTypes[0]['process_type'].'"'; }
				?> 
					/>
				</td>
				<td>
				<span id="process_type_error"></span>
				<input type="hidden" id="process_type_check" name="process_type_check">
				<input type="hidden" id="editId" name="editId" value="<?php echo $p['editId'];?>">
				</td>
                          </tr>
                         
                    </tbody></table></td>
                 </tr>
                      <tr>
                        <td nowrap="">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
         </table>
</td>
                      </tr>
                      <tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> <input type="button" onclick="return cancel('lotidprocesstype.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" value="Update" class="button" onclick="return validateProcessType();" name="cmdUpdate">                        </td>
                                              </tr>
                     
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        <tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
	<?php	
	}
	?>
	<?php
	if (isset($p["cmdAddNew"]) && $p["cmdAddNew"] != "") {
	?>
		<tr> 
      <td> <table width="90%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; 
                     Add New Process Type                  </td>
                </tr>
                <tr> 
                  <td width="1"></td>
                  <td colspan="2"> 
				  <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> <input type="button" onclick="return cancel('lotidprocesstype.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" onclick="return validateProcessType();" value="Add" class="button" name="cmdAdd">                        </td>
                                              </tr>
                      <input type="hidden" value="" name="hidRoleId">
                      <tr>
                        <td nowrap="" height="10" align="center" colspan="2">&nbsp;</td>
                      </tr>
                      <tr>
                        <td nowrap="" align="center" class="fieldName" colspan="2">
			<table width="500">
                          <tbody><tr>
                            <td class="fieldName">*  Process Type : </td>
			    <td>
				<input type="text" size="23" id="process_type" name="process_type" required 
				<?php 
					if(isset($_REQUEST['process_type'])) { echo 'value="'.$_REQUEST['process_type'].'"'; }
				?>  
				 />
				

				<span id="process_type_error"></span>
				<input type="hidden" id="process_type_check" name="process_type_check">
				</td>
                          </tr>
                         
                    </tbody></table></td>
                 </tr>
                      <tr>
                        <td nowrap="">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
         </table>
</td>
                      </tr>
                      <tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
                      <tr> 
                                                <td align="center" colspan="2"> <input type="button" onclick="return cancel('lotidprocesstype.php');" value=" Cancel " class="button" name="cmdAddCancel"> 
                          &nbsp;&nbsp; <input type="submit" value="Add" class="button" onclick="return validateProcessType();" name="cmdAdd">                        </td>
                                              </tr>
                     
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        <tr> 
                        <td height="10" colspan="2"></td>
                      </tr>
	<?php
	}
	?>
    <tr> 
      <td> <table width="80%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp;Manage 
                    Lot ID process type </td>
                </tr>
                <tr> 
                  <td height="10" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td>
						<input type="submit" onclick="return confirmDelete(this.form,'delId_',<?php echo sizeof($processTypes);?>);" name="cmdDelete" class="button" value=" Delete ">                          &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                        <!--<input type="button" onclick="return printWindow('PrintManageRole.php',700,600);" class="button" name="btnPrint" value=" Print "></td>-->
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                                <tr> 
                  <td width="1"></td>
                  <td style="padding-left:5px;padding-right:5px;" colspan="2">
<table width="50%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="1">
                                            <tbody><tr bgcolor="#f2f2f2" align="center"> 
                        <td width="20"><input type="checkbox" class="chkBox" onclick="checkAll(this.form,'delId_'); " id="CheckAll" name="CheckAll"></td>
                        <td nowrap="" style="padding-left:10px;padding-right:10px;" class="listing-head">Slno</td>
                        <td style="padding-left:10px;padding-right:10px;" class="listing-head">Process Type</td>
						<td style="padding-left:10px;padding-right:10px;" class="listing-head">Updated Date</td>
						<td width="45" class="listing-head"></td>
						                      </tr>
		                       
						<?php
							$i = 1;
							if(sizeof($processTypes) > 0)
							{
								foreach($processTypes as $types)
								{
						?>
                                <tr bgcolor="WHITE"> 
								<td width="20" height="25" align="center">
								<input type="checkbox" class="chkBox" value="<?php echo $types['id'];?>" id="delId_<?php echo $i;?>" name="delId_<?php echo $i;?>">
								</td>
								<td id="slno_<?php echo $i;?>" nowrap="" style="padding-left:10px;padding-right:10px;" class="listing-item">
								<?php echo $types['slno'];?>
								</td>
								<td nowrap="" style="padding-left:10px;padding-right:10px;" class="listing-item">
								<?php echo $types['process_type'];?>
								</td>
								<td nowrap="" style="padding-left:10px;padding-right:10px;" class="listing-item">
								<?php echo $types['update_date'];?>
								</td>
						                        <td width="45" align="center" class="listing-item"><input type="submit" onclick="assignValue(this.form,<?php echo $types['id'];?>,'editId'); this.form.action='lotidprocesstype.php';" name="cmdEdit" value=" Edit "></td>
						                      </tr>
											
						<?php 
									$i++;
								}
							}
						?>
                                 <input type="hidden" value="<?php echo sizeof($processTypes);?>" id="hidRowCount" name="hidRowCount">           
                                  <input type="hidden" name="editId" value="<?php echo $p['editId'];?>">         
					  			    	                      </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
                <tr> 
                  <td colspan="3"> <table align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td>
						<input type="submit" onclick="return confirmDelete(this.form,'delId_',<?php echo sizeof($processTypes);?>);" name="cmdDelete" class="button" value=" Delete ">                          &nbsp;<input type="submit" class="button" name="cmdAddNew" value=" Add New "> 
                        <!--<input type="button" onclick="return printWindow('PrintManageRole.php',700,600);" class="button" name="btnPrint" value=" Print "></td>-->
                      </tr>
                    </tbody></table></td>
                </tr>
                <tr> 
                  <td height="5" colspan="3"></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <tr> 
      <td height="10"></td>
    </tr>
<input type="hidden" value="" id="hidAddMode" name="hidAddMode">
  </tbody></table>
	</form>
<?php 
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>

<script>
jQuery(document).ready(function(){
	jQuery('#process_type').keyup(function(){
		var process_type_already = '';
		var process_type = jQuery(this).val();
		<?php 
		if(isset($editProcessTypes[0]['process_type'])) 
		{
		?>
			process_type_already = '<?php echo $editProcessTypes[0]['process_type'];?>';
		<?php
		}
		?>
		// alert('hi');
		jQuery.post('checkProcessType.php',{process_type:process_type},function(data){
			// alert('hi');
			// alert(data);
			if(data != '')
			{
				jQuery('#process_type_error').html(data);
				jQuery('#process_type_check').val('1');
			}
			else
			{
				jQuery('#process_type_error').html('');
				jQuery('#process_type_check').val('');
			}
		});
	});
});
	function validateProcessType()
	{
		var process_type_check = jQuery('#process_type_check').val();
		if(process_type_check == 1)
		{
			alert('Process type already exists');
			return false;
		}
	}
	
	function cancel(fileName)
	{
		var con=confirm('Do you want to cancel?');
		if(con==true)
		{
			window.location = fileName;
		}
	}
</script>