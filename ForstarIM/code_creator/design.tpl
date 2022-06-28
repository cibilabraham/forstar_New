<flexy:include src="../../ftemplate/topL.php" />

<form name="frm{modelName}" action="{routingPagePrefix}.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">
		<tr flexy:if="err">
			<td height="40" align="center" class="err1" >{err}</td>
		</tr>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>							
								{setBoxHeader(#{routingPagePrefix}#)}
								<flexy:include src="../../ftemplate/boxTL.tpl" />					
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="70%" align="center">		
		<tr flexy:if="showEntry()">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%">
					<tr>
						<td>			
							
							{setSubBoxHeader(heading)}
							<flexy:include src="../../ftemplate/rbTop.tpl" />
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10" ></td>
											</tr>
											<tr>							
												<td align="center" flexy:if="editMode">
												<input flexy:ignore="yes" type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('{routingPagePrefix}.php');">&nbsp;&nbsp;
												<input flexy:ignore="yes" type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validate{modelName}(document.frm{modelName});">	
												</td>												
												<td align="center" flexy:if="addMode">
												<input flexy:ignore="yes" type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('{routingPagePrefix}.php');">&nbsp;&nbsp;
												<input flexy:ignore="yes" type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validate{modelName}(document.frm{modelName});">
												</td>
											</tr>
											<input type="hidden" name="hid{modelName}Id" value="{editId}">
											<tr>
											  <td nowrap class="fieldName">											  </td>
										  </tr>
											

											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;"> 
			<table width="70%" border="0">
				<tr>
				<TD>
					<table>
						<TR>
						<TD valign="top">
						<table>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Machinery</td>
							<td class="listing-item">
								<input name="data[{tableName}][name]" type="text" id="machinery" size="28" value="" onblur="xajax_chkICExist(document.getElementById('machinery').value, '{editId}');">
								{if:editMode}
								<input type="hidden" name="data[{tableName}][id]" value="" readonly>
								{end:}
							</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">Description</td>
								<td class="listing-item"><textarea name="data[{tableName}][description]" id="description"></textarea></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Type of Operation</td>
								<td class="listing-item">
									<select name="data[{tableName}][operation_type_id]" id="operationType"></select>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Capacity</td>
								<td class="listing-item">
									<input name="data[{tableName}][capacity]" type="text" id="capacity" size="5" value="" style="text-align:right;" autocomplete="off">
								</td>
							</tr>
						</table>
						</TD>
						<td>&nbsp;</td>
						<td valign="top">
						<table>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Unit</td>
								<td class="listing-item">
									<select name="data[{tableName}][unit_id]" id="unitId"></select>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Per</td>
								<td class="listing-item" nowrap>
									<input name="data[{tableName}][per_val]" type="text" id="perVal" size="5" value="" style="text-align:right;" autocomplete="off">
									&nbsp;HR
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Monitor</td>
								<td class="listing-item">
									<select name="data[{tableName}][monitor]" id="monitor">
										<option value="">--Select--</option>
										<option value="S">SINGLE</option>
										<option value="M">MULTIPLE</option>
									</select>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Monitoring Parameters</td>
								<td class="listing-item">
									<select name="data[{tableName}][monitoring_parameter_id]" id="monitoringParameter"></select>
								</td>
							</tr>	
						</table>
						</td>
						</TR>
					</table>
				</TD>
				</tr>
                                          </table>
					</td>
					</tr>
											<tr>
											  <td colspan="2" height="5"></td>
										  </tr>
											<tr>							
												<td align="center" flexy:if="editMode">
												<input flexy:ignore="yes" type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('{routingPagePrefix}.php');">&nbsp;&nbsp;
												<input flexy:ignore="yes" type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validate{modelName}(document.frm{modelName});">	
												</td>												
												<td align="center" flexy:if="addMode">
												<input flexy:ignore="yes" type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('{routingPagePrefix}.php');">&nbsp;&nbsp;
												<input flexy:ignore="yes" type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validate{modelName}(document.frm{modelName});">
												</td>
											</tr>
											<tr>
												<td  height="10" ></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<flexy:include src="../../ftemplate/rbBottom.tpl" />
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>			
	</table>
		</td>
			</tr>	
	<tr flexy:if="showEntry()" >
		<td colspan="3" height="10" ></td>
	</tr>
	
	<tr>
		<td colspan="3" height="10" ></td>
	</tr>
	<tr flexy:if="listMode">	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<tr flexy:if="!printMode">
				<td>{if:del}<input flexy:ignore="yes" type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',{icmRecSize});">{end:}&nbsp;{if:add}<input flexy:ignore="yes" type="submit" value=" Add New " name="cmdAddNew" class="button">{end:}&nbsp;{if:print}<input flexy:ignore="yes" type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('{routingPagePrefix}.php?print=y',700,600);">{end:}</td>
			</tr>
			</table>
		</td>
		</tr>
	<tr>
		<td colspan="3" height="5" ></td>
	</tr>
	<tr flexy:if="errDel">
		<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
	</tr>
	<tr flexy:if="listMode">
				<td width="1" ></td>
				<td colspan="2" >
				<table cellpadding="1"  width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	{if:icmRecSize}
				<tr flexy:if="displayNavRow">
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						{printPagination(maxpage,pageNo,#{routingPagePrefix}.php?#):h}
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr>
				<thead>
			
	<tr align="center">
		<th width="20" flexy:if="!printMode"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Machinery</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Description</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Type of Operation</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Capacity</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Per</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Monitor</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Monitoring<br/>Parameters</th>
		{if:edit}
		<th width="45" flexy:if="!printMode">&nbsp;</th>
		{end:}
	</tr>
	</thead>
	<tbody>
	{setRow(1)}
	{foreach:icmRecs,icmR}
	<tr>
		<td width="20" align="center" flexy:if="!printMode">
			<input type="checkbox" name="data[{getRow():h}][{modelName}][__del]" id="delId_{getRow():h}" value="{icmR.id}" class="chkBox">
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.name}</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">{icmR.description}</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.operationtype}</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.capacity}</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.unitname}</td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.per_val}</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{getMonitor(icmR.monitor)}</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">{icmR.parameter}</td>
		{if:edit}
		  <td class="listing-item" width="45" align="center" flexy:if="!printMode"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,{icmR.id},'editId');">
			</td>
		{end:}
	</tr>
		{incrementRow()}
	{end:}
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="{icmRecSize}" >
	<input type="hidden" name="editId" value="">
	</tbody>
	<tr flexy:if="displayNavRow">
		<td colspan="10" style="padding-right:10px" class="navRow">
			<div align="right">
			{printPagination(maxpage,pageNo,#{routingPagePrefix}.php?#):h}
			</div>
		</td>
	</tr>
	{else:}
	<tr><TD align="center">{msg_NoRecs}</TD></tr>
	{end:}
		</table>
		</td>
								</tr>
								<tr flexy:if="listMode">
									<td colspan="3" height="5" ></td>
								</tr>
								<tr flexy:if="listMode">	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr flexy:if="!printMode">
												<td>{if:del}<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',{icmRecSize});">{end:}&nbsp;{if:add}<input type="submit" value=" Add New " name="cmdAddNew" class="button">{end:}&nbsp;{if:print}<input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('{routingPagePrefix}.php?print=y',700,600);">{end:}</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>
						<flexy:include src="../../ftemplate/boxBR.tpl" />					
						</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td height="10">
			<input type="hidden" name="entryExist" id="entryExist" value="" readonly />
			<input type="hidden" name="pageNo" value="{pageNo}" readonly /> 
			</td>
		</tr>	
</table>
</form>

<flexy:include src="../../ftemplate/bottomR.php" />

