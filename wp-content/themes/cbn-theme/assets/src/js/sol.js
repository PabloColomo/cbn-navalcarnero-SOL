(() => {
  "use strict";

  const config = window.cbnSolConfig || {};
  const root = document.querySelector("[data-cbn-sol]");
  const reducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;
  const finePointer = window.matchMedia(
    "(hover: hover) and (pointer: fine)",
  ).matches;
  const forcedColors = window.matchMedia("(forced-colors: active)").matches;

  const initFallbackNavigation = () => {
    if (config.mainBundle) {
      return;
    }

    const navToggle = document.querySelector("[data-cbn-nav-toggle]");
    const siteHeader = document.querySelector("[data-cbn-header]");
    const primaryNav = document.getElementById("cbn-primary-nav");

    if (!navToggle || !siteHeader || !primaryNav) {
      return;
    }

    const setOpen = (open) => {
      navToggle.setAttribute("aria-expanded", open ? "true" : "false");
      siteHeader.classList.toggle("is-nav-open", open);
      document.documentElement.classList.toggle("cbn-nav-open", open);
    };

    navToggle.addEventListener("click", () => {
      setOpen(navToggle.getAttribute("aria-expanded") !== "true");
    });

    primaryNav.addEventListener("click", (event) => {
      if (event.target.closest("a")) {
        setOpen(false);
      }
    });

    document.addEventListener("keydown", (event) => {
      if (
        event.key === "Escape" &&
        navToggle.getAttribute("aria-expanded") === "true"
      ) {
        setOpen(false);
        navToggle.focus();
      }
    });

    document.addEventListener("pointerdown", (event) => {
      if (
        navToggle.getAttribute("aria-expanded") === "true" &&
        !event.target.closest("[data-cbn-header]")
      ) {
        setOpen(false);
      }
    });
  };

  const initReveals = () => {
    if (!root) {
      return;
    }

    document.documentElement.classList.add("cbn-sol-ready");
    const elements = [...root.querySelectorAll("[data-sol-reveal]")];

    if (reducedMotion || !("IntersectionObserver" in window)) {
      elements.forEach((element) => element.classList.add("is-visible"));
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        });
      },
      { rootMargin: "0px 0px -8%", threshold: 0.12 },
    );

    elements.forEach((element) => observer.observe(element));
  };

  const initRoute = () => {
    if (!root || reducedMotion) {
      return;
    }

    const path = root.querySelector("[data-cbn-route]");
    const ball = root.querySelector("[data-cbn-route-ball]");
    const svg = path?.ownerSVGElement;

    if (!path || !ball || !svg || typeof path.getTotalLength !== "function") {
      return;
    }

    const length = path.getTotalLength();
    path.style.strokeDasharray = `${length}`;
    path.style.strokeDashoffset = `${length}`;
    let ticking = false;

    const render = () => {
      const available = Math.max(
        1,
        document.documentElement.scrollHeight - window.innerHeight,
      );
      const progress = Math.min(1, Math.max(0, window.scrollY / available));
      const point = path.getPointAtLength(length * progress);
      const viewBox = svg.viewBox.baseVal;

      path.style.strokeDashoffset = `${length * (1 - progress)}`;
      ball.style.left = `${((point.x - viewBox.x) / viewBox.width) * 100}%`;
      ball.style.top = `${((point.y - viewBox.y) / viewBox.height) * 100}%`;
      ball.style.transform = `translate(-50%, -50%) rotate(${progress * 1080}deg)`;
      ticking = false;
    };

    const requestRender = () => {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(render);
      }
    };

    window.addEventListener("scroll", requestRender, { passive: true });
    window.addEventListener("resize", requestRender);
    render();
  };

  const initTeamFilters = () => {
    if (!root) {
      return;
    }

    const filters = [...root.querySelectorAll("[data-cbn-team-filter]")];
    const cards = [...root.querySelectorAll("[data-cbn-team]")];

    filters.forEach((filter) => {
      filter.addEventListener("click", () => {
        const value = filter.dataset.cbnTeamFilter;

        filters.forEach((item) => {
          const active = item === filter;
          item.classList.toggle("is-active", active);
          item.setAttribute("aria-pressed", active ? "true" : "false");
        });

        cards.forEach((card) => {
          const visible = value === "all" || card.dataset.cbnTeam === value;
          card.classList.toggle("is-filtered-out", !visible);
        });
      });
    });
  };

  const initPointerEffects = () => {
    if (!root || !finePointer || reducedMotion) {
      return;
    }

    root.querySelectorAll("[data-cbn-tilt]").forEach((element) => {
      element.addEventListener("pointermove", (event) => {
        const rect = element.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;
        element.style.setProperty("--sol-tilt-x", `${x * 3.4}deg`);
        element.style.setProperty("--sol-tilt-y", `${y * -3.4}deg`);
      });

      element.addEventListener("pointerleave", () => {
        element.style.setProperty("--sol-tilt-x", "0deg");
        element.style.setProperty("--sol-tilt-y", "0deg");
      });
    });

    root.querySelectorAll("[data-cbn-parallax]").forEach((element) => {
      element.addEventListener("pointermove", (event) => {
        const rect = element.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;
        element.style.setProperty("--sol-parallax-x", `${x * 8}px`);
        element.style.setProperty("--sol-parallax-y", `${y * 8}px`);
      });

      element.addEventListener("pointerleave", () => {
        element.style.setProperty("--sol-parallax-x", "0px");
        element.style.setProperty("--sol-parallax-y", "0px");
      });
    });
  };

  const initShotClock = () => {
    const clock = document.querySelector("[data-cbn-shot-clock]");

    if (!clock || reducedMotion) {
      return;
    }

    let value = 24;
    window.setInterval(() => {
      value = value <= 0 ? 24 : value - 1;
      clock.textContent = String(value).padStart(2, "0");
    }, 1000);
  };

  const initCursor = () => {
    if (!finePointer || reducedMotion || forcedColors) {
      return;
    }

    const logoUrl =
      config.logoUrl || document.querySelector(".cbn-brand-mark")?.currentSrc;

    if (!logoUrl) {
      return;
    }

    const cursor = document.createElement("span");
    cursor.className = "cbn-custom-cursor";
    cursor.setAttribute("aria-hidden", "true");
    const image = document.createElement("img");
    image.src = logoUrl;
    image.alt = "";
    cursor.append(image);
    document.body.append(cursor);
    document.documentElement.classList.add("cbn-cursor-active");

    let x = -100;
    let y = -100;
    let frame = 0;

    const render = () => {
      cursor.style.transform = `translate3d(${x}px, ${y}px, 0) translate(-50%, -50%)`;
      frame = 0;
    };

    document.addEventListener(
      "pointermove",
      (event) => {
        x = event.clientX;
        y = event.clientY;
        cursor.classList.add("is-visible");
        cursor.classList.toggle(
          "is-active",
          Boolean(event.target.closest("a, button, [role='button']")),
        );

        if (!frame) {
          frame = window.requestAnimationFrame(render);
        }
      },
      { passive: true },
    );

    document.addEventListener("pointerdown", () => {
      cursor.classList.add("is-down");
    });
    document.addEventListener("pointerup", () => {
      cursor.classList.remove("is-down");
    });
    document.addEventListener("pointerout", (event) => {
      if (!event.relatedTarget) {
        cursor.classList.remove("is-visible");
      }
    });
  };

  const initCourtSound = () => {
    const toggles = [
      ...document.querySelectorAll(
        "[data-cbn-sound-toggle], [data-cbn-sound-secondary]",
      ),
    ];

    if (!toggles.length) {
      return;
    }

    const AudioContextClass = window.AudioContext || window.webkitAudioContext;

    if (!AudioContextClass) {
      toggles.forEach((toggle) => {
        toggle.disabled = true;
        toggle.setAttribute("aria-pressed", "false");
        toggle.setAttribute(
          "aria-label",
          "Los sonidos de pista no están disponibles en este navegador",
        );
      });
      return;
    }

    let enabled = sessionStorage.getItem("cbn-court-sound") === "on";
    let audioContext = null;
    let noiseBuffer = null;
    let lastStepAt = 0;
    let lastScrollY = window.scrollY;
    let pointerDistance = 0;
    let lastPointer = null;
    let stepSide = -1;

    const syncToggles = () => {
      toggles.forEach((toggle) => {
        toggle.setAttribute("aria-pressed", enabled ? "true" : "false");
        const state = toggle.querySelector("[data-cbn-sound-state]");
        if (state) {
          state.textContent = enabled ? "ON" : "OFF";
        }
      });
      document.documentElement.classList.toggle("cbn-sound-on", enabled);
    };

    const ensureContext = async () => {
      if (!AudioContextClass) {
        return null;
      }

      if (!audioContext) {
        audioContext = new AudioContextClass();
        const frameCount = Math.ceil(audioContext.sampleRate * 1.2);
        noiseBuffer = audioContext.createBuffer(
          1,
          frameCount,
          audioContext.sampleRate,
        );
        const data = noiseBuffer.getChannelData(0);
        for (let index = 0; index < frameCount; index += 1) {
          data[index] = Math.random() * 2 - 1;
        }
      }

      if (audioContext.state === "suspended") {
        await audioContext.resume();
      }

      return audioContext;
    };

    const connectWithPan = (node, panValue = 0) => {
      if (typeof audioContext.createStereoPanner !== "function") {
        node.connect(audioContext.destination);
        return;
      }

      const panner = audioContext.createStereoPanner();
      panner.pan.value = panValue;
      node.connect(panner);
      panner.connect(audioContext.destination);
    };

    const playStep = async () => {
      if (!enabled || Date.now() - lastStepAt < 420) {
        return;
      }

      const context = await ensureContext();
      if (!context || !noiseBuffer) {
        return;
      }

      lastStepAt = Date.now();
      stepSide *= -1;
      const now = context.currentTime;
      const variation = 0.92 + Math.random() * 0.16;

      const squeak = context.createOscillator();
      const squeakGain = context.createGain();
      const squeakFilter = context.createBiquadFilter();
      squeak.type = "triangle";
      squeak.frequency.setValueAtTime(1280 * variation, now);
      squeak.frequency.exponentialRampToValueAtTime(710 * variation, now + 0.095);
      squeakFilter.type = "bandpass";
      squeakFilter.frequency.value = 1250 * variation;
      squeakFilter.Q.value = 7;
      squeakGain.gain.setValueAtTime(0.0001, now);
      squeakGain.gain.exponentialRampToValueAtTime(0.035, now + 0.008);
      squeakGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.13);
      squeak.connect(squeakFilter);
      squeakFilter.connect(squeakGain);
      connectWithPan(squeakGain, stepSide * 0.24);
      squeak.start(now);
      squeak.stop(now + 0.14);

      const impact = context.createBufferSource();
      const impactFilter = context.createBiquadFilter();
      const impactGain = context.createGain();
      impact.buffer = noiseBuffer;
      impactFilter.type = "lowpass";
      impactFilter.frequency.value = 430 * variation;
      impactGain.gain.setValueAtTime(0.025, now);
      impactGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.075);
      impact.connect(impactFilter);
      impactFilter.connect(impactGain);
      connectWithPan(impactGain, stepSide * 0.3);
      impact.start(now);
      impact.stop(now + 0.08);
    };

    const playSwish = async () => {
      if (!enabled) {
        return;
      }

      const context = await ensureContext();
      if (!context || !noiseBuffer) {
        return;
      }

      const now = context.currentTime;
      const source = context.createBufferSource();
      const highpass = context.createBiquadFilter();
      const bandpass = context.createBiquadFilter();
      const gain = context.createGain();
      source.buffer = noiseBuffer;
      highpass.type = "highpass";
      highpass.frequency.setValueAtTime(720, now);
      highpass.frequency.exponentialRampToValueAtTime(1900, now + 0.24);
      bandpass.type = "bandpass";
      bandpass.frequency.setValueAtTime(3400, now);
      bandpass.frequency.exponentialRampToValueAtTime(1750, now + 0.28);
      bandpass.Q.value = 0.72;
      gain.gain.setValueAtTime(0.0001, now);
      gain.gain.exponentialRampToValueAtTime(0.12, now + 0.028);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.34);
      source.connect(highpass);
      highpass.connect(bandpass);
      bandpass.connect(gain);
      gain.connect(context.destination);
      source.start(now);
      source.stop(now + 0.36);

      [0, 0.045, 0.09].forEach((delay, index) => {
        const string = context.createOscillator();
        const stringGain = context.createGain();
        string.type = "sine";
        string.frequency.setValueAtTime(920 + index * 180, now + delay);
        string.frequency.exponentialRampToValueAtTime(
          620 + index * 120,
          now + delay + 0.12,
        );
        stringGain.gain.setValueAtTime(0.0001, now + delay);
        stringGain.gain.exponentialRampToValueAtTime(
          0.016,
          now + delay + 0.008,
        );
        stringGain.gain.exponentialRampToValueAtTime(
          0.0001,
          now + delay + 0.14,
        );
        string.connect(stringGain);
        stringGain.connect(context.destination);
        string.start(now + delay);
        string.stop(now + delay + 0.15);
      });

      const ball = context.createOscillator();
      const ballGain = context.createGain();
      ball.type = "sine";
      ball.frequency.setValueAtTime(105, now + 0.14);
      ball.frequency.exponentialRampToValueAtTime(58, now + 0.24);
      ballGain.gain.setValueAtTime(0.0001, now + 0.14);
      ballGain.gain.exponentialRampToValueAtTime(0.035, now + 0.155);
      ballGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.26);
      ball.connect(ballGain);
      ballGain.connect(context.destination);
      ball.start(now + 0.14);
      ball.stop(now + 0.27);
    };

    const toggleSound = async () => {
      enabled = !enabled;
      sessionStorage.setItem("cbn-court-sound", enabled ? "on" : "off");
      syncToggles();

      if (enabled) {
        await ensureContext();
        window.setTimeout(playSwish, 35);
      }
    };

    toggles.forEach((toggle) => toggle.addEventListener("click", toggleSound));

    document.addEventListener(
      "pointerdown",
      (event) => {
        if (
          enabled &&
          event.target.closest("a, button") &&
          !event.target.closest(
            "[data-cbn-sound-toggle], [data-cbn-sound-secondary]",
          )
        ) {
          playSwish();
        }
      },
      true,
    );

    document.addEventListener(
      "keydown",
      (event) => {
        if (
          enabled &&
          !event.repeat &&
          (event.key === "Enter" || event.key === " ") &&
          event.target.closest("a, button") &&
          !event.target.closest(
            "[data-cbn-sound-toggle], [data-cbn-sound-secondary]",
          )
        ) {
          playSwish();
        }
      },
      true,
    );

    window.addEventListener(
      "scroll",
      () => {
        const distance = Math.abs(window.scrollY - lastScrollY);
        if (distance >= 240) {
          lastScrollY = window.scrollY;
          playStep();
        }
      },
      { passive: true },
    );

    if (finePointer) {
      document.addEventListener(
        "pointermove",
        (event) => {
          if (!enabled) {
            lastPointer = { x: event.clientX, y: event.clientY };
            return;
          }

          if (lastPointer) {
            pointerDistance += Math.hypot(
              event.clientX - lastPointer.x,
              event.clientY - lastPointer.y,
            );
          }
          lastPointer = { x: event.clientX, y: event.clientY };

          if (pointerDistance > 175) {
            pointerDistance = 0;
            playStep();
          }
        },
        { passive: true },
      );
    }

    if (enabled) {
      const unlock = () => {
        ensureContext();
        document.removeEventListener("pointerdown", unlock, true);
        document.removeEventListener("keydown", unlock, true);
      };
      document.addEventListener("pointerdown", unlock, true);
      document.addEventListener("keydown", unlock, true);
    }

    syncToggles();
  };

  initFallbackNavigation();
  initReveals();
  initRoute();
  initTeamFilters();
  initPointerEffects();
  initShotClock();
  initCursor();
  initCourtSound();
})();
