/**
 * Created by sevens on 2016/10/18.
 */
(function($) {
    'use strict';
    $(function() {
        var alertMSG = new AlertMSG();
        //ajax post submit请求
        $('body').on('click', '.ajax-post', function() {
            var target, query, form;
            var target_form = $(this).attr('target-form');

            if (($(this).attr('type') == 'submit')) {
                form = $('#' + target_form);
                query = form.serialize();
                target = $(this).attr('href');
                $.post(target, query).success(function(data) {
                    if (data.url) {
                        alertMSG.showAlert({
                            msg:'<p><i class="icon-ok-sign mr10" style="color:#3c3"></i>'+data.msg+' 页面即将自动跳转~</p>',
                            callback: function(){
                                setTimeout(function(){
                                    window.location.href= data.url;
                                }, 2000);
                            }
                        });
                    } else {
                        alertMSG.showAlert({
                            msg:'<p>'+data.msg+'</p>',
                        });
                    }
                });
            }
            return false;
        });
    });
})(jQuery);

