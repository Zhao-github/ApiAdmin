var AlertMSG = function(){
  this.$modal = $('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">'
    + '<div class="modal-dialog modal-sm">'
      + '<div class="modal-content p20 tc">'
          + '<button type="button" class="close" style="position:relative;z-index:2;" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>'
          + '<div class="modal-body"></div>'
      + '</div>'
    + '</div>'
  + '</div>');
  this.$modal.appendTo('body');
  this.$modal.find('.close').on('click',function(){
    this.$modal.modal('hide');
  });
  return this;
};

AlertMSG.prototype={
  showAlert: function(_options){
    var options={
      msg:'',
      callback: null
    };
    options = $.extend(options, _options);

    this.$modal.find('.modal-body').html(options.msg);
    this.$modal.modal('show');
    (options.callback && typeof(options.callback) === "function") && options.callback();
  },
  hideAlert: function(){
    this.$modal.modal('hide');
    this.$modal.find('.modal-body').html('');
  }
}


