<?
require_once 'flib/AFController.php';

// load all required models
require_once 'components/user/user_model.php';

class user_controller extends AFController
{
	protected $templateFolder = "user";	  	
	
	function index()
	{	
		// load user model
		$user = new user_model();

		// now load template to use for this function
		$this->useTemplate("userlist.html");

		// create a class to represent the page and create the template variables in it. This will be used to render the page
		$page = new stdClass;
		$page->title = "Success";	// specify value for {title} template variable
		$page->userRecords = $user->findAll("");	// put values for the userRecords template variable		
		
		/* Example of how records can be read from a resultset containing objects
		$recs = $user->findAll("");
    		foreach($recs as $rec) {
        		echo $rec->username, " ";
    		}
		*/

		/* How to read a record from database, update in memory and save back to db
		$rec = $user->find("id=25");
		$rec->username="YYY";
		$save['user']=(array)$rec;
		$user->save($save);
		*/

		// finally render the template
		$this->render($page);
	}
	
    	function add($arr)
    	{	
		$user = new user_model();
		$user->save(AFProcessor::preprocess($arr));
	}

	function updateAll($arr)
	{
		//print_r(AFProcessor::preprocessMultiple($arr));
		$user = new user_model();
		$user->saveMultiple(AFProcessor::preprocessMultiple($arr));
	}
}
?>
