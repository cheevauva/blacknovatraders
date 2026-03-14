<?php
global $title, $l, $link_forums, $admin_mail, $userinfo;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html data-bs-theme="<?= $userinfo['theme'] ?? 'dark'; ?>">
    <head>
        <base href="/" />
        <title><?= $title; ?></title>
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

        function redirectToAfterMessages(redirectTo, messages) {
            const modalId = 'redirectModal_' + Date.now();

            const modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ${Array.isArray(messages) ? `
                            <ul class="mb-0">
                                ${messages.map(msg => `<li>${msg}</li>`).join('')}
                            </ul>
                        ` : `<p class="mb-0">${messages}</p>`}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modalElement = document.getElementById(modalId);
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            modalElement.addEventListener('hidden.bs.modal', function () {
                modalElement.remove();
                window.location.href = redirectTo;
            });

            modal.show();
        }


        function bntForm(id) {
            document.getElementById(id).addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = new FormData(e.target);

                const response = await fetch(e.target.getAttribute('action'), {
                    method: e.target.getAttribute('method'),
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const result = await response.json();

                if (result.type === 'redirectAfterMessages') {
                    redirectToAfterMessages(result.redirectTo, result.messages);
                } else {
                    alert(result.message, result.type);

                }
            });
        }
    </script>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?= route('index'); ?>"><img src="images/bnthed.gif" alt="Bootstrap" ></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <a class="nav-link" href="<?= route('news'); ?>"><?= $l->l_news_title; ?></a>
                            <a class="nav-link" href="<?= route('ranking'); ?>"><?= $l->l_rankings; ?></a>
                            <a class="nav-link" href="<?= route('settings'); ?>"><?= $l->l_settings_game; ?></A>
                            <?php if (isAdmin()) : ?>
                                <a class="nav-link" href="<?= route('admin'); ?>"><?= "$l->l_admin"; ?></a>
                            <?php endif; ?>
                            <?php if (!empty($userinfo)) : ?>
                                <a class="nav-link" href="<?= route('logout'); ?>"><?= "$l->l_logout"; ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="row mt-3">
                <div class="col" id="liveAlertPlaceholder">

                </div>
            </div>


