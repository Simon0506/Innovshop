import { Controller } from "@hotwired/stimulus"

export default class extends Controller {

    submit() {
        this.element.submit()
    }

    open() {
        filtersForm.classList.remove('hidden')
    }

    close() {
        filtersForm.classList.add('hidden')
    }
}
