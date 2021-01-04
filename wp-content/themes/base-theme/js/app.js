// import Vue from 'vue';
import $ from "jquery";
window.jQuery = $;
window.$ = $;
import Vue from 'vue/dist/vue.js';
import VueSweetalert2 from 'vue-sweetalert2';
import * as VeeValidate from 'vee-validate';
import { Validator } from "vee-validate";
import es from 'vee-validate/dist/locale/es';
import { VueReCaptcha } from 'vue-recaptcha-v3';
import ButtonSpinner from 'vue-button-spinner';

// Import Vue FILES
import formu from './components/ContactForm.vue';
import example from './components/Example.vue';
// Import Vue FILES

// Import Slick Slider & bootstrap
require('./app/slick.js');
require('./bootstrap.js');

// Recaptcha for vue form | this needs to be generated in google API panel
Vue.use(VueReCaptcha, { siteKey: '6LcQKbMZAAAAAHOOytb2hW0MUN1pGFHHZlA_GniE' })
// Recaptcha for vue form

Vue.use(VueSweetalert2);
Vue.use(VeeValidate);
Validator.localize("es", es);
Vue.config.productionTip = false;
Vue.component('formu', require('./components/ContactForm.vue'));
Vue.component('example', require('./components/Example.vue'));

// Import App Vue
const app = new Vue({
    el: '#app',
    components: {
        formu,
        ButtonSpinner,
        example
    }
});