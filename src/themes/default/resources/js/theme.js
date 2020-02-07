/* 
---------------------------------------------------------------------------------------------
Este script contém a estrutura do javascript a ser compilado para o tema.
---------------------------------------------------------------------------------------------
*/
try {

    window.$ = window.jQuery = require("jquery");

} catch (e) {
    console.log(e);
}

require("html5shiv");
require("./sidebar");

const hljs = require("highlight.js");
hljs.initHighlightingOnLoad();