<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/2
 * Time: 20:05
 */

use Res\Model\User;
use Res\Model\SimCard;

?>

<div class="data-table-action-wrapper col-md-12">
    <div class="pull-left" style="width: 100%;">
        <div class="pull-right" style="margin-bottom: 10px">
            <?php if (App::getUser()->role() === User::ROLE_MANAGER && ($display ?? true)): ?>
            <button class="btn btn-danger" data-role="delete" data-url="/admin/delete/simcard">
                <i class="fa fa-trash"></i> 删除测试卡
            </button>
            <button class="btn btn-primary" data-toggle="modal" data-target="#ajax-modal" data-url="/admin/new/simcard">
                <i class="fa fa-plus"></i> 添加测试卡
            </button>
            <?php endif; ?>
            <button class="btn btn-default" data-toggle="collapse" data-target="#simcard-filter">
                <i class="fa fa-caret-down"></i>搜索框
            </button>
            <button class="btn btn-info" data-role="refresh">
                <i class="fa fa-refresh"></i> 刷新
            </button>
        </div>
        <div id="simcard-filter" class="pull-left collapse">
            <form class="form-horizontal">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-2">号码:</label>
                        <div class="col-md-2">
                            <input type="text" name="phoneNumber" class="form-control" placeholder="">
                        </div>
                        <label class="control-label col-md-2">归属地:</label>
                        <div class="col-md-2">
                            <input type="text" name="place" class="form-control" placeholder="例: 广州">
                        </div>
                        <label class="control-label col-md-2">标识:</label>
                        <div class="col-md-2">
                            <input type="text" name="label" class="form-control" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-2">运营商:</label>
                        <div class="col-md-4 radio">
                            <?php foreach (SimCard::LABEL_CARRIER as $code => $label): ?>
                                <label><input type="radio" name="carrier" value="<?=$code?>"><?=$label?></label>
                            <?php endforeach; ?>
                        </div>
                        <label class="control-label col-md-2">添加时间:</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="timeAddedMin" class="form-control">
                                <span class="input-group-addon">-</span>
                                <input type="text" name="timeAddedMax" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-2">状态:</label>
                        <div class="col-md-4 checkbox">
                            <?php foreach (SimCard::LABEL_STATUS as $code => $label): ?>
                                <label><input type="checkbox" name="status[]" value="<?=$code?>"><?=$label?></label>
                            <?php endforeach; ?>
                        </div>
                        <label class="control-label col-md-2">IMSI:</label>
                        <div class="col-md-4">
                            <input type="text" name="imsi" class="form-control" placeholder="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-md-offset-5">
                    <button class="btn btn-primary submit">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <button class="btn btn-default" type="reset">
                        <i class="fa fa-undo"></i>重置
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<table class="table dataTable ajax-table table-striped table-bordered no-footer table-hover" data-url="<?=$url ?? ''?>">
    <thead>
    <tr>
    <?php if ($headConfigs ?? $headConfigs = []):?>
        <?php foreach ($headConfigs as $config):?>
        <th <?php foreach ($config['data'] as $key => $value): echo "data-{$key}=\"{$value}\" "; endforeach;?>>
            <?=$config['content']?>
        </th>
        <?php endforeach;?>
    <?php else:?>
        <th data-col-name="id" data-col-width="50px">
            <div class="checkbox" style="margin: 0">
                <label><input type="checkbox"> 序号</label>
            </div>
        </th>
        <th data-col-name="phoneNumber" data-orderable="false">
            手机号
        </th>
        <th data-col-name="label" data-orderable="false">
            标志
        </th>
        <th data-col-name="carrier" data-orderable="false">
            运营商
        </th>
        <th data-col-name="place" data-orderable="false">
            归属地
        </th>
        <th data-col-name="imsi" data-orderable="false">
            IMSI
        </th>
        <th data-col-name="status" data-orderable="false">
            状态
        </th>
        <th data-col-name="idCard" data-orderable="false">
            身份证
        </th>
        <th data-col-name="servicePassword" data-orderable="false">
            服务密码
        </th>
        <th data-col-name="remark" data-orderable="false">
            备注
        </th>
        <th data-col-name="timeAdded" data-orderable="false">
            添加时间
        </th>
        <th data-col-name="#action" data-orderable="false">
            操作
        </th>
    <?php endif;?>
    </tr>
    </thead>
</table>
