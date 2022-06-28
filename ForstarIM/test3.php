<?php
//require("include/include.php");
require_once("lib/test_ajax.php");
?> 
<html>
  <head>
	<?php
	$xajax->printJavascript("libjs/");
	?>
<script src="libjs/jquery/jquery-1.4.js" type="text/javascript"></script>

    <script type="text/javascript">
	function showHint(str)
	{	
		if (str.length==0) { 
			document.getElementById("txtHint").innerHTML="";
			return;
		}
	
	
		if (window.XMLHttpRequest){		
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {		
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		xmlhttp.onreadystatechange=function()
		{			
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","test4.php?q="+str,true);
		//xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xmlhttp.send(null);
	}
</script>
  </head>
  <body>
<div id="div1" name="div1">sss</div>
<button onClick="xajax_hide()">JQUERY HIDE</button>
  <button onClick="xajax_show()">JQUERY SHOW</button>
    <h3>
       Start typing a name in the input field below: 
    </h3>
    <form action="">
       First name: <input type="text" id="txt1" onkeyup="showHint(this.value)" />
    </form>
    <p>
       Suggestions: <span id="txtHint"></span>
    </p>
  </body>
</html>
