import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['option'];

    connect() {
        this.mode = 'system';
        try {
            const saved = localStorage.getItem('wcp360.appearance');
            if (['system', 'light', 'dark'].includes(saved)) this.mode = saved;
        } catch { /* Appearance remains usable when browser storage is unavailable. */ }
        this.render();
        this.element.dataset.appearanceReady = 'true';
    }

    choose(event) {
        const mode = event.currentTarget.dataset.mode;
        if (!['system', 'light', 'dark'].includes(mode)) return;
        this.mode = mode;
        try { localStorage.setItem('wcp360.appearance', mode); } catch { /* Per-page preference still applies. */ }
        this.render();
    }

    render() {
        document.documentElement.dataset.theme = this.mode;
        this.optionTargets.forEach(button => button.setAttribute('aria-pressed', String(button.dataset.mode === this.mode)));
    }
}
