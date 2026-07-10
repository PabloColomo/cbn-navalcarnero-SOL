(() => {
  "use strict";

  const pages = [...document.querySelectorAll("[data-cbn-teams-page]")];

  if (!pages.length) {
    return;
  }

  const reducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  ).matches;
  const finePointer = window.matchMedia(
    "(hover: hover) and (pointer: fine)",
  ).matches;

  const initFilters = (page) => {
    const filters = [
      ...page.querySelectorAll("[data-cbn-team-archive-filter]"),
    ];
    const cards = [
      ...page.querySelectorAll("[data-cbn-team-archive-card]"),
    ];
    const status = page.querySelector("[data-cbn-team-filter-status]");

    if (!filters.length || !cards.length) {
      return;
    }

    const showFilter = (value) => {
      let visibleCount = 0;

      filters.forEach((filter) => {
        const active = filter.dataset.cbnTeamArchiveFilter === value;
        filter.classList.toggle("is-active", active);
        filter.setAttribute("aria-pressed", active ? "true" : "false");
      });

      cards.forEach((card, index) => {
        const groups = (card.dataset.cbnTeamGroups || "").split(/\s+/);
        const visible = value === "all" || groups.includes(value);
        card.hidden = !visible;

        if (!visible) {
          return;
        }

        visibleCount += 1;

        if (!reducedMotion && typeof card.animate === "function") {
          card.animate(
            [
              { opacity: 0, transform: "translateY(16px)" },
              { opacity: 1, transform: "translateY(0)" },
            ],
            {
              duration: 360,
              delay: Math.min(index, 5) * 35,
              easing: "cubic-bezier(.16, 1, .3, 1)",
            },
          );
        }
      });

      page.dataset.activeTeamFilter = value;

      if (status) {
        status.textContent = `${visibleCount} ${visibleCount === 1 ? "equipo" : "equipos"} en pista`;
      }
    };

    filters.forEach((filter) => {
      filter.addEventListener("click", () => {
        showFilter(filter.dataset.cbnTeamArchiveFilter || "all");
      });
    });
  };

  const initCardLight = (page) => {
    if (!finePointer || reducedMotion) {
      return;
    }

    page
      .querySelectorAll("[data-cbn-team-archive-card]")
      .forEach((card) => {
        card.addEventListener(
          "pointermove",
          (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty(
              "--cbn-team-light-x",
              `${((event.clientX - rect.left) / rect.width) * 100}%`,
            );
            card.style.setProperty(
              "--cbn-team-light-y",
              `${((event.clientY - rect.top) / rect.height) * 100}%`,
            );
          },
          { passive: true },
        );
      });
  };

  pages.forEach((page) => {
    initFilters(page);
    initCardLight(page);
  });
})();
