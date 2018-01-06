const $ = require('jquery');

$(function () {
   let notify = $('#notification-bell');
   if (!notify.length) {
       return;
   }

    setTimeout(async () => {
        await getNotification();
    }, 0);

   async function getNotification() {
       let nr = 0;
       try {
           let ret = await $.post(notify.data('url'), null, null, 'json');
           nr = ret.result ? ret.message : 0;
       } catch (err) {
           nr = 0;
       } finally {
           notify.find('.badge.badge-jump').text(nr || null);
       }
       setTimeout(async () => {
           await getNotification();
       }, 5000);
   }
});

const appRes = {
    getFormData: (form) => {
        let formData = form.serializeArray();
        let dataObj = {};
        formData.forEach(function (data) {
            let key = data.name;
            let value = data.value;
            if (!dataObj.hasOwnProperty(key)) {
                dataObj[key] = value;
                return;
            }
            if (!$.isArray(dataObj[key])) {
                dataObj[key] = [dataObj[key], value];
                return;
            }
            dataObj[key].push(value);
        });
        return dataObj;
    },
    validForm: function (form) {
        let fields = form.find(':text[data-required="true"], select[data-required="true"]');
        for (let i = 0; i < fields.length; ++i) {
            let field = $(fields[i]);
            if (!field.is(':visible')) {
                continue;
            }
            if (!$.trim(field.val())) {
                return 1;
            }
        }
        fields = form.find('.checkbox[data-required="true"]');
        if (fields.length) {
            for (let i = 0; i < fields.length; ++i) {
                let field = $(fields[i]).find(':checkbox:checked');
                if (!field.length) {
                    return 2;
                }
            }
        }
        fields = form.find('.radio[data-required="true"]');
        if (fields.length) {
            for (let i = 0; i < fields.length; ++i) {
                let field = $(fields[i]).find(':radio:checked');
                if (!field.length) {
                    return 3;
                }
            }
        }
        return 0;
    }
};

const initObj = {
    ajaxForm: function (scope) {
        $('.ajax-form', scope).each(function () {
            let that = $(this);
            if (true === that.data('isInit')) {
                return;
            }
            that.data('isInit', true);
            that.on('submit', (e) => {
                e.preventDefault();
            });
            that.find(':text[data-required="true"], select[data-required="true"], .checkbox[data-required="true"], .radio[data-required="true"]').each(function () {
                let input = $(this);
                let wrapper = input.closest('div[class^=col]');
                if (!wrapper.length) {
                    return;
                }
                let label = wrapper.prev();
                if (!label.length || !label.is('.control-label')) {
                    return;
                }
                label.addClass('required');
            });
            // 监听我们自己的提交事件
            that.on('ajaxSubmit.resmanager', async function (e, submitTrigger) {
                if (appRes.validForm(that)) {
                    bootbox.alert('请填写所有必填项');
                    return;
                }
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
                }
                if (submitTrigger.prop('disabled')) {
                    return;
                }
                submitTrigger.prop('disabled', true);
                let url = that.attr('action') || '';
                let dataObj = appRes.getFormData(that);
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
                        done(ret, that);
                    } else if ($.isFunction(doneSucc) && ret.result) {
                        delete ret.result;
                        doneSucc(ret, that);
                    } else if ($.isFunction(doneFail) && !ret.result) {
                        delete ret.result;
                        doneFail(ret, that);
                    }
                } catch (err) {
                    result = false;
                    let fail = that.data('submitFail');
                    if ($.isFunction(fail)) {
                        result = fail(err, that);
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
            if (true === that.data('isInit')) {
                return;
            }
            that.data('isInit', true);
            let idElm = that.find('th[data-col-name="id"]');
            if (idElm.length) {
                idElm.on('click', '.checkbox', function (e) {
                    e.stopImmediatePropagation();
                });
            }
            let actionPanel = that.siblings('.data-table-action-wrapper');
            if (actionPanel.length) {
                actionPanel
                    .on('click', '[data-toggle="collapse"]', function () {
                        let btnCollapse = $(this);
                        if (actionPanel.find(btnCollapse.data('target')).is('.in')) {
                            btnCollapse.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
                        } else {
                            btnCollapse.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
                        }
                    })
                    .on('click', '[data-role="refresh"]', function () {
                        that.DataTable().draw(false);
                    });
                let filterForm = actionPanel.find('form');
                filterForm
                    .on('submit', function (e) {
                        e.preventDefault();
                    })
                    .on('click', '.submit', function (e) {
                        e.preventDefault();
                        let formData = appRes.getFormData(filterForm);
                        that.data('request', formData);
                        that.DataTable().ajax.reload();
                    })
                    .on('click', '[type="reset"]', function () {
                        that.data('request', null);
                    });
            }
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
                let colWidth = _head.data('colWidth');
                if (false === _head.data('orderable')) {
                    orderable = false;
                }
                let colDef = {
                    targets: index,
                    data: colName,
                    name: colName,
                    orderable: orderable,
                    width: colWidth,
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
                    data: function (request) {
                        return $.extend({}, request, that.data('request'));
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
    },
    ajaxModal: function (scope) {
        $('.modal.ajax-modal', scope).each(function () {
            let that = $(this);
            if (true === that.data('isInit')) {
                return;
            }
            that.data('isInit', true);
            that
                .on('show.bs.modal', async function (e) {
                    let _trigger = e.relatedTarget;
                    let _content = that.find('.modal-content');
                    if (!_trigger) {
                        e.preventDefault();
                        return;
                    }
                    _trigger = $(_trigger);
                    let url = _trigger.data('url');
                    if (!url) {
                        e.preventDefault();
                        return;
                    }
                    try {
                        let ret = await $.get(url);
                        _content.html(ret);
                        setTimeout(function () {
                            resRunInit(_content);
                        }, 0);
                    } catch (err) {
                        let _html = err.responseText;
                        _content.html(`
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">发生错误</h4>
                            </div>
                            <div class="modal-body">
                                ${_html}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            </div>
                        `);
                    }

                })
                .on('hide.bs.modal', function (e) {
                    ;
                })
                .on('hidden.bs.modal', function () {
                    let ah = that.data('afterHidden');
                    if ($.isFunction(ah)) {
                        ah(that);
                    }
                    that.find('.modal-content').empty();
                    that.removeData();
                    that.data('isInit', true).data('backdrop', 'static');
                });
        })
    },
    tooltip: function (scope) {
        $('[data-toggle="tooltip"]', scope).tooltip();
    }
};
let Initialize = function (scope, name) {
    if (!(scope instanceof $)) {
        scope = $('body');
    }
    if (name && initObj.hasOwnProperty(name)) {
        initObj[name](scope);
        return;
    }
    for (let key in initObj) {
        if (!initObj.hasOwnProperty(key)) {
            continue;
        }
        initObj[key](scope);
    }
};

window.resRunInit = Initialize;
window.resmanager = appRes;