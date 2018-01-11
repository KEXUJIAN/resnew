<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/11
 * Time: 17:02
 */

use Res\Model\User;

?>

<form class="form-horizontal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">用户详情</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">用户名:</label>
                    <div class="col-md-4">
                        <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($user->username())?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">姓名:</label>
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($user->name())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">邮箱:</label>
                    <div class="col-md-4">
                        <input type="text" name="email" class="form-control" value="<?=htmlspecialchars($user->email())?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
    </div>
</form>
