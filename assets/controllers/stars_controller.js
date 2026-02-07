import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { value: Number };

    connect() {
        this.update(this.value || 0);
    }

    set(event) {
        const value = event.params.index;
        this.value = value;
        this.update(value);
    }

    update(value) {
        const stars = this.element.querySelectorAll('.star');
        stars.forEach((star, index) => {
            star.classList.toggle('text-yellow-400', index < value);
            star.classList.toggle('text-gray-300', index >= value);
        });

        const input = this.element.querySelector('input[type="hidden"]');
        if (input) {
            input.value = value;
        }
    }
}
