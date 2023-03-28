
const template = document.querySelector('#comment-template');
const btn = document.querySelector('#load-more');
const loading = document.querySelector('#loading');

let LOADING = false;
let PAGE = 1;
let LIMIT = 5;

function loadComments() {
    if( LOADING ) 
        return;

    LOADING = true;

    console.log('loading comments...');

    loading.style.display = 'flex';
    btn.style.display = 'none';

    const parent = template.parentElement;

    const slug = btn.getAttribute('data-slug');

    fetch(`/tricks/${slug}/comments?limit=${LIMIT}&page=${PAGE}`)
        .then(response => response.json())
        .then(data => {
            data.data.forEach(comment => {
                let clone = template.cloneNode(true);
                let html = clone.innerHTML;

                html = html.replace(/{content}/g, comment.content);
                html = html.replace(/{avatar}/g, comment.user.avatar);

                clone.innerHTML = html;
                clone.style.display = 'flex';
                parent.appendChild(clone);
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
    loadComments();

    btn.addEventListener('click', function() {
        loadComments();
    });
}