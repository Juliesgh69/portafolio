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
