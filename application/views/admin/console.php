<?php
/**
 * Created by PhpStorm.
 * User: KE
 * Date: 2018/1/1
 * Time: 11:56
 */

App::view('templates/header', ['title' => '后台']);
?>
<style>
    #upload-area {
        position: relative;
        border: 2px dashed #1f1d1d;
        border-radius: 5px;
        background-color: #4F5155;
        color: #fff;
        text-align: center;
        height: 100px;
        font-size: 18px;
        cursor: pointer;
    }
    #upload-area > strong {
        position: relative;
        top: 35%;
    }
    .upload-file {
        opacity: 0;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        cursor: pointer;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <nav class="panel panel-default sidebar">
            <ul class="nav nav-pills nav-stacked">
                <li class="active">
                    <a href="#user-panel" data-toggle="tab">用户管理</a>
                </li>
                <li>
                    <a href="#phone-panel" data-toggle="tab">测试机管理</a>
                </li>
                <li>
                    <a href="#simcard-panel" data-toggle="tab">测试卡管理</a>
                </li>
            </ul>
        </nav>
        <div class="col-md-9 col-md-offset-3">
            <div class="panel panel-default content-panel">
                <div id="upload-area">
                    <strong>拖拽文件 / 点击上传</strong>
                    <input type="file" name="files" class="upload-file">
                </div>
                <div class="progress fade" style="margin: 10px 0; display: none">
                    <div class="progress-bar"></div>
                </div>
                <hr>
                <div class="tab-content">
                    <div id="user-panel" data-url="/admin/upload/user" class="tab-pane fade in active">
                        <?php
                        App::view('templates/datatable-user', [
                            'url' => '/admin/data/user',
                        ]);
                        ?>
                    </div>
                    <div id="phone-panel" data-url="/admin/upload/phone" class="tab-pane fade">
                        <?php
                        App::view('templates/datatable-phone', [
                            'url' => '/admin/data/phone',
                        ]);
                        ?>
                    </div>
                    <div id="simcard-panel" data-url="/admin/upload/simcard" class="tab-pane fade">
                        <?php
                        App::view('templates/datatable-simcard', [
                            'url' => '/admin/data/simcard',
                        ]);
                        ?>
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
    </div>
</div>
<script>
(function () {
    var currentPanel = $('#user-panel');
    var uploadArea = $('#upload-area');
    var uploadElm = uploadArea.find('.upload-file');
    var progressElm = $('.progress');
    var initList = {};
    initList[currentPanel.attr('id')] = true;
    $('.sidebar')
        .on('click', 'a[data-toggle="tab"]', function (e) {
            var that = $(this);
            if (that.closest('li').is('.active')) {
                return;
            }
            if (uploadElm.prop('disabled')) {
                e.stopImmediatePropagation();
                e.preventDefault();
                alert('等待上传完成');
                return;
            }
            var panelId = that.attr('href');
            currentPanel = $(panelId);
        })
        .on('shown.bs.tab', 'a[data-toggle="tab"]', function () {
            var panelId = currentPanel.attr('id');
            if (initList.hasOwnProperty(panelId)) {
                return;
            }
            initList[panelId] = true;
            resRunInit(currentPanel);
        });
    uploadElm
        .fileupload({
            url: '/welcome/upload',
            autoUpload: false,
            dropZone: uploadArea,
            fileInput: uploadElm,
        })
        .bind('fileuploadadd', function (e, data) {
            if (uploadElm.prop('disabled')) {
                return;
            }
            var file = data.files[0];
            if (file.size > 8388608) {
                bootbox.alert('文件过大');
                return;
            }
            var _clone = uploadElm.clone();
            uploadStart();
            _clone
                .fileupload({
                    url: currentPanel.data('url'),
                    fileInput: _clone,
                    replaceFileInput: false,
                })
                .bind('fileuploaddone', function (e, data) {
                    var ret = data.result;
                    if (!ret.result) {
                        bootbox.alert(ret.message);
                    }
                })
                .bind('fileuploadfail',function () {
                    ;
                })
                .bind('fileuploadalways', function () {
                    uploadEnd();
                    _clone.fileupload('destroy');
                    _clone.remove();
                    _clone = null;
                })
                .fileupload('send', {files: file});
        });
    resRunInit(currentPanel);
    resRunInit(null, 'ajaxModal');

    var forceDelete = {
        '#phone-panel': false,
        '#simcard-panel': false
    };
    $('[data-role="delete"]').each(function () {
        if ($(this).closest('.tab-pane').is('#user-panel')) {
            return;
        }
        $(this).data('beforeDelete', function (checkedItem, btn) {
            var i = 0;
            var name = '#' + btn.closest('.tab-pane').prop('id');
            var shouldAlert = false;

            if (forceDelete[name]) {
                forceDelete[name] = false;
                return true;
            }

            for (; i < checkedItem.length; ++i) {
                var tmp = $(checkedItem);
                var status = tmp.closest('tr').find('[data-role="status"]').data('value');
                if (0 !== status) {
                    shouldAlert = true;
                    break;
                }
            }

            if (!shouldAlert) {
                return true;
            }
            bootbox.confirm('要删除的资产中有资产状态为外借或不确定，确认删除？', function (result) {
                if (!result) {
                    return;
                }
                forceDelete[name] = true;
                setTimeout(function () {
                    btn.click();
                }, 700);
            });
            return false;
        });
    });

    function uploadStart() {
        uploadElm.prop('disabled', true);
        uploadArea.css('cursor', 'not-allowed').find('.upload-file').hide();
        progressElm.show().addClass('in');
    }
    function uploadEnd() {
        var progressBar = progressElm.children('.progress-bar');
        progressBar.css('width', '100%');
        setTimeout(function () {
            progressElm.removeClass('in');
            uploadElm.prop('disabled', false);
            uploadArea.css('cursor', 'pointer').find('.upload-file').show();
        }, 800);
        setTimeout(function () {
            progressElm.hide();
            progressBar.css('width', 0);
            currentPanel.find('table.ajax-table').DataTable().ajax.reload();
        }, 1000);
    }
})();
</script>
<?php
App::view('templates/footer');
?>