/**
 * @link https://github.com/imagitama/nodelist-foreach-polyfill/blob/master/index.js
 */
window.NodeList&&!NodeList.prototype.forEach&&(NodeList.prototype.forEach=function(o,t){t=t||window;for(var i=0;i<this.length;i++)o.call(t,this[i],i,this)});