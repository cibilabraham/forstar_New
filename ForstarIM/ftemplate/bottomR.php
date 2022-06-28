</td>
	</tr>
	<tr flexy:if="!printMode">
		<td width="100%" height="1"  colspan="3" bgcolor="#CCCCCC"></td>
	</tr>
	<tr flexy:if="!printMode">
    		<td height="20" align="center" class="copyright" colspan="3" > Copyright &copy; {copyrightYear}<!--2007-->  Pvt.Ltd. All rights reserved.</td>
	</tr>
	<tr flexy:if="!printMode">
		<td colspan='3' height="1" bgcolor="#CCCCCC"></td>
	</tr>
	<tr flexy:if="printMode"><TD>
	<table align="right" cellpadding="0" cellspacing="0">
	<TR>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Printed by &nbsp;<span class="listing-item" style="line-height:normal;font-size:11px;">{username}</span></td>
		<td class="fieldName" nowrap="nowrap" valign="top">&nbsp;On &nbsp;<span class="listing-item" style="line-height:normal;font-size:11px;">{GLOBALS.date(#j F Y, g:i A#)}</span></td>
	
	</TR>
</table>
	</TD></tr>
</table>
<!-- End ImageReady Slices -->
</body>
</html>