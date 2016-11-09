/**
 * Created by 7d-vision on 2016/11/7.
 */
(function ($) {
    /**
     * 创建新增表单
     * @param formObj
     * @returns {string}
     */
    $.buildAddForm = function ( formObj ) {
        return buildForm(formObj, 'box-success', 'ajax-post');
    };

    /**
     * 创建编辑表单
     * @param formObj
     * @returns {string}
     */
    $.buildEditForm = function ( formObj ) {
        return buildForm(formObj, 'box-warning', 'ajax-put');
    };

    /**
     * 根据规则创建表单
     * @param formObj 表单数据对象
     * @param boxType box样式
     * @param method 数据提交方式
     * @returns {string}
     */
    function buildForm( formObj, boxType, method ) {
        var formHtml = '<div class="box '+ boxType +'" id="formBox"><div class="box-body">';
        formHtml += '<div class="box-header with-border"><h3 class="box-title">新增菜单</h3></div>';
        formHtml += '<form id="'+ formObj.formAttr.formId +'" action="'+ formObj.formAttr.target +'"><div class="box-body">';
        $.each(formObj.formList, function (index, value) {
            switch (value.module){
                case 'text':
                    formHtml += buildInput(value);
                    break;
                case 'select':
                    formHtml += buildSelect(value);
                    break;
                case 'radio':
                    formHtml += buildRadio(value);
                    break;
                case 'hidden':
                    formHtml += buildHidden(value);
                    break;
            }
        });
        formHtml += '</div><div class="box-footer">';
        formHtml += '<button type="submit" target-form="'+ formObj.formAttr.formId +'" class="btn btn-primary '+ method +'">确认提交</button>';
        formHtml += ' <a class="btn btn-default refresh" url="'+ formObj.formAttr.backUrl +'" >放弃返回</a></div></form></div></div>';
        return formHtml;
    }

    /**
     * 创建文本框
     * @param inputObj
     * @returns {string}
     */
    function buildInput( inputObj ) {
        var formHtml = '<div><div class="col-xs-8 form-group"><label>'+ inputObj.info +'</label>';
        var placeholder = '', value = '';
        if( inputObj.attr.placeholder){
            placeholder = 'placeholder="'+ inputObj.attr.placeholder +'"';
        }
        if( inputObj.attr.value){
            value = 'value="'+ inputObj.attr.value +'"';
        }
        formHtml += '<input type="text" class="form-control" '+ placeholder +' '+ value +' name="'+ inputObj.attr.name +'"></div>';
        if( inputObj.description && inputObj.description.length ){
            formHtml += ' <div class="col-xs-4 form-group" style="margin-top: 30px"><span class="label label-info">'+ inputObj.description +'</span></div>';
        }
        formHtml += '</div>';
        return formHtml;
    }

    /**
     * 创建单选框
     * @param radioObj
     * @returns {string}
     */
    function buildRadio( radioObj ) {
        var formHtml = '<div>';
        formHtml += '<div class="col-xs-8 form-group"><label>'+ radioObj.info +'</label>';
        formHtml += '<div class="input-group radio">';
        if( radioObj.attr.options ){
            $.each(radioObj.attr.options, function (index, value) {
                if( index == radioObj.attr.value ){
                    formHtml += '<label><input type="radio" checked name="'+ radioObj.attr.name +'" value="'+ index +'"> '+ value +'</label>　';
                }else{
                    formHtml += '<label><input type="radio" name="'+ radioObj.attr.name +'" value="'+ index +'"> '+ value +'</label>　';
                }
            });
        }
        formHtml += '</div>';
        if( radioObj.description ){
            formHtml += ' <div class="col-xs-4 form-group" style="margin-top: 30px"><span class="label label-info">'+ radioObj.description +'</span></div>';
        }
        formHtml += '</div></div>';
        return formHtml;
    }

    /**
     * 创建隐藏表单
     * @param hiddenObj
     * @returns {string}
     */
    function buildHidden( hiddenObj ) {
        return '<input type="hidden" class="form-control" value="'+ hiddenObj.attr.value +'" name="'+ hiddenObj.attr.name +'">';
    }
    
    function buildTextarea( textareaObj ) {
        
    }

    /**
     * 创建下拉菜单
     * @param selectObj
     * @returns {string}
     */
    function buildSelect( selectObj ) {
        var formHtml = '<div>';
        formHtml += '<div class="col-xs-8 form-group"><label>'+ selectObj.info +'</label>';
        if( selectObj.attr.options ){
            formHtml += '<select class="form-control" name="'+ selectObj.attr.name +'">';
            formHtml += '<option>请选择</option>';
            $.each(selectObj.attr.options, function (index, value) {
                if( index == selectObj.attr.value ){
                    formHtml += '<option value="'+ index +'" selected>'+ value +'</option>';
                }else{
                    formHtml += '<option value="'+ index +'">'+ value +'</option>';
                }
            });
        }else{
            formHtml += '<select class="form-control" name="'+ selectObj.attr.name +'" disabled>';
        }
        formHtml += '</select></div>';
        if( selectObj.description && selectObj.description.length ){
            formHtml += ' <div class="col-xs-4 form-group" style="margin-top: 30px"><span class="label label-info">'+ selectObj.description +'</span></div>';
        }
        formHtml += '</div>';
        return formHtml;
    }
    
    function buildEditors() {
        
    }

    function buildUpload() {

    }

    function buildCode() {

    }
})(jQuery);