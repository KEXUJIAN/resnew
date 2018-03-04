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
                    <?php
                    $headConfigs = [
                        [
                            'content' => '序号',
                            'data' => [
                                'col-name' => 'id',
                                'col-width' => '50px',
                            ],
                        ],
                        [
                            'content' => '机型',
                            'data' => [
                                'col-name' => 'type',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '系统',
                            'data' => [
                                'col-name' => 'os',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '分辨率',
                            'data' => [
                                'col-name' => 'resolution',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => 'RAM (M)',
                            'data' => [
                                'col-name' => 'ram',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '运营商',
                            'data' => [
                                'col-name' => 'carrier',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '屏幕尺寸',
                            'data' => [
                                'col-name' => 'screenSize',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '编号',
                            'data' => [
                                'col-name' => 'label',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => 'IMEI',
                            'data' => [
                                'col-name' => 'imei',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '状态',
                            'data' => [
                                'col-name' => 'status',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '备注',
                            'data' => [
                                'col-name' => 'remark',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '操作',
                            'data' => [
                                'col-name' => '#action',
                                'orderable' => 'false',
                            ],
                        ],
                    ];
                    App::view('templates/datatable-phone', [
                        'url' => '/assets/ownAssets/phone',
                        'display' => false,
                        'headConfigs' => $headConfigs,
                    ]);
                    ?>
                </div>
                <div id="simcard-panel" class="tab-pane fade">
                    <?php
                    $headConfigs = [
                        [
                            'content' => '序号',
                            'data' => [
                                'col-name' => 'id',
                                'col-width' => '50px',
                            ],
                        ],
                        [
                            'content' => '手机号',
                            'data' => [
                                'col-name' => 'phoneNumber',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '标志',
                            'data' => [
                                'col-name' => 'label',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '运营商',
                            'data' => [
                                'col-name' => 'carrier',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '归属地',
                            'data' => [
                                'col-name' => 'place',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => 'IMSI',
                            'data' => [
                                'col-name' => 'imsi',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '状态',
                            'data' => [
                                'col-name' => 'status',
                                'orderable' => 'false',
                            ],
                        ],
                        [
                            'content' => '操作',
                            'data' => [
                                'col-name' => '#action',
                                'orderable' => 'false',
                            ],
                        ],
                    ];
                    App::view('templates/datatable-simcard', [
                        'url' => '/assets/ownAssets/simcard',
                        'display' => false,
                        'headConfigs' => $headConfigs,
                    ]);
                    ?>
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
