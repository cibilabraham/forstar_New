<?php 
$x = new HTML_Template_Flexy($this->options);
$x->compile('../../ftemplate/topL.php');
$_t = function_exists('clone') ? clone($t) : $t;
foreach(array()  as $k) {
    if ($k != 't') { $_t->$k = $$k; }
}
$x->outputObject($_t, $this->elements);
?>

<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'divContainerId'))) echo htmlspecialchars($t->divContainerId());?>

<?php echo $this->elements['frmFrznPkgRate']->toHtmlnoClose();?>
<script type="text/javascript">	
	/*	
	 $(function() {
                $("#container-1 ul").tabs();
            });
	*/

	function chk()
	{
		return true;
	}

	 $(function() {
                $("#container-1 ul").tabs();
		$('#container-1 ul').bind('tabsselect', function(event, ui) {
			return chk();
			// Objects available in the function context:				
		});
            });

/*
	 $(function() {
		$('#container-1 ul').tabs({
			onClick: function() {
				alert('onClick');
			}
		});
	});
*/

/*
	$('#container-1 ul').tabs({
		fxFade: true,
		fxSpeed: 'fast',
		onClick: function() {
			alert('onClick');
		},
		onHide: function() {
			alert('onHide');
		},
		onShow: function() {
			alert('onShow');
		}
	});
*/
	
	/* Modified section (Original) */
	/* 
	$(function() {
		$("#container-2").tabs().addClass('ui-tabs-vertical ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all');
		$("#container-2 li").removeClass('ui-corner-top').addClass('ui-corner-left');
	
		$("#container-2").tabs().removeClass('ui-tabs-nav').addClass('ui-vtabs-nav');
		$("#container-2 ul").removeClass('ui-tabs-nav ui-tabs-panel').addClass('ui-vtabs-nav ui-vtabs-panel');
	});
	*/	
 </script>

<!--<flexy:toJavascript fcRecs="ar" /> -->
<!--<flexy:toJavascript divContainerId="divContainerId" />-->
<script type='text/javascript'>
<?php $__tmp = HTML_Javascript_Convert::escapeString($t->divContainerId,'',false);echo (is_a($__tmp,"PEAR_Error")) ? ("<pre>".print_r($__tmp,false)."</pre>") : str_replace('\\','',$__tmp);?>
</script>

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
								<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setBoxHeader'))) echo htmlspecialchars($t->setBoxHeader(" Frozen Packing Rate"));?>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFrznPkgRate(document.frmFrznPkgRate);">	
												</td><?php }?>
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateFrznPkgRate(document.frmFrznPkgRate);">
												</td><?php }?>
											</tr>
											<?php 
if (!isset($this->elements['hidFrznPkgRateId']->attributes['value'])) {
    $this->elements['hidFrznPkgRateId']->attributes['value'] = '';
    $this->elements['hidFrznPkgRateId']->attributes['value'] .=  htmlspecialchars($t->editId);
}
$_attributes_used = array('value');
echo $this->elements['hidFrznPkgRateId']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidFrznPkgRateId']->attributes[$_a]);
}}
?>
											<tr>
											  <td nowrap class="fieldName">
											  </td>
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
								<?php 
if (!isset($this->elements['data[m_frzn_pkg_rate][name]']->attributes['onblur'])) {
    $this->elements['data[m_frzn_pkg_rate][name]']->attributes['onblur'] = '';
    $this->elements['data[m_frzn_pkg_rate][name]']->attributes['onblur'] .= "xajax_chkICExist(document.getElementById('machinery').value, '";
    $this->elements['data[m_frzn_pkg_rate][name]']->attributes['onblur'] .=  htmlspecialchars($t->editId);
    $this->elements['data[m_frzn_pkg_rate][name]']->attributes['onblur'] .= "');";
}
$_attributes_used = array('onblur');
echo $this->elements['data[m_frzn_pkg_rate][name]']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['data[m_frzn_pkg_rate][name]']->attributes[$_a]);
}}
?>
								<?php if ($t->editMode)  {?>
								<?php echo $this->elements['data[m_frzn_pkg_rate][id]']->toHtml();?>
								<?php }?>
							</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">Description</td>
								<td class="listing-item"><?php echo $this->elements['data[m_frzn_pkg_rate][description]']->toHtml();?></td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Type of Operation</td>
								<td class="listing-item">
									<?php echo $this->elements['data[m_frzn_pkg_rate][operation_type_id]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Capacity</td>
								<td class="listing-item">
									<?php echo $this->elements['data[m_frzn_pkg_rate][capacity]']->toHtml();?>
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
									<?php echo $this->elements['data[m_frzn_pkg_rate][unit_id]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Per</td>
								<td class="listing-item" nowrap>
									<?php echo $this->elements['data[m_frzn_pkg_rate][per_val]']->toHtml();?>
									&nbsp;HR
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Monitor</td>
								<td class="listing-item">
									<?php echo $this->elements['data[m_frzn_pkg_rate][monitor]']->toHtml();?>
								</td>
							</tr>
							<tr>
								<td class="fieldName" nowrap="nowrap">*Monitoring Parameters</td>
								<td class="listing-item">
									<?php echo $this->elements['data[m_frzn_pkg_rate][monitoring_parameter_id]']->toHtml();?>
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
												<?php if ($t->editMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateFrznPkgRate(document.frmFrznPkgRate);">	
												</td><?php }?>												
												<?php if ($t->addMode)  {?><td align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('FrozenPackingRate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateFrznPkgRate(document.frmFrznPkgRate);">
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
	<?php if (!$t->listMode)  {?><tr>	
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" align="center">			
			<?php if (!$t->printMode)  {?><tr>
				<td>
					<?php if ($t->edit)  {?>
						<input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgRate(document.frmFrznPkgRate);">
					<?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('FrozenPackingRate.php?print=y',700,600);"><?php }?></td>
			</tr><?php }?>
			</table>
		</td>
		</tr><?php }?>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
	<?php if ($t->errDel)  {?><tr>
		<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
	</tr><?php }?>
	<?php if (!$t->listMode)  {?><tr>
		<td colspan="3" height="15" align="center">
		<table>
		<TR><TD>
			<table>
				<TR>
					<TD class="fieldName">Fish Category</TD>
					<TD nowrap>
						<?php echo $this->elements['fishCategory']->toHtml();?>
					</TD>
					<TD class="fieldName">Fish</TD>
					<TD nowrap>
						<?php echo $this->elements['fishId']->toHtml();?>
					</TD>
					<td>&nbsp;</td>
					<td>
						<?php echo $this->elements['cmdSearch']->toHtml();?>
					</td>
				</TR>
			</table>
		</TD></TR>
		</table>
		</td>
	</tr><?php }?>
<div id="filter"></div>
<div id="box">
  <span id="boxtitle"></span>
  <iframe width="100%" height="300" id="gradeExptIFrame" src="" style="border:none;" frameborder="0"></iframe>	
	<!--<p align="center"> 
      <input type="button" name="cancel" value="Cancel" onClick="closeLightBox()">
    </p>-->
</div>
	<?php if ($t->listMode)  {?><tr>
		<td colspan="3" height="15" align="center">

<?php if (!$t->listMode)  {?><div id="container-1">
            <ul>		
		<li><a href="#fragment-1"><span>Section 1</span></a></li>
                <li><a href="#fragment-2"><span>Section 2</span></a></li>
                <li><a href="#fragment-3"><span>Section 3</span></a></li>		
            </ul>
            <div id="fragment-1">
                <div id="container-2">			
                    <ul>
                        <li><a href="#fragment-1a"><span>Section 1a</span></a></li>
                        <li><a href="#fragment-1b"><span>Section 1b</span></a></li>
                        <li><a href="#fragment-1c"><span>Section 1c</span></a></li>
                    </ul>			
                    <div id="fragment-1a" class="ui-content-region">
			Section 1 > Section 1A
		    </div>
                    <div id="fragment-1b" class="ui-content-region">
                       Section 1 > Section 1B
                    </div>
                    <div id="fragment-1c" class="ui-content-region">
                       Section 1 > Section 1C
                    </div>			
                </div>
            </div>
            <div id="fragment-2">
               Section 2		
            </div>
            <div id="fragment-3">
               Section 3
            </div>
        </div><?php }?>



<!-- New Container starts here -->
	<?php if ($t->listMode)  {?><div id="container-1">
            <ul>
		<?php if ($this->options['strict'] || (is_array($t->fcRecs)  || is_object($t->fcRecs))) foreach($t->fcRecs as $fcR) {?>
		<li><a href="#<?php echo htmlspecialchars($fcR->category);?>"><span><?php echo htmlspecialchars($fcR->category);?></span></a></li>
		<?php }?>
            </ul>
	  <?php if ($this->options['strict'] || (is_array($t->fcRecs)  || is_object($t->fcRecs))) foreach($t->fcRecs as $fcR) {?>
<!-- Fish Recs ==> qelFishRecs-->
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getQELWiseFishRecs'))) echo htmlspecialchars($t->getQELWiseFishRecs($fcR->id));?>
            <div id="<?php echo htmlspecialchars($fcR->category);?>">
		<?php if ($t->qelFishRecSize)  {?>
                <div id="container<?php echo htmlspecialchars($fcR->id);?>">
						
                   	<ul>			
			<?php if ($this->options['strict'] || (is_array($t->qelFishRecs)  || is_object($t->qelFishRecs))) foreach($t->qelFishRecs as $fr) {?>	
				<li><a href="#<?php echo htmlspecialchars($fcR->id);?><?php echo htmlspecialchars($fr->fishid);?>"><span><?php echo htmlspecialchars($fr->fishname);?></span></a></li>
			<?php }?>
			</ul>	
			<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(1));?>		
			<?php if ($this->options['strict'] || (is_array($t->qelFishRecs)  || is_object($t->qelFishRecs))) foreach($t->qelFishRecs as $fr) {?>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getQELWisePCRecs'))) echo htmlspecialchars($t->getQELWisePCRecs($fcR->id,$fr->fishid));?>
<!-- Get PC -->
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getSelPCRecs'))) echo htmlspecialchars($t->getSelPCRecs($fcR->id,$fr->fishid));?>	
			<div id="<?php echo htmlspecialchars($fcR->id);?><?php echo htmlspecialchars($fr->fishid);?>" class="ui-content-region">
			<table cellpadding="0" width="80%" cellspacing="0" border="0" align="center" class="tbl-pcl">
				<tr align="center">
					<th nowrap style="padding-left:10px; padding-right:10px;" width="10%">Process Code</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="16%">Freezing Stage</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="16%">Quality</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="26%">Frozen Code</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="2%">Default<br>Rate</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="2%">No.of <br>Expt. Rate</th>
					<th nowrap style="padding-left:10px; padding-right:10px;" width="10%">Rate</th>
					<!--<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>-->	
					<!--<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>-->
				</tr>
				<tr>
					<td valign="top">
<!-- List All Process codes -->
					<table cellpadding="0" cellspacing="0" class="tbl-nb" width="100%">
					<?php if ($this->options['strict'] || (is_array($t->qeFprPCRecs)  || is_object($t->qeFprPCRecs))) foreach($t->qeFprPCRecs as $fprPCR) {?>
						<TR><TD>
							<a href="###" onclick="xajax_getQEL('<?php echo htmlspecialchars($fprPCR->fcid);?>', '<?php echo htmlspecialchars($fprPCR->fishid);?>', '<?php echo htmlspecialchars($fprPCR->pcid);?>', '<?php echo htmlspecialchars($fcR->id);?>_<?php echo htmlspecialchars($fr->fishid);?>_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo htmlspecialchars($t->getRow());?>', '<?php echo htmlspecialchars($t->rateListId);?>', 'PW'); changeFZN(this.id, '<?php echo htmlspecialchars($fcR->id);?>_<?php echo htmlspecialchars($fr->fishid);?>_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo htmlspecialchars($t->getRow());?>');" class="tbl-pc" id="<?php echo htmlspecialchars($fprPCR->pcid);?>"><?php echo htmlspecialchars($fprPCR->processcode);?></a>
							<input type="hidden" name="rateModified_<?php echo htmlspecialchars($fcR->id);?>_<?php echo htmlspecialchars($fr->fishid);?>_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo htmlspecialchars($t->getRow());?>" id="rateModified_<?php echo htmlspecialchars($fcR->id);?>_<?php echo htmlspecialchars($fr->fishid);?>_<?php echo htmlspecialchars($fprPCR->pcid);?>" value="" readonly />
						</TD></TR>
					<?php }?>
					</table>
					</td>
					<td colspan="6" id="<?php echo htmlspecialchars($fcR->id);?>_<?php echo htmlspecialchars($fr->fishid);?>_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo htmlspecialchars($t->getRow());?>" valign="top">
						<!--<table cellpadding="0" cellspacing="0">
							<TR><TD>4543534</TD></TR>
						</table>-->	
						
					</td>
				</tr>
			</table>

<?php if (!$t->listMode)  {?><table cellpadding="1" width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->qeFprRecSize)  {?>
	<thead>			
	<tr align="center">
		<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Process Code</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Freezing Stage</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>	
		<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
	</tr>
	</thead>
	<tbody>
	<!--{setRow(1)}-->
	<?php if ($this->options['strict'] || (is_array($t->qeFprRecs)  || is_object($t->qeFprRecs))) foreach($t->qeFprRecs as $fprRec) {?>
	<tr>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?php echo htmlspecialchars($fprRec->fishname);?>
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][fish_id]" id="fishId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->fish_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][process_code_id]" id="processCodeId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->processcode_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][freezing_stage_id]" id="freezingStageId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->freezing_stage_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][quality_id]" id="qualityId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->quality_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][frozen_code_id]" id="frozenCodeId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->frozencode_id);?>" readonly />
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->processcode);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->freezingstage);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->qualityname);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->frozencode);?></td>			
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<input type="text" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][default_rate]" id="defaultRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="" size="3" style="text-align:right;" autocomplete="off">
		</td>
		<td class="listing-item" nowrap style="vertical-align: center;padding-left:5px; padding-right:5px;">
			<input type="hidden" name="frznPkgExceptionRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'gCRRec'))) echo htmlspecialchars($t->gCRRec($fprRec));?>" id="frznPkgExceptionRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'gCRRec'))) echo htmlspecialchars($t->gCRRec($fprRec));?>" value="" readonly />
			<a href="###" onclick="getGrade(<?php echo htmlspecialchars($fprRec->processcode_id);?>, <?php echo htmlspecialchars($fprRec->freezing_stage_id);?>, <?php echo htmlspecialchars($fprRec->quality_id);?>, <?php echo htmlspecialchars($fprRec->frozencode_id);?>, <?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>)">Exception Rate</a>
		</td>		
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>		
	</tbody>		
	<?php } else {?>
	<tr><TD align="center"><?php echo htmlspecialchars($t->msg_NoRecs);?></TD></tr>
	<?php }?>
	<?php 
if (!isset($this->elements['hidRowCount']->attributes['value'])) {
    $this->elements['hidRowCount']->attributes['value'] = '';
    $this->elements['hidRowCount']->attributes['value'] .=  htmlspecialchars($t->qeFprRecSize);
}
$_attributes_used = array('value');
echo $this->elements['hidRowCount']->toHtml();
if (isset($_attributes_used)) {  foreach($_attributes_used as $_a) {
    unset($this->elements['hidRowCount']->attributes[$_a]);
}}
?>
		</table><?php }?>
	</div>
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>	
                </div>
	<?php }?>
            </div>
		<?php }?>
<!-- Fish Loop Ends here -->
        </div><?php }?>
<!-- Main Container Div Ends here -->
		

<!-- Original container	fcRecs -->
	<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'setRow'))) echo htmlspecialchars($t->setRow(1));?>
	<?php if ($this->options['strict'] || (is_array($t->fcRecss)  || is_object($t->fcRecss))) foreach($t->fcRecss as $fcR) {?>	
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getQELWisePCRecs'))) echo htmlspecialchars($t->getQELWisePCRecs($fcR->id));?>
	<div id="<?php echo htmlspecialchars($fcR->category);?>">
	<table cellpadding="1" width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php if ($t->qeFprRecSize)  {?>
	<thead>			
	<tr align="center">
		<th nowrap style="padding-left:10px; padding-right:10px;">Fish</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Process Code</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Freezing Stage</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Quality</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Frozen Code</th>
		<th nowrap style="padding-left:10px; padding-right:10px;">Rate/Kg</th>	
		<th nowrap style="padding-left:10px; padding-right:10px;">Grade</th>
	</tr>
	</thead>
	<tbody>
	<!--{setRow(1)}-->
	<?php if ($this->options['strict'] || (is_array($t->qeFprRecs)  || is_object($t->qeFprRecs))) foreach($t->qeFprRecs as $fprRec) {?>
	<tr>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?php echo htmlspecialchars($fprRec->fishname);?>
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][fish_id]" id="fishId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->fish_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][process_code_id]" id="processCodeId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->processcode_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][freezing_stage_id]" id="freezingStageId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->freezing_stage_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][quality_id]" id="qualityId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->quality_id);?>" readonly />
			<input type="hidden" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][frozen_code_id]" id="frozenCodeId_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="<?php echo htmlspecialchars($fprRec->frozencode_id);?>" readonly />
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->processcode);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->freezingstage);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->qualityname);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?php echo htmlspecialchars($fprRec->frozencode);?></td>			
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
			<input type="text" name="data[<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>][FrznPkgRate][default_rate]" id="defaultRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>" value="" size="3" style="text-align:right;" autocomplete="off">
		</td>
		<td class="listing-item" nowrap style="vertical-align: center;padding-left:5px; padding-right:5px;">
			<input type="hidden" name="frznPkgExceptionRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'gCRRec'))) echo htmlspecialchars($t->gCRRec($fprRec));?>" id="frznPkgExceptionRate_<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'gCRRec'))) echo htmlspecialchars($t->gCRRec($fprRec));?>" value="" readonly />
			<a href="###" onclick="getGrade(<?php echo htmlspecialchars($fprRec->processcode_id);?>, <?php echo htmlspecialchars($fprRec->freezing_stage_id);?>, <?php echo htmlspecialchars($fprRec->quality_id);?>, <?php echo htmlspecialchars($fprRec->frozencode_id);?>, <?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'getRow'))) echo $t->getRow();?>)">Exception Rate</a>
		<!--<a href="##" onclick="openbox('Grade Exception Rate', 1)">Exception Rate</a>-->
			<!--
			width="50%"
		<span id="horizontalForm" >
			<fieldset >
				{setRow(1,1)}
				{foreach:gradeR,gR}	
					<label for="gradeRate_{getRow(#1#):h}">			
						{gR.code}
						<input type="text" name="data[{getRow():h}][FrznPkgRateGrade][rate]" id="gradeRate_{getRow(#1#):h}_{getRow():h}" value="" size="3" style="text-align:right;" autocomplete="off">
						<input type="hidden" name="data[{getRow():h}][FrznPkgRateGrade][grade_id]" id="frozenCodeId_{getRow():h}" value="{fprRec.frozencode_id}" readonly />
					</label>
				{incrementRow(1)}			
				{end:}	
			</fieldset>
			</span>
			<input type="hidden" name="data[{getRow():h}][gradeRowCount_{getRow():h}]" id="gradeRowCount_{getRow():h}" value="{gradeRecSize}" readonly />-->
		</td>		
	</tr>
		<?php if ($this->options['strict'] || (isset($t) && method_exists($t, 'incrementRow'))) echo htmlspecialchars($t->incrementRow());?>
	<?php }?>		
	</tbody>		
	<?php } else {?>
	<tr><TD align="center"><?php echo htmlspecialchars($t->msg_NoRecs);?></TD></tr>
	<?php }?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?php echo htmlspecialchars($t->qeFprRecSize);?>" />
		</table>
		</div>
		<?php }?>
		</td>
	</tr><?php }?>
<!-- Tab End here  -->
								<?php if ($t->listMode)  {?><tr>
									<td colspan="3" height="5"></td>
								</tr><?php }?>
								<?php if (!$t->listMode)  {?><tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<?php if (!$t->printMode)  {?><tr>
												<td><?php if ($t->edit)  {?><input type="submit" value=" Save " name="cmdSave" class="button" onClick="return validateFrznPkgRate(document.frmFrznPkgRate);"><?php }?>&nbsp;<?php if ($t->print)  {?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('FrozenPackingRate.php?print=y',700,600);"><?php }?></td>
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
<script language="JavaScript" type="text/javascript">
function refreshJS(selRowId, displayHTML)
{
	//alert(displayHTML);
	document.getElementById(selRowId).innerHTML=displayHTML;
} 
</script>
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