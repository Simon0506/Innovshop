import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['track'];
    static values = {
        delay: { type: Number, default: 2000 }
    };

    connect() {
        this.index = 0;
        this.total = this.trackTarget.children.length;
        this.startAutoplay();
    }

    disconnect() {
        this.stopAutoplay();
    }

    next() {
        this.stopAutoplay();
        this.index = (this.index + 1) % this.total;
        this.update();
        this.startAutoplay();
    }

    prev() {
        this.stopAutoplay();
        this.index = (this.index - 1 + this.total) % this.total;
        this.update();
        this.startAutoplay();
    }

    update() {
        this.trackTarget.style.transform = `translateX(-${this.index * 100}%)`;
    }

    startAutoplay() {
        this.stopAutoplay();
        this.interval = setInterval(() => this.next(), this.delayValue);
    }

    stopAutoplay() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }
}
