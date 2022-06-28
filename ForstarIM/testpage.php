<html>
   <head>
	<script type="text/javascript" src="libjs/jquery/jquery-1.7.1.js"></script>
    <script type="text/javascript" src="libjs/jquery/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="libjs/jquery/bootstrap.min.js"></script>
  	<script src="libjs/jquery/jquery.validate.min.js"></script>
	<script type="text/javascript" src="libjs/testpage.js"></script>
    <script type="text/javascript">
  /*     $(document).ready(function(){
       function showComment(){
            $.ajax({
            type:"post",
            url:"testpagesub.php",
            data:"action=showcomment",
            success:function(data){
				$("#name").val('');
				$("#message").val('');
                $("#comment").html(data);
			}
          });
        }
		//showComment();
		$("#button").click(function(){
			var name=$("#name").val();
            var message=$("#message").val();
			$.ajax({
              type:"post",
              url:"testpagesub.php",
              data:"name="+name+"&message="+message+"&action=addcomment",
              success:function(data){
              showComment();
            }
		});
	});
  });
*/
  
    // Setup form validation on the #register-form element
  
</script>
</head>
 
   <body>
         <form action="" method="post" id="register-form" novalidate="novalidate">
			<table>
				<tr>
					<td> name :</td><td><input type="text" name="name" id="name"/></td>
				</tr>
				<tr>
					<td> message:</td><td><input type="text" name="message" id="message" /></td>
				</tr>
                <tr>
					<td><input type="hidden" name="commentId" id="commentId" /></td>
				</tr>
			  <tr>
				<td colspan="2" align="center">
					
					<?/*<input type="submit" name="submit" value="Submit" />*/?>
					<input type="submit" value="Send Comment" id="submit" >
				</td>
				</tr>
               <tr>
					<td id="info"></td>
				</tr>
				<tr >
					<td id="comment"></td>
				</tr>
               
			   
			</table>
        </form>
   </body>
</html>