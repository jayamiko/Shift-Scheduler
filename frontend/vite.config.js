import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [vue()],
    root: ".", // root project tetap di frontend/
    base: "/", // public path

    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
    },

    build: {
        outDir: "dist", // hasil build Vue
        emptyOutDir: true,
        sourcemap: true,
    },

    server: {
        host: "0.0.0.0", // agar bisa diakses dari container lain
        port: 3000,
        strictPort: true,
    },
});
