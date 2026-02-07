import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static values = {
        message: String
    }

    submit(event) {
        event.preventDefault();

        if (confirm(this.messageValue)) {
            this.element.submit();
        }
    }
}
