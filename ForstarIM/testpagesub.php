<?php
  mysql_connect("localhost","root","password");
  mysql_select_db("athira");

 
$data=$_POST["data"];
//print_r(json_decode($data));
$dataItems=json_decode($data);
$action=$dataItems->action;
if($action=="addcomment")
{
	$name=$dataItems->name;
	$message=$dataItems->message;
	if($name!="" && $message!="")
	{
		$query=mysql_query("INSERT INTO comments(name,message) values('$name','$message')");
		if($query){
		 $response_array['status'] = 'success';  
		}else {
			$response_array['status'] = 'error';  
		}
	}
	else
	{
		$response_array['status'] = 'error';  
	}
	header('Content-type: application/json');
	echo json_encode($response_array);
	
}
else if($action=="showcomment")
{
	$show=mysql_query("Select * from comments order by id desc");
	$num=mysql_num_rows($show);
	if($num>0)
	{
		echo "<table><tr><td>Name</td><td>Message</td><td>edit</td><td>delete</td></tr>";
		while($row=mysql_fetch_array($show))
		{
			echo "<tr><td>$row[name]</td><td>$row[message]</td><td><a href='javascript:void(0)' onclick='editRow($row[id]);' >edit</a></td><td><a href='javascript:void(0)' onclick='deleteRow($row[id]);' >delete</a></td></tr>";
			//echo "<li><b>$row[name]</b> : $row[message]</b> <a href='javascript:void(0)' onclick='editRow($row[id]);' >edit</a></b> <a href='javascript:void(0)' onclick='deleteRow($row[id]);' >delete</a></li>";
		}
		echo "</table>";
	}
}
else if($action=="deletecomment")
{
	$deleteId=$dataItems->deleteId;
	$query=mysql_query("delete from comments where id='$deleteId'");
	//echo $query;
	if($query)
	{
		$result["status"]="success";
	}
	else
	{
		$result["status"]="failed";
	}
	header('content-type:application/json');
	echo json_encode($result);
}
else if($action=="editcomment")
{
	$editId=$dataItems->editId;
	$query=mysql_query("select * from comments where id='$editId'");
	$numrows=mysql_num_rows($query);
	if($numrows>0)
	{
		$fetch=mysql_fetch_array($query);
		header('content-type:application/json');
		echo json_encode($fetch);
	}
}
else if($action=="updatecomment")
{
	$commentId=$dataItems->commentId;
	$name=$dataItems->name;
	$message=$dataItems->message;
	$qry="update comments set name='$name',message='$message' where id='$commentId'";
	//echo $qry;
	$update=mysql_query($qry);
	if($update)
	{
		$result["status"]="updated";
	}
	else
	{
		$result["status"]="failed";
	}
	header('content-type:application/json');
	echo json_encode($result);
}


//echo "hiii".$action;
//die();
 /* $action=$_POST["action"];
 
	if($action=="showcomment")
	{
		 $show=mysql_query("Select * from comments order by id desc");
		while($row=mysql_fetch_array($show))
		{
			echo "<li><b>$row[name]</b> : $row[message]</li>";
		}
	}
	else if($action=="addcomment")
	{
		$name=$_POST["name"];
		$message=$_POST["message"];
		$query=mysql_query("INSERT INTO comments(name,message) values('$name','$message') ");
		if($query){
			echo "Your comment has been sent";
		}
		else{
			echo "Error in sending your comment";
		}
	}*/
?>