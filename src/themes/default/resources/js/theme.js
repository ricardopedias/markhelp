
try {

    window.$ = window.jQuery = require('jquery');

} catch (e) {}

require('html5shiv');
require('./sidebar');

const hljs = require('highlight.js');
hljs.initHighlightingOnLoad();