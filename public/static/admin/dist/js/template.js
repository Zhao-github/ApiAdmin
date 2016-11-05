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

    $.alertMsg = function( msg ){
        var dialog = bootbox.dialog({
            message: '<p class="text-center">'+msg+'</p>',
            closeButton: false
        });
        setTimeout(function(){
            dialog.modal('hide');
        }, 2000);
    };

    $.buildDom = function ( jsonStr ) {

    };

    var emptyList = function() {

    };

    var easyList = function( listObj ) {

    };

    var topButton = function( topObj ) {

    };

    var rightButton = function( rightObj ) {

    };

    var easyForm = function( formObj ) {

    };

    var input = function( inputObj ) {

    };

    var select = function( selectObj ) {

    };

    var button = function( buttonObj ) {

    };

    /**
     * 面包屑
     */
    var breadcrumb = function(  ) {

    };

    //ajax post submit请求
    $('body').on('click', '.ajax-post', function() {
        var message,query,form,target;
        var target_form = $(this).attr('target-form');

        if ( $(this).attr('type') == 'submit' ) {
            form = $('#' + target_form);
            query = form.serialize();
            target = form.attr('action');
            $.post(target, query).success(function(data) {
                if (data.status == 1) {
                    if (data.url) {
                        message = data.msg + ' 页面即将自动跳转~';
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
                    }, 2000);
                } else {
                    $.alertMsg(data.msg);
                }
            });
        }
        return false;
    });
})(jQuery);
