<?php 
	$pn = strtolower(basename($_SERVER["SCRIPT_FILENAME"], '.php'));
?>
<script language='javascript'>
	function funcSubmenu(ind){
		document.getElementById(ind).style="display:block;";
	}
</script>
<table cellpadding="0" cellspacing="0" align="left" width="200" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td class="rounded-company" widh="20%" style="cursor:hand;padding-left:5px;">
		<?php 
			$vardisplay = "none;";
			$isstrong = false;
			
			if($pn=='tasklist' or $pn=='productidentifiermaster' or $pn=='productstatus' or $pn=='distmarginstructure' or $pn=='productbatch'){
				$vardisplay="block;" ;
				$isstrong = true;
			} 
			
			if($isstrong) echo "<a href='tasklist.php' onclick='javascript:funcSubmenu(1); style='text-decoration:none;'><strong> >> Task Manage </strong></a>";  	
			else echo "<a href='tasklist.php' style='text-decoration:none;'> >> Task Manage </a>";  
		?>	
		<br>
	<div id="1" style="display:<?=$vardisplay?>;padding-left:5px;">
	<table cellpadding="0" cellspacing="0" border="0" >
		<tr>
			<td>
			<a href='DistMarginStructure.php' style='text-decoration:none;'> 
			<?php
				if($pn=='distmarginstructure')
					 echo "<strong> >> Add Task</strong>"; 
				else echo ">> Add Task";
			?>
			</a>
			</td>
		</tr>
		
		<tr>
			<td><a href='ProductStatus.php' style='text-decoration:none;'> 
				<?php 
					if($pn=='productstatus')echo "<strong> >> Product Management</strong>"; 
					else echo ">> Product Management";
				?>			
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='ProductIdentifierMaster.php' style='text-decoration:none;'> 
				<?php 
				if($pn=='productidentifiermaster')echo "<strong> >> Product Identifier Master</strong>"; 
				else echo ">> Product Identifier Master";
				?>	
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='ProductBatch.php' style='text-decoration:none;'> 
				<?php 
				if($pn=='productbatch')echo "<strong> >> Product Batch</strong>"; 
				else echo ">> Product Batch";
				?>	
				</a>
			</td>
		</tr>
		
		
	</table>
	<div>
	</td>
	</tr>

	<tr>
	<td class="rounded-company" widh="20%" style="cursor:hand;padding-left:5px;">
		<?php 
			$vardisplay = "none;";
			$isstrong = false;
			
			if($pn=='ingredientspex' or $pn=='ingredientpo' or $pn=='ingredientreceipt'){
				$vardisplay="block;";
				$isstrong = true;
			} 
			if($isstrong) echo "<a href='IngredientSpex.php' style='text-decoration:none;'><strong> >> Ingredient </strong></a>";  	
			else echo "<a href='IngredientSpex.php' style='text-decoration:none;'> >> Ingredient </a>";  
		?>	
			<br>
	<div id="4" style="display:<?=$vardisplay?>;padding-left:5px;">
	<table cellpadding="0" cellspacing="0" border="0" >
		<tr>
			<td><a href='IngredientPO.php' style='text-decoration:none;'> 
			<?php
				if($pn=='ingredientpo')
					echo "<strong> >> Create Ingredient PO</strong>"; 
				else echo ">> Create Ingredient PO";
			?>
				</a>
			</td>
		</tr>
		
		<tr>
			<td><a href='IngredientReceipt.php' style='text-decoration:none;'> 
			<?php
				if($pn=='ingredientreceipt')
					echo "<strong> >> Ingredient Receipt</strong>"; 
				else echo ">> Ingredient Receipt";
			?>
				</a>
			</td>
		</tr>
	
	</table>
	<div>
	
	
	</td>
	</tr>
	<tr>
		<td class="rounded-company" widh="20%" style="cursor:hand;padding-left:5px;">
	<?php 
		$vardisplay = "none;";
		$isstrong = false;
		
		if($pn=='recipespex'){
			//$vardisplay="block;" ;
			$vardisplay = "none;";			
			$isstrong = true;
		} 
		if($isstrong) echo "<a href='RecipeSpex.php' onclick='javascript:funcSubmenu(2);' style='text-decoration:none;'><strong> >> Recipe </strong></a>";  	
		else echo "<a href='RecipeSpex.php' style='text-decoration:none;'> >> Recipe </a>";  
	?>	
	</a>
	<br>
	<div id="2" style="display:<?=$vardisplay?>;padding-left:5px;">
	<!--
	<table cellpadding="0" cellspacing="0" border="0" >
		<tr>
			<td>Link 1</td>
		</tr>
		<tr>
			<td>Link 2</td>
		</tr>
		<tr>
			<td>Link 3</td>
		</tr>
		<tr>
			<td>Link 4</td>
		</tr>
	
	</table>-->
	<div>
	</td></tr>
	<tr>
	<td class="rounded-company" widh="20%" style="cursor:hand;padding-left:5px;">
	<?php 
		$vardisplay = "none;";
		$isstrong = false;
		if($pn=='productionspex' or $pn=='productionplanning'){
			$vardisplay="block;" ;	
			$isstrong = true;
		} 
		if($isstrong) echo "<a href='ProductionSpex.php' onclick='javascript:funcSubmenu(3);' style='text-decoration:none;'><strong> >> Production </strong></a>";  	
		else echo "<a href='ProductionSpex.php' onclick='javascript:funcSubmenu(3);' style='text-decoration:none;'> >> Production </a>";  
	?>	
	<br>
	<div id="3" style="display:<?=$vardisplay?>;padding-left:5px;">
	<table cellpadding="0" cellspacing="0" border="0" >
		<tr>
			<td><a href='ProductionPlanning.php' style='text-decoration:none;'>
				<?php 
					if($pn=='productionplanning')echo "<strong> >> Production Plan</strong>"; 
					else echo ">> Production Plan";
				?>
				</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='ProductionPlanning.php' style='text-decoration:none;'> >> Purchase Plan</a>
			</td>
		</tr>
	</table>
	
	</td></tr>
	<tr>
	<td class="rounded-company" widh="20%" style="cursor:hand;padding-left:5px;">
	<?php 
		//$vardisplay = "none;";
		$isstrong = false;
		
		if($pn=='purchaseintent'){
			//$vardisplay="block;" ;
			//$vardisplay = "none;";			
			$isstrong = true;
		} 
		if($isstrong) echo "<a href='PurchaseIntent.php' onclick='javascript:funcSubmenu(5);' style='text-decoration:none;'><strong> >> Purchase </strong></a>";  	
		else echo "<a href='PurchaseIntent.php' style='text-decoration:none;'> >> Purchase </a>";  
	?>
	</td>
	</tr>
</table>
<!-- <br><br><br> -->

