<?php
include("xmlrpc/lib/xmlrpc.inc");

$amount		= "18.00";
$selDate 	= "23/02/2010";

$format=new xmlrpcmsg('dashboard.onttax',array(new xmlrpcval($amount, "double")));
$client=new xmlrpc_client("/ForstarIM/webservice.php", "localhost", 80);
//$client->setDebug(2);
$request=$client->send($format);
//print "<PRE>" . htmlentities($request->serialize()) . "</PRE>";
//$value=$request->value();
//print_r($value);
//print $value->scalarval();


$format1=new xmlrpcmsg('dashboard.getDailyProdQty',array(new xmlrpcval($selDate, "string")));
$client1=new xmlrpc_client("/ForstarIM/webservice.php", "localhost", 80);
//$client1->setDebug(2);
$request1=$client1->send($format1);
$value1=$request1->value();
echo $value1->scalarval();
echo "<br><br>";

$format2=new xmlrpcmsg('dashboard.getMissingChallan',array(new xmlrpcval($selDate, "string")));
$client2=new xmlrpc_client("/ForstarIM/webservice.php", "localhost", 80);
//$client2->setDebug(2);
$request2=$client2->send($format2);
$value2=$request2->value();
echo $value2->scalarval();

echo "<br><br>";
$format3=new xmlrpcmsg('dashboard.getSOAmt',array(new xmlrpcval($selDate, "string")));
$client3=new xmlrpc_client("/ForstarIM/webservice.php", "localhost", 80);
//$client3->setDebug(2);
$request3=$client3->send($format3);
$value3=$request3->value();
echo $value3->scalarval();

echo "<br><br>";

$format4=new xmlrpcmsg('dashboard.getDespatch',array(new xmlrpcval($selDate, "string")));
$client4=new xmlrpc_client("/ForstarIM/webservice.php", "localhost", 80);
//$client3->setDebug(2);
$request4=$client4->send($format4);
$value4=$request4->value();
echo $value4->scalarval();

?>