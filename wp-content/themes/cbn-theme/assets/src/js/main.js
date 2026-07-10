import "swiper/css";
import "swiper/css/navigation";
import "../css/main.css";
import Lenis from "lenis";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import Swiper from "swiper";
import { A11y, Navigation } from "swiper/modules";

gsap.registerPlugin(ScrollTrigger);

const navToggle = document.querySelector("[data-cbn-nav-toggle]");
const siteHeader = document.querySelector("[data-cbn-header]");
const primaryNav = document.getElementById("cbn-primary-nav");

if (navToggle && siteHeader && primaryNav) {
  const desktopNav = window.matchMedia("(min-width: 1181px)");

  const setNavOpen = (open) => {
    navToggle.setAttribute("aria-expanded", open ? "true" : "false");
    siteHeader.classList.toggle("is-nav-open", open);
    document.documentElement.classList.toggle("cbn-nav-open", open);
  };

  navToggle.addEventListener("click", () => {
    setNavOpen(navToggle.getAttribute("aria-expanded") !== "true");
  });

  primaryNav.addEventListener("click", (event) => {
    if (event.target.closest("a")) {
      setNavOpen(false);
    }
  });

  document.addEventListener("keydown", (event) => {
    if (
      event.key === "Escape" &&
      navToggle.getAttribute("aria-expanded") === "true"
    ) {
      setNavOpen(false);
      navToggle.focus();
    }
  });

  document.addEventListener("pointerdown", (event) => {
    if (
      navToggle.getAttribute("aria-expanded") === "true" &&
      !event.target.closest("[data-cbn-header]")
    ) {
      setNavOpen(false);
    }
  });

  desktopNav.addEventListener("change", (event) => {
    if (event.matches) {
      setNavOpen(false);
    }
  });
}

const prefersReducedMotion = window.matchMedia(
  "(prefers-reduced-motion: reduce)",
).matches;

const hasFinePointer = window.matchMedia("(pointer: fine)").matches;

if (!prefersReducedMotion && hasFinePointer) {
  const lenis = new Lenis({
    duration: 0.95,
    smoothWheel: true,
  });

  lenis.on("scroll", ScrollTrigger.update);

  gsap.ticker.add((time) => {
    lenis.raf(time * 1000);
  });

  gsap.ticker.lagSmoothing(0);
}

if (!prefersReducedMotion) {
  const heroChildren = document.querySelectorAll("[data-cbn-hero] > *");

  if (heroChildren.length) {
    gsap.fromTo(
      heroChildren,
      { autoAlpha: 0, y: 24 },
      {
        autoAlpha: 1,
        y: 0,
        duration: 0.72,
        ease: "power2.out",
        stagger: 0.08,
      },
    );
  }

  gsap.utils.toArray("[data-cbn-reveal]").forEach((element) => {
    gsap.fromTo(
      element,
      { autoAlpha: 0, y: 30 },
      {
        autoAlpha: 1,
        y: 0,
        duration: 0.72,
        ease: "power2.out",
        scrollTrigger: {
          trigger: element,
          start: "top 84%",
          once: true,
        },
      },
    );
  });
}

document.querySelectorAll("[data-cbn-swiper]").forEach((element) => {
  const root = element.closest("[data-cbn-carousel]") || element;

  new Swiper(element, {
    modules: [A11y, Navigation],
    slidesPerView: 1.15,
    spaceBetween: 14,
    watchOverflow: true,
    navigation: {
      nextEl: root.querySelector("[data-cbn-swiper-next]"),
      prevEl: root.querySelector("[data-cbn-swiper-prev]"),
    },
    a11y: {
      enabled: true,
      nextSlideMessage: "Siguiente patrocinador",
      prevSlideMessage: "Patrocinador anterior",
    },
    breakpoints: {
      640: {
        slidesPerView: 2.2,
      },
      980: {
        slidesPerView: 4,
      },
    },
  });
});
