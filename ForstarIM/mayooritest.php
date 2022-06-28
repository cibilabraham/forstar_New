<?php
//require("lib/mayooritest_class.php");
//$testobj= new mayooritest($databaseConnect);
$con=mysqli_connect("localhost","root","password","mayoori");
	if($con)
	{
		echo "connected to db";
		echo "<br>";
	}
	if(mysqli_connect_errno())
	{
		echo "failed to connect db";
	}	
	
	//$totRecord = mysqli_query($con,"select count(ID) from student");
	
	//$result = mysqli_query($con,"select ID,NAME,COLLEGE from student");
	/*while($row=mysqli_fetch_array($result))
	{
		echo $row['NAME'];   echo "   :    ";
		echo $row['COLLEGE'];
		echo "<br>";
	}
	*/
	
	$editMode=false;
	$addMode=false;
	
	if($_POST["editBtn"]!="" ||($_POST["refreshBtn"]!="" && $_POST["recEditId"]!=""))
	{	
		$editMode=true;
		$recEditIdVal= $_POST["recEditId"];
		//echo $recEditIdVal;
		//$rowCount=$_POST["hdnRowCount"];
		//for($i=1;$i<=$rowCount;$i++)
		//{
			
			//$editId = $_POST["hdnEditId_".$i];
			
		//}
		$viewData = mysqli_query($con,"select ID,NAME,COLLEGE from student where ID='$recEditIdVal'");

	}
	
	
	if($_POST["saveBtn"]!="")
	{
		//$savemode=true;
		$id= $_POST["recEditId"];
		$name = $_POST["name"];
		$college=$_POST["college"];
		$editResult = mysqli_query($con,"update student set NAME = '$name',COLLEGE = '$college' where ID='$id' ");
		if($editResult)
		{
			echo "successfully updated";
		}
		else
		{
			echo "failed to update";
		}
	}
	
	if($_POST["delBtn"]!="")
	{
		
		$rcount = $_POST["hdnRowCount"];
		// echo $chkStId;
		for($i=1;$i<=$rcount;$i++)
		{
			$chkid=$_POST["chkId_".$i];
			//echo $chkid;
			//$delid = $_POST["hdnEditId_".$i];
			echo $delid;
			if($chkid!="")
			{
				$delRecord = mysqli_query($con,"delete from student where ID = '$chkid'");
			}
	
		}
	
	
		//print_r($delRecord);
		if($delRecord)
		{
			echo "record deleted successfully";
		}
		else
		{
			echo "record deletion failed";
		}
	}
	
	if($_POST["addBtn"]!="")
	{
		$addMode=true;
	}
	if($_POST["addNewBtn"]!="")
	{
		$newname = $_POST["newname"];
		//echo $name;
		$newcollege = $_POST["newcollege"];
		//echo $college;
		//$insert = mysqli_query($con,"insert into student(NAME,COLLEGE) values('$newname','$newcollege')");
		
	}
	if($_POST["selectAll"]!="")
	{
		
	}
	$testname=$_POST["nameAjax"];
	echo $testname;
	print_r($_POST);
	$result = mysqli_query($con,"select ID,NAME,COLLEGE from student");
?>
<html>
<head>
<script src="jquery-1.11.1.min.js"></script>
<script type="text/javascript">
	function assignEditValue(editvalue)
	{
		document.getElementById("recEditId").value=editvalue;		
		return true;
		
	}
	function saveValue()
	{
		var name = document.getElementById("name").value;
		var college = document.getElementById("college").value;
		if(name=="")
		{
			alert("please enter name");
			return false;
		}
		if(college=="")
		{
			alert("please enter college name");
			return false;
		}
		return true;
	}

	function checkboxValidation()
	{
		 var count=document.getElementById("hdnRowCount").value;
		 var flag=0;
		 for(i=1;i<=count;i++)
		 {
			if(document.getElementById("chkId_"+i).checked)
			{

				//alert("heloo");
				//document.getElementById("chkSelect").value;
				flag++;
			}
		 }
		 if(flag<=0)
		 {
			alert("please select atleast one name");
			return false;
		 }
		 return true;
		
	}
	function selectAllChekBox()
	{
		 var count=document.getElementById("hdnRowCount").value;
		 if(document.getElementById("selectAll").checked)
		 {
			for(i=1;i<=count;i++)
			{
				document.getElementById("chkId_"+i).checked=true;
				
			}
		}
		else
		{
			for(i=1;i<=count;i++)
			{
				document.getElementById("chkId_"+i).checked=false;
			}
		}
	}
	
	function addNewRcdValidation()
	{
		//alert("hai");
		var newname = document.getElementById("newname").value;
		var newcollege=document.getElementById("newcollege").value;
		//alert(newname);
		if(newname=="")
		{
			alert("please enter the name");
			return false;
		}
		if(newcollege=="")
		{
			alert("please enter college name");
			return false;
		}
		
		$.ajax({
			type="POST",
			data:{"nameAjax" :" newname","collegeAjax":"newcollege","action" : "addNewBtn"},
			url:"mayooritest.php",
			dataType: "json",
			success: successFunc,
			error: errorFunccontentType: "application/json; charset=utf-8",
		
		});
		
		return true;
	}
	function cancel()
	{
		var msg = "do you wish to cancel ?";
		if(confirm(msg))
		{
			window.location.href="mayooritest.php"
		}
		else
		{
			return false;
		}
		return true;
	}
	
	
</script>
</head>
<body>
<form  name="MayooriTestForm" action="mayooritest.php" method="post">
	<table id="studenttbl" align="center" border="1" width="30%">
	
			<tr>
				<td><input type="checkbox" name="selectAll" id="selectAll" onClick ="selectAllChekBox();" ></td>
				<th>NAME</th>
				<th colspan="2">COLLEGE</th>
				
			</tr>
			
			<?$rowCount=0;?>
			<?php
			
			while($row = mysqli_fetch_array($result))
			{
			$rowCount++;
			?>
			
			<tr>
			<td><input type="checkbox" id="chkId_<?=$rowCount?>" name="chkId_<?=$rowCount?>" value="<?=$row['ID']?>" ></td>
			<td><? echo $row['NAME']?></td>
			<td><? echo $row['COLLEGE'] ?></td>
			<td><input type="submit" id="editBtn" name="editBtn" value="edit" onClick="return assignEditValue(<?=$row['ID']?>);"></td>
			<td><input type="hidden" name="hdnEditId_<?=$rowCount?>" id="hdnEditId_<?=$rowCount?>" value="<?=$row['ID']?>"></td>
			
			</tr>	
			
			<?}?>
			<tr>
			<td colspan="4" align="center"><input type="submit" name="delBtn" value="Delete" 
			onClick=" return checkboxValidation();">
			<input type="submit" name="refreshBtn" id="refreshBtn" value="refresh">
			<input type="submit" name="addBtn" id="addBtn" value="Add">
			</td>
			</tr>
			
	</table>
	
	
			<input type="hidden" name="hdnRowCount" id="hdnRowCount" value="<?=$rowCount?>">
			<input type="hidden" name="recEditId" id="recEditId" value="<?= $recEditIdVal?>"> 
			
			<?if($editMode){?>
	<table id="studentEditTbl" name="studentEditTbl" align="center" border="1" >
			<tr>
				<th>NAME</th>
				<th colspan="3">COLLEGE</th>
			</tr>
			<?php 
			while($row = mysqli_fetch_array($viewData))
			{?>
			<tr>
				
				<td><input type="text" name="name" id="name" value="<?=$row['NAME']?>"> </td>
				<td><input type="text" name="college"  id="college" value="<?=$row['COLLEGE']?>" ></td>
				<td><input type="submit" name="saveBtn" value="save" onClick=" return saveValue();"></td>
				<td><input type="submit" name="cancelBtn" value="cancel" onClick=" return cancel()" ></td>
				
			</tr>
			<? } ?>
			
	</table>	
		
			<? }?>
			
	<? if($addMode) {?>		
	<table id="studentAddTbl" name="studentAddTbl" align="center" border="1">
		<tr>
			<th>NAME</th>
			<th colspan="3">COLLEGE</th>
		</tr>
		<tr>
			<td><input type="text" name="newname" id="newname" value="<?=$newname?>"></td>
			<td><input type="text" name="newcollege" id="newcollege" value="<?=$newcollege?>"></td>
			<td><input type="submit" name="addNewBtn" id="addNewBtn" value="Add" onClick="return addNewRcdValidation();"></td>
			<td><input type="submit" name="cancelBtn" value="cancel" onClick=" return cancel()"></td>
		</tr>
	</table>
	<? } ?>
		
</form>
</body>
</html>