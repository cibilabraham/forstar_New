<?
	require("include/include.php");
	$disableFilter					=	$text_disableTrue;
	# List records based on filter 
	$recordsFilterId				=	$p["selFilter"];
	
	if( $recordsFilterId!=0 ){	
		$fish2GradeMasterRecords	=	$fish2grademasterObj->fish2GradeRecFilter($recordsFilterId);
	}
	else{
		$fish2GradeMasterRecords	=	$fish2grademasterObj->fetchAllRecords();
	}
	
	$fish2GradeMasterRecordsSize	=	sizeof($fish2GradeMasterRecords);



	# Returns all fish master records 
	$fishMasterRecords		=	$fishmasterObj->fetchAllRecords();

?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" >&nbsp;Fish Grade Master</td>
								<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
											<tr>
											
											<td class="listing-item" > Grades for:&nbsp;</td>
											<td>
												<select name="selFilter" onChange="this.form.submit();" <?=$disableFilter;?>>
													<option value="0"> All Fish </option>
													<? 
														if( sizeof($fishMasterRecords)>0 )
														{
															foreach ($fishMasterRecords as $fl)
															{
																$fishId		=	$fl[0];
																$fishName	=	$fl[1];
																
																$selected	=	"";
																if( $fishId == $recordsFilterId ){
																	$selected	=	"selected";
																}
															
													?>
													<option value="<?=$fishId;?>" <?=$selected;?> ><?=$fishName;?> </option>
													<?
															}
														}
													?>

												</select>
											</td>
											<td width="1">&nbsp;</td>
										</tr>
										
									</table>
								</td>
							</tr>
							
							
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" >
									<table cellpadding="1"  width="95%" cellspacing="0" border="0" align="center" bgcolor="#f2f2f2">
										
										<tr  bgcolor="#f2f2f2"  >
											<td width="20"></td>
											<td class="listing-head" width="130" nowrap >Grade</td>
											<td class="listing-head" >Unit</td>
											<td class="listing-head" nowrap width="150">
											
											</td>
											<td width="10"></td>
										</tr>
										<?
											if( sizeof($fish2GradeMasterRecords) > 0 )
											{
												$i	=	0;

												foreach($fish2GradeMasterRecords as $fgr)
												{
												
													
													$i++;
													$fish2gradeId	=	$fgr[0];
													$fishId			=	$fgr[1];
													$gradeId		=	$fgr[2];
													$gradeCode		=	$fgr[7];
													$unit			=	$fgr[10];
													
										?>
										<tr  bgcolor="WHITE"  >
											<td width="20"></td>
											<td class="listing-item" width="130" nowrap ><?=$gradeCode;?></td>
											<td class="listing-item" ><?=$unit;?></td>
											<td class="listing-item" width="30"></td>
											<td width="10"></td>
										</tr>
										<?
												}
										?>
											
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										<?
											}
											else
											{
										?>
										<tr bgcolor="white">
											<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
										<tr bgcolor="white">
											<td colspan="7"  height="10" ></td>
										</tr>
										
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
							
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
