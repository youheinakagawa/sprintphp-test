<?= $themer->display('foundation:fragments/head') ?>

<?= $themer->display('foundation:fragments/topbar') ?>

<div class="row container-fluid">

    <br/>

    <!-- Sidebar -->
    <div class="small-3 medium-2 columns">
        <ul class="side-nav">
            <li class="active"><a href="#">Overview</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Analytics</a></li>
            <li><a href="#">Export</a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="small-9 medium-10 columns">

        <?= $notice ?>

        <?= $view_content ?>

    </div>

</div>


<?= $themer->display('foundation:fragments/footer') ?>
