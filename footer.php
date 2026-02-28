<?php
global $l_footer_until_update, $l_footer_players_on_1, $l_footer_players_on_2, $admin_mail, $l, $link_forums;
?>
<div class="bg-body-tertiary mt-5">
    <ul class="nav nav-pills nav-fill">
        <li class="nav-item">
            <a class="nav-link" href="help.php"><?= $l->help; ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="faq.html"><?= $l->faq; ?></a>
        </li>
        <?php if (!empty($link_forums)) : ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $link_forums; ?>" target="_blank"><?= $l->forums; ?></a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="mailto:<?= $admin_mail; ?>"><?= $l->emailus; ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">© 2000-<?php echo date('Y'); ?> Ron Harwood and L. Patrick Smallwood </a>
        </li>
    </ul>
    <nav class="navbar">
        <div class="container-fluid">
            <span class="navbar-text">
                <div id="footer_metrics">
                    <span class="myx">-</span> <?php echo $l_footer_until_update; ?> |
                    <?php echo $l_footer_players_on_1; ?>  <span class="counter_online">0</span>  <?php echo $l_footer_players_on_2; ?> |
                    M: <?php echo sprintf('%.2f', memory_get_peak_usage() / 1024 / 1024, 2); ?> MB |
                    E: <?php echo sprintf('%.3f', microtime(true) - MICROTIME_START); ?> S |
                    DF: <?php echo count(get_defined_functions()['user']); ?> |
                    DC: <?php echo count(get_declared_classes()) - COUNT_CLASS_CORE; ?>
                </div>
            </span>
        </div>
</div>
</div>
</div>
</body>
<script language="javascript" type="text/javascript">
    function rmyx() {
        fetch('<?= route('status');?>', {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            document.getElementById('footer_metrics').getElementsByClassName('counter_online')[0].innerHTML = data.online;
            document.getElementById('footer_metrics').getElementsByClassName('myx')[0].innerHTML = data.myx;
            if (data.unreadMessages) {
                alert(data.unreadMessages);
            }
        });
        setTimeout("rmyx();", 30000);
    }

    setTimeout("rmyx();", 1000);
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</html>
