/* 
---------------------------------------------------------------------------------------------
Este script contém a estrutura do javascript a ser compilado para o tema.
---------------------------------------------------------------------------------------------
*/

require("html5shiv");
require("./sidebar");
const hljs = require("highlight.js");
hljs.initHighlightingOnLoad();

// para o MiniCssExtractPlugin encontrar os estilos
import "../scss/theme.scss";