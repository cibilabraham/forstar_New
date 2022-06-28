<?php
//
// Initialize SOAP web services
//
//include("SOAP/Client.php");


$client = new SoapClient("http://localhost/ForstarIM/soap/wsdl/test.wsdl",array(
    "trace"      => 1,
    "exceptions" => 0));

try {
	print($client->getQuote("ibm")); 

	print "<pre>\n";
print "Request :\n".htmlspecialchars($client->__getLastRequest()) ."\n";
print "Response:\n".htmlspecialchars($client->__getLastResponse())."\n";
print "</pre>"; 
} 
catch (SoapFault $exception) {
	echo $exception;
}

?>