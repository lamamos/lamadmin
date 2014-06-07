#!/bin/bash


find . \( -iname "*.js" ! -path "./vendor*" ! -path "./doc*" ! -iname "*.min.js" \) -print0 | xargs -0 -n1 js-beautify --replace --config cfg_js_beautify.json


