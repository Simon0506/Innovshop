import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['confirm']

    showConfirm() {
        this.confirmTarget.classList.remove('hidden');
    }

    hideConfirm() {
        this.confirmTarget.classList.add('hidden');
    }
}