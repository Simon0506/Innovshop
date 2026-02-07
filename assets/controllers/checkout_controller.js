// assets/controllers/checkout_controller.js
import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = [
        "deliveryFormContainer",
        "deliveryAddresses",
        "showDeliveryForm",
        "hideDeliveryForm",
        "billingFormContainer",
        "billingAddresses",
        "showBillingForm",
        "hideBillingForm",
        "sameAsDelivery",
        "finalDeliveryAddress",
        "finalBillingAddress",
        "selectedDeliveryAddress",
        "selectedBillingAddress"
    ]

    connect() {
        this.syncSelectedAddresses()
    }

    // ----------------------------
    // Delivery form toggle
    // ----------------------------
    showDeliveryFormAction(event) {
        event.preventDefault()
        if (this.deliveryAddressesTarget) this.deliveryAddressesTarget.classList.add("hidden")
        this.deliveryFormContainerTarget.classList.remove("hidden")
        this.hideDeliveryFormTarget.classList.remove("hidden")
        this.showDeliveryFormTarget.classList.add("hidden")
    }

    hideDeliveryFormAction(event) {
        event.preventDefault()
        if (this.deliveryAddressesTarget && this.deliveryAddressesTarget.classList.contains("hidden")) {
            this.deliveryAddressesTarget.classList.remove("hidden")
        }
        this.deliveryFormContainerTarget.classList.add("hidden")
        this.hideDeliveryFormTarget.classList.add("hidden")
        this.showDeliveryFormTarget.classList.remove("hidden")
    }

    // ----------------------------
    // Billing form toggle
    // ----------------------------
    showBillingFormAction(event) {
        event.preventDefault()
        if (this.billingAddressesTarget) this.billingAddressesTarget.classList.add("hidden")
        this.billingFormContainerTarget.classList.remove("hidden")
        this.hideBillingFormTarget.classList.remove("hidden")
        this.showBillingFormTarget.classList.add("hidden")
    }

    hideBillingFormAction(event) {
        event.preventDefault()
        if (this.billingAddressesTarget && this.billingAddressesTarget.classList.contains("hidden")) {
            this.billingAddressesTarget.classList.remove("hidden")
        }
        this.billingFormContainerTarget.classList.add("hidden")
        this.hideBillingFormTarget.classList.add("hidden")
        this.showBillingFormTarget.classList.remove("hidden")
    }

    // ----------------------------
    // Same as delivery checkbox
    // ----------------------------
    sameAsDeliveryAction(event) {
        if (this.sameAsDeliveryTarget.checked) {
            this.billingAddressesTarget.classList.add("hidden")
            this.billingFormContainerTarget.classList.add("hidden")
            this.hideBillingFormTarget.classList.add("hidden")
            this.showBillingFormTarget.classList.add("hidden")
            this.finalBillingAddressTarget.value = this.finalDeliveryAddressTarget.value
        } else {
            this.billingAddressesTarget.classList.remove("hidden")
            this.showBillingFormTarget.classList.remove("hidden")
        }
    }

    // ----------------------------
    // Sync selected addresses
    // ----------------------------
    syncSelectedAddresses() {
        if (!this.finalDeliveryAddressTarget || !this.finalBillingAddressTarget || !this.sameAsDeliveryTarget) return

        const updateBilling = () => {
            if (this.sameAsDeliveryTarget.checked) {
                this.finalBillingAddressTarget.value = this.finalDeliveryAddressTarget.value
            } else {
                const selectedBillingRadio = this.selectedBillingAddressTargets.find(r => r.checked)
                if (selectedBillingRadio) this.finalBillingAddressTarget.value = selectedBillingRadio.value
            }
        }

        // Initial update
        updateBilling()

        // Event listeners
        this.sameAsDeliveryTarget.addEventListener("change", updateBilling)

        this.selectedDeliveryAddressTargets.forEach(radio => {
            radio.addEventListener("change", () => {
                this.finalDeliveryAddressTarget.value = radio.value
                updateBilling()
            })
        })

        this.selectedBillingAddressTargets.forEach(radio => {
            radio.addEventListener("change", () => {
                if (!this.sameAsDeliveryTarget.checked) {
                    this.finalBillingAddressTarget.value = radio.value
                }
            })
        })
    }
}
