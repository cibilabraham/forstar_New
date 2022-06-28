<?php
require("include/include.php");
$created_by = $userObj->getUserName($userId);
 function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
require("template/topLeftNav.php");
//print_r($p);
# script to execute backup 
	# db backup  
	if ($p["cmdcreatebackup"]!="") {
		/* rekha development work */
		$var_Backup_dir = $p["dir_bakup"]; 
		$t_flag = $CreateDBBackupObj->CreateDbBackupScript($userId,$DatabaseListObj,$created_by,$var_Backup_dir);
		if($t_flag='1'){
		  header("location:DatabaseListMaster.php");
		}
		else{
			echo "transaction Not completed.";
		}
		exit;
	}	
	#end code 
?>
<!-- tag here -->
	<form name="frmcreatedbbackup" action="createdatabase_bakup.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr>
			<td height="10" align="center" ></td>
		</tr>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Database Backup";
								include "template/boxTL.php";
							?>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="4" height="5" >
									</td>
								</tr>
		<tr>
			<td colspan="4" height="10" align='center'> <a href='DatabaseListMaster.php' class="link1"><strong>Database Backup List</strong></a></td>
		</tr>
		<tr>
			<td colspan="4" height="5" align='center'></td>
		</tr>
												
		<tr>
			<td colspan="4" height="5" ></td>
		</tr>
		<tr >	
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td>
							 <strong>Backup Directory:</strong> <input type='text' value='F:/DailyBackup/Databases' id='dir_bakup' name='dir_bakup' size='30' style='height: 30px;padding: 6px 6px;box-sizing: border-box;border: 2px solid #ccc;border-radius: 4px;background-color: #f8f8f8;'>
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<input type='submit' name='cmdcreatebackup' id='cmdcreatebackup' value='Generate Database Backup File' class="button" onclick="javascript: return confirm('You want to Generate Database Backup File? \n If yes click on OK and If no click on Cancel button.');" style='height:30;font-size:11px;'>&nbsp;&nbsp;<input type='button' name='cmdcancel' id='cmdcancel' value='Cancel' class="button" onclick="javascript: window.location.href='DatabaseListMaster.php';" style='height:30;font-size:11px;'></td>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		
		<tr>
			<td colspan="4" height="5" ></td>
		</tr>								
		</table>
		<?php
			include "template/boxBR.php"
		?>
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
	
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
