/**
 * Request Animation Frame Polyfill
 * Authored by Erik Möller
 * http://my.opera.com/emoller/blog
 *
 * Polyfills requestAnimationFrame and cancelAnimationFrame
 * for ms/mox/webkit/o vendor prefixes.
 *
 * Will fallback to setTimeout for non-supportive browsers.
 */

(function() {

  var lastTime = 0;
  var vendors = ['ms', 'moz', 'webkit', 'o'];

  for( var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x ) {
    window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
    window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame'] || window[vendors[x] + 'CancelRequestAnimationFrame'];
  }

  if ( !window.requestAnimationFrame || !window.cancelAnimationFrame ) {
    window.requestAnimationFrame = function(callback, element) {
      var currTime = new Date().getTime();
      var timeToCall = Math.max(0, 16 - (currTime - lastTime));
      var id = window.setTimeout(function() { callback(currTime + timeToCall); }, timeToCall);
      lastTime = currTime + timeToCall;
      return id;
    };

    window.cancelAnimationFrame = function(id) {
      clearTimeout(id);
    };
  }

}());