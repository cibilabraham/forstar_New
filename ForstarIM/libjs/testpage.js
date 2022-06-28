 $(function() {
  
    // Setup form validation on the #register-form element
    $("#register-form").validate({
    
        // Specify the validation rules
        rules: {
            name: "required",
            message: "required"
        },
        
        // Specify the validation error messages
        messages: {
            name: "Please enter your name",
            message: "Please enter your message"
        },
        
        submitHandler: function(form) {
           // form.submit();
		   save();
        }
    });

  });
  
$(document).ready(function(){
	showComment();
});

//SAVE DATA
function save()
{
	var name=$("#name").val();
    var message=$("#message").val();
	var commentId=$("#commentId").val();
	if(commentId=="")
	{
		var dataValue='{"name":"'+name+'","message":"'+message+'","action":"addcomment"}';
	}
	else
	{
		var dataValue='{"name":"'+name+'","message":"'+message+'","action":"updatecomment","commentId":'+commentId+'}';
	}
	//alert(dataValue);
    $.ajax({
		type:"post",
		dataType:"json",
		url:"testpagesub.php",
		data:{
			data: dataValue
		},
		//data:"name="+name+"&message="+message+"&action=addcomment",
		success: function(data) {
			if(data.status == 'success'){
				alert("comments added succesfully!");
				showComment();
			}
			else if(data.status=="updated")
			{
				alert("updated successfully!");
				showComment();
			}
			else if(data.status == 'error')
			{
				alert("Error on query!");
			}
		}
	});
}

//DELETE ROW
function deleteRow(id)
{
	var dataValue='{"action":"deletecomment","deleteId":'+id+'}';
	$.ajax({
		type:"post",
		url:"testpagesub.php",
		data:{
			data:dataValue
		},
		success:function(data)
		{
			if(data.status=="success")
			{
				alert("successfully deleted");
				showComment();
			}
			else if(data.status=="failed")
			{
				alert("failed to delete");
			}
		}
	});
}

//EDIT ROW
function editRow(id)
{
	var dataValue='{"action":"editcomment","editId":'+id+'}';
	$.ajax({
		type:"post",
		url:"testpagesub.php",
		data:{
			data:dataValue
		},
		success:function(data)
		{
			//alert(data.id);
			$("#commentId").val(data.id);
			$("#name").val(data.name);
			$("#message").val(data.message);
		}
	});
}

//LISTING OF RECORDS
function showComment()
{
	var dataValue='{"action":"showcomment"}';
	// alert("hui");
    $.ajax({
		type:"post",
        url:"testpagesub.php",
		data:{
			data:dataValue
		},
        success:function(data){
			$("#name").val('');
			$("#message").val('');
            $("#comment").html(data);
		}
	});
}