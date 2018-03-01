<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/7
 * Time: 18:22
 */

use Res\Model\Phone;
use Res\Model\User;

?>
<form class="form-horizontal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">测试机详情</h4>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">机型:</label>
                    <div class="col-md-4">
                        <input type="text" name="type" class="form-control" value="<?=htmlspecialchars($phone->type())?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">系统:</label>
                    <div class="col-md-4">
                        <input type="text" name="os" class="form-control" value="<?=htmlspecialchars($phone->os())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">分辨率:</label>
                    <div class="col-md-4">
                        <input type="text" name="resolution" class="form-control" value="<?=htmlspecialchars($phone->resolution())?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">RAM:</label>
                    <div class="col-md-4">
                        <input type="text" name="ram" class="form-control" value="<?=htmlspecialchars($phone->ram())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">屏幕尺寸:</label>
                    <div class="col-md-4">
                        <input type="text" name="screenSize" class="form-control" value="<?=htmlspecialchars($phone->screenSize())?>" readonly>
                    </div>
                    <label class="col-md-2 control-label">标识:</label>
                    <div class="col-md-4">
                        <input type="text" name="label" class="form-control" value="<?=htmlspecialchars($phone->label())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">运营商:</label>
                    <div class="col-md-10" >
                        <?php
                        if ($tmp = $phone->carrier()):
                            $intersect = array_intersect(explode(',', $tmp), array_keys(Phone::LABEL_CARRIER));
                            foreach ($intersect as $value):
                        ?>
                            <label class="label label-default"><?=Phone::LABEL_CARRIER[$value]?></label>
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
                        <?php $status = $phone->status();?>
                        <label class="label label-info"><?=Phone::LABEL_STATUS[$status]?></label>
                        <?php if (Phone::STATUS_RENT_OUT === $status): ?>
                            <?php $user = User::get($phone->userId());?>
                        <label class="label label-primary"><?=$user->name()?></label>
                        <?php endif;?>
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
                    <label class="col-md-2 control-label">IMEI:</label>
                    <div class="col-md-10">
                        <input type="text" name="imei" class="form-control" value="<?=htmlspecialchars($phone->imei())?>" readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="col-md-2 control-label">状态描述:</label>
                    <div class="col-md-10">
                        <textarea name="statusDescription" class="form-control no-resize" readonly><?=htmlspecialchars($phone->statusDescription())?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
</div>
</form>
