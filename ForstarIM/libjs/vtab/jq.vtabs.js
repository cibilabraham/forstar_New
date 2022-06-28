/*
    Description:
    ------------
    1. Function dynamically created vertical tabs for current "div" tag.
    2. In order to create vertical tabs you should call this function like this
       $(div_obj).addVtabs(tabsObj), where 

       "div_obj" - is container div tag we will 'stick' vertical tabs to, 
       "tabsObj" - is an array with tabs' data in format like this:
       var tabsObj = [ 
           {"myTabID":"myTab1", "myTabName":"Hello", "feed":"./tabs/tab-1.html", "curr":"true"}, 
           {"myTabID":"myTab2", "myTabName":"Good Bye", "feed":"./tabs/tab-2.html", "curr":"false"},
           {"myTabID":"myTab3", "myTabName":"Again", "feed":"./tabs/tab-3.html", "curr":"false"}
        ];
*/
(function($){
	
    // Adding new jQuery functions (Tabs)
    $.fn.addVtabs = function(tabsObj){

        var containerId = '#'+$(this).attr('id');  // this container ID
        var tabsId = '#tabs'; // UL tag ID 

        // Adding UL tag in container in order to place it vertically 
        $(containerId).before('<div id="moving_div"><ul align="inherit" class="mytabs" id="tabs"></ul></div>');

        for (var i=0; i<tabsObj.length; i++){		
            if (tabsObj[i].curr == 'true'){  // is the tab is default? Adding special class "current"
                //$(tabsId).append('<li class="current"><a href="'+tabsObj[i].feed+'" onclick="loadDiv('+tabsObj[i].myTabID+')">'+tabsObj[i].myTabName+'</a></li>');
		//$(tabsId).append('<li class="current"><a href="'+tabsObj[i].feed+'" id="'+tabsObj[i].myTabID+'" onclick="loadDiv(\''+tabsObj[i].locId+'\',\''+$(this).attr('id')+'\')" >'+tabsObj[i].myTabName+'</a><span class="editTab" href="#">Edit</span><span class="removeTab" href="#">Remove</span><a href="##">Edit</a><a href="##">Remove</a></li>');
		//<img src="images/x.png" width="20" height="20" border="0" />
		//$(tabsId).append('<li class="current"><a href="'+tabsObj[i].feed+'" id="'+tabsObj[i].myTabID+'" onclick="loadDiv(\''+tabsObj[i].locId+'\',\''+$(this).attr('id')+'\')" class="tab">'+tabsObj[i].myTabName+'</a><div class="ctlDiv"><a href="##" class="editTab">E</a>&nbsp;<a href="##" class="removeTab">X</a></div></li>');
		$(tabsId).append('<li class="current"><a href="'+tabsObj[i].feed+'" id="'+tabsObj[i].myTabID+'" onclick="loadDiv(\''+tabsObj[i].locId+'\',\''+$(this).attr('id')+'\')" class="tab">'+tabsObj[i].myTabName+'</a><div class="ctlDiv"><a href="##" class="removeTab">X</a></div></li>');
            } else {
                //$(tabsId).append('<li><a href="'+tabsObj[i].feed+'" onclick="loadDiv('+tabsObj[i].myTabID+')">'+tabsObj[i].myTabName+'</a></li>');
		//$(tabsId).append('<li><a href="'+tabsObj[i].feed+'" id="'+tabsObj[i].myTabID+'" onclick="loadDiv(\''+tabsObj[i].locId+'\',\''+$(this).attr('id')+'\')" class="tab">'+tabsObj[i].myTabName+'</a><div class="ctlDiv"><a href="##" class="editTab">E</a>&nbsp;<a href="##" class="removeTab">X</a></div></li>');
		$(tabsId).append('<li><a href="'+tabsObj[i].feed+'" id="'+tabsObj[i].myTabID+'" onclick="loadDiv(\''+tabsObj[i].locId+'\',\''+$(this).attr('id')+'\')" class="tab">'+tabsObj[i].myTabName+'</a><div class="ctlDiv"><a href="##" class="removeTab">X</a></div></li>');
            }
        }  

        // Preload tab on page load
        if($(tabsId + ' li.current a').length > 0){
		$(tabsId + ' li.current a').click();
		//loadDiv("'"+tabsObj[i].locId+"'","'"+$(this).attr('id')+"'");
		//$.fn.addVtabs.loadTab($(tabsId + ' li.current a'), containerId);
        }

        // Changing position of the tabs' container
        var pos = $(containerId).position();  // position of the main container
       // posLeft = parseInt(pos.left) - parseInt($("#moving_div").width()) + 1;
	posLeft = parseInt(pos.left) - parseInt($("#moving_div").width())+10;

	if($.browser.msie){
		//$("#moving_div").css({"left":60, "top":143});  // moving to new coordinates
		$("#moving_div").css({"left":posLeft, "top":pos.top});  // moving to new coordinates
	} else {
		//$("#moving_div").css({"left":80, "top":277});  // moving to new coordinates
		$("#moving_div").css({"left":posLeft, "top":pos.top});  // moving to new coordinates
	}

	//$("#moving_div").css({"left":posLeft, "top":pos.top});  // moving to new coordinates

        // Filling with background image
        $("#moving_div li a").each(function(){
            var text = $(this).text();
            $(this).text(text);
           // var myHeight = text.length * 10;
            //$(this).css({"background-image":"url(pic_gen.php?text="+escape(text)+")", "background-repeat":"no-repeat", "background-position":"center center", "height":myHeight+"px"});
		//$(this).css({"background-image":"url(pic_gen.php?text="+escape(text)+")", "background-repeat":"no-repeat", "background-position":"center center", "height":myHeight+"px"});	
        });

        // Binding an onClick event to anchors
        $(tabsId + ' a').click(function(){
            if($(this).parent().hasClass('current')){ return false; }
            $(tabsId + ' li.current').removeClass('current');
            $(this).parent().addClass('current');		
	    $($(this).attr('id')).show("slow");	
	    //$.fn.addVtabs.loadTab($(this), containerId);
            return false;
        });


    };

	

    // Load tab's content from external files 
    //(TODO: place here actual feed's data)
    $.fn.addVtabs.loadTab = function (tabObj, containerId){
	

        if(!tabObj || !tabObj.length){ return; }
	

        $(containerId).addClass('loading');
        $(containerId).fadeOut('fast');
	$(containerId).removeClass('loading');
        $(containerId).fadeIn('fast');

	/*
	$(containerId).load(loadDiv, function(){
            $(containerId).removeClass('loading');
            $(containerId).fadeIn('fast');
        });
	*/
	/*
        $(containerId).load(tabObj.attr('href'), function(){
            $(containerId).removeClass('loading');
            $(containerId).fadeIn('fast');
        });
	*/
    };

})(jQuery);