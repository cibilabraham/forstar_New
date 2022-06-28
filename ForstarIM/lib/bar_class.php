<?
	Class Bar
	{

		/****************************************************************
		This class deals with all the operations relating to Daily Rate 
		*****************************************************************/
		var $databaseConnect;


		//Constructor, which will create a db instance for this class
		function Bar(&$databaseConnect)
		{
			$this->databaseConnect =&$databaseConnect;
		}

#Fetch data for Graph

	function fetchFishProcessSummaryRecords($dateFrom,$dateTill)
	{
		$qry	=	"select id,fish,fish_code,effective_wt, sum(effective_wt) from t_dailycatchentry where select_date>='$dateFrom' and select_date<='$dateTill' group by fish_code";
		//echo $qry;		
		$result	=	array();
		$result	=	$this->databaseConnect->getRecords($qry);
		return $result;
	}

}	
?>