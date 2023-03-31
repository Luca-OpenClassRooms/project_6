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

// load comments
import './comments';

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

    const editForm = document.querySelector('form[name="trick_media_form"]');

    console.log(editForm);
    if( editForm ) {
        const nodeType = editForm.querySelector('#trick_media_form_type');

        const nodeUrl = editForm.querySelector('#trick_media_form_url');
        const nodeFile = editForm.querySelector('#trick_media_form_file');

        nodeUrl.parentElement.style.display = 'none';

        nodeType.addEventListener('change', function() {
            if( nodeType.value === 'iframe' ) {
                nodeUrl.parentElement.style.display = 'block';
                nodeFile.parentElement.style.display = 'none';
            } else {
                nodeUrl.parentElement.style.display = 'none';
                nodeFile.parentElement.style.display = 'block';
            }
        });
    }
});