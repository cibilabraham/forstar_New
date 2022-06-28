<?php
class QuoteService {
  private $quotes = array("ibm" => 98.42);  

  function getQuote($symbol) {
    if (isset($this->quotes[$symbol])) {
      return $this->quotes[$symbol];
    } else {
      throw new SoapFault("Server","Unknown Symbol '$symbol'.");
    }
  }
}

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache 

$server = new SoapServer("wsdl/test.wsdl");
$server->setClass("QuoteService");
$server->handle(); 
?> 