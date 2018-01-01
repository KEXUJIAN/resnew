<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/1
 * Time: 23:08
 */
?>

<div class="data-table-action-wrapper" style="float: left;margin-bottom: 10px">
    <div class="action" style="float: right;">
        <div class="btn-group">
            <button class="btn btn-primary">
                <i class="fa fa-plus"></i> 添加用户
            </button>
            <button class="btn btn-danger">
                <i class="fa fa-trash"></i> 删除用户
            </button>
            <button class="btn btn-default">
                <i class="fa fa-search"></i> 搜索
            </button>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<table class="table dataTable ajax-table table-striped table-bordered no-footer hover" data-url="<?=$url ?? ''?>">
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
            <th data-col-name="timeAdded" data-orderable="false">
                添加时间
            </th>
            <th data-col-name="role" data-orderable="false">
                角色
            </th>
            <th data-col-name="#action" data-orderable="false">
                操作
            </th>
        </tr>
    </thead>
</table>
