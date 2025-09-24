const mix = require('laravel-mix');

// Compile CSS files
mix.css('resources/css/sidebar.css', 'public/css')
   .css('resources/css/app.css', 'public/css');

// Compile JavaScript files
mix.js('resources/js/app.js', 'public/js');

// Enable versioning for production
if (mix.inProduction()) {
    mix.version();
}
