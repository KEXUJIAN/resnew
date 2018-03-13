<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 15:26
 */

App::view('templates/header', ['title' => '测试卡']);
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 panel panel-default panel-assets">
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
                'url' => '/assets/dataTable/simcard',
                'display' => false,
                'headConfigs' => $headConfigs,
            ]);
            ?>
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
    resRunInit();
    var table = $('table');
    table
        .on('click', '[data-role="rent-out"], [data-role="transfer"], [data-role="return"]', function () {
            var that = $(this);
            $.get(that.data('url'), null, null, 'json')
                .done(function (ret) {
                    if (!ret.result) {
                        bootbox.alert(ret.message || '发生错误', function () {
                            table.DataTable().draw(false);
                        });
                        return;
                    }
                    bootbox.alert(ret.message || '请求成功', function () {
                        table.DataTable().draw(false);
                    });
                })
                .fail(function () {
                    bootbox.alert('服务器错误', function () {
                        table.DataTable().draw(false);
                    });
                });
        });
})();
</script>
<?php
App::view('templates/footer', ['display' => true]);
?>
