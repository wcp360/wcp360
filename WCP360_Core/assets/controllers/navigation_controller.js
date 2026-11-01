import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dialog', 'query', 'item', 'empty', 'opener'];

    open() {
        if (this.dialogTarget.open) return;
        this.queryTarget.value = '';
        this.filter();
        this.dialogTarget.showModal();
        this.queryTarget.focus();
    }

    close() { this.dialogTarget.close(); }

    closed() { this.openerTargets.find(button => button.offsetParent)?.focus(); }

    keydown(event) {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            this.dialogTarget.open ? this.close() : this.open();
        }
    }

    filter() {
        const query = this.queryTarget.value.trim().toLocaleLowerCase('fr');
        let visible = 0;
        this.itemTargets.forEach(item => {
            item.hidden = !item.textContent.toLocaleLowerCase('fr').includes(query);
            if (!item.hidden) visible++;
        });
        this.emptyTarget.hidden = visible !== 0;
    }

    follow() { this.dialogTarget.close(); }
}
