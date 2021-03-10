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