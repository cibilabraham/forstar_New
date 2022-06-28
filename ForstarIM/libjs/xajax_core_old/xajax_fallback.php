<?php
///////////////////////////////////////////////////////////////////////////////
// xajax_fallback.inc.php :: extension with fallback functionality for ie if
//                          activex is disabled
//                          by R. Blumenthal (ralf@goldenzazu.de)
// based on xajax version 0.2.4; feel free to use it
///////////////////////////////////////////////////////////////////////////////

class xajax_fallback extends xajax
{
    /**
    * overwritten base function;
    * in case of fallback force RequestTypes to be GET
    */
    function processRequests()
    {
        $bUseFallback = (isset($_REQUEST['xajaxfb']) && $_REQUEST['xajaxfb']==1); 
        if ($bUseFallback)
        {
            foreach ($this->aFunctionRequestTypes as $key => $value)
                $this->aFunctionRequestTypes[$key] = XAJAX_GET;
        }
        parent::processRequests();
    }
    
    /**
    * overwritten to route the sever call via an xmlisland in order of a failure
    * to create the XMLHttpRequest object (IE specific approach)
    */
    function printJavascript($sJsURI="", $sJsFile=NULL, $sJsFullFilename=NULL)
    {
        print $this->getJavascript($sJsURI, $sJsFile);
?>
        <script type="text/javascript">
        xajax.realCall = xajax.call;
        xajax.call = function(sFunction, aArgs, sRequestType)
        {
            var i;
            var bOk = this.realCall(sFunction, aArgs, sRequestType);
            if (!bOk)
            {
                // if XMLHttpRequest object instantiation failed build uri...
                var uri = xajaxRequestUri;
                var uriGet = uri.indexOf("?")==-1?"?xajax="+encodeURIComponent(sFunction):"&xajax="+encodeURIComponent(sFunction);
                if (aArgs) {
                    for (i = 0; i<aArgs.length; i++)
                    {
                        value = aArgs[i];
                        if (typeof(value)=="object")
                            value = this.objectToXML(value);
                        uriGet += "&xajaxargs[]="+encodeURIComponent(value);
                    }
                }
                uriGet += "&xajaxr=" + new Date().getTime();
                uri += uriGet;
                postData = null;
                xajax.handleFBRequest(uri);
            }
            return true;
         }

        // insert xml data islands; need more than one in case of quick request sequence         
        uniqueFBId=0;
        xajax.handleFBRequest = function(uri)
        {
            uniqueFBId=(uniqueFBId<10)?uniqueFBId+1:1;
            var xmlisland = document.getElementById(this.workId+'fb'+uniqueFBId);
            if (xmlisland == null)
            {
                xmlisland = document.createElement("xml");
                xmlisland.setAttribute('id',this.workId+'fb'+uniqueFBId);
                xmlisland.setAttribute('name',this.workId+'fb'+uniqueFBId);
                document.body.appendChild(xmlisland);
            }
            uri=uri+'&xajaxfb=1';
            
            setTimeout("xajax.loadingFunction();",1);
            setTimeout('xajax.doLoadIntoIsland("'+uri+'",'+uniqueFBId+')',100);
            
/*          xajax.loadIntoIsland(uri, xmlisland);
            var xmldoc  = xmlisland.XMLDocument;
            xajax.processResponse(xmldoc);
*/               
        }
        
        xajax.doLoadIntoIsland = function(uri, uniqueFBId)
        {
        	var xmlisland = document.getElementById(this.workId+'fb'+uniqueFBId);
            xajax.loadIntoIsland(uri, xmlisland);
            var xmldoc  = xmlisland.XMLDocument;
               xajax.processResponse(xmldoc);
        }
        
        xajax.loadIntoIsland = function(fname, xmlisland)
        {
            xmlisland.async = false;
            xmlisland.load(fname);
             if(xmlisland.parseError.errorCode != 0)
                   alert(xmlisland.parseError.reason);
        }
        
        xajax.realProcessResponse = xajax.processResponse;
        xajax.processResponse = function(xml)
        {
            if (typeof xml != "undefined")
                this.realProcessResponse(xml);
            else
            {
				setTimeout("xajax.doneLoadingFunction();",400);
                //this.doneLoadingFunction();
                document.body.style.cursor = 'default';
                if (this.xajaxStatusMessages == true) window.status = 'Done';
            }
        }
        
		// copy of the original function; disable the alert        
		xajax.getRequestObject = function()
		{
			if (xajaxDebug) this.DebugMessage("Initializing Request Object..");
			var req = null;
			if (typeof XMLHttpRequest != "undefined")
				req = new XMLHttpRequest();
			if (!req && typeof ActiveXObject != "undefined")
			{
				try
				{
					req=new ActiveXObject("Msxml2.XMLHTTP");
				}
				catch (e)
				{
					try
					{
						req=new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch (e2)
					{
						try {
							req=new ActiveXObject("Msxml2.XMLHTTP.4.0");
						}
						catch (e3)
						{
							req=null;
						}
					}
				}
			}
			if(!req && window.createRequest)
				req = window.createRequest();
			//if (!req) this.DebugMessage("Request Object Instantiation failed.");
			return req;
		}
        
        </script>
<?php
    }
}
?>