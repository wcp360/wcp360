(() => {
  const root = document.documentElement;
  const themeButton = document.querySelector('[data-theme-toggle]');
  const menuButton = document.querySelector('[data-menu-toggle]');
  const sidebar = document.querySelector('[data-sidebar]');
  const commandButton = document.querySelector('[data-command-open]');
  const command = document.querySelector('[data-command]');
  const commandClose = document.querySelector('[data-command-close]');

  const preferred = localStorage.getItem('wcp360-theme');
  if (preferred === 'dark' || preferred === 'light') root.dataset.theme = preferred;

  themeButton?.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    root.dataset.theme = next;
    localStorage.setItem('wcp360-theme', next);
  });

  menuButton?.addEventListener('click', () => sidebar?.classList.toggle('is-open'));
  commandButton?.addEventListener('click', () => command?.showModal());
  commandClose?.addEventListener('click', () => command?.close());

  document.addEventListener('keydown', (event) => {
    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
      event.preventDefault();
      command?.showModal();
    }
    if (event.key === 'Escape') sidebar?.classList.remove('is-open');
  });
})();
