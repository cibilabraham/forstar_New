<?php
$n = "";

function d1($x)
{ // single digit terms
	
	switch($x)
	{
		case '0': $n= ""; break;
		case '1': $n= " One "; break;
		case '2': $n= " Two "; break;
		case '3': $n= " Three "; break;
		case '4': $n= " Four "; break;
		case '5': $n= " Five "; break;
		case '6': $n= " Six "; break;
		case '7': $n= " Seven "; break;
		case '8': $n= " Eight "; break;
		case '9': $n= " Nine "; break;
		default:  $n = "Not a Number";
	}
	return $n;
}

function d2($x)
{ // 10x digit terms
	switch($x)
	{
		case '0': $n= ""; break;
		case '1': $n= ""; break;
		case '2': $n= " Twenty "; break;
		case '3': $n= " Thirty "; break;
		case '4': $n= " Forty "; break;
		case '5': $n= " Fifty "; break;
		case '6': $n= " Sixty "; break;
		case '7': $n= " Seventy "; break;
		case '8': $n= " Eighty "; break;
		case '9': $n= " Ninety "; break;
		default: $n = "Not a Number";
	}
	return $n;
}

function d3($x)
{ // teen digit terms	
	switch($x)
	{
		case '0': $n= " Ten "; break;
		case '1': $n= " Eleven "; break;
		case '2': $n= " Twelve "; break;
		case '3': $n= " Thirteen "; break;
		case '4': $n= " Fourteen "; break;
		case '5': $n= " Fifteen "; break;
		case '6': $n= " Sixteen "; break;
		case '7': $n= " Seventeen "; break;
		case '8': $n= " Eighteen "; break;
		case '9': $n= " Nineteen "; break;
		default: $n= "Not a Number";
	}
	return $n;
}

function convert($input)
{
	$inputlength =strlen($input);
	$x = 0;
	$teen1 = "";
	$teen2 = "";
	$teen3 = "";
	$numName = "";
	$invalidNum = "";
	$a1 = ""; // for insertion of million, thousand, hundred
	$a2 = "";
	$a3 = "";
	$a4 = "";
	$a5 = "";
	$digit = array(); // stores output

	for ($i = 0; $i < $inputlength; $i++)
	{
		// puts digits into array
		$digit[$i] = substr($input,-($i+1),1);
	}

	$store = array(); // store output
	//$store = array(9);
for ($i =0; $i <$inputlength; $i++)
{
	$x= $inputlength-$i;
	switch ($x)
	{ // assign text to each digit
		
		case $x==9: d1($digit[$x-1]); $store[$x] = d1($digit[$x-1]); break;
		
		case $x==8: if ($digit[$x-1] == "1") {$teen3 = "yes";}
		else {$teen3 = "";} d2($digit[$x-1]); $store[$x] = d2($digit[$x-1]); break;
		
		case $x==7: if ($teen3 == "yes") {$teen3 = ""; d3($digit[$x-1]);}
		else {d1($digit[$x-1]);} $store[$x] = d1($digit[$x-1]); break;
		
		case $x==6: d1($digit[$x-1]); $store[$x] = d1($digit[$x-1]); break;
		
		case $x==5: if ($digit[$x-1] == "1") {$teen2 = "yes";}
		else {$teen2 = "";} d2($digit[$x-1]); $store[$x] = d2($digit[$x-1]); break;		
		
		case $x==4:if ($teen2 == "yes"){	$teen2 = ""; d3($digit[$x-1]); $store[$x] = d3($digit[$x-1]); break;}
		else { d1($digit[$x-1]); $store[$x] = d1($digit[$x-1]); break; }

		case $x==3:  d1($digit[$x-1]); $store[$x] =  d1($digit[$x-1]); break;
		
		case $x==2: if ($digit[$x-1] == "1") {$teen1 = "yes";}
		else {$teen1 = "";}  d2($digit[$x-1]); $store[$x] = d2($digit[$x-1]); break;
		
		case $x==1: if ($teen1 == "yes") {$teen1 = "";d3($digit[$x-1]); $store[$x] = d3($digit[$x-1]); break;}
		else { d1($digit[$x-1]); $store[$x] = d1($digit[$x-1]); break;}
	}
	
if ($store[$x] == "Not a Number"){ $invalidNum = "yes";}

switch ($inputlength)
{
	case 1: $store[2] = "";
	case 2: $store[3] = "";
	case 3: $store[4] = "";
	case 4: $store[5] = "";
	case 5: $store[6] = "";
	case 6: $store[7] = "";
	case 7: $store[8] = "";
	case 8: $store[9] = "";
}

if ($store[9] != "") { $a1 =" Hundred, "; } else {$a1 = "";}
if (($store[9] != "")||($store[8] != "")||($store[7] != ""))
{ $a2 =" Million, ";} else {$a2 = "";}
if ($store[6] != "") { $a3 =" Hundred ";} else {$a3 = "";}
if (($store[6] != "")||($store[5] != "")||($store[4] != ""))
{ $a4 =" Thousand, ";} else {$a4 = "";}
if ($store[3] != "") { $a5 =" Hundred "; } else {$a5 = "";}
}
// add up text, cancel if invalid input found
if ($invalidNum == "yes"){$numName = "Invalid Input";}
else {
	$numName = $store[9].$a1.$store[8].$store[7].$a2.$store[6].$a3.$store[5].$store[4].$a4.$store[3].$a5 .$store[2].$store[1];
}

$store[1] = ""; $store[2] = ""; $store[3] = "";
$store[4] = ""; $store[5] = ""; $store[6] = "";
$store[7] = ""; $store[8] = ""; $store[9] = "";
if ($numName == ""){$numName = "Zero" ; }
return $numName;
}

//$input = ceil(12.0);
//echo "<br>Val=".convert($input);
## FLOAT NUMBER CONVERTER STARTS HERE
/*
Copyright 2007-2008 Brenton Fletcher. http://bloople.net/num2text
You can use this freely and modify it however you want.
*/
function convertNum2Text($num)
{
   list($num, $dec) = explode(".", $num);

   $output = "";

   if($num{0} == "-")
   {
      $output = "negative ";
      $num = ltrim($num, "-");
   }
   else if($num{0} == "+")
   {
      $output = "positive ";
      $num = ltrim($num, "+");
   }
   
   if($num{0} == "0")
   {
      $output .= "zero";
   }
   else
   {
      $num = str_pad($num, 36, "0", STR_PAD_LEFT);
      $group = rtrim(chunk_split($num, 3, " "), " ");
      $groups = explode(" ", $group);

      $groups2 = array();
      foreach($groups as $g) $groups2[] = convertThreeDigit($g{0}, $g{1}, $g{2});

      for($z = 0; $z < count($groups2); $z++)
      {
         if($groups2[$z] != "")
         {
            $output .= $groups2[$z].convertGroup(11 - $z).($z < 11 && !array_search('', array_slice($groups2, $z + 1, -1))
             && $groups2[11] != '' && $groups[11]{0} == '0' ? " and " : ", ");
         }
      }

      $output = rtrim($output, ", ");
   }

   if($dec > 0)
   {
      $output .= " point";
      for($i = 0; $i < strlen($dec); $i++) $output .= " ".convertDigit($dec{$i});
   }

   return $output;
}

function convertGroup($index)
{
   switch($index)
   {
      case 11: return " decillion";
      case 10: return " nonillion";
      case 9: return " octillion";
      case 8: return " septillion";
      case 7: return " sextillion";
      case 6: return " quintrillion";
      case 5: return " quadrillion";
      case 4: return " trillion";
      case 3: return " billion";
      case 2: return " million";
      case 1: return " thousand";
      case 0: return "";
   }
}

function convertThreeDigit($dig1, $dig2, $dig3)
{
   $output = "";

   if($dig1 == "0" && $dig2 == "0" && $dig3 == "0") return "";

   if($dig1 != "0")
   {
      $output .= convertDigit($dig1)." hundred";
      if($dig2 != "0" || $dig3 != "0") $output .= " and ";
   }

   if($dig2 != "0") $output .= convertTwoDigit($dig2, $dig3);
   else if($dig3 != "0") $output .= convertDigit($dig3);

   return $output;
}

function convertTwoDigit($dig1, $dig2)
{
   if($dig2 == "0")
   {
      switch($dig1)
      {
         case "1": return "ten";
         case "2": return "twenty";
         case "3": return "thirty";
         case "4": return "forty";
         case "5": return "fifty";
         case "6": return "sixty";
         case "7": return "seventy";
         case "8": return "eighty";
         case "9": return "ninety";
      }
   }
   else if($dig1 == "1")
   {
      switch($dig2)
      {
         case "1": return "eleven";
         case "2": return "twelve";
         case "3": return "thirteen";
         case "4": return "fourteen";
         case "5": return "fifteen";
         case "6": return "sixteen";
         case "7": return "seventeen";
         case "8": return "eighteen";
         case "9": return "nineteen";
      }
   }
   else
   {
      $temp = convertDigit($dig2);
      switch($dig1)
      {
         case "2": return "twenty $temp";
         case "3": return "thirty $temp";
         case "4": return "forty $temp";
         case "5": return "fifty $temp";
         case "6": return "sixty $temp";
         case "7": return "seventy $temp";
         case "8": return "eighty $temp";
         case "9": return "ninety $temp";
      }
   }
}
      
function convertDigit($digit)
{
   switch($digit)
   {
      case "0": return "zero";
      case "1": return "one";
      case "2": return "two";
      case "3": return "three";
      case "4": return "four";
      case "5": return "five";
      case "6": return "six";
      case "7": return "seven";
      case "8": return "eight";
      case "9": return "nine";
   }
}

## FLOAT NUMBER CONVERTER ENDS HERE

//********************************************************
// this function converts an amount into alpha words
// and adds the words dollars and cents.  Pass it a float.
// works up to 999,999,999.99 dollars .
//********************************************************
function makewords($numval)
{
		$moneystr = "";
		// handle the millions
		$milval = (integer)($numval / 1000000);
		if($milval > 0)  {
		  $moneystr = getwords($milval) . " Million,";
		  }
		 
		// handle the thousands
		$workval = $numval - ($milval * 1000000); // get rid of millions
		$thouval = (integer)($workval / 1000);
		if($thouval > 0)  {
		  $workword = getwords($thouval);
		  if ($moneystr == "")    {
			$moneystr = $workword . " Thousand,";
			}else{
			$moneystr .= " " . $workword . " Thousand,";
			}
		  }
		 
		// handle all the rest of the dollars
		$workval = $workval - ($thouval * 1000); // get rid of thousands
		$tensval = (integer)($workval);
		if ($moneystr == ""){
		  if ($tensval > 0){
			$moneystr = getwords($tensval);
			}else{
			$moneystr = "Zero";
			}
		  }else // non zero values in hundreds and up
		  {
		  $workword = getwords($tensval);
		  $moneystr .= " " . $workword;
		  }
		 
		// plural or singular 'dollar'
		$workval = (integer)($numval);
		
		/*
		if ($workval == 1){
		  $moneystr .= " Dollar And ";
		  }else{
		  $moneystr .= " Dollars And ";
		  }
		  */
		 
		// do the cents - use printf so that we get the
		// same rounding as printf
		$workstr = sprintf("%3.2f",$numval); // convert to a string
		$intstr = substr($workstr,strlen - 2, 2);
		$workint = (integer)($intstr);

		if ($workval>0 && $workint>0) $moneystr .= " And ";

		if ($workint>0) $moneystr .= getwords($workint);
		
		if ($workint == 1) {
		  $moneystr .= " Cent";
		 } else if ($workint>0) {
		  $moneystr .= " Cents";
		 }
		/*
		if ($workint == 0){
		  $moneystr .= "Zero";
		  }else{
		  $moneystr .= getwords($workint);
		  }
		  
		if ($workint == 1) {
		  $moneystr .= " Cent";
		 }else{
		  $moneystr .= " Cents";
		 }
		*/
		 
		// done 
		return $moneystr;
}

//*************************************************************
// this function creates word phrases in the range of 1 to 999.
// pass it an integer value
//*************************************************************
function getwords($workval)
{
		$numwords = array(
		  1 => "One",
		  2 => "Two",
		  3 => "Three",
		  4 => "Four",
		  5 => "Five",
		  6 => "Six",
		  7 => "Seven",
		  8 => "Eight",
		  9 => "Nine",
		  10 => "Ten",
		  11 => "Eleven",
		  12 => "Twelve",
		  13 => "Thirteen",
		  14 => "Fourteen",
		  15 => "Fifteen",
		  16 => "Sixteen",
		  17 => "Seventeen",
		  18 => "Eighteen",
		  19 => "Nineteen",
		  20 => "Twenty",
		  30 => "Thirty",
		  40 => "Forty",
		  50 => "Fifty",
		  60 => "Sixty",
		  70 => "Seventy",
		  80 => "Eighty",
		  90 => "Ninety");
		 
		// handle the 100's
		$retstr = "";
		$hundval = (integer)($workval / 100);
		if ($hundval > 0){
		  $retstr = $numwords[$hundval] . " Hundred";
		  }
		 
		// handle units and teens
		$workstr = "";
		$tensval = $workval - ($hundval * 100); // dump the 100's
		 
		// do the teens
		if (($tensval < 20) && ($tensval > 0)){
		  $workstr = $numwords[$tensval];
		   // got to break out the units and tens
		  }else{
		  $tempval = ((integer)($tensval / 10)) * 10; // dump the units
		  $workstr = $numwords[$tempval]; // get the tens
		  $unitval = $tensval - $tempval; // get the unit value
		  if ($unitval > 0){
			$workstr .= " " . $numwords[$unitval];
			}
		  }
		 
		// join the parts together 
		if ($workstr != ""){
		  if ($retstr != "") {
			$retstr .= " " . $workstr;
			}else{
			$retstr = $workstr;
			}
		  }
		return $retstr;
}




?>