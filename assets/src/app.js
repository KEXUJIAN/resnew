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
    dataTable: function (scope) {
        if ($.fn.DataTable === undefined) {
            return;
        }
        /**
         * @link https://datatables.net/plug-ins/i18n/Chinese
         * @author Chi Cheng
         * @type object
         */
        const lang = {
            "sProcessing":   "处理中...",
            "sLengthMenu":   "显示 _MENU_ 项结果",
            "sZeroRecords":  "没有匹配结果",
            "sInfo":         "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty":    "显示第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
            "sInfoPostFix":  "",
            "sSearch":       "搜索:",
            "sUrl":          "",
            "sEmptyTable":     "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands":  ",",
            "oPaginate": {
                "sFirst":    "首页",
                "sPrevious": "上页",
                "sNext":     "下页",
                "sLast":     "末页"
            },
            "oAria": {
                "sSortAscending":  ": 以升序排列此列",
                "sSortDescending": ": 以降序排列此列"
            }
        };

        $('.dataTable.ajax-table', scope).each(function () {
            let that = $(this);
            const display = `
                <"row"<"col-sm-6"l><"col-sm-6"p>>
                <"row"<"col-sm-12"tr>>
                <"row"<"col-sm-6"i><"col-sm-6"p>>
            `;
            let heads = that.find('th');
            let colDefs = [];
            let order = [];
            heads.each(function (index) {
                let _head = $(this);
                let colName = _head.data('colName');
                let orderable = true;
                if (false === _head.data('orderable')) {
                    orderable = false;
                }
                let colDef = {
                    targets: index,
                    data: colName,
                    name: colName,
                    orderable: orderable,
                };
                if (!order.length && orderable) {
                    order.push([index, 'asc']);
                }
                colDefs.push(colDef);
            });
            let options = {
                language: lang,
                dom: display,
                order: order,
                columnDefs: colDefs,
                serverSide: true,
                processing: true,
                ajax: {
                    url: that.data('url'),
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function () {
                        let prevXHR = that.DataTable().settings()[0].jqXHR;
                        if (prevXHR) {
                            prevXHR.abort();
                        }
                    },
                    dataSrc: function (ret) {
                        if (!ret.result) {
                            return [];
                        }
                        return ret.data;
                    }
                },
            };
            let altOptions = that.data('option');
            that.DataTable($.extend({}, options, altOptions));
        });
        $('.dataTable.basic', scope).each(function () {
           ;
        });
    }
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