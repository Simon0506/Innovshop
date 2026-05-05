import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["form"]

    submit() {
        this.formTarget.submit()
    }

    open() {
        this.formTarget.classList.remove('hidden')
    }

    close() {
        this.formTarget.classList.add('hidden')
    }
}
