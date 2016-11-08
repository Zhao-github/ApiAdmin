/**
 * Created by 7d-vision on 2016/11/7.
 */
(function ($) {
    $.buildTable = function ( tableObj ) {
        var tableHtml = '<div class="box" id="tableBox"><div class="box-body">';
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
                if( value.confirm ){
                    value.class += ' confirm';
                }
                if( value.icon ){
                    topHtml += '<button href="'+value.href+'" type="button" class="btn '+value.class+'"><i class="'+value.icon+'"></i> '+value.info+'</button>';
                }else{
                    topHtml += '<button href="'+value.href+'" type="button" class="btn '+value.class+'">'+value.info+'</button>';
                }
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
        var dataListHtml = '<tr><td><input type="checkbox"></td>';
        $.each(tableObj.data, function (index, value) {
            dataListHtml += '<td></td>';
        });
        dataListHtml += '</tr>';
        return dataListHtml;
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
        emptyHtml += '<small> 本系统由<b> 七维视觉科技有限公司 </b>开发维护</small>';
        emptyHtml += '</div></td></tr>';
        return emptyHtml;
    }
})(jQuery);
