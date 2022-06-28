<?php
	
	$val=0;
	$submitmode=false;
	if($_POST["clickbtn"]!="")
	{
		$submitmode=true;
		$val =$_POST["txtValue"];
		$val++;
	}

?>
<html>
<script type="text/javascript">
	//function incrementValue(rec)
	//{
		//document.getElementById("hdnvalue").value=rec;
		//return true;
	//}
</script>
<head>
		<body>
			<form name="mayooritest2Form" action="mayooritest2.php" method="post">
			
			<?if(submitmode){
			
			?>
			<input type="submit" name="clickbtn" id="clickbtn" value="click">
			<input type="text" id="txtValue" name="txtValue" value="<?php echo $val ?>" >
			<? } ?>

			</form>
		</body>
</head>
</html>