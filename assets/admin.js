import './styles/app.css';
import './bootstrap.js';

document.addEventListener('click', function (event) {

    const toggle = event.target.closest('td[data-column="une"] input[type="checkbox"]');

    if (!toggle) {
        return;
    }
    setTimeout(() => {
        window.location.reload();
    }, 200);
});