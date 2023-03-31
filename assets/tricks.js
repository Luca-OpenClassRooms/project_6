
const template = document.querySelector('#trick-template');
const btn = document.querySelector('#load-more');
const loading = document.querySelector('#loading');
const goto = document.querySelector('#goto-tricks');

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
                html = html.replace(/{url}/g, "/tricks/" + trick.slug);
                html = html.replace(/{editUrl}/g, "/tricks/" + trick.slug + "/edit");

                let background = trick.medias.find(media => media.type == 'image')

                if( !background ) 
                    background = 'https://placehold.co/300x300'
                else
                    background = background.content;

                html = html.replace(/{background}/g, background);

                clone.innerHTML = html;
                clone.style.display = 'block';

                const parent = template.parentElement;
                const node = parent.appendChild(clone);

                const ifAuth = node.querySelector('[data-if="isAuth"]');

                if( !window.isAuth ) {
                    ifAuth.remove();
                }
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
    
    if( goto ) {
        if( window.scrollY >= parent.offsetTop ) {
            goto.style.display = 'block';
        } else {
            goto.style.display = 'none';
        }
    }
});