<html>
<head>
  
  <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
  <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
  
  <link href="runnable.css" rel="stylesheet" />
  <!-- Load jQuery and the validate plugin -->
  <script src="//code.jquery.com/jquery-1.9.1.js"></script>
  <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>
  
  <!-- jQuery Form Validation code -->
  <script>
  
  // When the browser is ready...
  $(function() {
  
    // Setup form validation on the #register-form element
    $("#register-form").validate({
    
        // Specify the validation rules
        rules: {
            firstname: "required",
            lastname: "required",
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
					              minlength: 5
            },
            agree: "required"
        },
        
        // Specify the validation error messages
        messages: {
            firstname: "Please enter your first name",
            lastname: "Please enter your last name",
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 5 characters long"
            },
            email: "Please enter a valid email address",
            agree: "Please accept our policy"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
    });

  });
  
  </script>
</head>
<body>
  <h1>Register here</h1>
  
  <!--  The form that will be parsed by jQuery before submit  -->
  <form action="" method="post" id="register-form" novalidate="novalidate">
  
    <div class="label">First Name</div><input type="text" id="firstname" name="firstname" /><br />
    <div class="label">Last Name</div><input type="text" id="lastname" name="lastname" /><br />
    <div class="label">Email</div><input type="text" id="email" name="email" /><br />
    <div class="label">Password</div><input type="password" id="password" name="password" /><br />
    <div style="margin-left:140px;"><input type="submit" name="submit" value="Submit" /></div>
    
  </form>
  
</body>
</html>




















<?php
$a = 5;
$b = "a";
echo "h==>".$$b;
?>


<script>
var url = 'http://www.newbornlog.com/dev/uploads/pending/NP_4BF63F3101F1C.png?id=66';


var extractTo = (url.indexOf("?")==-1)?url.length:url.indexOf("?");
var fileName = url.substring(url.lastIndexOf("/")+ 1, url.length);
 //alert(fileName);
</script>
<?php

echo "==>". trim(strtok("FL :",":"));

$url = "http://www.newbornlog.com/dev/uploads/processed_small_thumbs/NP_4BF63063E490A.png?id==&id=1274425548590";
echo strtok($url,'?');
echo "==>".basename(strtok($url,'?'));

//phpinfo();


?> 
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Adding and Removing Rows from a table using DHTML and JavaScript
		</title>
        <script language="javascript">

            //add a new row to the table
            function addRow()
            {
                //add a row to the rows collection and get a reference to the newly added row
                var newRow = document.getElementById("tblGrid").insertRow(-1);

                newRow.className = "even";
                //add 3 cells (<th>) to the new row and set the innerHTML to contain text boxes

                var oCell = newRow.insertCell(-1);
                oCell.innerHTML = "<input type='text' name='t1'>";

                oCell = newRow.insertCell(-1);
                oCell.innerHTML = "<input type='text' name='t2'>";

                oCell = newRow.insertCell(-1);
                oCell.innerHTML = "<span style='text-align:center'><input type='text' name='t3'></span>";

                oCell = newRow.insertCell(-1);
                oCell.innerHTML = "<input type='button' value='Delete' onclick='removeRow(this);'/>";

            }

            //deletes the specified row from the table
            function removeRow(src)
            {
                /* src refers to the input button that was clicked.
                   to get a reference to the containing <tr> element,
                   get the parent of the parent (in this case case <tr>)
                */
                var oRow = src.parentNode.parentNode;

                //once the row reference is obtained, delete it passing in its rowIndex
                document.getElementById("tblGrid").deleteRow(oRow.rowIndex);

            }

        </script>
        <style>
        /*---------------------------------*/
/*      Table Grid     */
/*---------------------------------*/

/* The initial default settings for the Table */
table.grid {
    width: 100%;
    height: 20px;
    border: 1px solid #6688A4;
    border-collapse: collapse;
}

/* Style for the title header of the table */
tr#header {
    border-bottom: 1px solid #6688A4;
    background-color: #6688A4;
    color: #FFFFFF;
    font-family: Arial;
    font-weight: bold;
    font-size: 11px;
    padding-left: 0px;
    height: 20px;
}

tr#header th{
    padding-left: 12px;
    color: #FFFFFF;
    font-family: arial;
    font-size: 11px;
    text-align: left;
}


/* Style for the row containing the MAIN PARAMETERS for the table (row after the
 Title row) */
tr#mainDiv {
    background-color: #CCCCCC;
    color: #000;
    border-top: 1px solid #FFFFFF;
    border-left: 1px solid #6688A4;
    border-right: 1px solid #6688A4;
    height: 18px;
}

tr#mainDiv th{
    text-align: center;
    font-family: arial;
    font-size: 11px;
    font-weight: normal;
    border-top: 1px solid #FFFFFF;
    border-left: 1px solid #FFFFFF;
    border-right: 1px solid #FFFFFF;
}



/* Style for the EVEN data rows with the white background */
tr.even {
    background-color: #FFFFFF;
    border-left: 1px solid #6688A4;
    border-right: 1px solid #6688A4;
    height: 28px;
}
tr.even th,tr.even td{
    padding-left: 5px;
    color: #333333;
    font-family: arial;
    font-size: 11px;
    font-weight:normal;
    text-align: left;
    padding-right: 5px;
    border: 1px solid #E6E6E6;
}

tr.even th#yr{
    text-align: center;
}

tr.even th#inc{
    color: #008000;
}

tr.even th#dec{
    color: #EF0303;
}
</style>
</head>
    <body>
	<p>
        Demo of a simple table grid that allows adding and deleting rows using D
HTML
        and Javascript
        </p>
		<p>
        Try it out - Click on the Delete button to delete the corresponding row.
 Click Add Row button to insert a new row.
        </p>
        <p>Browser compatility - this sample has been tested to work with IE5.0
 and above.
        </p>

        <input type="button" value="Add Row" onClick="addRow();" />
        <hr>        
        <table class="grid" id="tblGrid" style="table-layout:fixed" border="0">
            <tr id="mainDiv">
                <th style="width:150px;">Field1</th>
                <th style="width:150px;">Field2</th>
                <th style="width:250px;">Field3</th>
                <th style="width:250px;">Action</th>
            </tr>
            <tr class="even">
                <th><input type="text" name="t1" /></th>
                <th><input type="text" name="t2" /></th>
                <th><input type="text" name="t3" /></th>
                <th><input type="button" value="Delete" onClick="removeRow(this);" /></th>
</tr>
</table>
<hr>

    </body>
</html>-->