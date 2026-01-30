<?php include("header.php"); ?>

<CENTER>
    <?php bigtitle(); ?>

    <form action="login2.php" method="post">
        <BR><BR>

        <TABLE CELLPADDING="4">
            <TR>
                <TD align="right"><?php echo $l_login_email; ?>: </TD>
                <TD align="left"><INPUT TYPE="TEXT" NAME="email" SIZE="20" MAXLENGTH="40" VALUE="<?php echo htmlspecialchars($username); ?>"></TD>
            </TR>
            <TR>
                <TD align="right"><?php echo $l_login_pw; ?>: </TD>
                <TD align="left"><INPUT TYPE="PASSWORD" NAME="pass" SIZE="20" MAXLENGTH="20" VALUE="<?php echo htmlspecialchars($password); ?>"></TD>
            </TR>
            <TR><TD colspan=2><center>Forgot your password?  Enter it blank and press login.</center></TD></TR>
        </TABLE>
        <BR>
        <INPUT TYPE="SUBMIT" VALUE="<?php echo $l_login_title; ?>">
        <BR><BR>
        <?php echo $l_login_newp; ?>
        <BR><BR>
        <?php echo $l_login_prbs; ?> <A HREF="mailto:<?php echo htmlspecialchars($admin_mail); ?>"><?php echo $l_login_emailus; ?></A>
    </FORM>

    <?php if (!empty($link_forums)) : ?>
        <A HREF="<?php $link_forums; ?>" TARGET="_blank"><?php echo $l_forums; ?></A>
    <?php endif; ?>
    <A HREF="ranking.php"><?php echo $l_rankings; ?></A><?php echo " - "; ?>
    <A HREF="settings.php"><?php echo $l_login_settings; ?></A>
    <BR><BR>
</CENTER>

<?php include("footer.php"); ?>
