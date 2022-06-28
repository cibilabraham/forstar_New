<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmDAMSetting']->toHtmlnoClose();?>
	<table cellspacing="0" align="center" cellpadding="0" width="96%">
		<?php if ($t->err)  {?><tr>
			<td height="40" align="center" class="err1"><?php echo htmlspecialchars($t->err);?></td>
		</tr><?php }?>
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" align="center" width="100%">
					<tr>
						<td>							
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("Daily Activity Monitoring Setting"));?>
								<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/boxTL.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>					
							<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td colspan="3" align="center">
	<table width="70%" align="center">		
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?><tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" align="center" width="75%">
					<tr>
						<td>			
							
							<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setSubBoxHeader'))) echo htmlspecialchars($t->setSubBoxHeader($t->heading));?>
							<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/rbTop.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>
							<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">		
								<tr>
									<td width="1"></td>
									<td colspan="2">
										<table cellpadding="0" width="100%" cellspacing="0" border="0" align="center">
											<tr>
												<td height="10"></td>
											</tr>
											<tr>							
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDAMSetting(document.frmDAMSetting);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDAMSetting(document.frmDAMSetting);">
												</td><?php }?>
											</tr>
											<?php 
if (!isset($this->elements['hidDAMSettingId']->attributes['value'])) {
    $this->elements['hidDAMSettingId']->attributes['value'] = '';
    $this->elements['hidDAMSettingId']->attributes['value'] .=  htmlspecialchars($t->editId);
}
$_attributes_used = array('value');
echo $this->elements['hidDAMSettingId']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidDAMSettingId']->attributes[$_a]);
}}
?>
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
					<table border="0">
						<TR>
						<TD valign="top">
						<table>
							<tr>
							<td class="fieldName" nowrap="nowrap">*Head</td>
							<td class="listing-item">
<!-- onblur="xajax_chkICExist(document.getElementById('headName').value, '{editId}');" -->
								<?php echo $this->elements['data[DAMSetting][head_name]']->toHtml();?>
								<?php if ($t->editMode)  {?>
								<?php echo $this->elements['data[DAMSetting][id]']->toHtml();?>
								<?php }?>
							</td>
							</tr>
														
						</table>
						</TD>
						<td>&nbsp;</td>
						<td valign="top">
						<table>	
						<tr>
								<td class="fieldName" nowrap="nowrap">*NOS</td>
								<td class="listing-item">
									<?php echo $this->elements['data[DAMSetting][sub_head]']->toHtml();?>
									<?php echo $this->elements['hidTotalHead']->toHtml();?>
								</td>
							</tr>
						</table>
						</td>
						</TR>
					</table>
				</TD>
				</tr>
				<tr>
				<TD align="center" style="padding-left:5px; padding-right:5px;">
				<table>
				<TR>
					<TD align="center">
						<table cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSubhead" class="newspaperType">
							<tr align="center">
								<th nowrap style="text-align:center;">Sub Head</th>
								<th nowrap style="text-align:center;">Produced</th>
								<th nowrap style="text-align:center;">Stocked</th>
								<th nowrap style="text-align:center;">O/S Supply</th>
								<th nowrap style="text-align:center;">O/S Sale</th>
								<th nowrap style="text-align:center;">O/B</th>
								<th nowrap style="text-align:center;">Unit</th>
								<th nowrap style="text-align:center;">As On</th>
								<!--<th>&nbsp;</th>-->
							</tr>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(0));?>
	<?php if ($this->options['strict'] || (is_array($t->subHeadRecs)  || is_object($t->subHeadRecs))) foreach($t->subHeadRecs as $key => $shr) {?>	
	<tr align="center" class="whiteRow" id="row_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>">
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['subheadName_%s']->attributes['value'])) {
    $this->elements['subheadName_%s']->attributes['value'] = '';
    $this->elements['subheadName_%s']->attributes['value'] .=  htmlspecialchars($shr->sub_head_name);
}

if (!isset($this->elements['subheadName_%s']->attributes['id'])) {
    $this->elements['subheadName_%s']->attributes['id'] = '';
    $this->elements['subheadName_%s']->attributes['id'] .= "subheadName_";
    $this->elements['subheadName_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('value','id');

                $_element = $this->mergeElement(
                    $this->elements['subheadName_%s'],
                    isset($key) && isset($this->elements[sprintf('subheadName_%s',$key)]) ? $this->elements[sprintf('subheadName_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('subheadName_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['subheadName_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['produced_%s']->attributes['id'])) {
    $this->elements['produced_%s']->attributes['id'] = '';
    $this->elements['produced_%s']->attributes['id'] .= "produced_";
    $this->elements['produced_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['produced_%s'],
                    isset($key) && isset($this->elements[sprintf('produced_%s',$key)]) ? $this->elements[sprintf('produced_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('produced_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['produced_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['stocked_%s']->attributes['id'])) {
    $this->elements['stocked_%s']->attributes['id'] = '';
    $this->elements['stocked_%s']->attributes['id'] .= "stocked_";
    $this->elements['stocked_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['stocked_%s'],
                    isset($key) && isset($this->elements[sprintf('stocked_%s',$key)]) ? $this->elements[sprintf('stocked_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('stocked_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['stocked_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['osSupply_%s']->attributes['id'])) {
    $this->elements['osSupply_%s']->attributes['id'] = '';
    $this->elements['osSupply_%s']->attributes['id'] .= "osSupply_";
    $this->elements['osSupply_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['osSupply_%s'],
                    isset($key) && isset($this->elements[sprintf('osSupply_%s',$key)]) ? $this->elements[sprintf('osSupply_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('osSupply_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['osSupply_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['osSale_%s']->attributes['id'])) {
    $this->elements['osSale_%s']->attributes['id'] = '';
    $this->elements['osSale_%s']->attributes['id'] .= "osSale_";
    $this->elements['osSale_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['osSale_%s'],
                    isset($key) && isset($this->elements[sprintf('osSale_%s',$key)]) ? $this->elements[sprintf('osSale_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('osSale_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['osSale_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['openingBalance_%s']->attributes['value'])) {
    $this->elements['openingBalance_%s']->attributes['value'] = '';
    $this->elements['openingBalance_%s']->attributes['value'] .=  htmlspecialchars($shr->opening_balance);
}

if (!isset($this->elements['openingBalance_%s']->attributes['id'])) {
    $this->elements['openingBalance_%s']->attributes['id'] = '';
    $this->elements['openingBalance_%s']->attributes['id'] .= "openingBalance_";
    $this->elements['openingBalance_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('value','id');

                $_element = $this->mergeElement(
                    $this->elements['openingBalance_%s'],
                    isset($key) && isset($this->elements[sprintf('openingBalance_%s',$key)]) ? $this->elements[sprintf('openingBalance_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('openingBalance_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['openingBalance_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['selUnit_%s']->attributes['id'])) {
    $this->elements['selUnit_%s']->attributes['id'] = '';
    $this->elements['selUnit_%s']->attributes['id'] .= "selUnit_";
    $this->elements['selUnit_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['selUnit_%s'],
                    isset($key) && isset($this->elements[sprintf('selUnit_%s',$key)]) ? $this->elements[sprintf('selUnit_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('selUnit_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['selUnit_%s']->attributes[$_a]);
}}
?>
		</td>
		<td align="center" class="listing-item">
			<?php 
if (!isset($this->elements['startDate_%s']->attributes['id'])) {
    $this->elements['startDate_%s']->attributes['id'] = '';
    $this->elements['startDate_%s']->attributes['id'] .= "startDate_";
    $this->elements['startDate_%s']->attributes['id'] .=  htmlspecialchars($key);
}
$_attributes_used = array('id');

                $_element = $this->mergeElement(
                    $this->elements['startDate_%s'],
                    isset($key) && isset($this->elements[sprintf('startDate_%s',$key)]) ? $this->elements[sprintf('startDate_%s',$key)] : false
                );
                $_element->attributes['name'] = sprintf('startDate_%s',$key);
                
                echo $_element->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['startDate_%s']->attributes[$_a]);
}}
?>

			<input type="hidden" value="" id="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="status_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
			<input type="hidden" value="N" id="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="IsFromDB_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" />
			<input type="hidden" value="<?php echo htmlspecialchars($shr->id);?>" id="damEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" name="damEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" readonly="true" />
		</td>
		<!--<td align="center" class="listing-item">
			<a onclick="setItemStatus('0');" href="###">
				<img border="0" style="border: medium none ;" src="images/delIcon.gif" title="Click here to remove this item" />
			</a>			
		</td>-->
	</tr>	
<!--<tr align="center" class="whiteRow" id="row_{getRow():h}">
	<td align="center" class="listing-item">
		<input type="text" autocomplete="off" size="38" value="{shr.name}" id="chkListName_{getRow():h}" name="chkListName_{getRow():h}"/>
	</td>
	<td align="center" class="listing-item">
		<input type="checkbox" flexy:nameuses="key" class="chkBox" value="Y" id="required_{key}" name="required_%s" />
	</td>
	<td align="center" class="listing-item">
		<a onclick="setChkListItemStatus('{getRow():h}');" href="###"><img border="0" style="border: medium none ;" src="images/delIcon.gif" title=""/></a>
		<input type="hidden" value="" id="status_{getRow():h}" name="status_{getRow():h}"/>
		<input type="hidden" value="N" id="IsFromDB_{getRow():h}" name="IsFromDB_{getRow():h}"/>
		<input type="hidden" value="{shr.id}" id="chkListEntryId_{getRow():h}" name="chkListEntryId_{getRow():h}" readonly />
	</td>
 </tr>-->
	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	</table>
	<!--  Hidden Fields-->
	<?php 
if (!isset($this->elements['hidTableRowCount']->attributes['value'])) {
    $this->elements['hidTableRowCount']->attributes['value'] = '';
    $this->elements['hidTableRowCount']->attributes['value'] .=  htmlspecialchars($t->subHeadRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidTableRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidTableRowCount']->attributes[$_a]);
}}
?>
	<!--<input type='text' name="tableRowL" id="tableRowL" value="{subHeadRecSize}" readonly="true" />-->
	</TD>
				</TR>
				<tr><TD height="5"></TD></tr>
				<!--<tr>
					<TD>
						<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
					</TD>
				</tr>-->
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
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDAMSetting(document.frmDAMSetting);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DAMSetting.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDAMSetting(document.frmDAMSetting);">
												</td><?php }?>
											</tr>
											<tr>
												<td height="10"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/rbBottom.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr><?php }?>			
	</table>
		</td>
			</tr>	
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?><tr>
		<td colspan="3" height="10"></td>
	</tr><?php }?>
	
	<tr>
		<td colspan="3" height="10"></td>
	</tr>
	<?php if ($t->listMode)  {?><tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<?php if (!$t->printMode)  {?><tr>
				<td><?php if ($t->del)  {?><input type="submit" value=" Delete " class="button" name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?php echo htmlspecialchars($t->DAMSettingRecSize);?>);"><?php }?>&nbsp;<?php if ($t->add)  {?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('DAMSetting.php?print=y',700,600);"><?php }?></td>
			</tr><?php }?>
			</table>
		</td>
		</tr><?php }?>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<?php if ($t->errDel)  {?><tr>
		<td colspan="3" height="15" align="center" class="err1"><?php echo htmlspecialchars($t->errDel);?></td>
	</tr><?php }?>
	<?php if ($t->listMode)  {?><tr>
				<td width="1"></td>
				<td colspan="2">
				<table cellpadding="1" width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->DAMSettingRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="10" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"DAMSetting.php?");?>
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
				<thead>
			
	<tr align="center">
		<?php if (!$t->printMode)  {?><th width="20"><?php echo $this->elements['CheckAll']->toHtml();?></th><?php }?>
		<th nowrap style="padding-left:10px; padding-right:10px;">Head</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">NOS</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Sub Head</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Produced</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Stocked</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">O/S Supply</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">O/S Sale</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">O/B</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Unit</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">As On</th>
		<?php if ($t->edit)  {?>
		<?php if (!$t->printMode)  {?><th width="45">&nbsp;</th><?php }?>
		<?php }?>
		<?php if ($t->edit)  {?>
		<?php if (!$t->printMode)  {?><th width="45">&nbsp;</th><?php }?>
		<?php }?>
	</tr>
	</thead>
	<tbody>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(1));?>
	<?php if ($this->options['strict'] || (is_array($t->DAMSettingRecs)  || is_object($t->DAMSettingRecs))) foreach($t->DAMSettingRecs as $damsR) {?>
		 
	<tr>
		<?php if (!$t->printMode)  {?><td width="20" align="center">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkPrevDAMSettingId'))) if ($t->chkPrevDAMSettingId($damsR->id)) { ?>
			<input type="checkbox" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][DAMSetting][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($damsR->id);?>" class="chkBox">
			<?php } else {?>
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][DAMSetting][__del]" id="delId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="" readonly>
			<?php }?>
		</td><?php }?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkPrevDAMSettingId'))) if ($t->chkPrevDAMSettingId($damsR->id)) { ?>
			<?php echo htmlspecialchars($damsR->mainhead);?>
			<?php }?>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkPrevDAMSettingId'))) if ($t->chkPrevDAMSettingId($damsR->id)) { ?>
			<?php echo htmlspecialchars($damsR->numsubhead);?>
			<?php }?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($damsR->subhead);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?php echo htmlspecialchars($damsR->produced);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?php echo htmlspecialchars($damsR->stocked);?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?php echo htmlspecialchars($damsR->ossupply);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?php echo htmlspecialchars($damsR->ossale);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($damsR->ob);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($damsR->stkname);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?php echo htmlspecialchars($damsR->startdate);?></td>
		<?php if ($t->edit)  {?>			
		  	<?php if (!$t->printMode)  {?><td class="listing-item" width="45" align="center">
				<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkPrevDAMSettingId'))) if ($t->chkPrevDAMSettingId($damsR->id)) { ?>
				<?php 
if (!isset($this->elements['cmdEdit']->attributes['onClick'])) {
    $this->elements['cmdEdit']->attributes['onClick'] = '';
    $this->elements['cmdEdit']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['cmdEdit']->attributes['onClick'] .=  htmlspecialchars($damsR->id);
    $this->elements['cmdEdit']->attributes['onClick'] .= ",'editId');";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdEdit']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdEdit']->attributes[$_a]);
}}
?>
				<?php }?>
			</td><?php }?>			
		<?php }?>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'chkPrevDAMSettingId'))) if ($t->chkPrevDAMSettingId($damsR->id)) { ?>
		 <?php if (!$damsR->active)  {?><td class="listing-item" width="45" align="center"><?php 
if (!isset($this->elements['cmdConfirm']->attributes['onClick'])) {
    $this->elements['cmdConfirm']->attributes['onClick'] = '';
    $this->elements['cmdConfirm']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['cmdConfirm']->attributes['onClick'] .=  htmlspecialchars($t->icmR->id);
    $this->elements['cmdConfirm']->attributes['onClick'] .= ",'confirmId');";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdConfirm']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdConfirm']->attributes[$_a]);
}}
?>
			</td><?php }?>
		
		 <?php if ($damsR->active)  {?><td class="listing-item" width="45" align="center"><?php 
if (!isset($this->elements['btnRlConfirm']->attributes['onClick'])) {
    $this->elements['btnRlConfirm']->attributes['onClick'] = '';
    $this->elements['btnRlConfirm']->attributes['onClick'] .= "assignValue(this.form,";
    $this->elements['btnRlConfirm']->attributes['onClick'] .=  htmlspecialchars($t->icmR->id);
    $this->elements['btnRlConfirm']->attributes['onClick'] .= ",'rlconfirmId');";
}
$_attributes_used = array('onClick');
echo $this->elements['btnRlConfirm']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['btnRlConfirm']->attributes[$_a]);
}}
?>
			</td><?php }?>
			<?php }?>
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setPrevRec'))) echo htmlspecialchars($t->setPrevRec($damsR->id));?>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>
	<?php 
if (!isset($this->elements['hidRowCount']->attributes['value'])) {
    $this->elements['hidRowCount']->attributes['value'] = '';
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->DAMSettingRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidRowCount']->attributes[$_a]);
}}
?>
	<?php echo $this->elements['editId']->toHtml();?>
	<?php echo $this->elements['confirmId']->toHtml();?>
	<?php echo $this->elements['rlconfirmId']->toHtml();?>
	</tbody>
	<?php if ($t->displayNavRow)  {?><tr>
		<td colspan="10" style="padding-right:10px" class="navRow">
			<div align="right">
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"DAMSetting.php?");?>
			</div>
		</td>
	</tr><?php }?>
	<?php } else {?>
	<tr><TD align="center"><?php echo htmlspecialchars($t->msg_NoRecs);?></TD></tr>
	<?php }?>
		</table>
		</td>
								</tr><?php }?>
								<?php if ($t->listMode)  {?><tr>
									<td colspan="3" height="5"></td>
								</tr><?php }?>
								<?php if ($t->listMode)  {?><tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<?php if (!$t->printMode)  {?><tr>
												<td><?php if ($t->del)  {?><?php 
if (!isset($this->elements['cmdDelete']->attributes['onClick'])) {
    $this->elements['cmdDelete']->attributes['onClick'] = '';
    $this->elements['cmdDelete']->attributes['onClick'] .= "return confirmDelete(this.form,'delId_',";
    $this->elements['cmdDelete']->attributes['onClick'] .=  htmlspecialchars($t->DAMSettingRecSize);
    $this->elements['cmdDelete']->attributes['onClick'] .= ");";
}
$_attributes_used = array('onClick');
echo $this->elements['cmdDelete']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['cmdDelete']->attributes[$_a]);
}}
?><?php }?>&nbsp;<?php if ($t->add)  {?><?php echo $this->elements['cmdAddNew']->toHtml();?><?php }?>&nbsp;<?php if ($t->print)  {?><?php echo $this->elements['btnPrint']->toHtml();?><?php }?></td>
											</tr><?php }?>
										</table>
									</td>
								</tr><?php }?>
								<tr>
									<td colspan="3" height="5"></td>
								</tr>
							</table>
						<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/boxBR.tpl');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>					
						</td>
					</tr>
				</table>
			</td>
		</tr>			
		<tr>
			<td height="10">
				<?php echo $this->elements['entryExist']->toHtml();?>
				<?php 
if (!isset($this->elements['pageNo']->attributes['value'])) {
    $this->elements['pageNo']->attributes['value'] = '';
    $this->elements['pageNo']->attributes['value'] .=  htmlspecialchars($t->pageNo);
}
$_attributes_used = array('value');
echo $this->elements['pageNo']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['pageNo']->attributes[$_a]);
}}
?> 
				<?php 
if (!isset($this->elements['addMode']->attributes['value'])) {
    $this->elements['addMode']->attributes['value'] = '';
    $this->elements['addMode']->attributes['value'] .=  htmlspecialchars($t->addMode);
}
$_attributes_used = array('value');
echo $this->elements['addMode']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['addMode']->attributes[$_a]);
}}
?>
			</td>
		</tr>
</table>
<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showEntry'))) if ($t->showEntry()) { ?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		function addNewItem()
		{
			addNewItemRow('tblSubhead','','');		
		}
	</script>
<?php }?>
<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'showAddRow'))) if ($t->showAddRow()) { ?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		window.load = addNewItem();
	</script>
<?php }?>
	
<?php if ($t->subHeadRecSize)  {?> 
	<?php require_once 'HTML/Javascript/Convert.php';?>
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::convertVar($t->subHeadRecSize,'fieldId',true);echo (is_object($__tmp) && is_a($__tmp,"PEAR_Error")) ? ("<pre>".print_r($__tmp,true)."</pre>") : $__tmp;?>
</script>
<?php }?>
</form>

<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/bottomR.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>