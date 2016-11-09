/**
 * 模版引擎
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */

(function ($) {
    "use strict";
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
        var h = date.getHours() + ':';
        var m = date.getMinutes() + ':';
        var s = date.getSeconds();
        return Y+M+D+h+m+s;
    };

    /**
     * 消息弹框
     * @param msg
     */
    $.alertMsg = function( msg ){
        var dialog = bootbox.dialog({
            message: '<p class="text-center">'+msg+'</p>',
            closeButton: false
        });
        setTimeout(function(){
            dialog.modal('hide');
        }, 3000);
    };

    /**
     * Ajax Post 表单提交
     */
    $('body').on('click', '.ajax-post', function() {
        var message,query,form,target;
        var target_form = $(this).attr('target-form');

        if ( $(this).attr('type') == 'submit' ) {
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
                    $.alertMsg(message);
                    setTimeout(function() {
                        if (data.url) {
                            location.href = data.url;
                        } else {
                            location.reload();
                        }
                    }, wait);
                } else {
                    $.alertMsg(data.msg);
                    setTimeout(function() {
                        if (data.url) {
                            location.href = data.url;
                        }
                    }, wait);
                }
            });
        }
        return false;
    });

})(jQuery);

var refresh = function(url) {
    $.ajax({
        type: "GET",
        url: url,
        success: function(data){
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
                if( data.data.tempType == 'form' ){
                    if( $.buildForm ){
                        $('#content').html($.buildForm(data.data));
                        $('#tableBox').hide().fadeIn(800);
                    }else{
                        $.getScript(JS_PATH + '/template/form.js', function (){
                            $('#content').html($.buildForm(data.data));
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
