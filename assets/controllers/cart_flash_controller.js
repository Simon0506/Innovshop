import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    connect() {
        const cartOpen = this.element.dataset.cartOpen === "true"
        if (!cartOpen) return

        const cartMenu = document.getElementById('cartMenu')
        const accountMenu = document.getElementById('accountMenu')
        const categoriesMenu = document.getElementById('categoriesMenu')

        if (!cartMenu || !accountMenu || !categoriesMenu) return

        cartMenu.classList.remove('hidden')
        if (!accountMenu.classList.contains('hidden')) accountMenu.classList.add('hidden')
        if (!categoriesMenu.classList.contains('hidden')) categoriesMenu.classList.add('hidden')
    }
}
