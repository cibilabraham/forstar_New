	/**
	* Desc: Add day or month or year to  a date
	* @param $type: type to add
	* @param $number: number of days,months or year to add
	* @param $date: date 
	* return value: timestamp newly created date
	**/
	function ctDateAdd($type, $number, $date)
	{
		$ts = str2time($date,'Y-m-d');
		return ctDateAdd2($type,$number,$ts);
	}

	/**
	* Desc: Add Two dates 
	* @param $type: type to add, month, day, year, minutes, seconds,hours
	* @param $number: number of type to add
	* @param $ts: current time stamp
	* return value: return timestamp
	**/
	function ctDateAdd2($type,$number,$ts)
	{
		$date_time_array = getdate($ts);
		$hours = $date_time_array['hours'];
		$minutes = $date_time_array['minutes'];
		$seconds = $date_time_array['seconds'];
		$month = $date_time_array['mon'];
		$day = $date_time_array['mday'];
		$year = $date_time_array['year'];

		switch ($type) {
			case 'yyyy':
				$year+=$number;
				break;
			case 'q':
				$year+=($number*3);
				break;
			case 'm':
				$month+=$number;
				break;
			case 'y':
			case 'd':
			case 'w':
				$day+=$number;
				break;
			case 'ww':
				$day+=($number*7);
				break;
			case 'h':
				$hours+=$number;
				break;
			case 'n':
				$minutes+=$number;
				break;
			case 's':
				$seconds+=$number;
				break;            
		}
	   $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
		return $timestamp;
	}
	
	/**
	* Desc: convert date to timestamp
	* @param $strStr: date string 
	* @param $strPattern: date pattern
	* return value: timestamp 
	**/
	function str2time($strStr, $strPattern = null)
	{
	// an array of the valide date characters, see: http://php.net/date#AEN21898
	$arrCharacters = array(
       'd', // day
       'm', // month
       'y', // year, 2 digits
       'Y', // year, 4 digits
       'H', // hours
       'i', // minutes
       's'  // seconds
	);
	   // transform the characters array to a string
	   $strCharacters = implode('', $arrCharacters);

	   // splits up the pattern by the date characters to get an array of the delimiters between the date characters
	   $arrDelimiters = preg_split('~['.$strCharacters.']~', $strPattern);
	   // transform the delimiters array to a string
	   $strDelimiters = quotemeta(implode('', array_unique($arrDelimiters)));

	   // splits up the date by the delimiters to get an array of the declaration
	   $arrStr    = preg_split('~['.$strDelimiters.']~', $strStr);
	   // splits up the pattern by the delimiters to get an array of the used characters
	   $arrPattern = preg_split('~['.$strDelimiters.']~', $strPattern);

	   // if the numbers of the two array are not the same, return false, because the cannot belong together
	   if (count($arrStr) !== count($arrPattern)) {
		   return false;
	   }

	   // creates a new array which has the keys from the $arrPattern array and the values from the $arrStr array
	   $arrTime = array();
	   for ($i = 0;$i < count($arrStr);$i++) {
		   $arrTime[$arrPattern[$i]] = $arrStr[$i];
	   }

	   // gernerates a 4 digit year declaration of a 2 digit one by using the current year
	   if (isset($arrTime['y']) && !isset($arrTime['Y'])) {
		   $arrTime['Y'] = substr(date('Y'), 0, 2) . $arrTime['y'];
	   }

	   // if a declaration is empty, it will be filled with the current date declaration
	   foreach ($arrCharacters as $strCharacter) {
		   if (empty($arrTime[$strCharacter])) {
			   $arrTime[$strCharacter] = date($strCharacter);
		   }
	   }

	   // checks if the date is a valide date
	   if (!checkdate($arrTime['m'], $arrTime['d'], $arrTime['Y'])) {
		   return false;
	   }

	   // generates the timestamp
	   return mktime($arrTime['H'], $arrTime['i'], $arrTime['s'], $arrTime['m'], $arrTime['d'], $arrTime['Y']);
	}
	
	/**
	* Desc: find the difference between two date in months/days
	* @param $dateTimeBegin: starting date time 
	* @param $dateTimeEnd: ending date time
	* @param $mode: difference in days or in months
	* return date differnce in months/days
	**/
	function ctDateDiff($dateTimeBegin,$dateTimeEnd, $mode=null)
	{      
		 $dif=$dateTimeEnd - $dateTimeBegin;

		 $hours = floor($dif / 3600);
		 //echo "$hours<br>";
		 $temp_remainder = $dif - ($hours * 3600);
		  
		 $minutes = floor($temp_remainder / 60);
		 $temp_remainder = $temp_remainder - ($minutes * 60);
		  
		 $seconds = $temp_remainder;
			
		 // leading zero's - not bothered about hours
		 $min_lead=':';
		 if($minutes <=9)
		   $min_lead .= '0';
		 $sec_lead=':';
		 if($seconds <=9)
		   $sec_lead .= '0';

		 if( $mode=='D'){
			 return floor( ( $hours / 24 ));
		 }
		 else if( $mode=='H'){
			 return $hours;
		 }
		 else	{
			return floor( ( $hours / 24 ) / 30 );
		 }
	} 

