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

// HOME SLIDER
$(".hero__slider").slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    dots: false,
    arrows: true,
    fade: true,
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
$(".slider-for").on("setPosition", function(event, slick) {
    console.log(event);
});

// --------------------VARIABLES--------------------

// Menu dropdown Desktop
const tr_nav = document.querySelector("#menu-principal");
const tr_preventDefault = document.querySelectorAll(
    "#menu-principal .menu-item-has-children > a"
);

// btns burguer and search
const menu = document.querySelector(".menu-icon");
const tr_header = document.querySelector(".header");
const tr_search = document.querySelector(".search-mobile");
const menu_container = document.querySelector(".header__container");

// Height menu fixed
let responsives = window.matchMedia("(max-width: 979px)");

// ------------------EVENTLISTENERS------------------

// Menu dropdown Deskto
tr_nav.addEventListener("click", clickMenu);

// Columnas
document.addEventListener("DOMContentLoaded", columnas);
document.addEventListener("DOMContentLoaded", carts);

// btns burguer and search
menu.addEventListener("click", showHidden);
tr_search.addEventListener("click", showSearch);

// -------------------FUNCTIONS-------------------

// Menu dropdown Desktop
function clickMenu(a) {
    const menu = a.path[2].children;
    Array.from(menu).forEach((m) => {
        const item = a.target.parentNode;
        if (m.classList.contains("menu-item-has-children")) {
            if (
                item.classList === m.classList &&
                !item.classList.contains("active")
            ) {
                m.classList.add("active");
            } else {
                m.classList.remove("active");
            }
        }
    });
}

// Prevenir que se comporte como un enlace
tr_preventDefault.forEach((a) => {
    a.addEventListener("click", (e) => {
        e.preventDefault();
    });
});

// Columnas para el menu
function columnas() {
    const items = document.querySelectorAll(
        "#menu-principal > .menu-item-has-children > .sub-menu"
    );
    items.forEach((m) => {
        const item = m.children.length;
        for (let i = 0; i < item; i++) {
            if (m.children[i].children.length > 1) {
                const numero = m.children[i].children[1].children.length;
                const clase = m.children[i].children[1];

                if (numero >= 9) {
                    clase.classList.add("column3");
                } else if (numero > 3 && numero < 9) {
                    clase.classList.add("column2");
                } else {
                    clase.classList.add("column");
                }
            }
        }
    });
}

// btns burguer and search
menu_container.addEventListener("click", (e) => {
    const test = e.path;
    // const menuHeader_burguer = document.querySelector('.menu-icon');
    const menuHeader_search = document.querySelector(".header__menu--search");
    // console.log(menuHeader_search.children[1])
    // console.log(menuHeader_burguer.children[0])
    test.forEach((m) => {
        if (m.classList && m.classList[0]) {
            if (m.classList[0] === "search-mobile") {
                if (menu.classList.contains("active")) {
                    menu.classList.remove("active");
                    tr_header.classList.remove("active");
                }
            }

            if (m.classList[0] === "menu-icon") {
                if (menuHeader_search.classList.contains("srch")) {
                    menuHeader_search.classList.remove("srch");
                }
            }
        }
    });
});
function showHidden() {
    menu.classList.toggle("active");
    tr_header.classList.toggle("active");
}
function showSearch(e) {
    e.preventDefault();
    const menu_search = document.querySelector(".header__menu--search");
    menu_search.classList.toggle("srch");
}

// Height menu fixed
// navResponsive(responsives);
// responsives.addListener(navResponsive);

// function navResponsive(x) {
//     console.log(x)
//     if (x.matches) {
//         tr_menu.forEach((mes) => {
//             if (mes.classList.contains("menu-item-has-children")) {
//                 const btn = mes.children[0];
//                 const dropdown = mes.children[1];
//                 btn.addEventListener("click", (e) => {
//                     e.preventDefault();
//                     dropdown.classList.toggle("down");
//                     btn.classList.toggle("active");
//                     tr_rrss.style.top = `calc(${tr_menuHeight.clientHeight}px + 5px)`;
//                 });
//             }
//         });
//         tr_rrss.style.top = `-600px`;
//     }
// }

// Zoom img
// $(document).ready(function(){
//     $('.image-zoom')
//         .wrap('<span style="display:inline-block"></span>')
//         .css('display', 'block')
//         .parent()
//         .zoom({
//             url: $(this).find('img').attr('data-zoom')
//         });
// });
function carts() {
    const prueba = document.querySelectorAll(".w-cart__table--name a");
    if (prueba) {
        prueba.forEach((m) => {
            const number = m.textContent.indexOf(" - ");
            const final = m.textContent.length;
            m.innerHTML = `${m.textContent.substring(0, number)} <br>
                            <span> ${m.textContent.substring(
                                number + 3,
                                final
                            )} </span>
                            `;
        });
    }
}
