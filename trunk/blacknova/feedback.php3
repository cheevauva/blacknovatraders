<?

	include("config.php3");
	updatecookie();

	include($gameroot . $default_lang);
	$title=$l_feedback_title;
	include("header.php3");

	connectdb();

	if (checklogin()) {die();}

	$result = mysql_query ("SELECT * FROM ships WHERE email='$username'");
	$playerinfo=mysql_fetch_array($result);
        bigtitle();
	if (empty($content))
	{
		echo "<form action=feedback.php3 method=post>";
		echo "<table>";
		echo "<tr><td>$l_feedback_to</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=GameAdmin></td></tr>";
		echo "<tr><td>$l_feedback_from</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=\"$playerinfo[character_name] - $playerinfo[email]\"></td></tr>";
		echo "<tr><td>$l_feedback_topi</td><td><input disabled type=text name=dummy size=40 maxlength=40 value=$l_feedback_feedback></td></tr>";
		echo "<tr><td>$l_feedback_message</td><td><textarea name=content rows=5 cols=40></textarea></td></tr>";
		echo "<tr><td></td><td><input type=submit value=$l_submit><input type=reset value=$l_reset></td>";
		echo "</table>";
		echo "</form>";
		echo "<br>$l_feedback_info<br>";
	} else {
		echo "$l_feedback_messent<BR><BR>";
		mail("$admin_mail", $l_feedback_subj, "IP address - $ip\nGame Name - $playerinfo[character_name]\n\n$content","From: $playerinfo[email]\nX-Mailer: PHP/" . phpversion());
	}

    TEXT_GOTOMAIN();
	include("footer.php3");

?>
