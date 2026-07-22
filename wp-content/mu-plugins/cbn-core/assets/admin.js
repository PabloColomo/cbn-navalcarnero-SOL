(() => {
  "use strict";

  document.querySelectorAll("[data-cbn-native-media]").forEach((field) => {
    const input = field.querySelector("[data-cbn-native-media-input]");
    const preview = field.querySelector("[data-cbn-native-media-preview]");
    const selectButton = field.querySelector("[data-cbn-native-media-select]");
    const removeButton = field.querySelector("[data-cbn-native-media-remove]");

    if (
      !input ||
      !preview ||
      !selectButton ||
      !removeButton ||
      !window.wp?.media
    ) {
      return;
    }

    selectButton.addEventListener("click", () => {
      const frame = window.wp.media({
        title: "Elegir imagen CBN",
        button: { text: "Usar esta imagen" },
        library: { type: "image" },
        multiple: false,
      });

      frame.on("select", () => {
        const attachment = frame.state().get("selection").first().toJSON();
        const source = attachment.sizes?.medium?.url || attachment.url;

        input.value = String(attachment.id);
        preview.replaceChildren();

        const image = document.createElement("img");
        image.src = source;
        image.alt = "";
        preview.append(image);
        removeButton.hidden = false;
      });

      frame.open();
    });

    removeButton.addEventListener("click", () => {
      input.value = "";
      preview.replaceChildren();
      removeButton.hidden = true;
    });
  });
})();
