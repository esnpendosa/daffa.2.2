(function () {
  /* Konfirmasi sebelum menghapus */
  document.addEventListener('submit', function (e) {
    var f = e.target.closest ? e.target.closest('form[data-confirm]') : null;
    if (f && !window.confirm(f.getAttribute('data-confirm'))) e.preventDefault();
  });
  /* Menu samping di layar kecil */
  var b = document.getElementById('burger'), s = document.getElementById('side');
  if (b && s) {
    b.addEventListener('click', function () {
      var o = s.classList.toggle('open');
      b.setAttribute('aria-expanded', o);
    });
  }
  /* Pesan sukses hilang sendiri */
  var fl = document.querySelector('.flash.ok');
  if (fl) setTimeout(function () { fl.style.transition = 'opacity .4s'; fl.style.opacity = '0'; setTimeout(function () { fl.remove(); }, 450); }, 4500);
})();
