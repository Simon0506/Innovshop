import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.updatePreview();
        this.listenForChanges();
    }

    updatePreview() {
        const rows = this.element.querySelectorAll('.field-collection-item');

        for (const row of rows) {
            const input = row.querySelector('input[type="file"]');
            if (!input) continue;

            const label = row.querySelector('.custom-file-label');

            if (label.textContent !== 'Choisir un fichier') {
                this.createImage(row, `/uploads/${label.textContent}`);
            }
        }
    }

    listenForChanges() {
        this.element.addEventListener('change', (event) => {
            const input = event.target;

            if (input.type !== 'file') return;

            const row = input.closest('.field-collection-item');

            // ✅ 1. Mettre à jour le label EasyAdmin
            const label = row.querySelector('.custom-file-label');
            if (label && input.files.length) {
                label.textContent = input.files[0].name;
            }

            // ✅ 2. Supprimer ancienne preview si elle existe
            const oldImg = row.querySelector('.js-preview');
            if (oldImg) oldImg.remove();

            // ✅ 3. Afficher nouvelle preview
            if (!input.files.length) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.createImage(row, e.target.result, true);
            };

            reader.readAsDataURL(input.files[0]);
        });
    }

    createImage(row, src, isNew = false) {
        const img = document.createElement('img');
        img.src = src;
        img.style.maxWidth = '100px';
        img.style.maxHeight = '100px';

        if (isNew) {
            img.classList.add('js-preview');
        }

        row.appendChild(img);
    }
}