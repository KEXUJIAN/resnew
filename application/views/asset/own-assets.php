<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/5
 * Time: 16:41
 */

App::view('templates/header', ['title' => '我的库存']);
?>
<div class="container-fluid">
    <div class="row">
        <nav class="panel panel-default sidebar">
            <ul class="nav nav-pills nav-stacked">
                <li>
                    <a href="#phone-panel" data-toggle="tab">测试机</a>
                </li>
                <li>
                    <a href="#simcard-panel" data-toggle="tab">测试卡</a>
                </li>
            </ul>
        </nav>
        <div class="col-md-9 col-md-offset-3">
            <div class="tab-content panel panel-default content-panel">
                <div id="phone-panel" class="tab-pane fade">
                    <div class="data-table-action-wrapper col-md-12">
                        <div class="pull-left" style="width: 100%;">
                            <div class="pull-right" style="margin-bottom: 10px">
                                <button class="btn btn-info" data-role="refresh">
                                    <i class="fa fa-refresh"></i> 刷新
                                </button>
                            </div>
                        </div>
                    </div>
                    <table class="table dataTable ajax-table table-striped table-bordered no-footer table-hover" data-url="/assets/ownAssets/phone">
                        <thead>
                        <tr>
                            <th data-col-name="id" data-col-width="50px">
                                <div class="checkbox" style="margin: 0">
                                    <label><input type="checkbox"> 序号</label>
                                </div>
                            </th>
                            <th data-col-name="type" data-orderable="false">
                                机型
                            </th>
                            <th data-col-name="os" data-orderable="false">
                                系统
                            </th>
                            <th data-col-name="resolution" data-orderable="false">
                                分辨率
                            </th>
                            <th data-col-name="ram" data-orderable="false">
                                RAM (M)
                            </th>
                            <th data-col-name="carrier" data-orderable="false">
                                运营商
                            </th>
                            <th data-col-name="screenSize" data-orderable="false">
                                屏幕尺寸
                            </th>
                            <th data-col-name="label" data-orderable="false">
                                编号
                            </th>
                            <th data-col-name="imei" data-orderable="false">
                                IMEI
                            </th>
                            <th data-col-name="status" data-orderable="false">
                                状态
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
                </div>
                <div id="simcard-panel" class="tab-pane fade">
                    <div class="data-table-action-wrapper col-md-12">
                        <div class="pull-left" style="width: 100%;">
                            <div class="pull-right" style="margin-bottom: 10px">
                                <button class="btn btn-info" data-role="refresh">
                                    <i class="fa fa-refresh"></i> 刷新
                                </button>
                            </div>
                        </div>
                    </div>
                    <table class="table dataTable ajax-table table-striped table-bordered no-footer table-hover" data-url="/assets/ownAssets/simcard">
                        <thead>
                        <tr>
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
                            <th data-col-name="imsi" data-orderable="false">
                                IMSI
                            </th>
                            <th data-col-name="status" data-orderable="false">
                                状态
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
    var currentPanel = $(initialPanelName).addClass('in');
    var initList = {};
    initList[currentPanel.attr('id')] = true;
    $('a[href="' + initialPanelName + '"]').click();
    <?php if ($assetId ?? false):?>
    currentPanel.find('table')
        .data('request', {specificId: <?=$assetId?>})
        .one('xhr.dt', function () {
            $(this).removeData('request');
        });
    <?php endif;?>
    $('.sidebar')
        .on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            currentPanel = $($(e.target).attr('href'));
            if (initList.hasOwnProperty(currentPanel.attr('id'))) {
                return;
            }
            resRunInit(currentPanel);
        });
    $('table').each(function () {
        var that = $(this);
        that.on('click', '[data-role="rent-out"], [data-role="return"]', function () {
            var button = $(this);
            $.get(button.data('url'), null, null, 'json')
                .done(function (ret) {
                    if (!ret.result) {
                        bootbox.alert(ret.message || '发生错误', function () {
                            that.DataTable().draw(false);
                        });
                        return;
                    }
                    bootbox.alert(ret.message || '请求成功', function () {
                        that.DataTable().draw(false);
                    });
                })
                .fail(function () {
                    bootbox.alert('发生错误', function () {
                        that.DataTable().draw(false);
                    });
                });
        })
    });
    resRunInit(currentPanel);
    resRunInit();
})();
</script>
<?php
App::view('templates/footer', ['display' => true]);
?>
