<? header("Cache-Control: no-cache, must-revalidate"); ?>
<!doctype html public "-//w3c//dtd html 3.2//en">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<HTML>

<HEAD>
<TITLE><? echo $title; ?></TITLE>
<STYLE TYPE="text/css">
<!--
<?
if($interface == "")
{
  $interface = "main.php3";
}

if($interface == "main.php3")
{
	echo "
	a.mnu {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:white; font-weight:bold;}
	a.mnu:hover {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:#3366ff; font-weight:bold;}
	div.mnu {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:white; font-weight:bold;}
	a.dis {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:silver; font-weight:bold;}
	a.dis:hover {text-decoration:none; font-size: $stylefontsize; font-family: verdana; color:#3366ff; font-weight:bold;}
	";
}
echo "body {font-family: Arial, Tahoma, Helvetica, sans-serif; font-size: x-small}";
?>

-->
</STYLE>
</HEAD>

<?

if($interface=="main.php3")
{
	echo "<BODY BACKGROUND=\"images/bgoutspace1.gif\" bgcolor=#000000 text=\"#c0c0c0\" link=\"#ffffff\" vlink=\"#808080\" alink=\"#ff0000\">";
}
else
{
	echo "<BODY BACKGROUND=\"\" BGCOLOR=\"#000000\" TEXT=\"#c0c0c0\" LINK=\"#ffffff\" VLINK=\"#808080\" ALINK=\"#ff0000\">";
}
echo "\n";

?>