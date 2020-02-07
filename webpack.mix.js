let mix = require('laravel-mix');

mix.disableNotifications();
mix.js('src/themes/default/resources/js/theme.js', 'src/themes/default/assets/scripts.js');
mix.sass('src/themes/default/resources/scss/theme.scss', 'src/themes/default/assets/styles.css');
