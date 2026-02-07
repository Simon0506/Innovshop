import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['image', 'counter'];

    connect() {
        const firstImage = this.imageTargets[0];
        if (firstImage) {
            this.showImage(firstImage);
        }
    }
    showImage(image) {
        this.initializeImages();
        image.classList.remove('hidden');
    }
    initializeImages() {
        this.imageTargets.forEach((img) => {
            if (!img.classList.contains('hidden')) {
                img.classList.add('hidden');
            }
        });
    }
    next() {
        const currentImage = this.imageTargets.find(
            (img) => !img.classList.contains('hidden')
        );
        const currentIndex = this.imageTargets.indexOf(currentImage);
        const nextIndex =
            (currentIndex + 1) % this.imageTargets.length;
        this.showImage(this.imageTargets[nextIndex]);
        this.counterTarget.textContent = `${nextIndex + 1} / ${this.imageTargets.length}`;
    }
    prev() {
        const currentImage = this.imageTargets.find(
            (img) => !img.classList.contains('hidden')
        );
        const currentIndex = this.imageTargets.indexOf(currentImage);
        const prevIndex =
            (currentIndex - 1 + this.imageTargets.length) %
            this.imageTargets.length;
        this.showImage(this.imageTargets[prevIndex]);
        this.counterTarget.textContent = `${prevIndex + 1} / ${this.imageTargets.length}`;
    }
}