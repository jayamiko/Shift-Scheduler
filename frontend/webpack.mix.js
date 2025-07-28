const mix = require("laravel-mix");
const path = require("path");
const tailwindcss = require("tailwindcss");

mix
  .js("resources/js/app.js", "public/js")
  .vue({ version: 3 })
  .options({
    processCssUrls: false,
    postCss: [tailwindcss("./tailwind.config.js")],
  })
  .webpackConfig({
    resolve: {
      alias: {
        "@": path.resolve("resources/js"),
      },
    },
  })
  .browserSync({
    proxy: false,
    server: "public",
    port: 3001,
    open: true,
    notify: false,
  });
