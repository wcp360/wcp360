import '@hotwired/turbo';
import { Application } from '@hotwired/stimulus';
import FormGuard from './controllers/form_guard_controller.js';
const application = Application.start();
application.register('form-guard', FormGuard);
