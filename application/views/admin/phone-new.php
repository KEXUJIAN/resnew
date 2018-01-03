<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/3
 * Time: 21:20
 */

use Res\Model\Phone;

?>

<form id="new-phone-form" class="form-horizontal ajax-form" action="/admin/save/phone">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">添加测试机</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">机型:</label>
                    <div class="col-md-4">
                        <input type="text" name="type" class="form-control">
                    </div>
                    <label class="col-md-2 control-label">系统:</label>
                    <div class="col-md-4">
                        <input type="text" name="os" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">分辨率:</label>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="resolutionW" class="form-control">
                            <span class="input-group-addon">&times;</span>
                            <input type="text" name="resolutionH" class="form-control">
                        </div>
                    </div>
                    <label class="col-md-2 control-label">RAM:</label>
                    <div class="col-md-4">
                        <input type="text" name="ram" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">屏幕尺寸:</label>
                    <div class="col-md-4">
                        <input type="text" name="screenSize" class="form-control">
                    </div>
                    <label class="col-md-2 control-label">标识:</label>
                    <div class="col-md-4">
                        <input type="text" name="label" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">运营商:</label>
                    <div class="col-md-10 checkbox">
                        <?php foreach (Phone::LABEL_CARRIER as $code => $label): ?>
                        <label><input type="checkbox" name="carrier[]" value="<?=$code?>"><?=$label?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态:</label>
                    <div class="col-md-10 radio">
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
                        <select name="userId" class="form-control"></select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">IMEI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imei" class="form-control">
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
    $('#new-phone-form')
        .data('beforeSubmit', function (form) {
            var texts = form.find(':text');
            var i = 0;
            for (; i < texts.length; ++i) {
                if ($.trim(texts[i].value)) {
                    continue;
                }
                bootbox.alert('请填写所有字段');
                return false;
            }
            var checkboxs = form.find(':checkbox');
            var checked = false;
            for (i = 0;i < checkboxs.length; ++i) {
                if (!$(checkboxs[i]).prop('checked')) {
                    continue;
                }
                checked = true;
                break;
            }
            if (!checked) {
                bootbox.alert('请至少选择一个多选字段');
                return false;
            }
            var radios = form.find(':radio');
            var selected = false;
            for (i = 0;i < radios.length; ++i) {
                if (!$(radios[i]).prop('checked')) {
                    continue;
                }
                selected = true;
                break;
            }
            if (!selected) {
                bootbox.alert('请选择单选框');
                return false;
            }
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
})();
</script>