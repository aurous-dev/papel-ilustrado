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
import selectcompo from './components/SelectCompo.vue';
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
Vue.component('selectcompo', require('./components/SelectCompo.vue'));

// Import App Vue
const app = new Vue({
    el: '#app',
    components: {
        formu,
        selectcompo,
        ButtonSpinner,
        example
    }
});

// HOME SLIDER
$(".paiting__slider").slick({
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 1,
    dots: true,
    arrows: true,
});
$(".composition__slider").slick({
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    dots: true,
    arrows: true,
});
$(".picture__slider").slick({
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    dots: true,
    arrows: true,
});

// Menu dropdown Desktop
const tr_menu = document.querySelectorAll('#menu-principal .lista');
const tr_nav = document.querySelector('#menu-principal');
const tr_preventDefault = document.querySelectorAll('#menu-principal .lista > a');
tr_nav.addEventListener('click', clickMenu);

function clickMenu (a) {
    const menu = a.path[2].children;
    Array.from(menu).forEach(m => {
        const item = a.target.parentNode;
        if(m.classList.contains('menu-item-has-children')) {
            if( (item.classList === m.classList) && !item.classList.contains('active')) {
                m.classList.add('active');
            } else {
                m.classList.remove('active');
            }
        }
    });
}

// Prevenir que se comporte como un enlace
tr_preventDefault.forEach( a => {
    a.addEventListener('click', e => {
        e.preventDefault()
    })
})