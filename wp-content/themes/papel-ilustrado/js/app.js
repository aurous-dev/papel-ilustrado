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

// Test


// const formulario = document.querySelectorAll('#pa_marco');
// const formulario2 = document.querySelectorAll('#pa_tamano');

// const testInput = document.querySelector('#prueba');
// const testInput2 = document.querySelector('#prueba2');
// testInput.addEventListener('change', test)
// testInput2.addEventListener('change', test2)

// // formulario.forEach( (element) => {

// //     element.addEventListener('change', e => {
// //         console.log(e)
// //     })
// //     // const optionLength = element.children.length
// //     // for(let i = 0; i < optionLength; i++) {
// //     //     if(element.children[i].value === option.value) {
// //     //         element.children[i].selected = true
// //     //     }
// //     // }
// // })


// function test() {
//     let selectedOption = this.options[testInput.selectedIndex];
//     comprobar(selectedOption)
   
// }
// function test2() {
//     let selectedOption = this.options[testInput2.selectedIndex];
//     comprobar2(selectedOption)
// }


// function comprobar(option) {
//     const numero = formulario.length;
//     // for(let i = 0; i < numero; i++) {
//     //     console.log(formulario[i])
//     // }
//     formulario.forEach( (element, index, array) => {
//         const optionLength = element.children.length
//         setTimeout( () => {
//             for(let i = 0; i < optionLength; i++) {
//                 element.children[i].selected = false;     
//                 if(element.children[i].value === option.value) {
//                     element.children[i].selected = true;     
//                 }
//             }
//             element.submit()
//         },index * 1000)     
//     })
// }
// function comprobar2(option) {

//     formulario2.forEach( (element, index) => {
//         const optionLength = element.children.length
//         setTimeout( () => {
//             for(let i = 0; i < optionLength; i++) {
//                 element.children[i].selected = false;     
//                 if(element.children[i].value === option.value) {
//                     element.children[i].selected = true;     
//                 }
//             }
//             element.submit()
//         },index * 2000)     
//     })
// }

// // const tamano = document.querySelector('#pa_tamano');
// // const hijosFormulario = formulario.children;
// // const hijosTamano = tamano.children;

// // Array.prototype.forEach.call(hijosFormulario, (element )=> {
// //     if(element.value !== '' && element.value === 'madera-natural-encajonado') {
// //         console.log(element.selected = true)
// //     }
// // })
// // Array.prototype.forEach.call(hijosTamano, (element )=> {
// //     if(element.value !== '' && element.value === '30x30') {
// //         console.log(element.selected = true)
// //     }
// // })

// const formulario = document.querySelector(".woosg_products.woosg-table.woosg-products");
// formulario.addEventListener('click', (e) => {
//     const btn = document.querySelectorAll('.reset_variations');
//     const btn2 = document.querySelector('.single_add_to_cart_button.button.alt');
//     btn.forEach( item => {
//         if(e.target === item) {
//             // item.parentElement.parentElement.parentElement.parentElement.nextElementSibling.children[0].children[1].value = 0;
//             // let prueba = item.parentElement.parentElement.parentElement.parentElement.nextElementSibling.children[0].children[1].value;
//             // item.parentElement.parentElement.parentElement.parentElement.parentElement.dataset.qty = prueba
//             // item.parentElement.parentElement.parentElement.parentElement.parentElement.dataset.id = prueba
//             // item.parentElement.parentElement.parentElement.parentElement.parentElement.dataset.price = prueba
//             // btn2.classList.remove('woosg-disabled', 'woosg-selection')
//             // btn2.disabled = false;

//         }
//     })
// })