<?php
/* client.php */

/* Include the library */
include ( "kd_xmlrpc.php" );

/* Define variables to find the rpc server script */
$site = "localhost";
$location = "/ForstarIM.kd_xmlrpc/server.php";

/* Function to give us back a nice date */
function convert_date ( $date ) {
    $date = date ( "D M y H:i:s",
                    XMLRPC_convert_iso8601_to_timestamp ( $date ) );
    return ( $date );
}
?>

<html>
<head>
<title> KD XML RPC News Client </title>
<meta name="Generator" content="EditPlus">
<meta name="Author" content="HarryF">
<meta name="Keywords" content="XML RPC">
<meta name="Description" content="Gets news form server.php">
</head>

<body>
<?php 
/* If user is viewing a single news item, to this */
if ( ISSET ( $_GET['news_id'] ) ) {
    /* $success is 0 (fail) / 1 ( succeeded ). XMLPRC_request preforms
       the XML POST to the server script, calling the method and sending
       the correct parameters using XMLRPC_prepare */
    list($success, $response) = XMLRPC_request(
        $site,
        $location,
        'news.viewNewsItem',
        array(XMLRPC_prepare($_GET['news_id']),
        'HarryFsXMLRPCClient')
    );

    /* If all went well, show the article */
    if ($success) {
?>
<table align="center" width="600">
<tr valign="top">
<th colspan="2"><b><?php echo ( $response['title'] );?></b></th>
</tr>
<tr valign="top">
<th><?php echo ( $response['author'] );?></th>
<th><?php echo ( convert_date ( $response['date'] ) );?></th>
</tr>
<tr valign="top">
<td colspan="2">
<?php echo ( nl2br ( $response['full_desc'] ) );?>
</th>
</tr>
</table>
<?php
    /* Else display the error */
    } else {
        echo ( "<p>Error: " . nl2br ( $response['faultString'] ) );
    }
} else {
    /* Define the parameters to pass to the XML-RPC method as a PHP array */
    $query_info['limit'] = 10;
    $query_info['order'] = "author";

    /* XMLRPC_prepare works on an array and converts it to XML-RPC
       parameters */
    list($success, $response) = XMLRPC_request(
        $site,
        $location,
        'news.getNewsList',
        array(XMLRPC_prepare($query_info),
        'HarryFsXMLRPCClient')
    );
    /* On success, display the list as HTML table */
    if ($success) {
        echo ( "<table align=\"center\" width=\"600\">\n" );
        $count = 0;
        while ( list ( $key, $val ) = each ( $response ) ) {
?>
<tr valign="top">
<td colspan="2">
<a href="<?php echo ( $_SERVER['PHP_SELF'] );?>?news_id=<?php
echo ( $response[$count]['news_id'] );
?>">
<?php echo ( $response[$count]['title'] ); ?>
</a>
</td>
</tr>
<tr valign="top">
<td colspan="2">
<?php echo ( $response[$count]['short_desc'] ); ?>
</td>
</tr>
<tr valign="top">
<td>
<?php echo ( $response[$count]['author'] ); ?>
</td>
<td>
<?php echo ( convert_date ( $response[$count]['date'] ) ); ?>
</td>
</tr>
<?php
            $count++;
        }
        echo ( "</table>\n" );
    /* Or error */
    } else {
        echo ( "<p>Error: " . nl2br ( $response['faultString'] ) );
    }

}
;?>
</body>
</html>