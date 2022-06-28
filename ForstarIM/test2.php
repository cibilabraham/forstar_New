
<?
	phpinfo();
	echo 35%3;
	echo "D=".(int)(35/3);
	//if ((string)'0') { print 'ouch'; } else { print 'echo'; } // Ans: echo
	//echo $x = abs(26) + abs(-1) - abs(-0); // Ans: 27
/*
$x = 5;
$x++;
$y = 4;
print "$x";
*/ # 6

//function foo ($i) { return ((($i*2)-3)%3); } echo $x = foo(6); # 0

/*
$x = 3;
switch ($x) {
case 2: echo 'line 1'; break;
case 3:
case 4: echo 'line 2'; break;
default: echo 'line 3';
}
*/ # line2

//echo addslashes("~`!@#$%^&*()_-+=|\}]{[\"':;<,>.?/\\"); # ~`!@#$%^&*()_-+=|\\}]{[\"\':;<,>.?/\\

/*
$x = 4;
$rt = "Debug";
$rt_4 = "Test";
echo ${rt_.$x};
*/ # Test

//echo mysql_escape_string("~`!@#$%^&*()_-+=|\}]{[\"':;<,>.?/\\"); # ~`!@#$%^&*()_-+=|\\}]{[\"\':;<,>.?/\\
/*
$valid = TRUE;
$cnt = 0;
while ($valid = TRUE) {
$cnt++;
if ($cnt > 10) { $valid = FALSE; }
}
echo "$cnt";
*/  # not responding

/*
function x ($y) {
function y ($z) {
return ($z*2); }
return($y+3); }
$y = 4;
$y = x($y)*y($y);
echo "$y";
*/  # 56
/*
$x = 1;
switch ($x) {
case "0": echo "String"; break;
case 0: echo "Integer"; break;
case NULL: echo "NULL"; break;
case FALSE: echo "Boolean"; break;
case "": echo "Empty string"; break;
default: echo "Something else"; break;
}
*/ # Something else

/*
$x = "x";
$y = "x";
$$x = "y";
echo "$x $y";
*/ # Y X

/*
$n = array();
for ($i = 0; $i < 3; $i++) {
$n[] = null;
$num = count($n);
$index =& $n[$num ? $num - 1 : $num];
$index = $i;
}
foreach ($numbers as $index) { print "$index "; }
*/  # Invalid

//In PHP, if $a is TRUE and $b is TRUE, what is ($a xor $b)?

/*$x = $y = 10;
$z = ($x++)-2;
$a = (++$y)*2;
echo "$x $y $z $a";*/

/*
$x = 1;
$y = 2;
$x = $y += 3;
echo "$x $y";
*/

/*
$x=6;
switch ($x) {
case "6b": echo "6b"; break;
case "6": echo "6 empty"; break;
case 6: echo "6 full"; break;
default: echo "6 half";
}
*/

/*
$x = 5;
if ($x > 5) { print "Fruit"; }
elseif ($x = 6) { print "Ice cream"; }
elseif ($x < 6) { print "Vegetables"; }
else { print "Diamond"; }
*/

/*
$x = "y";
$y = "x";
$z = "a";
$$x = "z";
$$$x = "b";
echo "$x $y $z";
*/
/*With a table x (y int(10), z int(10)) having data of 1 1, 2 1, 3 4, 4 2, 5 1 (where first number is y, second number is z (there are 5 rows of data)), what is the output of this MySQL statement "select count(y) from x group by z having count(y) > 1;"*/
/*What is the MySQL output of this statement "select date_add('2004-10-11',interval -2 month);"*/

/*If the current date/time is 2004-10-14 13:05:34, what would be the output of the MySQL statement "select date_format(sysdate(),'%b-%Y-%d-%i');"*/
/* List last three letters Y*/
/*Of the following directories, which one is the most important to back up on a routine basis?*/

/*In Linux, how do you force a particular process to stop executing?*/

/*The sales increased _____ after they put an advertisement on the internet.*/

/*How many minutes before 9AM is it if 90 minutes later it will be 45 minutes to 11AM?*/

/*Ram's mother sent him to the store to get 9 large pineapples. Ram could only carry two pineapples at a time. How many trips to the store did Ram have to make?*/

/*What number is one half of one quarter of one tenth of 800?*/

/*A fish has a head 9 units long. The tail is equal to the size of the head plus one half the size of the body. The body is the size of the head plus the tail. How long is the fish?*/

/*Assume that these two statements are true: All brown-haired men have bad tempers. Harry is a brown-haired man. The statement that Harry has a bad temper is:*/

/*If the day before two days after the day before tomorrow is Monday, what day is today?*/


?>


