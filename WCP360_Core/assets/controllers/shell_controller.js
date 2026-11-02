import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['sidebar', 'overlay', 'toggle'];

    connect() {
        this.onResize = () => { if (window.innerWidth > 1100) this.close(); };
        window.addEventListener('resize', this.onResize);
        this.highlight();
        this.element.dataset.shellReady = 'true';
    }

    disconnect() { window.removeEventListener('resize', this.onResize); }

    toggle() {
        const open = !this.sidebarTarget.classList.contains('mobile-open');
        this.sidebarTarget.classList.toggle('mobile-open', open);
        this.overlayTarget.classList.toggle('active', open);
        this.toggleTargets.forEach(button => button.setAttribute('aria-expanded', String(open)));
        if (open) this.sidebarTarget.querySelector('a')?.focus();
    }

    close(event) {
        const wasOpen = this.sidebarTarget.classList.contains('mobile-open');
        this.sidebarTarget.classList.remove('mobile-open');
        this.overlayTarget.classList.remove('active');
        this.toggleTargets.forEach(button => button.setAttribute('aria-expanded', 'false'));
        if (wasOpen && event?.type !== 'click') this.toggleTargets.find(button => button.offsetParent)?.focus();
    }

    keydown(event) { if (event.key === 'Escape') this.close(event); }

    highlight() {
        const links = this.sidebarTarget.querySelectorAll('[data-current-link]');
        links.forEach(link => {
            const target = new URL(link.href);
            if (target.pathname !== window.location.pathname) return;
            const active = target.hash === window.location.hash;
            link.classList.toggle('active', active);
            if (active) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
        });
    }

    navigate(event) { if (event.target.closest('a')) this.close(event); }
}
