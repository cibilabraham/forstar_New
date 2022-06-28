function gradient(id, level)
{
	var box = document.getElementById(id);
	box.style.opacity = level;
	box.style.MozOpacity = level;
	box.style.KhtmlOpacity = level;
	box.style.filter = "alpha(opacity=" + level * 100 + ")";
	box.style.display="block";
	return;
}


function fadein(id) 
{
	var level = 0;
	while(level <= 1)
	{
		setTimeout( "gradient('" + id + "'," + level + ")", (level* 1000) + 10);
		level += 0.01;
	}
}


// Open the lightbox


function openbox(formtitle, fadin)
{
	var box = document.getElementById('box'); 
	document.getElementById('filter').style.display='block';
	
	var btitle = document.getElementById('boxtitle');
	btitle.innerHTML = formtitle;
	
	if (fadin) {
		gradient("box", 0);
		fadein("box");
	} else {
		box.style.display='block';
	}
}


// Close the lightbox
function closebox()
{
   document.getElementById('box').style.display='none';
   document.getElementById('filter').style.display='none';
}

function openModalBox(formtitle, fadin, filterDiv, boxDiv, boxTtleDiv)
{
	var box = document.getElementById(boxDiv); 
	var filter = document.getElementById(filterDiv);
	filter.style.display='block'	
	
	if (boxTtleDiv) {
		var btitle = document.getElementById(boxTtleDiv);
		btitle.innerHTML = formtitle;
	}

	if (fadin) {
		gradient(boxDiv, 0);
		fadein(boxDiv);
	} else {		
		box.style.display='block';
	}

	if (navigator.appName.substring(0, 3) == "Mic")  // for IE
	{		
		x = document.documentElement.scrollTop + document.body.scrollTop + box.offsetHeight / 4;
		box.style.top = x + "px";
		//alert(document.documentElement.scrollTop +"+"+ document.body.scrollTop);
		filter.style.top = document.documentElement.scrollTop + document.body.scrollTop;
		box.style.position='absolute'; // fixed does not work on IE
		filter.style.position='absolute';
	} else {		
		var top =  (viewHeight() - box.offsetHeight ) / 2;    
		box.style.top = top + 'px';
		box.style.position='fixed'; // fixed does not work on IE
		filter.style.position='fixed'; 
	}

}

function closeModalBox(boxDiv, filterDiv)
{
	document.getElementById(boxDiv).style.display='none';
  	document.getElementById(filterDiv).style.display='none';
}

// height of current view for all browsers but IE

function viewHeight() 
{
    if(window.innerHeight)return(window.innerHeight);
    if(document.documentElement && document.documentElement.clientHeight) 
         return(document.documentElement.clientHeight);
    if(document.body) return(document.body.clientHeight); 
    return 50;
}



