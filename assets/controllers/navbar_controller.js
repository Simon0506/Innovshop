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
        'mobileCategoriesMenu',
        'mobileCartMenu',
        'mobileAccountMenu',
        'categoriesArrow',
        'cartArrow',
        'accountArrow'
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

    toggleSection(menu, arrow) {
        const isOpen = menu.classList.contains('max-h-[500px]');

        // Ferme tout
        this.closeAllMobileSections();

        if (!isOpen) {
            menu.classList.remove('max-h-0');
            menu.classList.add('max-h-[500px]');

            arrow.classList.add('rotate-180');
        }
    }

    closeAllMobileSections() {
        this.mobileCategoriesMenuTarget.classList.add('max-h-0');
        this.mobileAccountMenuTarget.classList.add('max-h-0');
        this.mobileCartMenuTarget.classList.add('max-h-0');

        this.mobileCategoriesMenuTarget.classList.remove('max-h-[500px]');
        this.mobileAccountMenuTarget.classList.remove('max-h-[500px]');
        this.mobileCartMenuTarget.classList.remove('max-h-[500px]');

        this.categoriesArrowTarget.classList.remove('rotate-180');
        this.accountArrowTarget.classList.remove('rotate-180');
        this.cartArrowTarget.classList.remove('rotate-180');
    }

    toggleMobileCategories() {
        this.toggleSection(this.mobileCategoriesMenuTarget, this.categoriesArrowTarget);
    }

    toggleMobileCart() {
        this.toggleSection(this.mobileCartMenuTarget, this.cartArrowTarget);
    }

    toggleMobileAccount() {
        this.toggleSection(this.mobileAccountMenuTarget, this.accountArrowTarget);
    }
}