<?php
/**
 * Created by PhpStorm.
 * User: KE, XUJIAN
 * Date: 2018/2/27
 * Time: 22:52
 */

use Res\Model\SimCard;
use Res\Model\User;

?>

<form id="edit-simcard-form" class="form-horizontal ajax-form" action="/admin/update/simcard/<?=$simCard->id()?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">编辑测试卡</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">手机号:</label>
                    <div class="col-md-4">
                        <input type="text" name="phoneNumber" class="form-control" data-required="true" value="<?=htmlspecialchars($simCard->phoneNumber())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">归属地:</label>
                    <div class="col-md-4">
                        <input type="text" name="place" class="form-control" value="<?=htmlspecialchars($simCard->place())?>">
                    </div>
                    <label class="col-md-2 control-label">标识:</label>
                    <div class="col-md-4">
                        <input type="text" name="label" class="form-control" data-required="true" value="<?=htmlspecialchars($simCard->label())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">运营商:</label>
                    <?php $cLabelList = array_flip(explode(',', $simCard->carrier())); ?>
                    <div class="col-md-10 checkbox" data-required="true">
                        <?php foreach (SimCard::LABEL_CARRIER as $code => $label): ?>
                            <?php $match = array_key_exists($code, $cLabelList);?>
                            <label><input type="checkbox" name="carrier[]" value="<?=$code?>" <?php if ($match):?>checked<?php endif;?>><?=$label?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态:</label>
                    <div class="col-md-10 radio" data-required="true">
                        <?php $status = $simCard->status();?>
                        <?php foreach (SimCard::LABEL_STATUS as $code => $label): ?>
                            <label><input type="radio" name="status" value="<?=$code?>"><?=$label?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="display: none;">
                <div class="form-group">
                    <label class="col-md-2 control-label">借出人:</label>
                    <div class="col-md-4">
                        <select name="userId" class="form-control select2" data-url="/user/select2" data-required="true">
                            <?php if (SimCard::STATUS_RENT_OUT === $status):?>
                                <?php $user = User::get($simCard->userId());?>
                            <option value="<?=$user->id()?>" selected="selected"><?=$user->name()?></option>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">IMSI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imsi" class="form-control" value="<?=htmlspecialchars($simCard->imsi())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">身份证:</label>
                    <div class="col-md-10">
                        <input type="text" name="idCard" class="form-control" value="<?=htmlspecialchars($simCard->idCard())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">服务密码:</label>
                    <div class="col-md-10">
                        <input type="text" name="servicePassword" class="form-control" value="<?=htmlspecialchars($simCard->servicePassword())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态描述:</label>
                    <div class="col-md-10">
                        <textarea name="statusDescription" class="form-control no-resize"><?=htmlspecialchars($simCard->statusDescription())?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">备注:</label>
                    <div class="col-md-10">
                        <textarea name="remark" class="form-control no-resize"><?=htmlspecialchars($simCard->remark())?></textarea>
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
    var form = $('#edit-simcard-form')
        .data('beforeSubmit', function () {
            var newData = resmanager.getFormData(form);
            if (JSON.stringify(newData) === oldData) {
                bootbox.alert('没有值被改变，不需要保存');
                return false;
            }
            return true;
        })
        .data('submitDoneSucc', function (ret, form) {
            var _modal = form.closest('.ajax-modal');
            _modal
                .data('afterHidden', function () {
                    bootbox.alert(ret.message);
                    $('#simcard-panel').find('.ajax-table').DataTable().ajax.reload();
                })
                .find('button.btn-default').click();
        })
        .data('submitDoneFail', function (ret) {
            bootbox.alert(ret.message);
        })
        .on('click', ':radio[name="status"]', function (e) {
            var that = $(this);
            var form = $(e.delegateTarget);
            if (that.is('[value="<?=SimCard::STATUS_RENT_OUT?>"]')) {
                form.find('select[name="userId"]').closest('.col-md-12').show();
                return;
            }
            form
                .find('select[name="userId"]').val(null).trigger('changed').empty()
                .closest('.col-md-12').hide();
        });
    form.find(':radio[name="status"][value="<?=$status?>"]').click();
    var oldData = JSON.stringify(resmanager.getFormData(form));
})();
</script>
