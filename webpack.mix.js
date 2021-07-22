const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
	.extract(['vue'])

mix.sass('resources/sass/main.scss', 'public/css')
	.sass('resources/sass/style.scss', 'public/css')
	.sass('resources/sass/view.scss', 'public/css')

mix.copy('resources/js/worker.js', 'public/js/worker/worker.js')
	.copy('resources/js/jsmpeg.min.js', 'public/js/worker/jsmpeg.min.js')

if (!mix.inProduction()) {
	mix.sourceMaps()
} else {
	mix.version()
}