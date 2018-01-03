<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/1
 * Time: 23:08
 */
?>

<div class="data-table-action-wrapper col-md-12">
    <div class="pull-left" style="width: 100%;">
        <div class="pull-right" style="margin-bottom: 10px">
            <button class="btn btn-danger">
                <i class="fa fa-trash"></i> 删除用户
            </button>
            <button class="btn btn-primary">
                <i class="fa fa-plus"></i> 添加用户
            </button>
            <button class="btn btn-default" data-toggle="collapse" data-target="#user-filter">
                <i class="fa fa-caret-down"></i>搜索框
            </button>
        </div>
        <div id="user-filter" class="pull-left collapse">
            <form class="form-horizontal">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label col-md-2">姓名:</label>
                        <div class="col-md-2">
                            <input type="text" name="name" class="form-control">
                        </div>
                        <label class="control-label col-md-2">用户名:</label>
                        <div class="col-md-2">
                            <input type="text" name="username" class="form-control">
                        </div>
                        <label class="control-label col-md-2">邮箱:</label>
                        <div class="col-md-2">
                            <input type="text" name="email" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
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
            <th data-col-name="id" data-col-width="50px">
                <div class="checkbox" style="margin: 0">
                    <label><input type="checkbox"> 序号</label>
                </div>
            </th>
            <th data-col-name="name" data-orderable="false">
                姓名
            </th>
            <th data-col-name="username" data-orderable="false">
                用户名
            </th>
            <th data-col-name="email" data-orderable="false">
                邮箱
            </th>
            <th data-col-name="role" data-orderable="false">
                角色
            </th>
            <th data-col-name="timeAdded" data-orderable="false">
                添加时间
            </th>
            <th data-col-name="#action" data-orderable="false">
                操作
            </th>
        </tr>
    </thead>
</table>
