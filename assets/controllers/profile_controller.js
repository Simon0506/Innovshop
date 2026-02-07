import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'actualProfil',
        'updateProfil',
        'actualPassword',
        'updatePassword',
        'addressesList',
        'addAddressForm',
        'addressName',
        'addressFirstName',
        'addressAddress',
        'addressPostalCode',
        'addressCity',
        'addressPhone',
        'addressDeliveryDefault',
        'addressBillingDefault',
        'addressId',
        'addressSubmit',
        'addressFormElement'
    ];

    // ===== PROFIL =====
    showEditProfil() {
        this.actualProfilTarget.classList.add('hidden');
        this.updateProfilTarget.classList.remove('hidden');
    }

    cancelEditProfil() {
        this.updateProfilTarget.classList.add('hidden');
        this.actualProfilTarget.classList.remove('hidden');
    }

    // ===== PASSWORD =====
    showEditPassword() {
        this.actualPasswordTarget.classList.add('hidden');
        this.updatePasswordTarget.classList.remove('hidden');
    }

    cancelEditPassword() {
        this.updatePasswordTarget.classList.add('hidden');
        this.actualPasswordTarget.classList.remove('hidden');
    }

    // ===== ADDRESSES =====
    showAddAddress() {
        this.resetAddressForm();
        this.addressesListTarget.classList.add('hidden');
        this.addAddressFormTarget.classList.remove('hidden');
    }

    cancelAddAddress() {
        this.resetAddressForm();
        this.addAddressFormTarget.classList.add('hidden');
        this.addressesListTarget.classList.remove('hidden');
    }

    editAddress(event) {
        const btn = event.currentTarget;

        this.resetAddressForm();

        // Affichage
        this.addressesListTarget.classList.add('hidden');
        this.addAddressFormTarget.classList.remove('hidden');

        // Remplissage des champs
        this.addressIdTarget.value = btn.dataset.id;
        this.addressNameTarget.value = btn.dataset.name || '';
        this.addressFirstNameTarget.value = btn.dataset.firstName || '';
        this.addressAddressTarget.value = btn.dataset.address || '';
        this.addressPostalCodeTarget.value = btn.dataset.postalCode || '';
        this.addressCityTarget.value = btn.dataset.city || '';
        this.addressPhoneTarget.value = btn.dataset.phone || '';

        this.addressDeliveryDefaultTarget.checked = btn.dataset.deliveryDefault === '1';
        this.addressBillingDefaultTarget.checked = btn.dataset.billingDefault === '1';
        if (this.hasAddressSubmitTarget) {
            this.addressSubmitTarget.textContent = 'Mettre à jour cette adresse';
        }
    }

    resetAddressForm() {
        this.addressFormElementTarget.reset();
        this.addressIdTarget.value = '';

        // Sécurité : reset manuel des checkbox
        this.addressDeliveryDefaultTarget.checked = false;
        this.addressBillingDefaultTarget.checked = false;

        // Texte du bouton
        if (this.hasAddressSubmitTarget) {
            this.addressSubmitTarget.textContent = 'Ajouter cette adresse';
        }
    }
}
