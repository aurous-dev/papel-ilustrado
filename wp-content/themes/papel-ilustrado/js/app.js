// import Vue from 'vue';
import $ from "jquery";
window.jQuery = $;
window.$ = $;
import Vue from "vue/dist/vue.js";
import VueSweetalert2 from "vue-sweetalert2";
import * as VeeValidate from "vee-validate";
import { Validator } from "vee-validate";
import es from "vee-validate/dist/locale/es";
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
import initApp from './components/constructor.js'
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
// Marco tester
if (document.querySelector("#sigle-product-vue")) {
   const sigleProduct = new Vue(sigleProductScript);
}
// Marco tester

// HOME SLIDER
$(".hero__slider").slick({
   infinite: true,
   slidesToShow: 1,
   slidesToScroll: 1,
   dots: false,
   arrows: true,
   // fade: true,
});
$(".paiting__slider").slick({
   infinite: true,
   slidesToShow: 4,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      {
         breakpoint: 770,
         settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
         },
      },
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
$(".composition__slider").slick({
   infinite: true,
   slidesToShow: 2,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      // {
      //     breakpoint: 770,
      //     settings: {
      //         slidesToShow: 1,
      //         slidesToScroll: 1,
      //         dots: true,
      //     },
      // },
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
$(".picture__slider").slick({
   infinite: true,
   slidesToShow: 5,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      {
         breakpoint: 770,
         settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
         },
      },
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
$(".instagram__slider").slick({
   infinite: true,
   slidesToShow: 3,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});

// PAGE SLIDER
$(".fiveColumn__slider").slick({
   infinite: true,
   slidesToShow: 5,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      {
         breakpoint: 770,
         settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
         },
      },
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
$(".fourColumn__slider").slick({
   infinite: true,
   slidesToShow: 4,
   slidesToScroll: 1,
   dots: true,
   arrows: true,
   responsive: [
      {
         breakpoint: 770,
         settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            dots: true,
         },
      },
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
// Slider syncing

$(".slider-for").slick({
   slidesToShow: 1,
   slidesToScroll: 1,
   arrows: false,
   fade: true,
   asNavFor: ".slider-nav",
   responsive: [
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
$(".slider-nav").slick({
   slidesToShow: 3,
   slidesToScroll: 1,
   asNavFor: ".slider-for",
   dots: true,
   centerMode: true,
   focusOnSelect: true,
   responsive: [
      {
         breakpoint: 480,
         settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
         },
      },
   ],
});
// $(".slider-for").on("setPosition", function(event, slick) {
//     console.log(event);
// });
