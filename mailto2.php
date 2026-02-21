<?php
include 'config.php';

$title = $l_sendm_title;
include("header.php");

if (checkship()) {
    die();
}

bigtitle();

if (empty($content)) {
    $players = db()->fetchAll("SELECT character_name FROM ships  ORDER BY character_name ASC");
    $teams = db()->fetchAll("SELECT team_name FROM teams ORDER BY team_name ASC");
    
    echo "<FORM ACTION=mailto2.php METHOD=POST>";
    echo "<TABLE class='form-table'>";
    echo "<TR><TD>" . $l_sendm_to . ":</TD><TD><SELECT NAME=to>";
    
    foreach ($players as $row) {
        $selected = ($row['character_name'] == $name) ? "selected" : "";
        echo "<OPTION $selected>" . $row['character_name'] . "</OPTION>";
    }
    
    foreach ($teams as $row2) {
        echo "<OPTION>" . $l_sendm_ally . " " . $row2['team_name'] . "</OPTION>";
    }

    echo "</SELECT></TD></TR>";
    echo "<TR><TD>" . $l_sendm_from . ":</TD><TD><INPUT DISABLED TYPE=TEXT NAME=dummy SIZE=40 MAXLENGTH=40 VALUE=\"" . $playerinfo['character_name'] . "\"></TD></TR>";
    
    if (isset($subject)) {
        $subject = "RE: " . $subject;
    }
    
    echo "<TR><TD>" . $l_sendm_subj . ":</TD><TD><INPUT TYPE=TEXT NAME=subject SIZE=40 MAXLENGTH=40 VALUE=\"" . $subject . "\"></TD></TR>";
    echo "<TR><TD>" . $l_sendm_mess . ":</TD><TD><TEXTAREA NAME=content ROWS=5 COLS=40></TEXTAREA></TD></TR>";
    echo "<TR><TD></TD><TD><INPUT TYPE=SUBMIT VALUE=" . $l_sendm_send . "><INPUT TYPE=RESET VALUE=" . $l_reset . "></TD>";
    echo "</TABLE>";
    echo "</FORM>";
} else {
    echo "$l_sendm_sent<BR><BR>";

    if (strpos($to, $l_sendm_ally) === false) {
        $timestamp = date("Y-m-d H:i:s");
        
        $target_info = db()->fetch("SELECT * FROM ships WHERE character_name= :to", [
            'to' => $to
        ]);
        
        $content = htmlspecialchars($content);
        $subject = htmlspecialchars($subject);
        
        db()->q("INSERT INTO messages (sender_id, recp_id, sent, subject, message) VALUES (:sender_id, :recp_id, :sent, :subject, :message)", [
            'sender_id' => $playerinfo['ship_id'],
            'recp_id' => $target_info['ship_id'],
            'sent' => $timestamp,
            'subject' => $subject,
            'message' => $content
        ]);
    } else {
        $timestamp = date("Y-m-d H:i:s");

        $to = str_replace($l_sendm_ally, "", $to);
        $to = trim($to);
        $to = addslashes($to);
        
        $team = db()->fetch("SELECT id FROM teams WHERE team_name= :team_name", [
            'team_name' => $to
        ]);
        
        $team_members = db()->fetchAll("SELECT * FROM ships where team= :team_id", [
            'team_id' => $team['id']
        ]);

        foreach ($team_members as $member) {
            db()->q("INSERT INTO messages (sender_id, recp_id, sent, subject, message) VALUES (:sender_id, :recp_id, :sent, :subject, :message)", [
                'sender_id' => $playerinfo['ship_id'],
                'recp_id' => $member['ship_id'],
                'sent' => $timestamp,
                'subject' => $subject,
                'message' => $content
            ]);
        }
    }
}

include("footer.php");
?>