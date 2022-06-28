<?php
	 
	//require("include/include.php");	
	require("lib/databaseConnect.php");
	require("lib/config.php");
	require("lib/session_class.php");
	require("lib/LogManager_class.php");

	$databaseConnect	= new DatabaseConnect();
 	$sessObj		= new Session($databaseConnect);
	$logManagerObj		= new LogManager($databaseConnect, $sessObj);
	
	if ($p["cmdFileDelete"]) {
		$rowCount	= $p["hidDirFileRowCount"];
		$command	= $p["command"]; 
		if ($command=="FIM") {
			for ($i=1; $i<=$rowCount; $i++) {
				$filename	=	$p["delDirFileId_".$i];
	
				if ($filename!="") {
					// Need to check the selected Department is link with any other process
					$delFile	=	$logManagerObj->deleteLogFile($filename);
				}
			}
		} else {
			echo "Please enter correct password";
		}
	}

	# Log data
	$logData = $logManagerObj->readLogFile();
	
	# List Directory files
	$dirList = $logManagerObj->readDirectory();	
	
	# Include Template [topLeftNav.php]
	require("template/btopLeftNav.php");
?>
<form name="frmLog" action="Log.php" method="post">
<script>
function showTblRow(tableRowId, moduleName)	
{
	//alert(tableRowId+","+ moduleName);
	var displayRow  = false;
	var rowCount	= document.getElementById("hidRowCount1_"+tableRowId).value;
	
	for (var j=1; j<=rowCount; j++) {		
		if (document.getElementById(tableRowId+"_"+j).style.display == "none" ) {
			document.getElementById(tableRowId+"_"+j).style.display = '';
			displayRow = true;
		} else {
			document.getElementById(tableRowId+"_"+j).style.display = "none";
		}		
	}
	if (displayRow) {
		document.getElementById("t_"+tableRowId).innerHTML = "<a href=\"###\"  onClick=\"showTblRow('"+tableRowId+"','"+moduleName+"');\" class=\"expandLink\">-</a>&nbsp;"+moduleName;
	} else {
		document.getElementById("t_"+tableRowId).innerHTML = "<a href=\"###\" onClick=\"showTblRow('"+tableRowId+"','"+moduleName+"');\" class=\"expandLink\">+</a>&nbsp;"+moduleName;
	}
	//alert(document.getElementById("t_"+tableRowId).innerHTML);
}

function showDirFile(tableRowId, fileName)	
{		
	var displayRow  = false;
	var rowCount	= document.getElementById("hidDirFileRowCount").value;
	
	for (var j=1; j<=rowCount; j++) {		
		if (tableRowId==j && document.getElementById("file_"+j).style.display == "none" ) {
			document.getElementById("file_"+j).style.display = '';
			displayRow = true;
		} else {
			document.getElementById("file_"+j).style.display = "none";
		}		
	}

	if (displayRow) {
		document.getElementById("tf_"+tableRowId).innerHTML = "<a href=\"###\"  onClick=\"showDirFile('"+tableRowId+"','"+fileName+"');\" class=\"expandLink\">-</a>&nbsp;"+fileName;
	} else {
		document.getElementById("tf_"+tableRowId).innerHTML = "<a href=\"###\" onClick=\"showDirFile('"+tableRowId+"','"+fileName+"');\" class=\"expandLink\">+</a>&nbsp;"+fileName;
	}
}
</script>
    <table cellspacing="0"  align="center" cellpadding="0" width="50%">
    <tr> 
      <td height="10" align="center" ></td>
    </tr>
    <tr> 
      <td> 
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Log </td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="1"  width="20%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                <?
		if ( sizeof($logData) > 0 ) {
			$i	=	0;
		?>
                      <tr  bgcolor="#f2f2f2" align="center"> 
                        <td width="20" class="listing-head">User</td>
                        <td class="listing-head" nowrap>Date </td>
                        <td class="listing-head" align="center">URL</td>						
                      </tr>
                      <?
			foreach($logData as $selUserName=>$dataArr) {				
				$i++;								
				echo "<tr bgcolor='WHITE'><td colspan='3' class='listing-item' style='color:Maroon'><div id ='t_$i'><a href='###'  onClick=\"showTblRow($i,'$selUserName');\" class=\"expandLink\">+</a>&nbsp;$selUserName</div></td></tr>";
				$j = 0;
				foreach ($dataArr as $data) {
					$j++;
			?>
                      <tr  bgcolor="WHITE" id="<?=$i?>_<?=$j?>" style="display:none;">
                        <td class="listing-item" nowrap colspan="2"><?=$data[0]?></td>
                        <td class="listing-item" nowrap><?=$data[1]?></td>
                      </tr>
                        <?
				} // Loop IInd
			?>
			<input type="hidden" name="hidRowCount1_<?=$i?>" id="hidRowCount1_<?=$i?>" value="<?=$j;?>">
			<?
			}
			?>
                      <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
                      <input type="hidden" name="editId" value="">					  
                      <? } else { ?>
                      <tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center" nowrap="true"> 
                          No Log Records
                        </td>
                      </tr>
                      <?
				}
			?>
                    </table></td>
                </tr>
		 <tr> 
      <td height="10"></td>
    </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
    <tr> 
      <td height="10"></td>
    </tr>
<!-- =======================================================================================================================================  -->
<tr> 
      <td> 
	<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
          <tr> 
            <td   bgcolor="white"> 
              <!-- Form fields start -->
              <table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
                <tr> 
                  <td width="1" background="images/heading_bg.gif" class="page_hint"></td>
                  <td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Log Dir Files </td>
                </tr>
                <tr> 
                  <td colspan="3" height="10" ></td>
                </tr>
                <tr> 
                  <td width="1" ></td>
                  <td colspan="2" > <table cellpadding="1"  width="20%" cellspacing="1" border="0" align="center" bgcolor="#999999">
                <?
		if ( sizeof($dirList) > 0 ) {
			$k	=	0;
		?>
                      <tr  bgcolor="#f2f2f2" align="center" > 
                        <td width="20">
			<input type="submit" value=" File Delete " class="button"  name="cmdFileDelete">
			<input type="text" value=""  name="command" size="5" title="enter secret code">
			</td>
                        <td class="listing-head" nowrap>Files</td>
                      </tr>
                      <?
			foreach($dirList as $key=>$selFileName) {				
				$k++;				
			
			?>
                      <tr  bgcolor="WHITE"> 
                        <td width="20" height="25" align="center" valign="top">
				<input type="checkbox" name="delDirFileId_<?=$k;?>" id="delDirFileId_<?=$k;?>" value="<?=$selFileName?>" >
			</td>
                        <td class="listing-item" nowrap>				
				<div id ='tf_<?=$k?>'><a href='###'  onClick="showDirFile('<?=$k?>', '<?=$selFileName?>');" class="expandLink">+</a>&nbsp;<?=$selFileName?></div>
				<div id="file_<?=$k?>" style="padding-left:5px; z-index:1000px; position:absolute; border:1px solid #f2f2f2; background-color:white; display:none;">
					<?php
					/*
					$l = 0;
					$lines = file('user_log/'.$selFileName);

					foreach ($lines as $line) {
						if ($line!="\n") {
							$l++;
							echo $l.'. '.$line.'<br />'."\n";
						}
					} 
					*/
					?>
				<object type="text/plain" data="user_log/<?=$selFileName?>" width="600" >
				<a href="user_log/<?=$selFileName?>"><?=$selFileName?></a>
				</object>
				</div>
			</td>
                      </tr>
			<?
			}
			?>
                      <input type="hidden" name="hidDirFileRowCount" id="hidDirFileRowCount" value="<?=$i?>" >
                      <? } else { ?>
                      <tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center" nowrap="true"> 
                          No File exist
                        </td>
                      </tr>
                      <?
				}
			?>
                    </table></td>
                </tr>
		 <tr> 
      <td height="10"></td>
    </tr>
              </table></td>
          </tr>
        </table>
        <!-- Form fields end   -->
      </td>
    </tr>
<tr><TD height="10">
<!--<div>
  <object type="text/plain" data="user_log/userLog.log" width="100%" >
   <a href="user_log/userLog.log">Log</a>
  </object>
</div>-->

</TD></tr>
  </table>
	</form>
<?
	# Include Template [bottomRightNav.php]
	//require("template/bottomRightNav.php");
?>