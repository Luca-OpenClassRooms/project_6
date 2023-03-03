
const template = document.querySelector('#trick-template');
const btn = document.querySelector('#load-more');
const loading = document.querySelector('#loading');
const goto = document.querySelector('#goto-tricks');
const parent = template.parentElement;

let LOADING = false;
let PAGE = 1;
let LIMIT = 10;

function loadTricks() {
    if( LOADING ) 
        return;

    LOADING = true;


    console.log('loading tricks...');

    loading.style.display = 'flex';
    btn.style.display = 'none';

    fetch(`/tricks?limit=${LIMIT}&page=${PAGE}`)
        .then(response => response.json())
        .then(data => {
            data.data.forEach(trick => {
                let clone = template.cloneNode(true);
                let html = clone.innerHTML;

                html = html.replace(/{title}/g, trick.name);
                html = html.replace(/{slug}/g, trick.slug);

                clone.innerHTML = html;
                clone.style.display = 'block';
                parent.appendChild(clone);
                // parent.innerHTML += html;
            });

            if( data.data.length < LIMIT ) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'block';
            }
        })
        .catch(error => console.log(error))
        .finally(() => {
            LOADING = false;
            PAGE++;

            loading.style.display = 'none';
        })
}

if( template ) {
    loadTricks();

    btn.addEventListener('click', function() {
        loadTricks();
    });
}

window.addEventListener('scroll', function() {
    // if( window.scrollY + window.innerHeight >= document.body.offsetHeight ) {
    //     goto.style.display = 'block';
    // }

    // Check if the user has scrolled to the tricks section
    // if( window.scrollY + window.innerHeight >= parent.offsetTop ) {
    //     goto.style.display = 'none';
    //     console.log('scrolling to tricks...');
    // }

    // if( window.scrollY <= parent.offsetTop ) {
    //     goto.style.display = 'none';
    // }
    // Check if scroll is higher than the tricks section
    
    if( window.scrollY >= parent.offsetTop ) {
        goto.style.display = 'block';
    } else {
        goto.style.display = 'none';
    }
});