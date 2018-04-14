<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/6
 * Time: 22:48
 */

App::view('templates/header', ['title' => '个人页']);
?>

<div class="container-fluid">
    <div class="row">
        <nav class="panel panel-default sidebar">
            <ul class="nav nav-pills nav-stacked">
                <li>
                    <a href="#user-panel" data-toggle="tab">个人资料</a>
                </li>
                <li>
                    <a href="#notification-panel" data-toggle="tab">通知</a>
                </li>
                <li>
                    <a href="#request-panel" data-toggle="tab">请求</a>
                </li>
            </ul>
        </nav>
        <div class="col-md-9 col-md-offset-3">
            <div class="tab-content panel panel-default content-panel">
                <div id="user-panel" class="tab-pane fade">
                    <form class="form-horizontal ajax-form" action="/user/reset">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="col-md-2 control-label">用户名:</label>
                                <div class="col-md-4">
                                    <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($user->username())?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="col-md-2 control-label">姓名:</label>
                                <div class="col-md-4">
                                    <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($user->name())?>" readonly>
                                </div>
                                <label class="col-md-2 control-label">邮箱:</label>
                                <div class="col-md-4">
                                    <input type="text" name="email" class="form-control" value="<?=htmlspecialchars($user->email())?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="col-md-2 control-label">新密码:</label>
                                <div class="col-md-4">
                                    <input type="password" name="password" class="form-control" data-required="true">
                                </div>
                                <label class="col-md-2 control-label">确认密码:</label>
                                <div class="col-md-4">
                                    <input type="password" name="passwordCfm" class="form-control" data-required="true">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary submit pull-right">
                                更改密码
                            </button>
                        </div>
                    </form>
                </div>
                <div id="notification-panel" class="tab-pane fade">
                    <div class="data-table-action-wrapper col-md-12">
                        <div class="pull-left" style="width: 100%;">
                            <div class="pull-right" style="margin-bottom: 10px">
                                <button class="btn btn-info" data-role="refresh">
                                    <i class="fa fa-refresh"></i> 刷新
                                </button>
                            </div>
                        </div>
                    </div>
                    <table class="table dataTable ajax-table table-striped table-hover" data-url="/notification/dataTable">
                        <thead>
                        <tr>
                            <th data-col-name="id" data-col-width="50px" data-orderable="false">
                                序号
                            </th>
                            <th data-col-name="message" data-orderable="false">
                                消息
                            </th>
                            <th data-col-name="#action" data-orderable="false" data-col-width="50px">
                                操作
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div id="request-panel" class="tab-pane fade">
                    <?=App::view('templates/datatable-request', [
                        'url' => '/request/dataTable',
                    ], true)?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="ajax-modal" class="modal ajax-modal fade" data-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>
<script>
(function () {
    var initialPanelName = '#' + '<?=$panel?>' + '-panel';
    var currentPanel = $(initialPanelName).addClass('in active');

    var initList = {};
    initList[currentPanel.attr('id')] = true;
    $('.sidebar')
        .on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            currentPanel = $($(e.target).attr('href'));
            if (initList.hasOwnProperty(currentPanel.attr('id'))) {
                return;
            }
            resRunInit(currentPanel);
        });
    $('#user-panel').find('form.ajax-form')
        .data('submitDone', function (ret) {
            if (!ret.result) {
                bootbox.alert(ret.message);
                return;
            }
            bootbox.alert(ret.message, function () {
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            });
        });
    var requestPanel = $('#request-panel');
    requestPanel
        .on('change', '[name="assetType"]', function () {
            var that = $(this);
            requestPanel.find('[name="assetId"]').data('url', '/assets/select2/' + that.val());
        })
        .find('table.ajax-table')
        .on('click', '[data-role="action"]', function (e) {
            var table = $(e.delegateTarget);
            var that = $(this);
            $.getJSON(that.data('url'))
                .done(function (ret) {
                    bootbox.alert(ret.message);
                })
                .fail(function () {
                    bootbox.alert('服务器错误');
                })
                .always(function () {
                    table.DataTable().draw(false);
                });
        });
    <?php if ($id ?? false):?>
    currentPanel.find('table')
        .data('request', {specificId: <?=$id?>})
        .one('xhr.dt', function () {
            $(this).removeData('request');
        });
    <?php endif;?>
    $('a[href="' + initialPanelName + '"]').click();
    resRunInit(currentPanel);
    resRunInit(null, 'ajaxModal');
})();
</script>

<?php
App::view('templates/footer', ['display' => true]);
?>
