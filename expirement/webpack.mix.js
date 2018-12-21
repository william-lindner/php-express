let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

// eslint-disable-next-line no-sync
mix
  .setPublicPath('/')
  .setResourceRoot('website/root/includes')
  .js(
    'resources/js/navigation/app.js',
    'website/root/includes/js/navigation.js'
  )
  .js('resources/js/schedule/App.vue', 'website/root/includes/js/schedule.js')
  .sass('resources/sass/main.scss', 'website/root/includes/css/mentoring.css')
  .options({
    processCssUrls: false
  })
  .copyDirectory('resources/images', 'website/root/includes/images')
  .copyDirectory('resources/fonts', 'website/root/includes/fonts')
  .sourceMaps()
  .version()
  .browserSync({
    proxy: 'localhost',
    port: 8888,
    files: ['website/root/includes/css/mentoring.css']
  });
