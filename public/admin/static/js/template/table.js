/**
 * Created by 7d-vision on 2016/11/7.
 */
(function ($) {
    $.buildTable = function ( tableObj ) {
        var tableHtml = '<div class="box"><div class="box-body">';
        if( tableObj.topButton ){
            tableHtml += buildTopButton( tableObj );
        }
        if( tableObj.header ){
            tableHtml += buildHeader( tableObj );
        }
        if( tableObj.data ){
            tableHtml += buildDataList( tableObj );
        }else{
            tableHtml += buildEmptyTable();
        }
        tableHtml += '</div></div>';
        return tableHtml;
    };
    
    function buildHeader( tableObj ) {
        
    }
    
    function buildTopButton( tableObj ) {
        
    }

    function buildDataList( tableObj ) {

    }
    
    function buildEmptyTable() {
        
    }
})(jQuery);
