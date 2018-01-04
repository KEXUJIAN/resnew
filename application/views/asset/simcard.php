<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 15:26
 */

App::view('templates/header');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 panel panel-default panel-assets">
            <?php
            App::view('templates/datatable-simcard', [
                'url' => '/assets/dataTable/simcard',
                'display' => false,
            ]);
            ?>
        </div>
    </div>
</div>
<script>
(function () {
    resRunInit();
})();
</script>
<?php
App::view('templates/footer', ['display' => true]);
?>
