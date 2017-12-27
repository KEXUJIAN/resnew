<?php
App::view('templates/header', ['title' => 'Login']);
?>
<style type="text/css">
    .login-panel {
        width: 50%;
        padding-top: 15px;
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
            <div class="col-md-9 col-md-offset-2" role="alert-container">
                <div class="alert alert-danger">
                    <a class="close">
                        &times;
                    </a>
                    <strong>警告！</strong><span></span>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
(function () {
    let alertContainer = $('[role="alert-container"]');
    let alertElm = alertContainer.children('.alert');
    let form = $('form');
    form.data('formData', (data) => {
        console.log(data);
    });
    resRunInit();
    alertElm.on('click', '.close', function(event) {
        event.preventDefault();
        alertElm.animate({
            opacity: 0
        },
        'normal', function() {
            alertElm.hide();
        });
    });
})();
</script>
<?php
App::view('templates/footer');
?>