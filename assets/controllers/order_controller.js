import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['total'];

    connect() {
        this.recalculate();
    }

    recalculate() {
        let total = 0;

        const rows = this.element.querySelectorAll('.field-collection-item');

        for (const row of rows) {
            const productSelect = row.querySelector('[data-product]');
            const quantityInput = row.querySelector('[data-quantity]');
            const unitPriceInput = row.querySelector('[data-unit-price]');
            const subtotalInput = row.querySelector('[data-subtotal]');

            if (!productSelect || !quantityInput || !unitPriceInput || !subtotalInput) return;

            const quantity = parseInt(quantityInput.value || 0, 10);
            const selectedOption = productSelect.selectedOptions[0];

            if (!selectedOption || quantity <= 0) {
                unitPriceInput.value = '0.00';
                subtotalInput.value = '0.00';
                return;
            }

            const unitPrice = parseFloat(selectedOption.dataset.price || 0);
            const subtotal = unitPrice * quantity;

            unitPriceInput.value = unitPrice.toFixed(2);
            subtotalInput.value = subtotal.toFixed(2);

            total += subtotal;
        }

        this.totalTarget.value = total.toFixed(2);
    }
}
