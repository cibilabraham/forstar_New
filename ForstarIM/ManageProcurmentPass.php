<?php
	require("include/include.php");
	require_once("lib/invoice_ajax.php");	

	ob_start();
	
	$ON_LOAD_SAJAX 		= "Y"; 

	$offset = 0;
	// $limit  = 25;
	
	
	
	
	if(isset($p['cmdUpdate']))
	{
		$alpha_code_prefix =  strtoupper($p['alpha_code_prefix']);
		$dateFrom  = explode('/',$p['idDateFrom']);
		$date_from = $dateFrom[2].'-'.$dateFrom[1].'-'.$dateFrom[0];
		
		$dateTo  = explode('/',$p['idDateTo']);
		$date_to = $dateTo[2].'-'.$dateTo[1].'-'.$dateTo[0];
				
		$updateArray = array('alpha_code_prefix'         => $alpha_code_prefix,
							 'date_from'         => $date_from,
							 'date_to'           => $date_to,
							 'number_from'       => $p['startNo'],
							 'number_to'         => $p['endNo'],
							 );
		 $id = 1;
		 $manageProcurmentPassObj->updateProcurmentPass($updateArray,$id);
		 $sessObj->createSession("displayMsg",$msgSuccUpdateGatePass);
		 $sessObj->createSession("nextPage",$url_afterGenerateGatePass.$selection);
	}
	
	$editRecords = array();
	$editRecords = $manageProcurmentPassObj->getRecords();	
	
	
	
	// print_r($idGenRecords);
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	// print_r($p);
?>
    <table width="60%" align="center" cellspacing="0" cellpadding="0">	
    <tbody><tr> 
      <td height="10" align="center">&nbsp;</td>
    </tr>
	<!--<tr> 
		<td height="10" align="center" style="color:Maroon;" class="listing-item">
			<strong>Challan restrictions for current financial year.</strong>
		</td>
	</tr>-->
	    <tr> 
      <td height="10" align="center" class="err1"> 
              </td>
    </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	<form method="post" action="ManageProcurmentPass.php" name="frmManageProcurmentPass">
	
	<tr> 
      <td> <table width="50%" border="0" bgcolor="#D3D3D3" align="center" cellspacing="1" cellpadding="0">
          <tbody><tr> 
            <td bgcolor="white"> 
              <!-- Form fields start -->
              <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0">
                <tbody><tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td width="581" background="images/heading_bg.gif" class="pageName" colspan="2">&nbsp; 
                    Procurment Gate Pass                  </td>
                </tr>
                <tr> 
                  <td width="1"></td>
                  <td colspan="2"> <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">
                      <tbody><tr> 
                        <td height="10"></td>
                      </tr>
                      <input type="hidden" name="updateID" id="updateID" value="<?php echo $editRecords[0];?>" />
                      
                      <tr>
                        <td colspan="2">&nbsp;</td>
                      </tr>
                      
				
				<tr>
					<td nowrap="true" class="fieldName">Gate Pass Prefix:</td>
					<td>
						<input type="text" id="alpha_code_prefix" name="alpha_code_prefix" value="<?php echo $editRecords[5];?>" />
					</td>
				</tr>
				
				


	<tr>
				<td nowrap="" class="fieldName">*Date range: From</td>
				<td>
				<?php
					$dateFrom  = explode('-',$editRecords[1]);
					$date_from = $dateFrom[2].'/'.$dateFrom[1].'/'.$dateFrom[0];
					$dateTo    = explode('-',$editRecords[2]);
					$date_to   = $dateTo[2].'/'.$dateTo[1].'/'.$dateTo[0];
				?>
					<input type="text" autocomplete="off" value="<?php echo $date_from;?>" size="9" id="idDateFrom" name="idDateFrom" required>
				</td>
				<td nowrap="" class="fieldName">To</td>
				<td>
					<input type="text" autocomplete="off" value="<?php echo $date_to;?>" size="9" id="idDateTo" name="idDateTo" required>
				</td>
			</tr>
			<tr>
				<td nowrap="" class="fieldName">*Number accepted: From</td>
				<td><input type="text" maxlength="10" value="<?php echo $editRecords[3];?>" size="10" name="startNo" required></td>
				<td nowrap="" class="fieldName">To</td>
				<td><input type="text" maxlength="10" value="<?php echo $editRecords[4];?>" size="10" name="endNo" required></td>
			</tr>
			
	
         
                      
                      <tr> 
                        <td height="10"></td>
                      </tr>
                      <tr> 
						<td align="center" colspan="4"> 
						<input type="button" onclick="return cancel('ManageProcurmentPass.php');" value=" Cancel " class="button" name="cmdCancel"> 
                          &nbsp;&nbsp; <input type="submit"  value=" Update " class="button" name="cmdUpdate">
						  </td>
                                              </tr>
                      <tr> 
                        <td height="10"></td>
						
                      </tr>
                    </tbody></table></td>
                </tr>
              </tbody></table></td>
          </tr>
        </tbody></table>
        <!-- Form fields end   -->
      </td>
    </tr>
	 </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	
	
	 </tr>	
        <tr> 
      <td height="10" align="center"></td>
    </tr>
	
	</form>
	

    <tr> 
      <td height="10"></td>
    </tr>
  </tbody></table>
	
 	<script type="text/javascript" language="JavaScript">
	Calendar.setup 
	(	
		{
			inputField  : "idDateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	</script>
	<script type="text/javascript" language="JavaScript">
	Calendar.setup 
	(	
		{
			inputField  : "idDateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "idDateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	</script>

	<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
	?>