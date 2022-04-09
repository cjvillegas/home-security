import Vue from "vue";

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.GlobalEventBus = new EventBus()

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import EventBus from "./services/EventBus";

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "bb0a4dcaba7ba81bd676",
    cluster: "ap1",
    forceTLS: true
});

window.Echo.private(`user.1`)
    .listen('.monitoring.deleted', (data) => {
        window.GlobalEventBus.fire('GLOBAL_MONITORING_DELETED', data.monitoring)
    })
    .listen('.monitoring.status-change', (data) => {
        window.GlobalEventBus.fire('GLOBAL_MONITORING_STATUS_CHANGE', data.monitoring)
    })
