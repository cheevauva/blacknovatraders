<?php $self = \BNT\Controller\AdminController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<div class="container mt-1">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">BlackNova Traders Administration Module</h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <p class="lead text-center mb-4">
                        Welcome to the BlackNova Traders administration module
                    </p>
                    <p class="text-center mb-4">
                        Select a function from the list below:
                    </p>
                </div>
            </div>
            <div clas="row">
                <div class="col-md-12">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=user&operation=list">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=sector&operation=list">Sectors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=ship&operation=list">Ships</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=zone&operation=list">Zones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=planet&operation=list">Planets</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php?module=config&operation=edit">Config</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_footer(); ?>
