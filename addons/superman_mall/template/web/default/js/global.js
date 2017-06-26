// Extend the default Number object with a formatCurrency() method:
// usage: someVar.formatCurrency(decimalPlaces, symbol, thousandsSeparator, decimalSeparator)
// defaults: (2, '$', ',', '.')
Number.prototype.formatCurrency = function (places, symbol, thousand, decimal) {
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : '';
    //symbol = symbol !== undefined ? symbol : '&#165;';
    thousand = thousand || ',';
    decimal = decimal || '.';
    var number = this,
        negative = number < 0 ? '-' : '',
        i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + '',
        j = (j = i.length) > 3 ? j % 3 : 0;
    return symbol + negative + (j ? i.substr(0, j) + thousand : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : '');
};
Number.prototype.isCurrency = function(){
    var reg = /^[0-9]*(\.[0-9]{1,2})?$/;
    return reg.test(this)?true:false;
};
Number.prototype.formatCurrency2 = function (places, symbol, thousand, decimal) {
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : '';
    //symbol = symbol !== undefined ? symbol : '&#165;';
    thousand = thousand || ',';
    decimal = decimal || '.';
    var number = this,
        negative = number < 0 ? '-' : '',
        i = parseInt(number = Math.abs(+number || 0), 10) + '',
        j = (j = i.length) > 3 ? j % 3 : 0;
    return symbol + negative + (j ? i.substr(0, j) + thousand : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand) + (places ? decimal + Math.abs(number - i).slice(2) : '');
};
//rgb(0,0,0)
String.prototype.rgb2Hex = function(prefix){
    var str = this.toLowerCase();
    var arr = str.replace('rgb(', '').replace(')', '').split(',');
    var r = arr[0], g = arr[1], b = arr[2];
    return (typeof(prefix)=='undefined'?'#':prefix)+((r << 16) | (g << 8) | b).toString(16);
};
require(['jquery'], function(jQuery){
    jQuery.fn.rowspan = function(colIdx) {
        return this.each(function(){
            var that, rowspan;
            $('tr', this).each(function(row) {
                $('td:eq('+colIdx+')', this).filter(':visible').each(function(col) {
                    if (that != null && $(this).html() == $(that).html()) {
                        rowspan = $(that).attr("rowSpan");
                        if (rowspan == undefined) {
                            $(that).attr("rowSpan",1);
                            rowspan = $(that).attr("rowSpan");
                        }
                        rowspan = Number(rowspan)+1;
                        $(that).attr("rowSpan",rowspan);
                        $(this).hide();
                    } else {
                        that = this;
                    }
                });
            });
        });
    };
    jQuery.fn.array2row = function() {
        var arr = this, len = arr.length;
        if (len>=2){
            var len1 = arr[0].length;
            var len2 = arr[1].length;
            var newlen = len1 * len2;
            var temp = new Array(newlen);
            var index = 0;
            for (var i=0; i<len1; i++) {
                for (var j=0; j<len2; j++) {
                    temp[index] = arr[0][i] + ',' + arr[1][j];
                    index++;
                }
            }
            var newarray = new Array(len-1);
            for (var i=2; i<len; i++) {
                newarray[i-1] = arr[i];
            }
            newarray[0] = temp;
            return $(newarray).array2row();
        } else {
            return arr[0];
        }
    };
});