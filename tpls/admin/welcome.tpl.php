<div class="container mt-5">
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
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <?php include 'tpls/admin/userlist.tpl.php'; ?>
                            </div>
                            <div class="col-md-6">
                                <?php include 'tpls/admin/expand_universe.tpl.php'; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php include 'tpls/admin/sectorlist.tpl.php'; ?>
                            </div>
                            <div class="col-md-6">
                                <?php include 'tpls/admin/planetlist.tpl.php'; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php include 'tpls/admin/zonelist.tpl.php'; ?>
                            </div>
                            <div class="col-md-6">
                                <?php include 'tpls/admin/logview.tpl.php'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
