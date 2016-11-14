/**
 * 模版引擎
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */

(function ($) {
    "use strict";
    var bodyDom = $('body');
    /**
     * 格式化时间戳（为了和PHP的date函数统一，这里的时间戳都是10位，不包含毫秒）
     * @param timestamp
     * @returns {string}
     */
    $.formatDate = function ( timestamp ) {
        timestamp *= 1000;
        var date = new Date(timestamp);
        var Y = date.getFullYear() + '-';
        var M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        var D = (date.getDate()+1 < 10 ? '0'+(date.getDate()+1) : date.getDate()+1) + ' ';
        var h = (date.getHours() < 10 ? '0'+date.getHours() : date.getHours()) + ':';
        var m = (date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes()) + ':';
        var s = date.getSeconds() < 10 ? '0'+date.getSeconds() : date.getSeconds();
        return Y+M+D+h+m+s;
    };

    /**
     * 消息弹框
     * @param msg
     * @param wait 等待时间（毫秒）
     */
    $.alertMsg = function( msg, wait ){
        wait = wait ? wait : 2800;
        var dialog = bootbox.dialog({
            message: '<p class="text-center">'+msg+'</p>',
            closeButton: false
        });
        setTimeout(function(){
            dialog.modal('hide');
        }, wait);
    };

    /**
     * 刷新数据，允许带参数刷新
     * @param url
     * @param urlData
     */
    $.refresh = function ( url, urlData ) {
        urlData = urlData ? urlData : '';
        var loadingBox = bootbox.dialog({
            message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
            closeButton: false
        });
        $.ajax({
            type: "GET",
            url: url,
            data: urlData,
            success: function(data){
                loadingBox.modal('hide');
                if( data.code == 200 ){
                    if( data.data.tempType == 'table' ){
                        if( $.buildTable ){
                            $('#content').html($.buildTable(data.data));
                            $('#tableBox').hide().fadeIn(800);
                        }else{
                            $.getScript(JS_PATH + '/template/table.js', function (){
                                $('#content').html($.buildTable(data.data));
                                $('#tableBox').hide().fadeIn(800);
                            });
                        }
                    }
                    if( data.data.tempType == 'add' ){
                        if( $.buildAddForm ){
                            $('#content').html($.buildAddForm(data.data));
                            $('#formBox').hide().fadeIn(800);
                        }else{
                            $.getScript(JS_PATH + '/template/form.js', function (){
                                $('#content').html($.buildAddForm(data.data));
                                $('#formBox').hide().fadeIn(800);
                            });
                        }
                    }
                    if( data.data.tempType == 'edit' ){
                        if( $.buildEditForm ){
                            $('#content').html($.buildEditForm(data.data));
                            $('#formBox').hide().fadeIn(800);
                        }else{
                            $.getScript(JS_PATH + '/template/form.js', function (){
                                $('#content').html($.buildEditForm(data.data));
                                $('#formBox').hide().fadeIn(800);
                            });
                        }
                    }
                }else{
                    $.alertMsg(data.msg);
                    setTimeout(function() {
                        if (data.url) {
                            location.href = data.url;
                        }
                    }, 1000*data.wait);
                }
            }
        });
    };

    /**
     * Ajax Post 表单提交(增) *
     */
    bodyDom.on('click', '.ajax-post', function() {
        var message,query,form,target;
        var target_form = $(this).attr('target-form');
        var isRedirect = $(this).hasClass('redirect');
        form = $('#' + target_form);
        query = form.serialize();
        target = form.attr('action');
        $.post(target, query).success(function(data) {
            var wait = 1000*data.wait;
            if (data.code == 1) {
                if (data.url) {
                    message = data.msg + ' 页面即将自动跳转...';
                } else {
                    message = data.msg;
                }
                $.alertMsg(message, wait);
                if( isRedirect ){
                    setTimeout(function() {
                        if (data.url) {
                            location.href = data.url;
                        } else {
                            location.reload();
                        }
                    }, wait);
                }else{
                    setTimeout(function() {
                        if (data.url) {
                            $.refresh(data.url);
                        }
                    }, wait);
                }
            } else {
                $.alertMsg(data.msg, wait);
            }
        });
        return false;
    });

    /**
     * Ajax Put 表单提交(改) *
     */
    bodyDom.on('click', '.ajax-put', function() {
        var message,query,form,target;
        var target_form = $(this).attr('target-form');
        form = $('#' + target_form);
        query = form.serialize();
        target = form.attr('action');
        $.ajax({
            type: "PUT",
            url: target,
            data: query
        }).done(function( data ) {
            var wait = 1000*data.wait;
            if (data.code == 1) {
                if (data.url) {
                    message = data.msg + ' 页面即将自动跳转...';
                } else {
                    message = data.msg;
                }
                $.alertMsg(message, wait);
                setTimeout(function() {
                    if (data.url) {
                        $.refresh(data.url);
                    }
                }, wait);
            } else {
                $.alertMsg(data.msg, wait);
            }
        });
        return false;
    });

    /**
     * Ajax Delete 请求(删) *
     */
    bodyDom.on('click', '.ajax-delete', function() {
        var url = $(this).attr('url'), urlData = '';
        if( $(this).attr('data') ){
            urlData = $(this).attr('data');
        }
        if( $(this).hasClass('confirm') ){
            bootbox.confirm({
                title: "温馨提醒：",
                message: "您确定要这么做么？",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> 取消'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> 确定'
                    }
                },
                callback: function (result) {
                    if( result ){
                        $.ajax({
                            type: "DELETE",
                            url: url,
                            data: urlData
                        }).done(function( data ) {
                            var wait = 1000*data.wait;
                            if (data.code == 1) {
                                $.alertMsg(data.msg, wait);
                                setTimeout(function() {
                                    if (data.url) {
                                        $.refresh(data.url);
                                    }
                                }, wait);
                            } else {
                                $.alertMsg(data.msg, wait);
                            }
                        });
                    }
                }
            });
        }
        return false;
    });

    /**
     * Ajax put by url 请求(改) *
     */
    bodyDom.on('click', '.ajax-put-url', function() {
        var url = $(this).attr('url'), urlData = '';
        if( $(this).attr('data') ){
            urlData = $(this).attr('data');
        }
        if( $(this).hasClass('confirm') ){
            bootbox.confirm({
                title: "温馨提醒：",
                message: "您确定要这么做么？",
                buttons: {
                    cancel: {
                        label: '<i class="fa fa-times"></i> 取消'
                    },
                    confirm: {
                        label: '<i class="fa fa-check"></i> 确定'
                    }
                },
                callback: function (result) {
                    if( result ){
                        $.ajax({
                            type: "PUT",
                            url: url,
                            data: urlData
                        }).done(function( data ) {
                            var wait = 1000*data.wait;
                            if (data.code == 1) {
                                $.alertMsg(data.msg, wait);
                                setTimeout(function() {
                                    if (data.url) {
                                        $.refresh(data.url);
                                    }
                                }, wait);
                            } else {
                                $.alertMsg(data.msg, wait);
                            }
                        });
                    }
                }
            });
        }
        return false;
    });

    /**
     * Ajax 刷新页面 *
     */
    bodyDom.on('click', '.refresh', function() {
        var url = $(this).attr('url'), urlData = '';
        if( $(this).attr('data') ){
            urlData = $(this).attr('data');
        }
        $.refresh(url, urlData);
    });

    /**
     * 转为权限修改定制的Ajax请求
     */
    bodyDom.on('click', '.auth', function () {
        var tdDom = $(this).parent().parent().children();
        var urlName = tdDom.eq(2).html();
        var url = $(this).attr('url');
        var message;
        $.ajax({
            type: "PUT",
            url: url,
            data: {urlName:urlName, get:Number(tdDom.find('[name=get]').is(':checked')), post:Number(tdDom.find('[name=post]').is(':checked')), put:Number(tdDom.find('[name=put]').is(':checked')), delete:Number(tdDom.find('[name=delete]').is(':checked'))}
        }).done(function( data ) {
            var wait = 1000*data.wait;
            if (data.code == 1) {
                if (data.url) {
                    message = data.msg + ' 页面即将自动跳转...';
                } else {
                    message = data.msg;
                }
                $.alertMsg(message, wait);
                setTimeout(function() {
                    if (data.url) {
                        $.refresh(data.url);
                    }
                }, wait);
            } else {
                $.alertMsg(data.msg, wait);
            }
        });
    })

})(jQuery);
