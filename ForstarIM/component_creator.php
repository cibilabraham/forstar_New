<?php
	require_once("code_creator/code_creator.php");
	if ( $_POST["create"] != "" )	{
		$cc = new code_creator($_POST);
		$cc->create_component();
	}
?>
<html>
	<body>
		<form method="POST">
		<table width="80%" align="center">
			<tr>
				<td>
					Functionality Name:				
				</td>
				<td>
					<input type="text" name="functionalityName">
				</td>
			</tr>
			<tr>
				<td>
					Table Name:				
				</td>
				<td>
					<input type="text" name="tableName">
				</td>
			</tr>
			<tr>
				<td>
					Model Name:				
				</td>
				<td>
					<input type="text" name="modelName">
				</td>
			</tr>
			<tr>
				<td>
					Model Short Name:				
				</td>
				<td>
					<input type="text" name="modelShortName">
				</td>
			</tr>
			<tr>
				<td>
					Routing Page Prefix:				
				</td>
				<td>
					<input type="text" name="routingPagePrefix">
				</td>
			</tr>			
			<tr>
				<td colspan="2" align="center">					
					<input type="submit" name="create" value="Create">
				</td>
			</tr>
		</table>
		</form>
	</body>
</html>
