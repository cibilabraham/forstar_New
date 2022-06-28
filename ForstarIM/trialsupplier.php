<?php

$con=mysql_connect("localhost","root","");
$db=mysql_select_db("forstar_staging");
$qry="select *from supplier";
$result=mysql_query($qry);
while($row=mysql_fetch_array($result))
{
$code= $row['code'];
$id=$row['id'];
$qry1="update supplier set code='$id' where id='$id'";
echo $qry1;
mysql_query($qry1);

}

?>
