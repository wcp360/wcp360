import { Controller } from '@hotwired/stimulus';
export default class extends Controller {
  static targets = ['button', 'status'];
  connect() { this.reset(); }
  submit(event) {
    if (!this.element.checkValidity()) return;
    if (this.pending) { event.preventDefault(); return; }
    this.pending = true;
    this.buttonTarget.disabled = true;
    this.statusTarget.textContent = 'Envoi de la demande…';
    this.element.addEventListener('turbo:submit-end', () => this.reset(), { once: true });
  }
  reset() { this.pending = false; this.buttonTarget.disabled = false; this.statusTarget.textContent = ''; }
}
