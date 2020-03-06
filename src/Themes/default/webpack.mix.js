/* 
---------------------------------------------------------------------------------------------
Este arquivo é responsável pela compilação dos scripts sass e javascript
usando laravel mix. Ex: 
    $ npm run dev 
    ou 
    $ npm run prod
---------------------------------------------------------------------------------------------
*/

let mix = require("laravel-mix");

mix.disableNotifications();
mix.js("resources/js/theme.js", "assets/scripts.js");
mix.sass("resources/scss/theme.scss", "assets/styles.css");
