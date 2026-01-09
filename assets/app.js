import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';


document.addEventListener('click', function (event) {
    const cart = event.target.closest('#cart');

    if (!cart) {
        return;
    }
        
    const cartMenu = document.getElementById('cartMenu');
    const accountMenu = document.getElementById('accountMenu');

    cartMenu.classList.toggle('hidden');
    if (!accountMenu.classList.contains('hidden')){
        accountMenu.classList.add('hidden');
    }
});

document.addEventListener('click', function (event) {
    const account = event.target.closest('#account');

    if (!account) {
        return;
    }
    
    const cartMenu = document.getElementById('cartMenu');
    const accountMenu = document.getElementById('accountMenu');

    accountMenu.classList.toggle('hidden');
    if (!cartMenu.classList.contains('hidden')) {
        cartMenu.classList.add('hidden');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.add-to-cart-form');

    forms.forEach(form => {
        form.addEventListener('submit', e => {
            e.preventDefault();

            const data = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: data,
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(() => {
                const cartMenu = document.getElementById('cartMenu');
                if (cartMenu) {
                    cartMenu.classList.remove('hidden');
                }
            })
            .catch(err => console.error(err));
        });
    });
});
