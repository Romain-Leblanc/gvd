/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// import bootstrap
require('bootstrap');

// import jquery from 'jquery';
const $ = require('jquery');
// Use $ jQuery variable
global.$ = global.jQuery = $;

require('select2');

// select2-value-100 => max-witdh
// select2-value-50 => max-witdh depending on windows size
$('.select2-value-100').select2({
    language: 'fr',
    dropdownAutoWidth : true,
    width: '100%'
});
function resizeSelect2() {
    $('.select2-value-50').select2({
        language: 'fr',
        dropdownAutoWidth : true,
        width: document.querySelector('.input-50').scrollWidth+'px',
    });
}
window.onresize = function( event ) {
    resizeSelect2();
}
if (document.querySelector('.input-50') !== null || document.querySelector('.select2-value-50') !== null) {
    resizeSelect2();
}
