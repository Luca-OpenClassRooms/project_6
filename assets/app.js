/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// load tricks
import './tricks';

document.addEventListener('DOMContentLoaded', function() {
    const btnsGoto = document.querySelectorAll('[data-goto]');

    btnsGoto.forEach(btn => {
        btn.addEventListener('click', function() {
            const goto = btn.getAttribute('data-goto');
            const target = document.querySelector(goto);

            if( target ) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});