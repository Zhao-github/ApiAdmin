(function ($) {
    /**
     * 创建表格HTML字符串
     * 1、严重依赖Bootstrap的样式
     * 2、严格的Json数据格式
     * 3、如果有右侧操作按钮的话会默认自动加入{field:"action",info:"操作"}所以字段名不支持使用action，action是关键字
     * @param tableObj
     * @returns {string}
     */
    $.buildTable = function ( tableObj ) {
        var tableHtml = '<div class="box box-primary" id="tableBox"><div class="box-body">';
        if( tableObj.rightButton && tableObj.rightButton.length ){
            tableObj.header.push({field:"action",info:"操作"});
        }
        if( tableObj.topButton && tableObj.topButton.length ){
            tableHtml += buildTopButton( tableObj );
        }
        tableHtml += '<table class="table table-bordered"> <tbody>';
        if( tableObj.header && tableObj.header.length ){
            tableHtml += buildHeader( tableObj );
        }
        if( tableObj.data && tableObj.data.length ){
            tableHtml += buildDataList( tableObj );
        }else{
            tableHtml += buildEmptyTable( tableObj );
        }
        tableHtml += '</tbody></table></div></div>';
        return tableHtml;
    };

    /**
     * 创建表格头部分
     * @param tableObj
     * @returns {string}
     */
    function buildHeader( tableObj ) {
        var headerHtml = '<tr><th style="width: 10px"><input type="checkbox"></th>';
        $.each(tableObj.header, function (index, value) {
            headerHtml += '<th>'+ value.info +'</th>';
        });
        headerHtml += '</tr>';
        return headerHtml;
    }

    /**
     * 创建顶部功能按钮
     * @param tableObj
     * @returns {string}
     */
    function buildTopButton( tableObj ) {
        var topHtml = '<div class="btn-group margin-bottom">';
        if( tableObj.topButton ){
            $.each(tableObj.topButton, function(index, value) {
                topHtml += createButton(value);
            });
        }
        topHtml += '</div>';
        return topHtml;
    }

    /**
     * 创建数据部分
     * @param tableObj
     * @returns {string}
     */
    function buildDataList( tableObj ) {
        var paramStr, dataListHtml = '';
        $.each(tableObj.data, function (dataIndex, dataValue) {
            dataListHtml += '<tr><td><input type="checkbox"></td>';
            $.each(tableObj.header, function (fieldIndex, fieldValue) {
                var fieldName = fieldValue.field;
                if( fieldName == 'action' ){
                    dataListHtml += '<td><div class="btn-group">';
                    $.each(tableObj.rightButton, function(buttonIndex, buttonValue) {
                        dataListHtml += createButton(buttonValue, dataValue);
                    });
                    dataListHtml += '</div></td>';
                }else{
                    if( tableObj.typeRule[fieldName] ){
                        var rule = tableObj.typeRule[fieldName];
                        var styleList ,detailInfo, classAttr = '';
                        switch (rule.module){
                            case 'label':
                                if( rule.rule[dataValue[fieldName]] ){
                                    styleList = rule.rule[dataValue[fieldName]];
                                    detailInfo = prepareInfo( styleList, dataValue, fieldName);
                                    dataListHtml += '<td><span class="'+styleList['class']+'">'+detailInfo+'</span></td>';
                                }else{
                                    dataListHtml += '<td style="color:red;">' + dataValue[fieldName] + '</td>';
                                }
                                break;
                            case 'a':
                                styleList = rule.rule;
                                detailInfo = prepareInfo( styleList, dataValue, fieldName);
                                paramStr = prepareParamStr( styleList, dataValue );
                                if( styleList['class'] ){
                                    classAttr = 'class="'+ styleList['class'] +'"';
                                }
                                dataListHtml += '<td><a '+ classAttr +' url="'+styleList['href']+'" data="'+paramStr+'">' + detailInfo + '</a></td>';
                                break;
                            case 'date':
                                dataListHtml += '<td>' + $.formatDate(dataValue[fieldName]) + '</td>';
                                break;
                            case 'icon':
                                if( rule.rule[dataValue[fieldName]] ){
                                    styleList = rule.rule[dataValue[fieldName]];
                                    dataListHtml += '<td><i class="'+styleList['class']+'"></i></td>';
                                }else{
                                    dataListHtml += '<td style="color:red;">' + dataValue[fieldName] + '</td>';
                                }
                                break;
                            case 'auth':
                                if( dataValue['url'] ){
                                    if( dataValue[fieldName] && dataValue[fieldName] != 0 ){
                                        dataListHtml += '<td><input class="auth" checked type="checkbox" url="'+ rule.rule['url'] +'" name="'+fieldName+'" ></td>';
                                    }else{
                                        dataListHtml += '<td><input class="auth" type="checkbox" url="'+ rule.rule['url'] +'" name="'+fieldName+'" ></td>';
                                    }
                                }
                                break;
                            case 'listValue':
                                if( rule.rule[dataValue[fieldName]] ){
                                    styleList = rule.rule[dataValue[fieldName]];
                                    dataListHtml += '<td>'+ styleList +'</td>';
                                }else{
                                    dataListHtml += '<td style="color:red;">' + dataValue[fieldName] + '</td>';
                                }
                                break;
                        }
                    }else{
                        dataListHtml += '<td>' + dataValue[fieldName] + '</td>';
                    }
                }
            });
            dataListHtml += '</tr>';
        });
        return dataListHtml;
    }

    /**
     * 创建按钮
     * @param buttonValue 按钮属性对象
     * @param dataValue 当前行数据对象
     * @returns {string}
     */
    function createButton( buttonValue, dataValue ) {
        var paramStr = '', buttonStr = '', iconStr = '';
        if( buttonValue.confirm ){
            buttonValue.class += ' confirm';
        }else{
            buttonValue.class += ' refresh';
        }
        if( dataValue ){
            paramStr = prepareParamStr( buttonValue, dataValue );
        }
        if( buttonValue.icon ){
            iconStr = '<i class="'+buttonValue.icon+'"></i>';
        }
        if( buttonValue.show ){
            if( dataValue[buttonValue.show[0]] == buttonValue.show[1] ){
                buttonStr = '<button url="'+buttonValue.href+'" data="'+paramStr+'" type="button" class="btn '+buttonValue.class+'">'+ iconStr + ' ' +buttonValue.info+'</button>';
            }
        }else{
            buttonStr = '<button url="'+buttonValue.href+'" data="'+paramStr+'" type="button" class="btn '+buttonValue.class+'">'+ iconStr + ' ' +buttonValue.info+'</button>';
        }
        return buttonStr;
    }

    /**
     * 预处理显示信息
     * @param styleList 当前字段对应的规则对象
     * @param dataValue 当前行数据对象
     * @param fieldName 需要处理的数据的字段名
     * @returns {*}
     */
    function prepareInfo( styleList, dataValue, fieldName ) {
        var detailInfo;
        if( styleList['info'] && styleList['info'].length ){
            detailInfo = styleList['info'];
        }else{
            detailInfo = dataValue[fieldName];
        }
        return detailInfo;
    }

    /**
     * 预处理参数信息
     * @param styleList 当前字段对应的规则对象
     * @param dataValue 当前行数据对象
     * @returns {string}
     */
    function prepareParamStr( styleList, dataValue ) {
        var paramStr = '';
        if( styleList['param'].length ){
            $.each(styleList['param'], function (paramIndex, paramValue) {
                paramStr += paramValue + '=' + dataValue[paramValue] + '&';
            });
            paramStr = paramStr.substring(0, paramStr.length-1);
        }
        return paramStr;
    }

    /**
     * 创建空数据表
     * @param tableObj
     * @returns {string}
     */
    function buildEmptyTable( tableObj ) {
        var emptyHtml = '<tr>';
        var spanNum = tableObj.header.length + 1;
        emptyHtml += '<td colspan="'+spanNum+'" class="builder-data-empty">';
        emptyHtml += '<div class="am-text-center no-data" >';
        emptyHtml += '<i class="fa fa-cogs"></i> 暂时没有数据<br>';
        emptyHtml += '<small> 本系统由<b> '+ COMPANY_NAME +' </b>开发维护</small>';
        emptyHtml += '</div></td></tr>';
        return emptyHtml;
    }
})(jQuery);
