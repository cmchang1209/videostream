window._ = require('lodash')

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

//window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest',
    'Content-Type': 'application/json;charset=UTF-8'
}

window.Vue = require('vue')
window.Base64 = require('js-base64').Base64

import ElementUI from 'element-ui'
// Fixes an issue with filters not working on mobile
ElementUi.Select.computed.readonly = function() {
    // trade-off for IE input readonly problem: https://github.com/ElemeFE/element/issues/10403
    const isIE = !this.$isServer && !Number.isNaN(Number(document.documentMode));

    return !(this.filterable || this.multiple || !isIE) && !this.visible
}

export default ElementUi

import 'element-ui/lib/theme-chalk/display.css'
Vue.use(ElementUI)

import VueSocketIOExt from 'vue-socket.io-extended'
import { io } from 'socket.io-client'
//const socket = io('http://videostream.fidodarts.com:8003')
//Vue.use(VueSocketIOExt, socket)

const isDebug_mode = process.env.NODE_ENV !== 'production';
Vue.config.debug = isDebug_mode;
Vue.config.devtools = isDebug_mode;
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
