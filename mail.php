<?php

include 'config.php';

$title = $l_mail_title;
include("header.php");

bigtitle();

$playerinfo = db()->fetch("SELECT email, password FROM ships WHERE email= :mail", [
    'mail' => $mail
]);

if ($playerinfo) {
    $l_mail_message = str_replace("[pass]", $playerinfo['password'], $l_mail_message);
    mail("$mail", "$l_mail_topic", "$l_mail_message\r\n\r\nhttp://$SERVER_NAME", "From: webmaster@$SERVER_NAME\r\nReply-To: webmaster@$SERVER_NAME\r\nX-Mailer: PHP/" . phpversion());
    echo "$l_mail_sent $mail.";
} else {
    echo "<strong>$l_mail_noplayer</strong><br>";
}

include("footer.php");
