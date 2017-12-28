<?php
App::view('templates/header', ['title' => 'Login']);
?>
<style type="text/css">
    .login-panel {
        width: 50%;
        padding-top: 30px;
        border-radius: 5px;
        background-color: #f8f8f8;
        border-color: #e7e7e7;
    }
</style>
<div class="container login-panel">
    <form class="form-horizontal ajax-form" action="/user/doLogin">
        <div class="form-group">
            <label for="username" class="control-label col-md-3">用户名: </label>
            <div class="col-md-8"><input type="text" name="username" class="form-control" placeholder="用户名"></div>
        </div>
        <div class="form-group">
            <label for="password" class="control-label col-md-3">用户密码: </label>
            <div class="col-md-8"><input type="password" name="password" class="form-control" placeholder="密码"></div>
        </div>
        <div class="form-group">
            <div class="col-md-3 col-md-offset-8"><button class="btn btn-primary pull-right submit">登录</button></div>
        </div>
        <div class="form-group">
            <div class="col-md-9 col-md-offset-2">
                <div class="alert alert-danger" style="display: none;">
                    <a class="close">
                        &times;
                    </a>
                    <strong>警告！</strong><span></span>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
(function () {
    var alertElm = $('.alert');
    var form = $('form');

    resRunInit();

    form
    .data('formData', function (data) {
        var username = data.username;
        var password = data.password;
        data.password = sha1(username + password);
    })
    .data('submitDoneSucc', function (ret) {
        window.location.href = ret.message;
    })
    .data('submitDoneFail', function (ret) {
        showError(ret.message);
    });

    alertElm.on('click', '.close', function (event) {
        event.preventDefault();
        alertElm.animate({
            opacity: 0
        },
        'normal', function() {
            alertElm.hide();
        });
    });
    function showError(msg) {
        alertElm.find('span').text(msg);
        alertElm.css('opacity', 0);
        alertElm.show();
        alertElm.animate({opacity: 1});
    }

})();
</script>
<?php
App::view('templates/footer');
?>