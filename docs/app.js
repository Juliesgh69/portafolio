/* ============================================================
   Julieta García — Portfolio  ·  interactions
   ============================================================ */
(function () {
  "use strict";

  /* ---------- Bilingual toggle ---------- */
  var lang = localStorage.getItem("jg_lang") || "es";
  var langBtns = document.querySelectorAll(".lang button");

  function applyLang(l) {
    lang = l;
    localStorage.setItem("jg_lang", l);
    document.documentElement.lang = l;
    document.querySelectorAll("[data-en]").forEach(function (el) {
      var val = el.getAttribute("data-" + l);
      if (val != null) {
        if (el.placeholder !== undefined && el.tagName === "INPUT" || el.tagName === "TEXTAREA") {
          // elements that use placeholder
          if (el.hasAttribute("data-ph")) { el.setAttribute("placeholder", val); return; }
        }
        el.innerHTML = val;
      }
    });
    document.querySelectorAll("[data-ph-en]").forEach(function (el) {
      el.setAttribute("placeholder", el.getAttribute("data-ph-" + l));
    });
    langBtns.forEach(function (b) {
      b.setAttribute("aria-pressed", String(b.dataset.lang === l));
    });
  }
  langBtns.forEach(function (b) {
    b.addEventListener("click", function () { applyLang(b.dataset.lang); });
  });
  applyLang(lang);

  /* ---------- Scroll reveals ---------- */
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add("in"); io.unobserve(e.target); }
    });
  }, { threshold: 0.12, rootMargin: "0px 0px -8% 0px" });
  document.querySelectorAll(".reveal").forEach(function (el) { io.observe(el); });

  /* ---------- Nav theme (dark over green sections) ---------- */
  var nav = document.querySelector(".nav");
  var darkSections = document.querySelectorAll("[data-dark]");
  var navIO = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting && e.intersectionRatio > 0.5) {
        nav.classList.add("is-dark");
      }
    });
    // recompute: dark if any dark section covers nav line
    var darkNow = false;
    darkSections.forEach(function (s) {
      var r = s.getBoundingClientRect();
      if (r.top <= 64 && r.bottom >= 64) darkNow = true;
    });
    nav.classList.toggle("is-dark", darkNow);
  }, { threshold: [0, 0.5, 1] });
  darkSections.forEach(function (s) { navIO.observe(s); });
  window.addEventListener("scroll", function () {
    var darkNow = false;
    darkSections.forEach(function (s) {
      var r = s.getBoundingClientRect();
      if (r.top <= 64 && r.bottom >= 64) darkNow = true;
    });
    nav.classList.toggle("is-dark", darkNow);
  }, { passive: true });

  /* ---------- Work filters ---------- */
  var filterBtns = document.querySelectorAll(".filter");
  var cards = document.querySelectorAll(".card");
  filterBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      filterBtns.forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
      btn.setAttribute("aria-pressed", "true");
      var f = btn.dataset.filter;
      cards.forEach(function (c) {
        var show = f === "all" || c.dataset.cat === f;
        c.classList.toggle("hide", !show);
      });
    });
  });

  /* ---------- Contact form ---------- */
  var form = document.getElementById("contactForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      form.classList.add("sent");
    });
  }

  /* ---------- Lightbox ---------- */
  (function () {
    // Crear el lightbox en el DOM
    var lb = document.createElement("div");
    lb.id = "lb";
    lb.innerHTML =
      '<button class="lb__close" aria-label="Cerrar">✕</button>' +
      '<div class="lb__wrap"><img class="lb__img" alt=""></div>' +
      '<span class="lb__hint">scroll para zoom · arrastra para mover · ESC para cerrar</span>';
    document.body.appendChild(lb);

    var lbImg  = lb.querySelector(".lb__img");
    var lbWrap = lb.querySelector(".lb__wrap");

    // Estado de zoom y pan
    var scale = 1, tx = 0, ty = 0;
    var dragging = false, dragX0, dragY0;

    function applyT(animate) {
      lbImg.style.transition = animate ? "transform .18s ease" : "none";
      lbImg.style.transform  = "translate(" + tx + "px," + ty + "px) scale(" + scale + ")";
    }

    function clampPan() {
      if (scale <= 1) { tx = 0; ty = 0; return; }
      var r   = lbImg.getBoundingClientRect();
      var maxX = Math.max(0, (r.width  * scale - window.innerWidth)  / 2 / scale);
      var maxY = Math.max(0, (r.height * scale - window.innerHeight) / 2 / scale);
      tx = Math.max(-maxX, Math.min(maxX, tx));
      ty = Math.max(-maxY, Math.min(maxY, ty));
    }

    function open(src, alt) {
      lbImg.src = src; lbImg.alt = alt || "";
      scale = 1; tx = 0; ty = 0;
      applyT(false);
      lb.classList.add("lb--open");
      document.body.style.overflow = "hidden";
    }

    function close() {
      lb.classList.remove("lb--open");
      document.body.style.overflow = "";
      setTimeout(function () { lbImg.src = ""; }, 280);
    }

    // Abrir al hacer click en cualquier imagen de tarjeta
    document.querySelectorAll(".card__media img").forEach(function (img) {
      img.addEventListener("click", function (e) {
        e.stopPropagation();
        open(img.src, img.alt);
      });
    });

    // Cerrar
    lb.querySelector(".lb__close").addEventListener("click", close);
    lb.addEventListener("click", function (e) {
      if (e.target === lb || e.target === lbWrap) close();
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && lb.classList.contains("lb--open")) close();
    });

    // Zoom con rueda del mouse (centrado en cursor)
    lbWrap.addEventListener("wheel", function (e) {
      e.preventDefault();
      var factor = e.deltaY > 0 ? 0.88 : 1.14;
      var newScale = Math.min(5, Math.max(0.5, scale * factor));

      // Ajustar traducción para que el zoom sea hacia el cursor
      var rect = lbImg.getBoundingClientRect();
      var cx   = e.clientX - (rect.left + rect.width  / 2);
      var cy   = e.clientY - (rect.top  + rect.height / 2);
      tx += cx * (1 - newScale / scale);
      ty += cy * (1 - newScale / scale);
      scale = newScale;
      clampPan();
      applyT(false);
      lbWrap.style.cursor = scale > 1 ? "grab" : "zoom-in";
    }, { passive: false });

    // Doble click: zoom 2× / reset
    lbImg.addEventListener("dblclick", function (e) {
      e.stopPropagation();
      if (scale !== 2) {
        scale = 2;
      } else {
        scale = 1; tx = 0; ty = 0;
      }
      applyT(true);
      lbWrap.style.cursor = scale > 1 ? "grab" : "zoom-in";
    });

    // Arrastrar para desplazar (solo si hay zoom)
    lbImg.addEventListener("mousedown", function (e) {
      if (scale <= 1 || e.button !== 0) return;
      dragging = true;
      dragX0 = e.clientX - tx;
      dragY0 = e.clientY - ty;
      lb.classList.add("lb--dragging");
      e.preventDefault();
    });
    document.addEventListener("mousemove", function (e) {
      if (!dragging) return;
      tx = e.clientX - dragX0;
      ty = e.clientY - dragY0;
      clampPan();
      applyT(false);
    });
    document.addEventListener("mouseup", function () {
      if (!dragging) return;
      dragging = false;
      lb.classList.remove("lb--dragging");
    });

    // Pinch-to-zoom en táctil
    var pinch0 = 0, scale0 = 1;
    lbWrap.addEventListener("touchstart", function (e) {
      if (e.touches.length === 2) {
        pinch0  = Math.hypot(
          e.touches[0].clientX - e.touches[1].clientX,
          e.touches[0].clientY - e.touches[1].clientY
        );
        scale0 = scale;
      }
    }, { passive: true });
    lbWrap.addEventListener("touchmove", function (e) {
      if (e.touches.length === 2) {
        e.preventDefault();
        var dist = Math.hypot(
          e.touches[0].clientX - e.touches[1].clientX,
          e.touches[0].clientY - e.touches[1].clientY
        );
        scale = Math.min(5, Math.max(0.5, scale0 * (dist / pinch0)));
        clampPan();
        applyT(false);
      }
    }, { passive: false });
  })();

  /* ---------- Subtle parallax on hero blobs ---------- */
  var blobs = document.querySelectorAll(".blob");
  if (!matchMedia("(prefers-reduced-motion: reduce)").matches) {
    window.addEventListener("scroll", function () {
      var y = window.scrollY;
      blobs.forEach(function (b, i) {
        b.style.transform = "translateY(" + (y * (0.06 + i * 0.03)) + "px)";
      });
    }, { passive: true });
  }
})();
