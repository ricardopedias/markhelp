/* 
---------------------------------------------------------------------------------------------
Este arquivo é responsável pela compilação dos scripts sass e javascript
usando laravel mix. Ex: 
    $ npm run dev 
    ou 
    $npm run prod
---------------------------------------------------------------------------------------------
*/

let mix = require("laravel-mix");

mix.disableNotifications();
mix.js("src/themes/default/resources/js/theme.js', 'src/themes/default/assets/scripts.js");
mix.sass("src/themes/default/resources/scss/theme.scss', 'src/themes/default/assets/styles.css");
