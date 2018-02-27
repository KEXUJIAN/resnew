<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/7
 * Time: 18:36
 */

use Res\Model\SimCard;

?>

<form class="form-horizontal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">测试卡详情</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">手机号:</label>
                    <div class="col-md-4">
                        <input type="text" name="phoneNumber" class="form-control" value="<?=htmlspecialchars($simCard->phoneNumber())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">归属地:</label>
                    <div class="col-md-4">
                        <input type="text" name="place" class="form-control" value="<?=htmlspecialchars($simCard->place())?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">标识:</label>
                    <div class="col-md-4">
                        <input type="text" name="label" class="form-control" value="<?=htmlspecialchars($simCard->label())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">运营商:</label>
                    <div class="col-md-10" >
                        <?php
                        if ($tmp = $simCard->carrier()):
                            $intersect = array_intersect(explode(',', $tmp), array_keys(SimCard::LABEL_CARRIER));
                            foreach ($intersect as $value):
                                ?>
                            <label class="label label-default"><?=SimCard::LABEL_CARRIER[$value]?></label>
                            <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态:</label>
                    <div class="col-md-10" >
                        <label class="label label-info"><?=SimCard::LABEL_STATUS[$simCard->status()]?></label>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="display: none;">
                <div class="form-group">
                    <label class="col-md-2 control-label">借出人:</label>
                    <div class="col-md-4">
                        <select name="userId" class="form-control" ></select>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">IMSI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imei" class="form-control" value="<?=htmlspecialchars($simCard->imsi())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态描述:</label>
                    <div class="col-md-10">
                        <textarea name="statusDescription" class="form-control no-resize" readonly><?=htmlspecialchars($simCard->statusDescription())?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
    </div>
</form>
