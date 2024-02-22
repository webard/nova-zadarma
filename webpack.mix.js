let mix = require("laravel-mix");
let path = require("path");

require("./nova.mix");

mix
  .setPublicPath("dist")
  .js("resources/js/asset.js", "js")
  .vue({ version: 3 })
  .postCss("resources/css/asset.css", "css", [require("tailwindcss")])
  .alias({
    "@": path.join(__dirname, "resources/js/"),
  })
  .nova("sylapi/nova-phone-call-modal");
