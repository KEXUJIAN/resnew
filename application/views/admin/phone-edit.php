<?php
/**
 * Created by PhpStorm.
 * User: KE, XUJIAN
 * Date: 2018/2/23
 * Time: 22:23
 */

use Res\Model\Phone;
use Res\Model\User;

?>

<form id="edit-phone-form" class="form-horizontal ajax-form" action="/admin/update/phone/<?=$phone->id()?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">编辑测试机</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">机型:</label>
                    <div class="col-md-4">
                        <input type="text" name="type" class="form-control" data-required="true" value="<?=htmlspecialchars($phone->type())?>">
                    </div>
                    <label class="col-md-2 control-label">系统:</label>
                    <div class="col-md-4">
                        <input type="text" name="os" class="form-control" data-required="true" value="<?=htmlspecialchars($phone->os())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">分辨率:</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <?php
                            $resolution = $phone->resolution();
                            if ($resolution):
                                $resolution = explode(' X ', $resolution);
                            else:
                                $resolution = ['', ''];
                            endif;
                            ?>
                            <input type="text" name="resolutionW" class="form-control" value="<?=$resolution[0]?>">
                            <span class="input-group-addon">&times;</span>
                            <input type="text" name="resolutionH" class="form-control" value="<?=$resolution[1]?>">
                        </div>
                    </div>
                    <label class="col-md-2 control-label">RAM:</label>
                    <div class="col-md-4">
                        <input type="text" name="ram" class="form-control" value="<?=htmlspecialchars($phone->ram())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">屏幕尺寸:</label>
                    <div class="col-md-4">
                        <input type="text" name="screenSize" class="form-control" value="<?=htmlspecialchars($phone->screenSize())?>">
                    </div>
                    <label class="col-md-2 control-label">标识:</label>
                    <div class="col-md-4">
                        <input type="text" name="label" class="form-control" data-required="true" value="<?=htmlspecialchars($phone->label())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">运营商:</label>
                    <?php $cLabelList = array_flip(explode(',', $phone->carrier())); ?>
                    <div class="col-md-10 checkbox" data-required="true">
                        <?php foreach (Phone::LABEL_CARRIER as $code => $label): ?>
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
                        <?php $status = $phone->status();?>
                        <?php foreach (Phone::LABEL_STATUS as $code => $label): ?>
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
                            <?php if (Phone::STATUS_RENT_OUT === $status):?>
                                <?php $user = User::get($phone->userId());?>
                            <option value="<?=$user->id()?>" selected="selected"><?=$user->name()?></option>
                            <?php endif;?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">IMEI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imei" class="form-control" value="<?=htmlspecialchars($phone->imei())?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态描述:</label>
                    <div class="col-md-10">
                        <textarea name="statusDescription" class="form-control no-resize"><?=htmlspecialchars($phone->statusDescription())?></textarea>
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
    var form = $('#edit-phone-form')
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
                    $('#phone-panel').find('.ajax-table').DataTable().ajax.reload();
                })
                .find('button.btn-default').click();
        })
        .data('submitDoneFail', function (ret) {
            bootbox.alert(ret.message);
        })
        .on('click', ':radio[name="status"]', function (e) {
            var that = $(this);
            var form = $(e.delegateTarget);
            if (that.is('[value="<?=Phone::STATUS_RENT_OUT?>"]')) {
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
