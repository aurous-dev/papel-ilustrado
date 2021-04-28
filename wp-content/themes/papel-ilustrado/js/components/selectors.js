// --------------------VARIABLES--------------------

// Menu dropdown Desktop
export const tr_nav = document.querySelector("#menu-principal");
export const tr_preventDefault = document.querySelectorAll( "#menu-principal .menu-item-has-children > a");

// btns burguer and search
export const menu = document.querySelector(".menu-icon");
export const tr_header = document.querySelector(".header");
export const tr_search = document.querySelector(".search-mobile");
export const menu_container = document.querySelector(".header__container");

// Height menu fixed
export let responsives = window.matchMedia("(max-width: 979px)");

// Composiciones Agrupadas
export const frameInput = document.querySelectorAll('#pa_marco');
export const sizeInput = document.querySelectorAll('#pa_tamano');
export const clearBtn = document.querySelectorAll('.reset_variations');

// Product name for Checkout
export const nameCart = document.querySelectorAll(".w-cart__table--name a");
export const nameCheckout = document.querySelectorAll('form.w-checkout__container .woocommerce-checkout-review-order table.shop_table tbody td.product-name')

// API
export const cartAPI = document.querySelector('#main');
export const observerOptions = {
   characterData: true,
   childList: true,
   subtree: true
}