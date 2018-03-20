<?php
/**
 * Created by PhpStorm.
 * User: KE, XUJIAN
 * Date: 2018/3/19
 * Time: 19:00
 */

use Res\Model\Request;

?>

<div class="data-table-action-wrapper col-md-12">
    <div class="pull-left" style="width: 100%;">
        <div class="pull-right" style="margin-bottom: 10px">
            <button class="btn btn-default" data-toggle="collapse" data-target="#request-filter">
                <i class="fa fa-caret-down"></i>搜索框
            </button>
            <button class="btn btn-info" data-role="refresh">
                <i class="fa fa-refresh"></i> 刷新
            </button>
        </div>
        <div id="request-filter" class="col-md-12 collapse" style="padding-right: 0;padding-left: 0;">
            <form class="form-horizontal">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-2">资产种类:</label>
                        <div class="col-md-4">
                            <select name="assetType" class="select2">
                                <option value="">请选择</option>
                                <?php foreach (Request::LABEL_ASSET_TYPE as $value => $label): ?>
                                <option value="<?=$value?>"><?=htmlspecialchars($label)?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <label class="control-label col-md-2">标志:</label>
                        <div class="col-md-4">
                            <select name="assetId" class="select2" data-url="/assets/select2" title="先选择资产种类">
                            </select>
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
<table class="table dataTable ajax-table table-striped table-bordered table-hover" data-url="<?=$url ?? ''?>">
    <thead>
    <tr>
        <th data-col-name="id" data-col-width="50px" data-orderable="false">
            序号
        </th>
        <th data-col-name="fromUserId" data-orderable="false">
            发起人
        </th>
        <th data-col-name="toUserId" data-orderable="false">
            接收人
        </th>
        <th data-col-name="assetId" data-orderable="false">
            资产
        </th>
        <th data-col-name="assetType" data-orderable="false">
            资产种类
        </th>
        <th data-col-name="type" data-orderable="false">
            请求类型
        </th>
        <th data-col-name="status" data-orderable="false">
            状态
        </th>
        <th data-col-name="timeAdded" data-orderable="false">
            发起时间
        </th>
        <th data-col-name="timeModified" data-orderable="false">
            结束时间
        </th>
        <th data-col-name="#action" data-orderable="false" data-col-width="50px">
            操作
        </th>
    </tr>
    </thead>
</table>
