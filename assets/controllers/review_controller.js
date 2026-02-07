import { Controller } from '@hotwired/stimulus';
import { parse } from 'postcss';

export default class extends Controller {
    static values = {
        productTitle: String,
        productId: Number,
        note: Number,
        comment: String
    };
    static targets = [
        'reviewForm',
        'productTitle',
        'productId',
        'note',
        'comment'
    ];

    showReviewForm(event) {
        const btn = event.currentTarget;

        this.productTitleTarget.textContent = `Produit : ${btn.dataset.reviewProductTitleValue}`;
        this.productIdTarget.value = btn.dataset.reviewProductIdValue;
        this.noteTarget.value = parseInt(btn.dataset.reviewNoteValue || '0', 10);
        this.commentTarget.value = btn.dataset.reviewCommentValue || '';

        const starsElement = this.reviewFormTarget.querySelector('[data-controller="stars"]');
        if (starsElement) {
            const starsController = this.application.getControllerForElementAndIdentifier(starsElement, 'stars');
            if (starsController) {
                starsController.update(this.noteTarget.value);
            }
        }
        this.reviewFormTarget.classList.remove('hidden');
    }

    hideReviewForm() {
        this.reviewFormTarget.classList.add('hidden');
    }
}