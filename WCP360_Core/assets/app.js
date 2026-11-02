import '@hotwired/turbo';
import { Application } from '@hotwired/stimulus';
import FormGuard from './controllers/form_guard_controller.js';
import Appearance from './controllers/appearance_controller.js';
import Shell from './controllers/shell_controller.js';
import Navigation from './controllers/navigation_controller.js';

const application = Application.start();
application.register('form-guard', FormGuard);
application.register('appearance', Appearance);
application.register('shell', Shell);
application.register('navigation', Navigation);
