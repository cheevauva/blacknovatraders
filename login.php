<?php
include("config.php");
loadlanguage($lang);
include("header.php");
bigtitle($l_login_title);
?>

<form action="login2.php" method="post">
    <TABLE CELLPADDING="4">
        <TR>
            <TD align="right"><? echo $l_login_email; ?></TD>
            <TD align="left"><INPUT TYPE="TEXT" NAME="email" SIZE="20" MAXLENGTH="40" VALUE="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"></TD>
        </TR>
        <TR>
            <TD align="right"><? echo $l_login_pw; ?></TD>
            <TD align="left"><INPUT TYPE="PASSWORD" NAME="pass" SIZE="20" MAXLENGTH="20" VALUE="<?php echo htmlspecialchars($_POST['password'] ?? '') ?>"></TD>
        </TR>
        <TR>
            <TD colspan="2">
                Forgot your password?  Enter it blank and press login.
            </TD>
        </TR>
        <TR>
            <TD colspan=2>
                <INPUT TYPE="SUBMIT" VALUE="<? echo $l_login_title; ?>">
            </TD>
        </TR>
        <TR>
            <TD colspan=2>
                <? echo $l_login_newp; ?><br/>
                <? echo $l_login_prbs; ?> <A HREF="mailto:<?php echo $admin_mail; ?>"><? echo $l_login_emailus; ?></A>
            </TD>
        </TR>
        <TR>
            <TD colspan=2>
                <?php if (!empty($link_forums)) : ?><A HREF=\"$link_forums\" TARGET=\"_blank\"><?php echo $l_forums; ?></A> -<?php endif; ?>
                <A HREF="ranking.php"><? echo $l_rankings; ?></A> -
                <A HREF="settings.php"><? echo $l_login_settings; ?></A>
            </TD>
        </TR>
    </TABLE>
</FORM>

<?php include 'footer.php'; ?>
