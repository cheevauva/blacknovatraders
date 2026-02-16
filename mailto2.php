<?php
include 'config.php';



$title = $l_sendm_title;
include("header.php");



if (checklogin()) {
    die();
}


bigtitle();

if (empty($content)) {
    $res = $db->adoExecute("SELECT character_name FROM ships WHERE email NOT LIKE '%@Xenobe' ORDER BY character_name ASC");
    $res2 = $db->adoExecute("SELECT team_name FROM teams ORDER BY team_name ASC");
    echo "<FORM ACTION=mailto2.php METHOD=POST>";
    echo "<TABLE>";
    echo "<TR><TD>$l_sendm_to:</TD><TD><SELECT NAME=to>";
    while (!$res->EOF) {
        $row = $res->fields;
        ?>
    <OPTION <?php if ($row[character_name] == $name) {
        echo "selected";
            } ?>><?php echo $row[character_name] ?></OPTION>
        <?php
        $res->MoveNext();
    }
    while (!$res2->EOF) {
        $row2 = $res2->fields;
        echo "<OPTION>$l_sendm_ally $row2[team_name]</OPTION>";
        $res2->MoveNext();
    }

    echo "</SELECT></TD></TR>";
    echo "<TR><TD>$l_sendm_from:</TD><TD><INPUT DISABLED TYPE=TEXT NAME=dummy SIZE=40 MAXLENGTH=40 VALUE=\"$playerinfo[character_name]\"></TD></TR>";
    if (isset($subject)) {
        $subject = "RE: " . $subject;
    }
    echo "<TR><TD>$l_sendm_subj:</TD><TD><INPUT TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40 VALUE=\"$subject\"></TD></TR>";
    echo "<TR><TD>$l_sendm_mess:</TD><TD><TEXTAREA NAME=content ROWS=5 COLS=40></TEXTAREA></TD></TR>";
    echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=$l_sendm_send><INPUT TYPE=RESET VALUE=$l_reset></TD>";
    echo "</TABLE>";
    echo "</FORM>";
} else {
    echo "$l_sendm_sent<BR><BR>";

    if (strpos($to, $l_sendm_ally) === false) {
        $timestamp = date("Y\-m\-d H\:i\:s");
        $res = $db->adoExecute("SELECT * FROM ships WHERE character_name='$to'");
        $target_info = $res->fields;
        $content = htmlspecialchars($content);
        $subject = htmlspecialchars($subject);
        $db->adoExecute("INSERT INTO messages (sender_id, recp_id, sent, subject, message) VALUES ('" . $playerinfo[ship_id] . "', '" . $target_info[ship_id] . "', '" . $timestamp . "', '" . $subject . "', '" . $content . "')");
    } else {
        $timestamp = date("Y\-m\-d H\:i\:s");

         $to = str_replace($l_sendm_ally, "", $to);
         $to = trim($to);
         $to = addslashes($to);
         $res = $db->adoExecute("SELECT id FROM teams WHERE team_name='$to'");
         $row = $res->fields;

         $res2 = $db->adoExecute("SELECT * FROM ships where team='$row[id]'");

        while (!$res2->EOF) {
            $row2 = $res2->fields;
            $db->adoExecute("INSERT INTO messages (sender_id, recp_id, sent, subject, message) VALUES ('" . $playerinfo[ship_id] . "', '" . $row2[ship_id] . "', '" . $timestamp . "', '" . $subject . "', '" . $content . "')");
            $res2->MoveNext();
        }
    }
}



include("footer.php");

?>
