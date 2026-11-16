(function () {
  document.documentElement.classList.add('js');

  /* Menu HP */
  var nav = document.getElementById('menu'), btn = document.querySelector('.menu-toggle');
  function closeMenu() { if (!nav) return; nav.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); btn.setAttribute('aria-label', 'Buka menu'); }
  if (nav && btn) {
    btn.addEventListener('click', function () {
      var o = nav.classList.toggle('open');
      btn.setAttribute('aria-expanded', o);
      btn.setAttribute('aria-label', o ? 'Tutup menu' : 'Buka menu');
    });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) closeMenu(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });
  }

  /* Muncul saat digulir */
  var els = document.querySelectorAll('.rv');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) {
      en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } });
    }, { threshold: 0.12 });
    els.forEach(function (e) { io.observe(e); });
  } else { els.forEach(function (e) { e.classList.add('in'); }); }

  /* Filter artikel */
  var fb = document.querySelectorAll('[data-filter]'), arts = document.querySelectorAll('.art[data-cat]');
  fb.forEach(function (b) {
    b.addEventListener('click', function () {
      fb.forEach(function (x) { x.setAttribute('aria-pressed', x === b); });
      var f = b.getAttribute('data-filter');
      arts.forEach(function (a) { a.hidden = !(f === 'semua' || a.getAttribute('data-cat') === f); });
    });
  });

  /* Buka tips artikel bila ditautkan dari halaman lain (artikel.php#id) */
  function openHash() {
    var id = location.hash.slice(1), el = id && document.getElementById(id), d = el && el.querySelector('details');
    if (d) d.open = true;
  }
  openHash(); window.addEventListener('hashchange', openHash);

  /* Akordeon kategori: buka satu, ganti gambar di kiri */
  var items = document.querySelectorAll('.acc-i'), pics = document.querySelectorAll('.svc-pic img');
  items.forEach(function (it) {
    it.querySelector('button').addEventListener('click', function () {
      items.forEach(function (x, j) {
        var on = x === it; x.classList.toggle('open', on);
        x.querySelector('button').setAttribute('aria-expanded', on);
        if (pics[j]) pics[j].classList.toggle('on', on);
      });
    });
  });
})();

/* ========== ANIMASI SITUS (versi ringan) ==========
   Prinsip: hanya animasikan yang terlihat, hanya ubah transform/opacity,
   dan jangan memicu hitung ulang gaya seluruh halaman saat menggulir. */
(function () {
  var reduce = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
  var root = document.documentElement;

  /* Bilah progres gulir + navbar + hero bergeser halus */
  var bar = document.createElement('div'); bar.className = 'scroll-progress'; bar.setAttribute('aria-hidden', 'true');
  document.body.appendChild(bar);
  var nav = document.querySelector('.navbar'), heroBox = document.querySelector('.hero > .container, .page-hero > .container');
  var ticking = false, scrolled = false, heroSeen = true, max = 0;
  function measure() { max = root.scrollHeight - window.innerHeight; }
  function onScroll() {
    var y = window.pageYOffset || root.scrollTop;
    bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
    var s = y > 20;
    if (nav && s !== scrolled) { scrolled = s; nav.classList.toggle('scrolled', s); }
    if (heroBox && !reduce) {
      if (y < 1200) { heroSeen = true; heroBox.style.translate = '0 ' + (y * 0.12).toFixed(1) + 'px'; heroBox.style.opacity = Math.max(0, 1 - y / 900).toFixed(3); }
      else if (heroSeen) { heroSeen = false; heroBox.style.translate = '0 ' + (1200 * 0.12) + 'px'; heroBox.style.opacity = 0; }
    }
    ticking = false;
  }
  window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
  window.addEventListener('resize', function () { measure(); onScroll(); });
  window.addEventListener('load', function () { measure(); onScroll(); });
  measure(); onScroll();

  if (reduce) return;

  /* Jeda semua animasi dekoratif saat tab tidak aktif */
  document.addEventListener('visibilitychange', function () { root.classList.toggle('js-paused', document.hidden); });

  /* Daun dan titik cahaya di hero: jumlah menyesuaikan perangkat, dijeda saat hero di luar layar */
  var weak = (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4) || (navigator.deviceMemory && navigator.deviceMemory <= 4);
  var small = window.innerWidth < 700;
  var nLeaf = weak ? (small ? 3 : 6) : (small ? 5 : 10), nDot = weak ? 3 : (small ? 4 : 7);
  var heroes = document.querySelectorAll('.hero, .page-hero');
  heroes.forEach(function (h) {
    var fx = document.createElement('div'); fx.className = 'fx'; fx.setAttribute('aria-hidden', 'true');
    var frag = document.createDocumentFragment(), i, el;
    for (i = 0; i < nLeaf; i++) {
      el = document.createElement('span'); el.className = 'leaf' + (i % 4 === 0 ? ' o' : '');
      el.style.left = (Math.random() * 100) + '%';
      el.style.setProperty('--s', (12 + Math.random() * 16) + 'px');
      el.style.setProperty('--d', (13 + Math.random() * 12) + 's');
      el.style.setProperty('--dl', '-' + (Math.random() * 20) + 's');
      frag.appendChild(el);
    }
    for (i = 0; i < nDot; i++) {
      el = document.createElement('span'); el.className = 'dot';
      el.style.left = (Math.random() * 100) + '%';
      el.style.setProperty('--s', (3 + Math.random() * 4) + 'px');
      el.style.setProperty('--d', (9 + Math.random() * 8) + 's');
      el.style.setProperty('--dl', '-' + (Math.random() * 14) + 's');
      frag.appendChild(el);
    }
    fx.appendChild(frag);
    h.insertBefore(fx, h.firstChild);
  });

  if ('IntersectionObserver' in window) {
    var ho = new IntersectionObserver(function (en) {
      en.forEach(function (x) { x.target.classList.toggle('fx-off', !x.isIntersecting); });
    }, { threshold: 0 });
    heroes.forEach(function (h) { ho.observe(h); });

    /* Kilau label "kicker" hanya jalan saat terlihat */
    var ko = new IntersectionObserver(function (en) {
      en.forEach(function (x) { x.target.classList.toggle('on', x.isIntersecting); });
    }, { threshold: 0 });
    document.querySelectorAll('.kicker').forEach(function (k) { ko.observe(k); });

    /* Penghitung angka naik */
    var stats = document.querySelectorAll('.stat b');
    if (stats.length) {
      var so = new IntersectionObserver(function (en) {
        en.forEach(function (x) {
          if (!x.isIntersecting) return;
          var b = x.target, end = parseInt(b.textContent, 10), t0 = null;
          if (isNaN(end)) return;
          so.unobserve(b); b.textContent = '0';
          (function step(t) {
            if (t0 === null) t0 = t;
            var p = Math.min((t - t0) / 1400, 1), e = 1 - Math.pow(1 - p, 3);
            b.textContent = Math.round(end * e);
            if (p < 1) requestAnimationFrame(step);
          })(performance.now());
        });
      }, { threshold: 0.6 });
      stats.forEach(function (b) { so.observe(b); });
    }
  }

  /* Kartu artikel muncul bergantian */
  document.querySelectorAll('.arts').forEach(function (g) {
    g.querySelectorAll('.art.rv').forEach(function (a, i) { a.style.transitionDelay = (i % 3) * 0.12 + 's'; });
  });
})();
