import { defineConfig } from "vite";
import path from "node:path";

const themeDir = "wp-content/themes/cbn-theme";

export default defineConfig({
  base: `/${themeDir}/assets/dist/`,
  build: {
    manifest: true,
    emptyOutDir: true,
    outDir: `${themeDir}/assets/dist`,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, `${themeDir}/assets/src/js/main.js`),
      },
    },
  },
});
