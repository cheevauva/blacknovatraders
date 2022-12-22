<?php
global $db, $dbtables;
connectdb();
try {
    $res = $db->Execute("SELECT COUNT(*) as loggedin from {$dbtables['ships']} WHERE (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP({$dbtables['ships']}.last_login)) / 60 <= 5 and email NOT LIKE '%@xenobe'");
    $online = $res->fields['loggedin'] ?? '?';
} catch (\Exception $ex) {
    $online = '?';
}

try {
    $res = $db->Execute("SELECT last_run FROM {$dbtables['scheduler']} LIMIT 1");
    $mySEC = ($sched_ticks * 60) - (TIME() - ($res->fields['last_run'] ?? 0));
} catch (\Exception $ex) {
    $mySEC = 0;
}
?>
        <tr>
            <td><b><span id=myx><?php echo $mySEC; ?></span></b> <?php echo $l_footer_until_update; ?> <br></td>
            <td>
                <?php
                if ($online == 1) {
                    echo "  ";
                    echo $l_footer_one_player_on;
                } else {
                    echo "  ";
                    echo $l_footer_players_on_1;
                    echo " ";
                    echo $online;
                    echo " ";
                    echo $l_footer_players_on_2;
                }
                ?>
            </td>
        </tr>