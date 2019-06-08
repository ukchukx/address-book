let mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .extract(Object.keys(require('./package.json').dependencies || []))
   .sass('resources/sass/app.scss', 'public/css')
   .version();
