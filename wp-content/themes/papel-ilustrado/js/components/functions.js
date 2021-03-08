import {menu, tr_header} from './selectors.js';

// Menu dropdown Desktop
export function clickMenu(a) {
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

// Columnas para el menu
export function columnas() {
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

// Ocultar y Mostrar Menu y Buqueda (Mobile)
export function showHidden() {
   menu.classList.toggle("active");
   tr_header.classList.toggle("active");
}
export function showSearch(e) {
   e.preventDefault();
   const menu_search = document.querySelector(".header__menu--search");
   menu_search.classList.toggle("srch");
}

// Test para separar palabras en carrito
export function carts() {
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

// Funcion de cerrar y abrir en menu mobile
export function menuMobile(e) {
   const positions = e.path;
   const menuHeader_search = document.querySelector(".header__menu--search");
   positions.forEach((m) => {
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
}