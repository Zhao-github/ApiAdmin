function JsonFormater(opt) {
    this.options = $.extend({
        dom: '',
        tabSize: 2,
        singleTab: "  ",
        quoteKeys: true,
        imgCollapsed: "images/Collapsed.gif",
        imgExpanded: "images/Expanded.gif",
        isCollapsible: true
    }, opt || {});
    this.isFormated = false;
    this.obj = {
        _dateObj: new Date(),
        _regexpObj: new RegExp()
    };
    this.init();
}
JsonFormater.prototype = {
    init: function () {
        this.tab = this.multiplyString(this.options.tabSize, this.options.singleTab);
        this.bindEvent();
    },
    doFormat: function (json) {
        var html;
        var obj;
        try {
            if(typeof json == 'object'){
                obj = [json];
            }else{
                if (json == ""){
                    json = "\"\"";
                }
                obj = eval("[" + json + "]");
            }
            html = this.ProcessObject(obj[0], 0, false, false, false);
            $(this.options.dom).addClass('jf-CodeContainer');
            $(this.options.dom).html(html);
            this.isFormated = true;
        } catch (e) {
            alert("JSON数据格式不正确:\n" + e.message);
            $(this.options.dom).html("");
            this.isFormated = false;
        }
    },
    bindEvent: function () {
        var that = this;
        $(this.options.dom).off('click','.imgToggle');
        $(this.options.dom).on('click', '.imgToggle', function () {
            if (that.isFormated == false) {
                return;
            }
            that.makeContentVisible($(this).parent().next(), !$(this).data('status'));
        });
    },
    expandAll: function () {
        if (this.isFormated == false) {
            return;
        }
        var that = this;
        this.traverseChildren($(this.options.dom), function(element){
            if(element.hasClass('jf-collapsible')){
                that.makeContentVisible(element, true);
            }
        }, 0);
    },
    collapseAll: function () {
        if (this.isFormated == false) {
            return;
        }
        var that = this;
        this.traverseChildren($(this.options.dom), function(element){
            if(element.hasClass('jf-collapsible')){
                that.makeContentVisible(element, false);
            }
        }, 0);
    },
    collapseLevel: function(level){
        if (this.isFormated == false) {
            return;
        }
        var that = this;
        this.traverseChildren($(this.options.dom), function(element, depth){
            if(element.hasClass('jf-collapsible')){
                if(depth >= level){
                    that.makeContentVisible(element, false);
                }else{
                    that.makeContentVisible(element, true);
                }
            }
        }, 0);

    },
    isArray: function (obj) {
        return  obj &&
            typeof obj === 'object' &&
            typeof obj.length === 'number' && !(obj.propertyIsEnumerable('length'));
    },
    getRow: function (indent, data, isPropertyContent) {
        var tabs = "";
        if (!isPropertyContent) {
            tabs = this.multiplyString(indent, this.tab);
        }
        if (data != null && data.length > 0 && data.charAt(data.length - 1) != "\n") {
            data = data + "\n";
        }
        return tabs + data;
    },
    formatLiteral: function (literal, quote, comma, indent, isArray, style) {
        if (typeof literal == 'string') {
            literal = literal.split("<").join("&lt;").split(">").join("&gt;");
        }
        var str = "<span class='jf-" + style + "'>" + quote + literal + quote + comma + "</span>";
        if (isArray) str = this.getRow(indent, str);
        return str;
    },
    formatFunction: function (indent, obj) {
        var tabs;
        var i;
        var funcStrArray = obj.toString().split("\n");
        var str = "";
        tabs = this.multiplyString(indent, this.tab);
        for (i = 0; i < funcStrArray.length; i++) {
            str += ((i == 0) ? "" : tabs) + funcStrArray[i] + "\n";
        }
        return str;
    },
    multiplyString: function (num, str) {
        var result = '';
        for (var i = 0; i < num; i++) {
            result += str;
        }
        return result;
    },
    traverseChildren: function (element, func, depth) {
        var length = element.children().length;
        for (var i = 0; i < length; i++) {
            this.traverseChildren(element.children().eq(i), func, depth + 1);
        }
        func(element, depth);
    },
    makeContentVisible : function(element, visible){
        var img = element.prev().find('img');
        if(visible){
            element.show();
            img.attr('src', this.options.imgExpanded);
            img.data('status', 1);
        }else{
            element.hide();
            img.attr('src', this.options.imgCollapsed);
            img.data('status', 0);
        }
    },
    ProcessObject: function (obj, indent, addComma, isArray, isPropertyContent) {
        var html = "";
        var comma = (addComma) ? "<span class='jf-Comma'>,</span> " : "";
        var type = typeof obj;
        var clpsHtml = "";
        var prop;
        if (this.isArray(obj)) {
            if (obj.length == 0) {
                html += this.getRow(indent, "<span class='jf-ArrayBrace'>[ ]</span>" + comma, isPropertyContent);
            } else {
                clpsHtml = this.options.isCollapsible ? "<span><img class='imgToggle' data-status='1' src='" + this.options.imgExpanded + "'/></span><span class='jf-collapsible'>" : "";
                html += this.getRow(indent, "<span class='jf-ArrayBrace'>[</span>" + clpsHtml, isPropertyContent);
                for (var i = 0; i < obj.length; i++) {
                    html += this.ProcessObject(obj[i], indent + 1, i < (obj.length - 1), true, false);
                }
                clpsHtml = this.options.isCollapsible ? "</span>" : "";
                html += this.getRow(indent, clpsHtml + "<span class='jf-ArrayBrace'>]</span>" + comma);
            }
        } else if (type == 'object') {
            if (obj == null) {
                html += this.formatLiteral("null", "", comma, indent, isArray, "Null");
            } else {
                var numProps = 0;
                for (prop in obj) numProps++;
                if (numProps == 0) {
                    html += this.getRow(indent, "<span class='jf-ObjectBrace'>{ }</span>" + comma, isPropertyContent);
                } else {
                    clpsHtml = this.options.isCollapsible ? "<span><img class='imgToggle' data-status='1' src='" + this.options.imgExpanded + "'/></span><span class='jf-collapsible'>" : "";
                    html += this.getRow(indent, "<span class='jf-ObjectBrace'>{</span>" + clpsHtml, isPropertyContent);
                    var j = 0;
                    for (prop in obj) {
                        var quote = this.options.quoteKeys ? "\"" : "";
                        html += this.getRow(indent + 1, "<span class='jf-PropertyName'>" + quote + prop + quote + "</span>: " + this.ProcessObject(obj[prop], indent + 1, ++j < numProps, false, true));
                    }
                    clpsHtml = this.options.isCollapsible ? "</span>" : "";
                    html += this.getRow(indent, clpsHtml + "<span class='jf-ObjectBrace'>}</span>" + comma);
                }
            }
        } else if (type == 'number') {
            html += this.formatLiteral(obj, "", comma, indent, isArray, "Number");
        } else if (type == 'boolean') {
            html += this.formatLiteral(obj, "", comma, indent, isArray, "Boolean");
        }else if (type == 'undefined') {
            html += this.formatLiteral("undefined", "", comma, indent, isArray, "Null");
        } else {
            html += this.formatLiteral(obj.toString().split("\\").join("\\\\").split('"').join('\\"'), "\"", comma, indent, isArray, "String");
        }
        return html;
    }
};
