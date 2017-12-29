const $ = require('jquery');
const initObj = {
    ajaxForm: function (scope) {
        $('.ajax-form', scope).each(function () {
            let that = $(this);
            that.on('submit', (e) => {
                e.preventDefault();
            });
            // 监听我们自己的提交事件
            that.on('ajaxSubmit.resmanager', async function (e, submitTrigger) {
                let bs = that.data('beforeSubmit');
                if ($.isFunction(bs) && bs(that) === false) {
                    return;
                }
                if (!submitTrigger) {
                    throw {
                        message: 'need a submit trigger',
                        name: 'submitException',
                        code: 1,
                    };
                    return;
                }
                if (submitTrigger.prop('disabled')) {
                    return;
                }
                submitTrigger.prop('disabled', true);
                let url = that.attr('action') || '';
                let formData = that.serializeArray();
                let dataObj = {};
                formData.forEach(function (data) {
                    let key = data.name;
                    let value = data.value;
                    if (!dataObj.hasOwnProperty(key)) {
                        dataObj[key] = value;
                        return;
                    }
                    if (!$.isArray(dataObj[key])) {
                        let tmp = [dataObj[key], value];
                        dataObj[key] = tmp;
                        return;
                    }
                    dataObj[key].push(value);
                });
                let fd = that.data('formData');
                if ($.isFunction(fd)) {
                    fd(dataObj);
                }
                let result;
                try {
                    result = true;
                    let ret = await $.post(url, dataObj, null, 'json');
                    let done = that.data('submitDone');
                    let doneSucc = that.data('submitDoneSucc');
                    let doneFail = that.data('submitDoneFail');
                    if ($.isFunction(done)) {
                        done(ret);
                    } else if ($.isFunction(doneSucc) && ret.result) {
                        delete ret.result;
                        doneSucc(ret);
                    } else if ($.isFunction(doneFail) && !ret.result) {
                        delete ret.result;
                        doneFail(ret);
                    }
                } catch (err) {
                    result = false;
                    let fail = that.data('submitFail');
                    if ($.isFunction(fail)) {
                        result = fail(err);
                    } else {
                        bootbox.alert('发生错误, 检查您的网络设置或联系管理员');
                    }
                } finally {
                    submitTrigger.prop('disabled', false);
                }
                if (!result) {
                    return;
                }
                let afs = that.data('afterSubmit');
                if ($.isFunction(afs) && afs(that) === false) {
                    return;
                }
                // let target =
            });
            that.on('click', '.submit', function (e) {
                e.preventDefault();
                that.trigger('ajaxSubmit.resmanager', [$(this)]);
            });
        });
    },
};
let Initialize = function (scope) {
    if (!scope) {
        scope = $('body');
    }
    for (let key in initObj) {
        if (!initObj.hasOwnProperty(key)) {
            continue;
        }
        initObj[key](scope);
    }
}

window.resRunInit = Initialize;