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