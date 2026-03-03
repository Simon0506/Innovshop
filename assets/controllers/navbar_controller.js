import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'categoriesMenu',
        'cartMenu',
        'accountMenu',
        'categoriesBtn',
        'cartBtn',
        'accountBtn',
        'mobileMenu',
    ];

    // ===== PROFIL =====
    showCategoriesMenu() {
        if (this.categoriesMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.categoriesMenuTarget.classList.remove('hidden');
        }
    }

    showCartMenu() {
        if (this.cartMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.cartMenuTarget.classList.remove('hidden');
        }
    }

    showAccountMenu() {
        if (this.accountMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.accountMenuTarget.classList.remove('hidden');
        }
    }

    hideAllMenus() {
        if (!this.categoriesMenuTarget.classList.contains('hidden')) {
            this.categoriesMenuTarget.classList.add('hidden');
        }
        if (!this.cartMenuTarget.classList.contains('hidden')) {
            this.cartMenuTarget.classList.add('hidden');
        }
        if (!this.accountMenuTarget.classList.contains('hidden')) {
            this.accountMenuTarget.classList.add('hidden');
        }
    }

    toggleMobileMenu() {
        this.mobileMenuTarget.classList.toggle('hidden');
    }

    toggleCategoriesMenu() {
        if (this.categoriesMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.categoriesMenuTarget.classList.remove('hidden');
        } else {
            this.categoriesMenuTarget.classList.add('hidden');
        }
    }

    toggleCartMenu() {
        if (this.cartMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.cartMenuTarget.classList.remove('hidden');
        } else {
            this.cartMenuTarget.classList.add('hidden');
        }
    }

    toggleAccountMenu() {
        if (this.accountMenuTarget.classList.contains('hidden')) {
            this.hideAllMenus();
            this.accountMenuTarget.classList.remove('hidden');
        } else {
            this.accountMenuTarget.classList.add('hidden');
        }
    }
}