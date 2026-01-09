import './styles/admin.scss';
import './stimulus_bootstrap.js';

document.addEventListener('click', function (event) {

    const toggle = event.target.closest('.form-switch');

    if (!toggle) {
        return;
    }
    setTimeout(() => {
        window.location.reload();
    }, 600);
});