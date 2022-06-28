<?php 
	require("include/include.php");
	require("template/topLeftNav.php");
?>
<!-- tag here -->
	<form name="frmFishMaster" action="FishMaster.php" method="post">
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
								$bxHeader="Title Here";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="4" height="5" ></td>
								</tr>

								<tr>
									<td width="1" ></td>
									<td colspan="2" >
							<table  cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">							
								<thead>
									<tr>
										<td colspan="6" align="right" style="padding-right:10px;">
										<div align="right" class="navRow">
										kkek
										</div>
										</td>
										
										</tr>
										<tr>
										<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
										<th nowrap>Code</th>
										<th>Name</td>
										<th nowrap>Category </th>
									</tr>
								</thead>
							<tbody>
								<tr>
									<td align="right" style="padding-right:10px" colspan="6" class="navRow">
									</td>
								</tr>	
							</tbody>
		<tr bgcolor="white">
			<td colspan="5"  class="err1" height="10" align="center">fkkfk</td>
		</tr>	
		</table>
			</td>
		</tr>
		<tr>
			<td colspan="4" height="5" ></td>
		</tr>
		<tr >	
			<td colspan="4">
				<table cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td>buttons here </td>
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
<!-- end code -->
<?php
	require("template/bottomRightNav.php");
?>
