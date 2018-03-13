<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/2/7
 * Time: 22:08
 */

?>

<form id="edit-user-form" class="form-horizontal ajax-form" action="/admin/update/user/<?=$user->id()?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">编辑用户</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">用户名:</label>
                    <div class="col-md-4">
                        <input type="text" name="username" class="form-control" value="<?=$user->username()?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">姓名:</label>
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" value="<?=$user->name()?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">密码:</label>
                    <div class="col-md-4">
                        <input type="text" name="password" class="form-control" value="" placeholder="更改密码，为空则保留原密码">
                    </div>
                    <label class="col-md-2 control-label">邮箱:</label>
                    <div class="col-md-4">
                        <input type="text" name="email" class="form-control" value="<?=$user->email()?>">
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
    $('#edit-user-form')
        .data('formData', function (data) {
            var username = data.username;
            var password = data.password;
            if (!$.trim(password)) {
                data.password = '';
                return;
            }
            data.password = sha1(username + password);
        })
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
