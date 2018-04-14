<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/3
 * Time: 21:20
 */

?>

<form id="new-user-form" class="form-horizontal ajax-form" action="/admin/save/user">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">添加普通用户</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">用户名:</label>
                    <div class="col-md-4">
                        <input type="text" name="username" class="form-control" data-required="true">
                    </div>
                    <label class="col-md-2 control-label">姓名:</label>
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" data-required="true">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">密码:</label>
                    <div class="col-md-4">
                        <input type="text" name="password" class="form-control" value="123456" data-required="true">
                    </div>
                    <label class="col-md-2 control-label">邮箱:</label>
                    <div class="col-md-4">
                        <input type="text" name="email" class="form-control" data-required="true">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary submit">保存</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
    </div>
</form>
<script>
(function () {
    $('#new-user-form')
        .data('submitDoneSucc', function (ret, form) {
            var _modal = form.closest('.ajax-modal');
            _modal
                .data('afterHidden', function () {
                    bootbox.alert(ret.message);
                    $('#user-panel').find('.ajax-table').DataTable().ajax.reload();
                })
                .find('button.btn-default').click();
        })
        .data('submitDoneFail', function (ret) {
            bootbox.alert(ret.message);
        });
})();
</script>
