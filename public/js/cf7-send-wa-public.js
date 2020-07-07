// number spinner
function wcqib_refresh_quantity_increments() {
    jQuery("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").each(function(a, b) {
        var c = jQuery(b);
        c.addClass("buttons_added"), c.children().first().before('<input type="button" value="-" class="sp-woopos-minus" />'), c.children().last().after('<input type="button" value="+" class="sp-woopos-plus" />')
    });
}
String.prototype.getDecimals || (String.prototype.getDecimals = function() {
    var a = this,
        b = ("" + a).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
    return b ? Math.max(0, (b[1] ? b[1].length : 0) - (b[2] ? +b[2] : 0)) : 0
}), jQuery(document).ready(function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("updated_wc_div", function() {
    wcqib_refresh_quantity_increments()
}), jQuery(document).on("click", ".sp-woopos-plus, .sp-woopos-minus", function() {
    var a = jQuery(this).closest(".quantity").find(".qty"),
        b = parseFloat(a.val()),
        c = parseFloat(a.attr("max")),
        d = parseFloat(a.attr("min")),
        e = a.attr("step");
    b && "" !== b && "NaN" !== b || (b = 0), "" !== c && "NaN" !== c || (c = ""), "" !== d && "NaN" !== d || (d = 0), "any" !== e && "" !== e && void 0 !== e && "NaN" !== parseFloat(e) || (e = 1), jQuery(this).is(".sp-woopos-plus") ? c && b >= c ? a.val(c) : a.val((b + parseFloat(e)).toFixed(e.getDecimals())) : d && b <= d ? a.val(d) : b > 0 && a.val((b - parseFloat(e)).toFixed(e.getDecimals())), a.trigger("change")
});
// jQuery Number
!function(e){"use strict";function t(e,t){if(this.createTextRange){var a=this.createTextRange();a.collapse(!0),a.moveStart("character",e),a.moveEnd("character",t-e),a.select()}else this.setSelectionRange&&(this.focus(),this.setSelectionRange(e,t))}function a(e){var t=this.value.length;if(e="start"==e.toLowerCase()?"Start":"End",document.selection){var a,i,n,l=document.selection.createRange();return a=l.duplicate(),a.expand("textedit"),a.setEndPoint("EndToEnd",l),i=a.text.length-l.text.length,n=i+l.text.length,"Start"==e?i:n}return"undefined"!=typeof this["selection"+e]&&(t=this["selection"+e]),t}var i={codes:{46:127,188:44,109:45,190:46,191:47,192:96,220:92,222:39,221:93,219:91,173:45,187:61,186:59,189:45,110:46},shifts:{96:"~",49:"!",50:"@",51:"#",52:"$",53:"%",54:"^",55:"&",56:"*",57:"(",48:")",45:"_",61:"+",91:"{",93:"}",92:"|",59:":",39:'"',44:"<",46:">",47:"?"}};e.fn.number=function(n,l,s,r){r="undefined"==typeof r?",":r,s="undefined"==typeof s?".":s,l="undefined"==typeof l?0:l;var u="\\u"+("0000"+s.charCodeAt(0).toString(16)).slice(-4),h=new RegExp("[^"+u+"0-9]","g"),o=new RegExp(u,"g");return n===!0?this.is("input:text")?this.on({"keydown.format":function(n){var u=e(this),h=u.data("numFormat"),o=n.keyCode?n.keyCode:n.which,c="",v=a.apply(this,["start"]),d=a.apply(this,["end"]),p="",f=!1;if(i.codes.hasOwnProperty(o)&&(o=i.codes[o]),!n.shiftKey&&o>=65&&90>=o?o+=32:!n.shiftKey&&o>=69&&105>=o?o-=48:n.shiftKey&&i.shifts.hasOwnProperty(o)&&(c=i.shifts[o]),""==c&&(c=String.fromCharCode(o)),8!=o&&45!=o&&127!=o&&c!=s&&!c.match(/[0-9]/)){var g=n.keyCode?n.keyCode:n.which;if(46==g||8==g||127==g||9==g||27==g||13==g||(65==g||82==g||80==g||83==g||70==g||72==g||66==g||74==g||84==g||90==g||61==g||173==g||48==g)&&(n.ctrlKey||n.metaKey)===!0||(86==g||67==g||88==g)&&(n.ctrlKey||n.metaKey)===!0||g>=35&&39>=g||g>=112&&123>=g)return;return n.preventDefault(),!1}if(0==v&&d==this.value.length?8==o?(v=d=1,this.value="",h.init=l>0?-1:0,h.c=l>0?-(l+1):0,t.apply(this,[0,0])):c==s?(v=d=1,this.value="0"+s+new Array(l+1).join("0"),h.init=l>0?1:0,h.c=l>0?-(l+1):0):45==o?(v=d=2,this.value="-0"+s+new Array(l+1).join("0"),h.init=l>0?1:0,h.c=l>0?-(l+1):0,t.apply(this,[2,2])):(h.init=l>0?-1:0,h.c=l>0?-l:0):h.c=d-this.value.length,h.isPartialSelection=v==d?!1:!0,l>0&&c==s&&v==this.value.length-l-1)h.c++,h.init=Math.max(0,h.init),n.preventDefault(),f=this.value.length+h.c;else if(45!=o||0==v&&0!=this.value.indexOf("-"))if(c==s)h.init=Math.max(0,h.init),n.preventDefault();else if(l>0&&127==o&&v==this.value.length-l-1)n.preventDefault();else if(l>0&&8==o&&v==this.value.length-l)n.preventDefault(),h.c--,f=this.value.length+h.c;else if(l>0&&127==o&&v>this.value.length-l-1){if(""===this.value)return;"0"!=this.value.slice(v,v+1)&&(p=this.value.slice(0,v)+"0"+this.value.slice(v+1),u.val(p)),n.preventDefault(),f=this.value.length+h.c}else if(l>0&&8==o&&v>this.value.length-l){if(""===this.value)return;"0"!=this.value.slice(v-1,v)&&(p=this.value.slice(0,v-1)+"0"+this.value.slice(v),u.val(p)),n.preventDefault(),h.c--,f=this.value.length+h.c}else 127==o&&this.value.slice(v,v+1)==r?n.preventDefault():8==o&&this.value.slice(v-1,v)==r?(n.preventDefault(),h.c--,f=this.value.length+h.c):l>0&&v==d&&this.value.length>l+1&&v>this.value.length-l-1&&isFinite(+c)&&!n.metaKey&&!n.ctrlKey&&!n.altKey&&1===c.length&&(p=d===this.value.length?this.value.slice(0,v-1):this.value.slice(0,v)+this.value.slice(v+1),this.value=p,f=v);else n.preventDefault();f!==!1&&t.apply(this,[f,f]),u.data("numFormat",h)},"keyup.format":function(i){var n,s=e(this),r=s.data("numFormat"),u=i.keyCode?i.keyCode:i.which,h=a.apply(this,["start"]),o=a.apply(this,["end"]);0!==h||0!==o||189!==u&&109!==u||(s.val("-"+s.val()),h=1,r.c=1-this.value.length,r.init=1,s.data("numFormat",r),n=this.value.length+r.c,t.apply(this,[n,n])),""===this.value||(48>u||u>57)&&(96>u||u>105)&&8!==u&&46!==u&&110!==u||(s.val(s.val()),l>0&&(r.init<1?(h=this.value.length-l-(r.init<0?1:0),r.c=h-this.value.length,r.init=1,s.data("numFormat",r)):h>this.value.length-l&&8!=u&&(r.c++,s.data("numFormat",r))),46!=u||r.isPartialSelection||(r.c++,s.data("numFormat",r)),n=this.value.length+r.c,t.apply(this,[n,n]))},"paste.format":function(t){var a=e(this),i=t.originalEvent,n=null;return window.clipboardData&&window.clipboardData.getData?n=window.clipboardData.getData("Text"):i.clipboardData&&i.clipboardData.getData&&(n=i.clipboardData.getData("text/plain")),a.val(n),t.preventDefault(),!1}}).each(function(){var t=e(this).data("numFormat",{c:-(l+1),decimals:l,thousands_sep:r,dec_point:s,regex_dec_num:h,regex_dec:o,init:this.value.indexOf(".")?!0:!1});""!==this.value&&t.val(t.val())}):this.each(function(){var t=e(this),a=+t.text().replace(h,"").replace(o,".");t.number(isFinite(a)?+a:0,l,s,r)}):this.text(e.number.apply(window,arguments))};var n=null,l=null;e.isPlainObject(e.valHooks.text)?(e.isFunction(e.valHooks.text.get)&&(n=e.valHooks.text.get),e.isFunction(e.valHooks.text.set)&&(l=e.valHooks.text.set)):e.valHooks.text={},e.valHooks.text.get=function(t){var a,i=e(t),l=i.data("numFormat");return l?""===t.value?"":(a=+t.value.replace(l.regex_dec_num,"").replace(l.regex_dec,"."),(0===t.value.indexOf("-")?"-":"")+(isFinite(a)?a:0)):e.isFunction(n)?n(t):void 0},e.valHooks.text.set=function(t,a){var i=e(t),n=i.data("numFormat");if(n){var s=e.number(a,n.decimals,n.dec_point,n.thousands_sep);return e.isFunction(l)?l(t,s):t.value=s}return e.isFunction(l)?l(t,a):void 0},e.number=function(e,t,a,i){i="undefined"==typeof i?"1000"!==new Number(1e3).toLocaleString()?new Number(1e3).toLocaleString().charAt(1):"":i,a="undefined"==typeof a?new Number(.1).toLocaleString().charAt(1):a,t=isFinite(+t)?Math.abs(t):0;var n="\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4),l="\\u"+("0000"+i.charCodeAt(0).toString(16)).slice(-4);e=(e+"").replace(".",a).replace(new RegExp(l,"g"),"").replace(new RegExp(n,"g"),".").replace(new RegExp("[^0-9+-Ee.]","g"),"");var s=isFinite(+e)?+e:0,r="",u=function(e,t){return""+ +(Math.round((""+e).indexOf("e")>0?e:e+"e+"+t)+"e-"+t)};return r=(t?u(s,t):""+Math.round(s)).split("."),r[0].length>3&&(r[0]=r[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,i)),(r[1]||"").length<t&&(r[1]=r[1]||"",r[1]+=new Array(t-r[1].length+1).join("0")),r.join(a)}}(jQuery);
// jQuery Loading 2.0.0-rc.2
!function(t,e){if("object"==typeof exports&&"object"==typeof module)module.exports=e(require("jquery"));else if("function"==typeof define&&define.amd)define(["jquery"],e);else{var n="object"==typeof exports?e(require("jquery")):e(t.jQuery);for(var i in n)("object"==typeof exports?exports:t)[i]=n[i]}}(window,function(t){return function(t){var e={};function n(i){if(e[i])return e[i].exports;var o=e[i]={i:i,l:!1,exports:{}};return t[i].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,i){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:i})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(i,o,function(e){return t[e]}.bind(null,o));return i},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=1)}([function(e,n){e.exports=t},function(t,e,n){"use strict";n.r(e);var i=n(0),o=n.n(i);n(2);const r=function(t,e){this.element=t,this.settings=o.a.extend({},r.defaults,e),this.settings.fullPage=this.element.is("body"),this.init(),this.settings.start&&this.start()};r.defaults={overlay:void 0,zIndex:void 0,message:"Loading...",theme:"light",shownClass:"loading-shown",hiddenClass:"loading-hidden",stoppable:!1,start:!0,onStart:function(t){t.overlay.fadeIn(150)},onStop:function(t){t.overlay.fadeOut(150)},onClick:function(){}},r.setDefaults=function(t){r.defaults=o.a.extend({},r.defaults,t)},o.a.extend(r.prototype,{init:function(){this.isActive=!1,this.overlay=this.settings.overlay||this.createOverlay(),this.resize(),this.attachMethodsToExternalEvents(),this.attachOptionsHandlers()},createOverlay:function(){var t=o()('<div class="loading-overlay loading-theme-'+this.settings.theme+'"><div class="loading-overlay-content">'+this.settings.message+"</div></div>").addClass(this.settings.hiddenClass).hide().appendTo("body"),e=this.element.attr("id");return e&&t.attr("id",e+"_loading-overlay"),t},attachMethodsToExternalEvents:function(){var t=this;t.element.on("loading.start",function(){t.overlay.removeClass(t.settings.hiddenClass).addClass(t.settings.shownClass)}),t.element.on("loading.stop",function(){t.overlay.removeClass(t.settings.shownClass).addClass(t.settings.hiddenClass)}),t.settings.stoppable&&t.overlay.on("click",function(){t.stop()}),t.overlay.on("click",function(){t.element.trigger("loading.click",t)}),o()(window).on("resize",function(){t.resize()}),o()(function(){t.resize()})},attachOptionsHandlers:function(){var t=this;t.element.on("loading.start",function(e,n){t.settings.onStart(n)}),t.element.on("loading.stop",function(e,n){t.settings.onStop(n)}),t.element.on("loading.click",function(e,n){t.settings.onClick(n)})},calcZIndex:function(){return void 0!==this.settings.zIndex?this.settings.zIndex:(parseInt(this.element.css("z-index"))||0)+1+this.settings.fullPage},resize:function(){var t=this.element,e=t.outerWidth(),n=t.outerHeight();this.settings.fullPage&&(n="100%",e="100%"),this.overlay.css({position:this.settings.fullPage?"fixed":"absolute",zIndex:this.calcZIndex(),top:t.offset().top,left:t.offset().left,width:e,height:n})},start:function(){this.isActive=!0,this.resize(),this.element.trigger("loading.start",this)},stop:function(){this.isActive=!1,this.element.trigger("loading.stop",this)},active:function(){return this.isActive},toggle:function(){this.active()?this.stop():this.start()},destroy:function(){this.overlay.remove()}});var s="jquery-loading";o.a.fn.loading=function(t){return this.each(function(){var e=o.a.data(this,s);e?void 0===t?e.start():"string"==typeof t?e[t].apply(e):(e.destroy(),o.a.data(this,s,new r(o()(this),t))):void 0!==t&&"object"!=typeof t&&"start"!==t&&"toggle"!==t||o.a.data(this,s,new r(o()(this),t))})},o.a.fn.Loading=function(t){var e=o()(this).data(s);return e&&void 0===t||o()(this).data(s,e=new r(o()(this),t)),e},o.a.expr[":"].loading=function(t){var e=o.a.data(t,s);return!!e&&e.active()},o.a.Loading=r},function(t,e,n){var i=n(3);"string"==typeof i&&(i=[[t.i,i,""]]);var o={insert:"head",singleton:!1};n(5)(i,o);i.locals&&(t.exports=i.locals)},function(t,e,n){(t.exports=n(4)(!1)).push([t.i,"/* Default jquery-loading styles */\r\n\r\n.loading-overlay {\r\n  display: table;\r\n  opacity: 0.7;\r\n}\r\n\r\n.loading-overlay-content {\r\n  text-transform: uppercase;\r\n  letter-spacing: 0.4em;\r\n  font-size: 1.15em;\r\n  font-weight: bold;\r\n  text-align: center;\r\n  display: table-cell;\r\n  vertical-align: middle;\r\n}\r\n\r\n.loading-overlay.loading-theme-light {\r\n  background-color: #fff;\r\n  color: #000;\r\n}\r\n\r\n.loading-overlay.loading-theme-dark {\r\n  background-color: #000;\r\n  color: #fff;\r\n}\r\n",""])},function(t,e,n){"use strict";t.exports=function(t){var e=[];return e.toString=function(){return this.map(function(e){var n=function(t,e){var n=t[1]||"",i=t[3];if(!i)return n;if(e&&"function"==typeof btoa){var o=(s=i,a=btoa(unescape(encodeURIComponent(JSON.stringify(s)))),l="sourceMappingURL=data:application/json;charset=utf-8;base64,".concat(a),"/*# ".concat(l," */")),r=i.sources.map(function(t){return"/*# sourceURL=".concat(i.sourceRoot).concat(t," */")});return[n].concat(r).concat([o]).join("\n")}var s,a,l;return[n].join("\n")}(e,t);return e[2]?"@media ".concat(e[2],"{").concat(n,"}"):n}).join("")},e.i=function(t,n){"string"==typeof t&&(t=[[null,t,""]]);for(var i={},o=0;o<this.length;o++){var r=this[o][0];null!=r&&(i[r]=!0)}for(var s=0;s<t.length;s++){var a=t[s];null!=a[0]&&i[a[0]]||(n&&!a[2]?a[2]=n:n&&(a[2]="(".concat(a[2],") and (").concat(n,")")),e.push(a))}},e}},function(t,e,n){"use strict";var i,o={},r=function(){return void 0===i&&(i=Boolean(window&&document&&document.all&&!window.atob)),i},s=function(){var t={};return function(e){if(void 0===t[e]){var n=document.querySelector(e);if(window.HTMLIFrameElement&&n instanceof window.HTMLIFrameElement)try{n=n.contentDocument.head}catch(t){n=null}t[e]=n}return t[e]}}();function a(t,e){for(var n=[],i={},o=0;o<t.length;o++){var r=t[o],s=e.base?r[0]+e.base:r[0],a={css:r[1],media:r[2],sourceMap:r[3]};i[s]?i[s].parts.push(a):n.push(i[s]={id:s,parts:[a]})}return n}function l(t,e){for(var n=0;n<t.length;n++){var i=t[n],r=o[i.id],s=0;if(r){for(r.refs++;s<r.parts.length;s++)r.parts[s](i.parts[s]);for(;s<i.parts.length;s++)r.parts.push(v(i.parts[s],e))}else{for(var a=[];s<i.parts.length;s++)a.push(v(i.parts[s],e));o[i.id]={id:i.id,refs:1,parts:a}}}}function c(t){var e=document.createElement("style");if(void 0===t.attributes.nonce){var i=n.nc;i&&(t.attributes.nonce=i)}if(Object.keys(t.attributes).forEach(function(n){e.setAttribute(n,t.attributes[n])}),"function"==typeof t.insert)t.insert(e);else{var o=s(t.insert||"head");if(!o)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");o.appendChild(e)}return e}var u,d=(u=[],function(t,e){return u[t]=e,u.filter(Boolean).join("\n")});function f(t,e,n,i){var o=n?"":i.css;if(t.styleSheet)t.styleSheet.cssText=d(e,o);else{var r=document.createTextNode(o),s=t.childNodes;s[e]&&t.removeChild(s[e]),s.length?t.insertBefore(r,s[e]):t.appendChild(r)}}var h=null,p=0;function v(t,e){var n,i,o;if(e.singleton){var r=p++;n=h||(h=c(e)),i=f.bind(null,n,r,!1),o=f.bind(null,n,r,!0)}else n=c(e),i=function(t,e,n){var i=n.css,o=n.media,r=n.sourceMap;if(o&&t.setAttribute("media",o),r&&btoa&&(i+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(r))))," */")),t.styleSheet)t.styleSheet.cssText=i;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(i))}}.bind(null,n,e),o=function(){!function(t){if(null===t.parentNode)return!1;t.parentNode.removeChild(t)}(n)};return i(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;i(t=e)}else o()}}t.exports=function(t,e){(e=e||{}).attributes="object"==typeof e.attributes?e.attributes:{},e.singleton||"boolean"==typeof e.singleton||(e.singleton=r());var n=a(t,e);return l(n,e),function(t){for(var i=[],r=0;r<n.length;r++){var s=n[r],c=o[s.id];c&&(c.refs--,i.push(c))}t&&l(a(t,e),e);for(var u=0;u<i.length;u++){var d=i[u];if(0===d.refs){for(var f=0;f<d.parts.length;f++)d.parts[f]();delete o[d.id]}}}}}])});
/**
 * Copyright (c) 2007 Ariel Flesler - aflesler ○ gmail • com | https://github.com/flesler
 * Licensed under MIT
 * @author Ariel Flesler
 * @version 2.1.2
 */
;(function(f){"use strict";"function"===typeof define&&define.amd?define(["jquery"],f):"undefined"!==typeof module&&module.exports?module.exports=f(require("jquery")):f(jQuery)})(function($){"use strict";function n(a){return!a.nodeName||-1!==$.inArray(a.nodeName.toLowerCase(),["iframe","#document","html","body"])}function h(a){return $.isFunction(a)||$.isPlainObject(a)?a:{top:a,left:a}}var p=$.scrollTo=function(a,d,b){return $(window).scrollTo(a,d,b)};p.defaults={axis:"xy",duration:0,limit:!0};$.fn.scrollTo=function(a,d,b){"object"=== typeof d&&(b=d,d=0);"function"===typeof b&&(b={onAfter:b});"max"===a&&(a=9E9);b=$.extend({},p.defaults,b);d=d||b.duration;var u=b.queue&&1<b.axis.length;u&&(d/=2);b.offset=h(b.offset);b.over=h(b.over);return this.each(function(){function k(a){var k=$.extend({},b,{queue:!0,duration:d,complete:a&&function(){a.call(q,e,b)}});r.animate(f,k)}if(null!==a){var l=n(this),q=l?this.contentWindow||window:this,r=$(q),e=a,f={},t;switch(typeof e){case "number":case "string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(e)){e= h(e);break}e=l?$(e):$(e,q);case "object":if(e.length===0)return;if(e.is||e.style)t=(e=$(e)).offset()}var v=$.isFunction(b.offset)&&b.offset(q,e)||b.offset;$.each(b.axis.split(""),function(a,c){var d="x"===c?"Left":"Top",m=d.toLowerCase(),g="scroll"+d,h=r[g](),n=p.max(q,c);t?(f[g]=t[m]+(l?0:h-r.offset()[m]),b.margin&&(f[g]-=parseInt(e.css("margin"+d),10)||0,f[g]-=parseInt(e.css("border"+d+"Width"),10)||0),f[g]+=v[m]||0,b.over[m]&&(f[g]+=e["x"===c?"width":"height"]()*b.over[m])):(d=e[m],f[g]=d.slice&& "%"===d.slice(-1)?parseFloat(d)/100*n:d);b.limit&&/^\d+$/.test(f[g])&&(f[g]=0>=f[g]?0:Math.min(f[g],n));!a&&1<b.axis.length&&(h===f[g]?f={}:u&&(k(b.onAfterFirst),f={}))});k(b.onAfter)}})};p.max=function(a,d){var b="x"===d?"Width":"Height",h="scroll"+b;if(!n(a))return a[h]-$(a)[b.toLowerCase()]();var b="client"+b,k=a.ownerDocument||a.document,l=k.documentElement,k=k.body;return Math.max(l[h],k[h])-Math.min(l[b],k[b])};$.Tween.propHooks.scrollLeft=$.Tween.propHooks.scrollTop={get:function(a){return $(a.elem)[a.prop]()}, set:function(a){var d=this.get(a);if(a.options.interrupt&&a._last&&a._last!==d)return $(a.elem).stop();var b=Math.round(a.now);d!==b&&($(a.elem)[a.prop](b),a._last=this.get(a))}};return p});
/**
 * @A WordPress-like hook system for JavaScript.
 * @author Rheinard Korf
 * @license GPL2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @requires underscore.js (http://underscorejs.org/)
 */
var Hooks = Hooks || {}; // Extend Hooks if exists or create new Hooks object.
Hooks.actions = Hooks.actions || {}; // Registered actions
Hooks.filters = Hooks.filters || {}; // Registered filters
Hooks.add_action = function( tag, callback, priority ) {
    if( typeof priority === "undefined" ) {
        priority = 10;
    }
    Hooks.actions[ tag ] = Hooks.actions[ tag ] || [];
    Hooks.actions[ tag ].push( { priority: priority, callback: callback } );
}
Hooks.add_filter = function( tag, callback, priority ) {
    if( typeof priority === "undefined" ) {
        priority = 10;
    }
    Hooks.filters[ tag ] = Hooks.filters[ tag ] || [];
    Hooks.filters[ tag ].push( { priority: priority, callback: callback } );
}
Hooks.remove_action = function( tag, callback ) {
    Hooks.actions[ tag ] = Hooks.actions[ tag ] || [];
    Hooks.actions[ tag ].forEach( function( filter, i ) {
        if( filter.callback === callback ) {
            Hooks.actions[ tag ].splice(i, 1);
        }
    } );
}
Hooks.remove_filter = function( tag, callback ) {
    Hooks.filters[ tag ] = Hooks.filters[ tag ] || [];
    Hooks.filters[ tag ].forEach( function( filter, i ) {
        if( filter.callback === callback ) {
            Hooks.filters[ tag ].splice(i, 1);
        }
    } );
}
Hooks.do_action = function( tag, options ) {
    var actions = [];
    if( typeof Hooks.actions[ tag ] !== "undefined" && Hooks.actions[ tag ].length > 0 ) {
        Hooks.actions[ tag ].forEach( function( hook ) {
            actions[ hook.priority ] = actions[ hook.priority ] || [];
            actions[ hook.priority ].push( hook.callback );
        } );
        actions.forEach( function( hooks ) {
            hooks.forEach( function( callback ) {
                callback( options );
            } );
        } );
    }
}
Hooks.apply_filters = function( tag, value, options ) {
    var filters = [];
    if( typeof Hooks.filters[ tag ] !== "undefined" && Hooks.filters[ tag ].length > 0 ) {
        Hooks.filters[ tag ].forEach( function( hook ) {
            filters[ hook.priority ] = filters[ hook.priority ] || [];
            filters[ hook.priority ].push( hook.callback );
        } );
        filters.forEach( function( hooks ) {
            hooks.forEach( function( callback ) {
                value = callback( value, options );
            } );
        } );
    }
    return value;
}
/* Main Class */
function Woo_QuickShop_Cart() {
	var self = this;
	self.products = ko.observableArray();
	self.items = ko.observableArray();
	self.total = ko.pureComputed( function(){
		var total = 0;
		_.each( self.items(), function( item,index,list ){
			total = total + ( item.price * item.qty() );
		} );
		return total;
	} );
	self.price_total = ko.pureComputed( function(){			
		return 'Rp ' + jQuery.number( self.total(), cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator );
	} );
	
	self.gotoItem = function(e){
		if( this.constructor.name == 'Woo_QuickShop_Cart_Item' ) {
			var id = this.id;
			var $target = jQuery( '#'+id ).closest( '.product-item' );
			jQuery( 'body' ).scrollTo( $target, 400, {offset:-50} );
			var _x = window.setInterval(function(){
				$target.fadeOut(250).fadeIn(250);
				window.clearInterval( _x );
			}, 300);
		}
	}
	self.removeItem = function(e){
		if( this.constructor.name == 'Woo_QuickShop_Cart_Item' ) {
			var id = this.id;
            self.items.remove( this );
            jQuery('#'+id).val(0);
            jQuery('#'+id).trigger( 'change' );
        }
	}
	
	self.viewdetail = ko.observable({
		title:'Product Title',
		sku:'',
		images: '',
		price: '',
		excerpt:'',
		description:'',
		attributes: '',
		stock:''
	});
}
function Woo_QuickShop_ProductItem( cls, catId, title, prop ) {
	var self = this;
	self.cls = cls; // class container
	self.catId = catId; // category container id
	self.title = title; // product title
	self.prop = prop;
}
function Woo_QuickShop_Cart_Item( id, title, subtitle, qty, price, prop ){
	var self = this;
	self.id = id;
	self.title = title;
	self.subtitle = subtitle;
	self.qty = ko.observable(qty);
	self.price = price;
	self.subtotal = ko.pureComputed( function(){
		return self.qty() * self.price;
	} );
	self.price_html = ko.pureComputed( function(){
		return jQuery.number(self.price, cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator);
	} );
	self.subtotal_html = ko.pureComputed( function(){
		return 'Rp ' + jQuery.number(self.subtotal(), cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator );
	} );
	self.prop = prop;
}

(function( $ ) {
	'use strict';	
    function wrap_spinner( id ) {
        $( '#'+id ).css( 'display','inline-block' )
                   .css( 'margin-top', '0px' )
                   .css( 'border', '0px' );
        $( '#'+id ).attr( 'step', '1' )
        $( '#'+id ).addClass( 'input-text qty text' );
        $( '#'+id ).wrap( '<div class="quantity buttons_added"></div>' );		
        $( '#'+id ).parent().prepend( '<input type="button" data-bind="enable: qty_btn_enable_minus" value="-" class="button sp-woopos-minus">' );
        $( '#'+id ).parent().append( '<input type="button" data-bind="enable: qty_btn_enable_add" value="+" class="button sp-woopos-plus">' );
    } 
	
	var ajax_search_txt = '';
	var qty_buttons = [];
	var vm = new Woo_QuickShop_Cart();
    $.extend( Woo_QuickShop_Cart, {
        getVM: function(){
            return vm;
        }
    } );
	
	$( document ).ready( function() {
		ko.applyBindings( vm );
		if( $( '#quickshop-products' ).length ) {
			var $qs = $( '#quickshop-products' );
			$qs.loading();
			var el_id = $qs.attr("id");
			load_products( el_id, '', function(el_id, cat_slug, data_count){
				$qs.loading( 'stop' );
			} );
		} else {
			$( '.product-cat-container' ).each( function( index, element ){
				var delay = Math.floor( Math.random() * 200 ) + ( index * 200 );
				var ajaxRun = window.setInterval( function(){
					var $el = $( element );	
					var el_id = $el.attr('id');
					var cat_slug = el_id.replace( 'cat-', '' );		
					$('#'+el_id).loading();
					load_products( el_id, cat_slug, function(el_id, cat_slug, data_count){
						$( '#'+el_id ).loading( 'stop' );
						if( data_count < 1 ) {
							$( '#'+el_id ).remove();
							$( '#cf7sendwa_woo_cat_filter option[value="'+cat_slug+'"]' ).remove();
						}	
						$( '#'+ el_id +'.product-cat-container' ).css( 'min-height', '0px' );
					} );
					clearInterval(ajaxRun);	
				}, delay );
			} );
		}
		
		var html_hidden = '<input type="hidden" name="quickshop_cart" id="cf7sendwa_quickshop_cart" value="">';
		if( $( '.cf7sendwa-cf7-container' ).length ) {
			$( '.cf7sendwa-cf7-container .wpcf7-submit' ).parent().append( html_hidden );
			Hooks.do_action( 'cf7sendwa_after_cf7_submit' );
		} else if( $( '.cf7sendwa-quickshop-checkout' ).length ) {
			var button_html = '<button class="button cf7sendwa-add-to-cart">' + cf7sendwa_qsreview.cart_label + '</button>';
			var button_container_selector = '.cf7sendwa-quickshop-checkout';
			if( cf7sendwa_qsreview.button_append_to != '' ) {
				button_container_selector = cf7sendwa_qsreview.button_append_to;
			}
			$( button_container_selector ).append( html_hidden ).append( button_html );
		}
		$( 'body' ).on( 'change', '.product-item .qty', product_qty_change );
		$( 'body' ).on( 'keyup', '.product-item .qty', product_qty_change );
		$( 'body' ).on( 'focus', '.product-item .qty', function(evt){
            $(this).select();
        } );
		
		$( 'body' ).on( 'click', '.variant-option-button', function(evt){
			evt.preventDefault();
			var var_id = $(this).attr('data-var-id');
			if( cf7sendwa.quickshop_atts.render == 'grid' ) {
				$( '.product-item-variations-var-'+var_id ).modal();
			} else {
				var $tgglEl = $( '.variations.var-'+var_id );
				$(this).toggleClass( 'active' );
				$tgglEl.slideToggle();			
				if( $(this).hasClass('active') ) {
					$(this).find( '.angle-down' ).hide();	
					$(this).find( '.angle-up' ).show();	
				} else {
					$(this).find( '.angle-down' ).show();	
					$(this).find( '.angle-up' ).hide();	
				}
			}	
		} );
		$( 'body' ).on( 'click', '.wpcf7-form-control.wpcf7-submit', function(evt){
			var quickshop = ko.toJS( vm );
			delete quickshop.products;
			delete quickshop.viewdetail;
			$( '#cf7sendwa_quickshop_cart' ).val( ko.toJSON(quickshop) );
			if( quickshop && quickshop.total <= 0 ) {
				evt.preventDefault();
			}
		} );
		
		// text filter 
		$( '#cf7sendwa_woo_text_filter' ).keyup( function( evt ){
			var text = $(this).val();
			if( (evt.which >= 65 && evt.which <= 90) || (evt.which >= 48 && evt.which <= 57) ) {				
				if( text.length >= 3 ) {
					var matches = _.filter( ko.toJS(vm.products()), function(item){
						var r = item.title.toLowerCase().search( text.toLowerCase() ) > -1 ? true : false;
						return r;
					} );
					if( matches.length > 0 ) {
						$( '#cf7sendwa-quickshop-container .product-item' ).hide();
						_.each( matches, function(item, index, list){
							$( '#cf7sendwa-quickshop-container ' + item.cls ).show();
						} );
					} else {
						$( '#cf7sendwa-quickshop-container .product-item' ).hide();
					}
				} 
			} else if( text == '' ) {
				$( '#cf7sendwa-quickshop-container .product-item' ).show();
			}
		} );
		
		// category filter 
		$( '.cf7sendwa-woo-categories' ).on( 'change', function(evt){
			var cat = $(this).val();
			if( cat == '' ) {
				$( '#cf7sendwa-quickshop-container .product-cat-container' ).show();
			} else {
				$( '#cf7sendwa-quickshop-container .product-cat-container' ).hide();	
				$( '#cf7sendwa-quickshop-container #cat-' + cat +'.product-cat-container' ).show();
			}
		} );
		
		
		// add to cart from quickshop
		$( 'body' ).on( 'click', '.cf7sendwa-add-to-cart', function(evt){
			var $this_button = $(this);
			$this_button.attr('disabled', true);
			var quickshop = ko.toJS( vm );	
			delete quickshop.products;
			delete quickshop.viewdetail;
			$( '#cf7sendwa_quickshop_cart' ).val( ko.toJSON(quickshop) );			
			if( quickshop && quickshop.total > 0 ) {
                var btn_text = $this_button.html();
                $this_button.attr( 'disabled', true );
                $this_button.html( btn_text + '&nbsp;<img src="' + cf7sendwa.assets_dir + 'img/ajax-loader.gif">' );
				$.ajax( {
					url: cf7sendwa.ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						'action':'cf7sendwa_add_to_cart',
						'quickshop_cart': $( '#cf7sendwa_quickshop_cart' ).val(),
						'security': cf7sendwa.security,
						'redirect': cf7sendwa_qsreview.redirect
					},
					success: function(response){
						if( response ) {
							document.location = response.redirect_url;
                            $this_button.attr( 'disabled', false );
                            $this_button.html( btn_text );
						} else {
							$this_button.attr('disabled', false);;	
						}						
					}
				} );
			} else {
				$this_button.attr('disabled', false);;
			}
		} );
		
	    if( cf7sendwa_qsreview.sticky == 'yes' ) { // sticky checkout
		    var prop = {
			    getWidthFrom:'#cf7sendwa-checkout'
		    };
		    if( cf7sendwa_qsreview.top != '' ) {
			    prop.topSpacing = Hooks.apply_filters( 'cf7sendwa_checkout_sticky_top', parseInt(cf7sendwa_qsreview.top), { atts: cf7sendwa_qsreview } );
		    }
		    if( cf7sendwa_qsreview.bottom != '' ) {
			    prop.bottomSpacing = Hooks.apply_filters( 'cf7sendwa_checkout_sticky_bottom', parseInt(cf7sendwa_qsreview.bottom), { atts: cf7sendwa_qsreview } );
		    }
		    $( '.cf7sendwa-quickshop-checkout-container' ).sticky(prop);
	    }
	    
	    if( $( '#cf7sendwa_woo_ajax_filter' ).length ) {
			$( '#cf7sendwa_woo_ajax_filter' ).keydown(function(evt){
				if( evt.which == 13 ) {
					evt.preventDefault();
					var text = $(this).val();
					do_ajax_search( text );
				}						
			});
			$( '#cf7sendwa_woo_ajax_filter_button' ).click( function(evt){
				var text = $( '#cf7sendwa_woo_ajax_filter' ).val();
				do_ajax_search( text );
			} );
	    }
	    
    } );

    function product_qty_change( evt ) {
		var qty = parseInt( $(this).val() );
		if( isNaN(qty) ) {
			qty = 0;
			$(this).val(qty);
		}
		var stock_status = $( this ).attr( 'data-stock' );
		if( stock_status == 'outofstock' ) {
			$(this).val(0);
			return false;	
		}
		var price = parseFloat( $(this).attr( 'data-price' ) );
		var $item = $(this).closest( '.item-price' );
		var subtotal = price * qty;
		var el_subtotal = $item.find( '.item-subtotal' );
		el_subtotal.html( cf7sendwa.currency+' '+jQuery.number(subtotal, cf7sendwa.decimals, cf7sendwa.decimal_separator, cf7sendwa.thousand_separator) );

		var id = $(this).prop( 'id' );
		var match = ko.utils.arrayFirst(vm.items(), function(item) {
		    return id === item.id;
		});
		if( !match && qty > 0 ){
			var $product = $( this ).closest( '.product-item' ).find( '.product-item-info h4' );
			var product_title = $product.text();
			var subtitle = '';
			var pa = $(this).attr( 'data-pa' );
			var pa_terms = {};
			if( pa ) {
				pa = jQuery.parseJSON( decodeURIComponent( pa ) );
				_.each(pa, function(item, index, list){
					_.each( item, function(val,key){
						pa_terms[key] = val;
					} );
				});
				var $prd_parent = $( '.product-item.prd-' + $(this).attr( 'data-product_id' ) + ' .product-item-info h4' );
				subtitle = product_title;
				product_title = $prd_parent.text();
			}
			var prop = {
				'product_id': $( this ).attr( 'data-product_id' ),
				'product_type': $( this ).attr( 'data-product_type' ),
				'variation_id': $( this ).attr( 'data-variation_id' ),
				'sku': $( this ).attr( 'data-sku' ),
				'weight': $( this ).attr( 'data-weight' ),
				'pa': pa_terms
			};
			var cart_item = new Woo_QuickShop_Cart_Item( id, product_title, subtitle, qty, price, prop );				
			vm.items.push(cart_item);
		} else if(match) {
			match.qty(qty);
			if( match.qty() == 0 ) {
				vm.items.remove(match);
			}
		}
    }
    
    $( 'body' ).on( 'click', '.woo-link-detail', function(evt){
	    evt.preventDefault();
	    if( cf7sendwa.quickshop_atts.detail == 'yes' ) {
		    var selector = $(this).attr( 'data-el-cls' );
			var detail = _.find( ko.toJS(vm.products()), function(item){
				return item.cls == selector;
			} );	    
			var view = (function( detail ){
				var obj = {};
				obj.title = detail.title;
				var __sku = detail.prop.sku;
				if( __sku != '' ) {
					__sku = '<span class="cf7sendwa-product-sku-label">SKU: </span>' + __sku;
				}
				obj.sku = __sku;
				obj.price = detail.prop.price_html;
				obj.excerpt = detail.prop.short_description;
				obj.description = detail.prop.full_description;
				
				var img = '';
				if( detail.prop.images.length ) {
					var img_src = detail.prop.images[0].src;	
					img = '<img src="'+ img_src +'">';
					if( detail.prop.gallery.length ) {
						_.each( detail.prop.gallery, function( item ){
							img += '<img src="' + item + '">';
						} );
					}
				}
				obj.images = img;
				obj.attributes = detail.prop.attributes;
				obj.stock = detail.prop.stock_status;
				return obj;
			})(detail);
			vm.viewdetail( view );
		    $('#cf7sendwa-product-detail').modal();
		    $('#cf7sendwa-product-detail .fotorama' ).fotorama();
		}
    } );
    
    function load_products( el_id, cat_slug, callback ) {
	    if( cat_slug != '' ) {
		    cf7sendwa.quickshop_atts.category = cat_slug;
	    }
	    var products_page = 1;
	    if( _.has( cf7sendwa.quickshop_atts, 'page' ) ) {
		    products_page = cf7sendwa.quickshop_atts.page;
	    }
		$.ajax( {
			url: cf7sendwa.ajaxurl,
			type: 'POST',
			dataType: 'html',
			data: { 
				'action':'cf7sendwa_products', 
				'args': cf7sendwa.quickshop_atts,
				'security': cf7sendwa.security,
				'cf7sendwa_search': ajax_search_txt
			},
			success: function( response ) {
				$( '#'+el_id ).append( response );	
				var data_count = parseInt( $( '#'+el_id + ' .product-items' ).attr( 'data-total' ) );	
				
				var _cart = {};
				var _cart_subtot = {};
				if( vm.items().length ){
					var __cart = ko.toJS( vm.items );
					_.each( __cart, function( item, index, list ){
						_cart[ item.id ] = item.qty;	
						_cart_subtot[ item.id ] = item.subtotal_html;
					} );
				}
				
				$( '#'+el_id+' .item-subtotal' ).html( cf7sendwa.currency + ' 0' );
				$( '#'+el_id+' .product-items.page-'+ products_page + ' .qty' ).each( function(index, element) {
					var qty_id = $(element).attr('id');
					if( _.indexOf( qty_buttons, qty_id ) == -1 ) {
						wrap_spinner( qty_id );
						qty_buttons.push( qty_id );
						if( _cart[qty_id] != undefined ) {
							$( '#'+qty_id ).val( _cart[qty_id] );
							var $item = $('#'+qty_id).closest( '.item-price' );
							$item.find( '.item-subtotal' ).html( _cart_subtot[qty_id] );
						}
					} else {
						var $prd_item = $(element).closest( '.product-item' );
						$prd_item.remove();
					}
				} );		
                if( cf7sendwa.quickshop_atts.render == 'list' ) {
					$( '#'+el_id+' .variations' ).hide();
					$( '#'+el_id+' .variant-option-button .angle-up' ).hide();
				}
				if( typeof callback == 'function' ) {
					callback( el_id, cat_slug, data_count );
				}
				// add to item 
				var catId = '';
				var $container = $( '#'+el_id ).closest( '.product-cat-container' );
				if( $container.length ) {
					catId = $container.attr( 'id' );
				}
				if( cf7sendwa.quickshop_atts.filter == 'yes' || cf7sendwa.quickshop_atts.detail == 'yes' ) {
					$( '#'+el_id+' .product-items .product-item:not(.variations)' ).each( function(index){
						var $h4 = $(this).find( '.product-item-info h4' );
						var cls = $(this).attr('class');
						var clsArr = cls.split(" ");
						cls = "."+clsArr.join(".");
						var title = $h4.text();
						var $prop = $(this).find( '.woo-product-prop' );
						$( cls ).find( 'a.woo-link-detail' ).attr( 'data-el-cls', cls );
						vm.products.push( new Woo_QuickShop_ProductItem( cls, catId, title, jQuery.parseJSON($prop.val()) ) );
						$prop.remove();
					} );				
				}	
				
				if( cf7sendwa.quickshop_atts.paging == 'loadmore' ) {
					if( cf7sendwa.quickshop_atts.page == undefined ) {
						cf7sendwa.quickshop_atts.page = 1;
					}
					$( '#'+cat_slug+'-page-'+cf7sendwa.quickshop_atts.page ).click( function(evt){
						evt.preventDefault();
						var $link = $(this);
						var link_id = $link.attr('id');
						var page = $link.attr( 'data-next' );
						var $cat_item = $link.closest('.product-cat-container');
						var el_id = $cat_item.attr('id');
						if( el_id != undefined ) {
							var cat_slug = el_id.replace( 'cat-', '' );
							var loader_id = link_id+'-loader';
							$link.after( '<div id="'+loader_id+'">&nbsp;</div>' );
							$( '#'+loader_id ).css( 'height', '50px' );
							$link.remove();
							$( '#'+loader_id ).loading();
							cf7sendwa.quickshop_atts.page = page;
							load_products( el_id, cat_slug, function(){
								$( '#'+loader_id ).loading('toggle');
								$( '#'+loader_id ).remove();
							} );
						}
					} );
				} else if( cf7sendwa.quickshop_atts.paging == 'auto' ) {
					// fetch next page 
					if( $( '#' + el_id  +' .quickshop-load-more' ).length ) {
						var $next = $( '#' + el_id  +' .quickshop-load-more' );
						var page = $next.attr( 'data-next' );
						cf7sendwa.quickshop_atts.page = page;
						$next.remove();
						load_products( el_id, cat_slug, callback );
					}
				}
			}
		} );
    }
	function do_ajax_search( txt ) {
		ajax_search_txt = txt;
		qty_buttons = [];
		cf7sendwa.quickshop_atts.page = 1;
		$( '.product-items' ).remove();
		$( '.cf7sendwa-quickshop-paging' ).remove();
		if( $( '#quickshop-products' ).length ) {
			var $qs = $( '#quickshop-products' );
			$qs.loading();
			var el_id = $qs.attr("id");
			load_products( el_id, '', function(el_id, cat_slug, data_count){
				$qs.loading( 'stop' );
			} );
		} else {
			$( '.product-cat-container' ).each( function( index, element ){
				var delay = Math.floor( Math.random() * 200 ) + ( index * 200 );
				var ajaxRun = window.setInterval( function(){
					var $el = $( element );	
					var el_id = $el.attr('id');
					var cat_slug = el_id.replace( 'cat-', '' );		
					$('#'+el_id).loading();
					load_products( el_id, cat_slug, function(el_id, cat_slug, data_count){
						$( '#'+el_id ).loading( 'stop' );
						$( '#'+ el_id +'.product-cat-container' ).css( 'min-height', '0px' );
					} );
					clearInterval(ajaxRun);	
				}, delay );
			} );
		}
	}
	// select2
	if( $( '.cf7sendwa-woo-categories' ).length ) {
		$( '.cf7sendwa-woo-categories' ).select2( {
			placeholder: "Select Category", 
			allowClear: true
		} );
	}
})( jQuery );