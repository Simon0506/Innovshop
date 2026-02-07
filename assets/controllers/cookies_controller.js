import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['banner'];

    connect() {
        if (!this.hasSeenCookies()) {
            this.bannerTarget.classList.remove('hidden');
        }
    }

    acknowledge() {
        document.cookie = `cookies_seen=true;path=/;max-age=${60 * 60 * 24 * 180}`;
        this.bannerTarget.classList.add('hidden');
    }

    hasSeenCookies() {
        return document.cookie.includes('cookies_seen=true');
    }
}
