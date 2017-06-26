﻿/**
 
 @Name : laytpl v1.1 - 精妙的JavaScript模板引擎
 @Author: 贤心
 @Date: 2014-08-16
 @Site：http://sentsin.com/layui/laytpl
 @License：MIT license
 
 */

;!function(win){
"use strict";

var config = {
    open: '{{',
    close: '}}'
};

var tool = {
    exp: function(str){
        return new RegExp(str, 'g');
    },
    //匹配满足规则内容
    query: function(type, _, __){
        var types = [
            '#([\\s\\S])+?',   //js语句
            '([^{#}])*?' //普通字段
        ][type || 0];
        return exp((_||'') + config.open + types + config.close + (__||''));
    },   
    escape: function(html){
        return String(html||'').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
        .replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    },
    error: function(e, tplog){
        var error = 'Laytpl Error：';
        typeof console === 'object' && console.error(error + e + '\n'+ (tplog || ''));
        return error + e;
    }
};

var exp = tool.exp, Tpl = function(tpl){
    this.tpl = tpl;
};

Tpl.pt = Tpl.prototype;

//核心引擎
Tpl.pt.parse = function(tpl, data){
    var that = this, tplog = tpl;
    var jss = exp('^'+config.open+'#', ''), jsse = exp(config.close+'$', '');
    
    tpl = tpl.replace(/[\r\t\n]/g, ' ').replace(exp(config.open+'#'), config.open+'# ')
    .replace(exp(config.close+'}'), '} '+config.close).replace(/\\/g, '\\\\')
    .replace(/(?="|')/g, '\\').replace(tool.query(), function(str){
        str = str.replace(jss, '').replace(jsse, '');
        return '";' + str.replace(/\\/g, '') + '; view+="';
    }).replace(tool.query(1), function(str){
        var start = '"+(';
        if(str.replace(/\s/g, '') === config.open+config.close){
            return '';
        }
        str = str.replace(exp(config.open+'|'+config.close), '');
        if(/^=/.test(str)){
            str = str.replace(/^=/, '');
            start = '"+_escape_(';
        }
        return start + str.replace(/\\/g, '') + ')+"';
    });
    
    tpl = '"use strict";var view = "' + tpl + '";return view;';
    //console.log(tpl);
    try{
        that.cache = tpl = new Function('d, _escape_', tpl);
        return tpl(data, tool.escape);
    } catch(e){
        delete that.cache;
        return tool.error(e, tplog);
    }
};

Tpl.pt.render = function(data, callback){
    var that = this, tpl;
    if(!data) return tool.error('no data');
    tpl = that.cache ? that.cache(data, tool.escape) : that.parse(that.tpl, data);
    if(!callback) return tpl;
    callback(tpl);
};

var laytpl = function(tpl){
    if(typeof tpl !== 'string') return tool.error('Template not found');
    return new Tpl(tpl);
};

laytpl.config = function(options){
    options = options || {};
    for(var i in options){
        config[i] = options[i];
    }
};

laytpl.v = '1.1';

"function" == typeof define ? define(function() {
    return laytpl
}) : "undefined" != typeof exports ? module.exports = laytpl : window.laytpl = laytpl

}();
