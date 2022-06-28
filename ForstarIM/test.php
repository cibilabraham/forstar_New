<?
require_once("components/user/user_controller.php");
$uc = new user_controller();

// if go button is clicked, add a record to database
if ( isset($_POST['go']) ) $uc->add($_POST);

// if update button is clicked, update records to db
if ( isset($_POST['update']) ) $uc->updateAll($_POST);

// display page
$uc->index();

?>
