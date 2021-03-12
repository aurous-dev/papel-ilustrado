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

// Test


const frameInput = document.querySelectorAll('#pa_marco');
const sizeInput = document.querySelectorAll('#pa_tamano');
const clearBtn = document.querySelectorAll('.reset_variations');


frameInput.forEach((element) => {
   element.addEventListener('change', inputValue)
})
sizeInput.forEach((element) => {
   element.addEventListener('change', inputValue)
})
clearBtn.forEach((element) => {
   element.addEventListener('click', reset)
})


function  inputValue(e) {
   const varationsDiv = e.target.parentElement.parentElement.parentElement;
   const inputDiv1 = varationsDiv.children[0].children[1].children[0].value;
   const inputDiv2 = varationsDiv.children[1].children[1].children[0].value;
   const btnAdd = document.querySelector(".single_add_to_cart_button.button.alt");
   
   if (inputDiv1 === '' || inputDiv2 === '') {
      crearUnDiv(varationsDiv)
      setTimeout(() => {
         const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
         if (qtyVal.innerText.length > 5) {
            btnAdd.classList.add("woosg-disabled", 'woosg-selection');
            btnAdd.disabled = true;
         }
      }, 500);
   } else {
      setTimeout(() => {
         const mensaje = document.querySelector('.mensaje-oculto');
         mensaje.remove();
         const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
         if (qtyVal.innerText.length > 5) {
            btnAdd.classList.remove("woosg-disabled", 'woosg-selection');
            btnAdd.disabled = false;
         }
      }, 500);
   }
}

function reset(e) {
   const btnAdd = document.querySelector(".single_add_to_cart_button.button.alt");
   setTimeout(() => {
      const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
      if (qtyVal.innerText.length > 5) {
         btnAdd.classList.remove("woosg-disabled", 'woosg-selection');
         btnAdd.disabled = false;
      }
   }, 500);
}

function crearUnDiv(e) {
   const divMessage = document.createElement('div');
   divMessage.innerHTML = `
      <p> Por favor seleccione las dos opciones </p>
   `
   divMessage.classList.add('mensaje-oculto');
   e.appendChild(divMessage);
}
