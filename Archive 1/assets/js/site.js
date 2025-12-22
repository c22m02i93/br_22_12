(function () {
  'use strict';

  var onReady = function (callback) {
    if (document.readyState !== 'loading') {
      callback();
    } else {
      document.addEventListener('DOMContentLoaded', callback);
    }
  };

  onReady(function () {
    var toTop = document.getElementById('toTop');
    if (toTop) {
      var toggleButton = function () {
        if (window.scrollY > 300) {
          toTop.style.display = 'flex';
        } else {
          toTop.style.display = 'none';
        }
      };

      toggleButton();
      window.addEventListener('scroll', toggleButton);

      toTop.addEventListener('click', function (event) {
        event.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  });
})();