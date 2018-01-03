<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/3
 * Time: 21:20
 */

use Res\Model\SimCard;

?>

<form id="new-simcard-form" class="form-horizontal ajax-form" action="/admin/save/simcard">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">添加测试卡</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">手机号:</label>
                    <div class="col-md-4">
                        <input type="text" name="phoneNumber" class="form-control">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">归属地:</label>
                    <div class="col-md-4">
                        <input type="text" name="place" class="form-control">
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
                        <?php foreach (SimCard::LABEL_CARRIER as $code => $label): ?>
                            <label><input type="checkbox" name="carrier[]" value="<?=$code?>"><?=$label?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态:</label>
                    <div class="col-md-10 radio">
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
                        <select name="userId" class="form-control"></select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">IMSI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imsi" class="form-control">
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
    $('#new-simcard-form')
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
            if (that.is('[value="<?=SimCard::STATUS_RENT_OUT?>"]')) {
                form.find('select[name="userId"]').closest('.col-md-12').show();
                return;
            }
            form
                .find('select[name="userId"]').val(null).trigger('changed').empty()
                .closest('.col-md-12').hide();
        });
})();
</script>
