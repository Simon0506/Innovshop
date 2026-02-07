import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.updatePreview();
    }

    updatePreview() {
        const rows = this.element.querySelectorAll('.field-collection-item');
        for (const row of rows) {
            const input = row.querySelector('input[type="file"]');
            if (input) {
                const img = document.createElement('img');
                img.src = input.placeholder ? `/uploads/${input.placeholder}` : '';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                row.appendChild(img);
            }
        }
    }
}