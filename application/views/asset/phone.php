<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 15:22
 */

App::view('templates/header');
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 panel panel-default panel-assets">
            <?php
            App::view('templates/datatable-phone', [
                'url' => '/assets/dataTable/phone',
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
