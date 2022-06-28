<?php
class Xajax_jquery extends xajaxResponsePlugin {
  var $sCallName = 'jquery';
  var $validBaseMethods = Array('addclass','append','background','after','removeClass','hide','show');


  function buildArgs($iArgs) {
	$args = false;
	// Skip the first arg it is always the jquery expression
	$count = sizeof($iArgs);
	for($i = 1; $i < $count; $i++) {
	if($args) {
		$args .= ',';
	}
	$args .= '"'.$value[$i].'"';
	}
	return $args;
  }

  function __call($method, $args) {	
    if(!in_array($method, $this->validBaseMethods)) {
      error_log($method.' method has not been tested with jquery to work!');
    }
    $exp = $args[0];
    $args = $this->buildArgs($args);
    $this->_objResponse->script('$("'.$exp.'").'.$method.'('.$args.')');
  }

}

$pluginManager = &xajaxPluginManager::getInstance();
$pluginManager->registerPlugin(new Xajax_jquery());
//$xajax->pluginManager->registerPlugin(new Xajax_jquery()); 
?>