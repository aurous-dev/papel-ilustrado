// import Vue from 'vue';
import $ from "jquery";
window.jQuery = $;
window.$ = $;
import Vue from "vue/dist/vue.js";
import VueSweetalert2 from "vue-sweetalert2";
import * as VeeValidate from "vee-validate";
import { Validator } from "vee-validate";
import es, { messages } from "vee-validate/dist/locale/es";
import { VueReCaptcha } from "vue-recaptcha-v3";
import ButtonSpinner from "vue-button-spinner";
// import Vue from 'vue';

// Import Vue FILES
import formu from "./components/ContactForm.vue";
import example from "./components/Example.vue";
import selectcompo from "./components/SelectCompo.vue";
// Import Vue FILES

// Start AOS library
import AOS from "aos";
import "aos/dist/aos.css";
AOS.init({
  disable: "mobile",
  once: true,
});

// Start AOS library

// JS Modules
import initApp from "./components/constructor.js";
import { allSliders } from "./components/sliders.js";
// JS Modules

// Import Slick Slider & bootstrap
require("./app/slick.js");
require("./bootstrap.js");

// Recaptcha for vue form | this needs to be generated in google API panel
Vue.use(VueReCaptcha, { siteKey: "6LcQKbMZAAAAAHOOytb2hW0MUN1pGFHHZlA_GniE" });
// Recaptcha for vue form

Vue.use(VueSweetalert2);
Vue.use(VeeValidate);
Validator.localize("es", es);
Vue.config.productionTip = false;
Vue.component("formu", require("./components/ContactForm.vue"));
Vue.component("example", require("./components/Example.vue"));
Vue.component("selectcompo", require("./components/SelectCompo.vue"));

import regeneratorRuntime from "regenerator-runtime";

import { sigleProductScript } from "./components/SingleProduct";
import { compositionComponentScript } from "./components/CompositionComponent";

// Import App Vue
const app = new Vue({
  el: "#app",
  components: {
    formu,
    selectcompo,
    ButtonSpinner,
    example,
  },
});

// Import JS
const application = new initApp();
// Import JS

// Marco tester
if (document.querySelector("#sigle-product-vue")) {
  const sigleProduct = new Vue(sigleProductScript);
}
// Marco tester
// Create Composition
if (document.querySelector("#composition-component")) {
  const compositionComponent = new Vue(compositionComponentScript);
}
// Create Composition

allSliders();

const lightbox__btn = document.querySelector('.lightbox .lightbox__body .btn-principal');
const lightbox__closed = document.querySelector('.lightbox .lightbox__body .closed');
const lightbox__div = document.querySelector('.lightbox');

lightbox__btn.addEventListener('click', prueba);
lightbox__closed.addEventListener('click', prueba);

function prueba(e) {
  e.preventDefault();
  sessionStorage.setItem('visito', 1)
  lightbox__div.classList.remove('active')
}

if (!sessionStorage.getItem('visito')) {
  lightbox__div.classList.add('active');
} else {
  lightbox__div.classList.remove('active')
}
