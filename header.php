<?php
global $title, $l, $link_forums, $admin_mail;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html data-bs-theme="">
    <head>
        <title><?php echo $title; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <script type="text/javascript">
        function alert(message, type) {
            const alertPlaceholder = document.getElementById('liveAlertPlaceholder');
            alertPlaceholder.innerHTML = '';
            const wrapper = document.createElement('div');
            wrapper.innerHTML = [
                `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                `   <div>${message}</div>`,
                '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                '</div>'
            ].join('');

            alertPlaceholder.append(wrapper);
        }

        function bntForm(id) {
            document.getElementById(id).addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);

                const response = await fetch(e.target.getAttribute('action'), {
                    method: e.target.getAttribute('method'),
                    body: formData
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const result = await response.json();

                alert(result.message, result.type);
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
                            <a class="nav-link" href="news.php"><?php echo $l->news_title; ?></a>
                            <a class="nav-link" href="ranking.php"><?php echo $l->rankings; ?></a>
                            <a class="nav-link" href="settings.php"><?php echo $l->settings_game; ?></A>
                            <a class="nav-link" href="help.php"><?php echo $l->help; ?></a>
                            <a class="nav-link" href="faq.html"><?php echo "$l->faq"; ?></a>
                            <a class="nav-link" href="mailto:<?php echo $admin_mail; ?>"><?php echo $l->emailus; ?></a>

                            <?php if (!empty($link_forums)) : ?>
                                <a class="nav-link" href="<?php echo $link_forums; ?>" target="_blank"><?php echo $l->forums; ?></A>
                            <?php endif; ?>
                            <a class="nav-link" target="_blank" href="http://www.sourceforge.net/projects/blacknova">BlackNova Traders</a>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="row mt-3">
                <div class="col" id="liveAlertPlaceholder">

                </div>
            </div>


