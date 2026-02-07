import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['price', 'quantity', 'form', 'submit', 'priceht'];

    connect() {
        const checkedOption = this.element.querySelector(
            'input[type="radio"][name="option"]:checked'
        );

        if (checkedOption) {
            this.updateFromInput(checkedOption);
        }
    }

    select(event) {
        this.updateFromInput(event.target);
    }

    updateFromInput(input) {
        const price = input.dataset.productOptionsPriceValue;
        const priceHT = input.dataset.productOptionsPricehtValue;
        const stock = input.dataset.productOptionsStockValue;
        const id = input.dataset.productOptionsIdValue;

        // Prix
        this.priceTarget.textContent = Number(price).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        this.pricehtTarget.textContent = Number(priceHT).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Quantité
        this.quantityTarget.max = stock;
        this.quantityTarget.value = stock > 0 ? 1 : 0;

        // Action du formulaire
        this.formTarget.action = `/products/add-to-cart/${id}`;

        // Bouton
        this.submitTarget.disabled = stock <= 0;
    }
}
