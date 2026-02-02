<?php
$shiptypes = [];
$shiptypes[0] = "tinyship.gif";
$shiptypes[1] = "smallship.gif";
$shiptypes[2] = "mediumship.gif";
$shiptypes[3] = "largeship.gif";
$shiptypes[4] = "hugeship.gif";

$planettypes = [];
$planettypes[0] = "tinyplanet.gif";
$planettypes[1] = "smallplanet.gif";
$planettypes[2] = "mediumplanet.gif";
$planettypes[3] = "largeplanet.gif";
$planettypes[4] = "hugeplanet.gif";

function options($options, $selected)
{
    foreach ($options as $value => $label) {
        $selectedAttr = '';

        if (is_array($selected) && in_array($value, $selected)) {
            $selectedAttr = 'selected';
        }

        if ($selected === $value) {
            $selectedAttr = 'selected';
        }

        echo '<option value="', htmlspecialchars($value), '" ', $selectedAttr, '>', htmlspecialchars($label), '</option>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html data-bs-theme="">
    <head>
        <title><?php echo $title; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <script type="text/javascript">
        function bntForm(id) {
            document.getElementById(id).addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);

                const response = await fetch(e.target.action, {
                    method: e.target.method,
                    body: formData
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const result = await response.json();

                document.getElementById('alertMain').classList.remove('d-none');
                document.getElementById('alertMain').getElementsByClassName('error')[0].innerHTML = result.error;
            });
        }
    </script>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php"><img src="images/bnthed.gif" alt="Bootstrap" ></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <a class="nav-link" href="news.php"><?php echo $l_news_title; ?></a>
                            <a class="nav-link" href="ranking.php"><?php echo $l_rankings; ?></a>
                            <a class="nav-link" href="settings.php"><?php echo $l_login_settings; ?></A>
                            <a class="nav-link" href="help.php"><?php echo $l_help; ?></a>
                            <a class="nav-link" href="faq.html"><? echo "$l_faq"; ?></a>
                            <a class="nav-link" href="mailto:<? echo $admin_mail; ?>"><?php echo $l_login_emailus; ?></a>

                            <?php if (!empty($link_forums)) : ?>
                                <a class="nav-link" href="<?php echo $link_forums; ?>" target="_blank"><?php echo $l_forums; ?></A>
                            <?php endif; ?>
                            <a class="nav-link" target="_blank" href="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</a>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="alert alert-warning alert-dismissible d-none" id="alertMain" role="alert">
                <span class="error"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>


