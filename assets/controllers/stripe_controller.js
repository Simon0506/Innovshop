import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        checkoutUrl: String,
    };

    async checkout() {
        console.log('Checkout clicked');

        const response = await fetch(this.checkoutUrlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            alert('Erreur lors de la création du paiement');
            return;
        }

        const data = await response.json();

        if (!data.url) {
            alert('URL Stripe manquante');
            return;
        }

        window.location.href = data.url;
    }
}
