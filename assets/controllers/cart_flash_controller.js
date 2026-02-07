import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    connect() {
        // Vérifie si le flash Symfony indique d'ouvrir le panier
        const cartOpen = this.element.dataset.cartOpen === "true"
        if (!cartOpen) return

        const cartMenu = document.getElementById('cartMenu')
        const accountMenu = document.getElementById('accountMenu')
        const categoriesMenu = document.getElementById('categoriesMenu')

        if (!cartMenu || !accountMenu || !categoriesMenu) return

        // Ouvre le panier et masque les autres menus (même logique que ton toggle)
        cartMenu.classList.remove('hidden')
        if (!accountMenu.classList.contains('hidden')) accountMenu.classList.add('hidden')
        if (!categoriesMenu.classList.contains('hidden')) categoriesMenu.classList.add('hidden')
    }
}
