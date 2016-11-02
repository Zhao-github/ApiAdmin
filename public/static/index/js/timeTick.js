  var TimeTick = function(_options){
    var options = {
      btn: '#J-btn-getPhoneMsg',
      time: 60,//倒计时时间
      onClicked: function(){
        console.log('onClicked');
      }
    };
    options = $.extend(options, _options);
    var time_sec = options.time;
    var timerID = null;
    //页面加载后开始执行
    var stop = function (){
       clearTimeout(timerID);
    }
    var setTime = function (_callback){
        // console.log(time_sec);
       if (time_sec <= 0) {
         stop();
         (_callback && typeof(_callback) === "function") && _callback();
         time_sec = options.time;
         return;
       };
       time_sec -=1;
       $('.second').text(time_sec);
       timerID=setTimeout(function(){
          setTime(_callback);
       }, 1000);//递归调用，每1s执行一次
    }

    var init = function (){
      return this;
    }

    var $modal = $('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">'
      + '<div class="modal-dialog modal-sm">'
        + '<div class="modal-content p20 tc">'
            + '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>'
        + '</div>'
      + '</div>'
    + '</div>');
    $modal.appendTo('body');

    $(options.btn).on('click', function(){
        var stateTimerId = null
        var $this = $(this);
        // var $btn = $(this).button('loading');
        var state = options.onClicked();
        $this.attr('disabled', 'disabled').html('正在发送</span>')
        if(state === true){
            $modal.find('.modal-content').html('短信验证码已发送到您的手机');
            $modal.modal('show');
            setTimeout(function(){
              $modal.modal('hide');
              clearTimeout(stateTimerId);
            },1200);
            $this.attr('disabled', 'disabled').addClass('disabled').html('<span class="second-txt"><i class="second">60</i> 秒后重新发送</span>').show(function(){
                setTime(function(){
                  $this.html('重新发送验证码').removeAttr('disabled').removeClass('disabled');
                  // $this.removeAttr('disabled');
                  // $btn.button('reset');
                });
            });
          }else{
            $modal.find('.modal-content').html('验证码发送失败请重试');
            $modal.modal('show');
            setTimeout(function(){
              clearTimeout(stateTimerId);
              $modal.modal('hide');
              $this.html('重新发送验证码').removeAttr('disabled').removeClass('disabled');
            },1200);
          }
    });
    return this;
  };
