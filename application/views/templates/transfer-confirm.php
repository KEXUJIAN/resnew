<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/5
 * Time: 22:52
 */
?>

<form id="confirmation-form" class="form-horizontal ajax-form" action="<?=$url?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><?=$title?></h4>
    </div>
    <div class="modal-body">
        <?php foreach ($contents as $content): ?>
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 5px;">

                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary submit" data-role="accept">同意</button>
        <button class="btn btn-danger submit" data-role="reject">拒绝</button>
        <button class="btn btn-default" data-dismiss="modal">关闭</button>
    </div>
</form>
<script>
(function () {
    var form = $('#confirmation-form');
    form
        .on('click', '[data-role="accept"]', function () {
            form.removeData('formData');
            form.data('formData', function (data) {
                data['action'] = 'accept';
            });
        })
        .on('click', '[data-role="reject"]', function () {
            form.removeData('formData');
            form.data('formData', function (data) {
                data['action'] = 'reject';
            });
        })
        .data('submitDoneSucc', function (ret, form) {
            var _modal = form.closest('.ajax-modal');
            _modal
                .data('afterHidden', function () {
                    bootbox.alert(ret.message);
                    $('table:visible').DataTable().draw(false);
                })
                .find('button.btn-default').click();
        })
        .data('submitDoneFail', function (ret) {
            bootbox.alert(ret.message);
        });
})();
</script>
