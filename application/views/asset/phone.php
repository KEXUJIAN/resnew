<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/4
 * Time: 15:22
 */

App::view('templates/header', ['title' => '测试机']);
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
                'url' => '/assets/dataTable/phone',
                'display' => false,
                'headConfigs' => $headConfigs,
            ]);
            ?>
        </div>
    </div>
</div>
<div class="service">
    <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=1689118050&site=qq&menu=yes">
        <img border="0" src="http://wpa.qq.com/pa?p=2:1689118050:41" alt="点击这里给我发消息" title="点击这里给我发消息"/>
    </a>
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
