<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php echo $this->elements['frmFrznPkgAccounts']->toHtmlnoClose();?>
	<table cellspacing="0" align="center" cellpadding="0" width="96%">
		<?php if ($t->err)  {?><tr>
			<td height="20" align="center" class="err1"><?php echo htmlspecialchars($t->err);?></td>
		</tr><?php }?>
			<tr>
				<td height="10" align="center"></td>
			</tr>
			<tr>
			<td>
				<table cellpadding="0" cellspacing="1" border="0" align="center" width="100%">
					<tr>
						<td>							
							<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader("Frozen Packing Accounts"));?>
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
	<?php if ($t->listMode)  {?><tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<?php if (!$t->printMode)  {?><tr>
				<td>
					<?php if ($t->edit)  {?>
						<input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgAccounts('Y');">
					<?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('FrznPkgAccounts.php?print=y',700,600);">
					<?php }?>
				</td>
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
		<td colspan="3" height="15" align="center">
		<table>
		<TR><TD>
			<table>
				<TR>
					<td class="fieldName">Select Date:</td>
					<td class="fieldName">*From:</td>
					<td nowrap>					
						<?php 
if (!isset($this->elements['dateFrom']->attributes['value'])) {
    $this->elements['dateFrom']->attributes['value'] = '';
    $this->elements['dateFrom']->attributes['value'] .=  htmlspecialchars($t->dateFrom);
}
$_attributes_used = array('value');
echo $this->elements['dateFrom']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['dateFrom']->attributes[$_a]);
}}
?>
					</td>
					<td class="fieldName">*To:</td>
					<td nowrap>
						<?php 
if (!isset($this->elements['dateTo']->attributes['value'])) {
    $this->elements['dateTo']->attributes['value'] = '';
    $this->elements['dateTo']->attributes['value'] .=  htmlspecialchars($t->dateTo);
}
$_attributes_used = array('value');
echo $this->elements['dateTo']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['dateTo']->attributes[$_a]);
}}
?>		
					</td>
					<td class="fieldName" nowrap="nowrap">*Pre-Processor:</td>
                                <td>
                       			<?php echo $this->elements['selProcessor']->toHtml();?>
				</td>					
					<td style="padding-left:10px; padding-right:10px;">
						<?php echo $this->elements['cmdSearch']->toHtml();?>
					</td>
				</TR>
			</table>
		</TD></TR>
		</table>
		</td>
	</tr><?php }?>	
<!-- New Frzn Pkg Acs Starts here -->
<?php if ($t->listMode)  {?><tr>
				<td width="1"></td>
				<td colspan="2">
				<table cellpadding="1" width="30%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->frznPkgRecSize)  {?>
				<?php if ($t->displayNavRow)  {?><tr>
					<td colspan="17" style="padding-right:10px" class="navRow">
						<div align="right">
						<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"FrznPkgAccounts.php?dateFrom=$t->dateFrom&dateTo=$t->dateTo&selProcessor=$t->selProcessorId");?>
						<!--<input type="hidden" name="pageNo" value="{pageNo}"> Set to last section -->
						</div>
					</td>
				</tr><?php }?>
				<thead>
			
	<tr align="center">
		<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Processcode</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Freezing <br>Stage</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">MC<br> Pkg</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Qty</th>	
		<th nowrap style="padding-left:10px; padding-right:10px;">Pkg<br> Wt</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Glaze<br> (%)</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Net Wt</th><!--title="Wt without glaze"-->
		<th nowrap style="padding-left:10px; padding-right:10px;">Total Units</th>	
		<th nowrap style="padding-left:10px; padding-right:10px;">Qty</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Rate</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Total Amt</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Setld</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Setld<br> Date</th>
	</tr>
	</thead>
	<tbody>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(1));?>
	<?php if ($this->options['strict'] || (is_array($t->frznPkgRecs)  || is_object($t->frznPkgRecs))) foreach($t->frznPkgRecs as $key => $fpR) {?>
		<!--{fpQty(dateFrom,dateTo,selProcessorId,fpR.processcodeid,fpR.freezingstageid,fpR.qualityid,fpR.frozencodeid)}-->
		<?php if (!$fpR->rategradeid)  {?> 
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRate'))) echo htmlspecialchars($t->getRate($fpR->seldate,$t->selProcessorId,$fpR->processcodeid,$fpR->freezingstageid,$fpR->qualityid,$fpR->frozencodeid));?>
		<?php }?>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setSettledChk'))) echo htmlspecialchars($t->setSettledChk($key,$fpR->settled));?>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'calcTotQty'))) echo htmlspecialchars($t->calcTotQty($fpR->frozenqty,$fpR->slab,$fpR->pkdqty));?>
	<tr>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?php echo htmlspecialchars($fpR->fishname);?>
			<input type="hidden" name="gradeEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="gradeEntryId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->gentryid);?>" autocomplete="off" readonly />
			<input type="hidden" name="numpack_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="numpack_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->numpack);?>" autocomplete="off" readonly />
			<input type="hidden" name="gnummc_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="gnummc_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->gnummc);?>" autocomplete="off" readonly />
			<input type="hidden" name="gnumls_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="gnumls_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->gnumls);?>" autocomplete="off" readonly />
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fpR->processcode);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fpR->freezingstage);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fpR->qualityname);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fpR->frozencode);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fpR->mcpkgcode);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($fpR->frozenqty);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($fpR->declwt);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($fpR->glaze);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
			<?php echo htmlspecialchars($fpR->filledwt);?>
			<input type="hidden" name="filledwt_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="filledwt_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->filledwt);?>" readonly />
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($fpR->slab);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
			<?php echo htmlspecialchars($fpR->pkdqty);?>
			<input type="hidden" name="pkdQty_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="pkdQty_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php echo htmlspecialchars($fpR->pkdqty);?>" readonly />
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?php echo htmlspecialchars($fpR->grade);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
<!-- {fpR.pkgrate} -->
			<input type="text" name="pkgRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="pkgRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="5" style="text-align:right" value="<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRate'))) echo htmlspecialchars($t->setRate($fpR->pkgrate));?>" onkeyup="calcFPRAmt();" autocomplete="off" />
			
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right">
			<input type="text" name="totPkgAmt_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" id="totPkgAmt_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" size="8" style="text-align:right; border:none;" readonly />
		</td>	
		<td class="listing-item" nowrap align="center">
			<!--{fpR.settled}-->
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setldChk'))) if ($t->setldChk($fpR->settled)) { ?>
<?php 
	echo "<input name=\"settled_".$t->getRow()."\" type=\"checkbox\" id=\"settled_".$t->getRow()."\" value=\"Y\" class=\"chkBox\" checked >";
?>
			<?php } else {?>
				<input name="settled_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" type="checkbox" id="settled_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="Y" class="chkBox" />
			<?php }?>
		</td>
                 <td class="listing-item" nowrap align="center"><?php echo htmlspecialchars($fpR->setlddate);?></td>
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>	
	<?php 
if (!isset($this->elements['hidRowCount']->attributes['value'])) {
    $this->elements['hidRowCount']->attributes['value'] = '';
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->frznPkgRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidRowCount']->attributes[$_a]);
}}
?>
	<?php echo $this->elements['editId']->toHtml();?>
	</tbody>
	<tr bgcolor="White">
		<TD colspan="6" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
			Total:
		</TD>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->totFrznQty);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>			
			</strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>			
			</strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>			
			</strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->totSlab);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->totPkdQty);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>			
			</strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>			
			</strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong>
				<?php 
if (!isset($this->elements['totalAmt']->attributes['value'])) {
    $this->elements['totalAmt']->attributes['value'] = '';
    $this->elements['totalAmt']->attributes['value'] .=  htmlspecialchars($t->totalamt);
}
$_attributes_used = array('value');
echo $this->elements['totalAmt']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['totalAmt']->attributes[$_a]);
}}
?>
			</strong>
		</td>
		<td colspan="2">&nbsp;</td>
	</tr>
<!-- Grand Total -->
	<?php if ($t->displayNavRow)  {?><tr bgcolor="White">
		<TD colspan="6" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
			Grand Total:
		</TD>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->grandTotalFrznQty);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->grandTotalSlab);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?php echo htmlspecialchars($t->grandTotalPkdQty);?></strong>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
			&nbsp;
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" title="Rate entered grand total amt">
			<strong><?php echo htmlspecialchars($t->grandTotalPkgAmt);?></strong>
		</td>
		<td colspan="2">&nbsp;</td>
	</tr><?php }?>
	<?php if ($t->displayNavRow)  {?><tr>
		<td colspan="17" style="padding-right:10px" class="navRow">
			<div align="right">
			<!--{printPagination(maxpage,pageNo,#FrznPkgAccounts.php?#):h}-->
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'printPagination'))) echo $t->printPagination($t->maxpage,$t->pageNo,"FrznPkgAccounts.php?dateFrom=$t->dateFrom&dateTo=$t->dateTo&selProcessor=$t->selProcessorId");?>
			</div>
		</td>
	</tr><?php }?>
	
	<?php }?>
		</table>
		</td>
	</tr><?php }?>
<!-- Frzn Pkg Acs Ends here -->								
	<?php if ($t->listMode)  {?><tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">
			<?php if (!$t->printMode)  {?><tr>
				<td>
					<?php if ($t->edit)  {?>
						<input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgAccounts('Y');">
					<?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('FrznPkgAccounts.php?print=y',700,600);">
					<?php }?>
				</td>
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
			</td>
		</tr>	
</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</script>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</script>
	<?php if ($t->frznPkgRecSize)  {?>
	<script language="JavaScript" type="text/javascript">
		calcFPRAmt();
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

