import { menu, tr_header, tr_preventDefault } from './selectors.js';

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

// Composiciones agrupadas

export function inputValue(e) {
   // Div padre
   const varationsDiv = e.target.parentElement.parentElement.parentElement;

   // Valor del input de cuadro
   const inputDiv1 = varationsDiv.children[0].children[1].children[0].value;

   // valor del input de size
   const inputDiv2 = varationsDiv.children[1].children[1].children[0].value;

   // btn de anadir al carrito
   const btnAdd = document.querySelector(".single_add_to_cart_button.button.alt");

   // Mensaje de alerta
   const mensaje = document.querySelector('.alerta');

   console.log(varationsDiv, inputDiv1, inputDiv1, btnAdd, mensaje)
   // Confirma que el value de los dos inputs no sean vacios
   if (inputDiv1 === '' || inputDiv2 === '') {

      if (!mensaje) {
         crearUnDiv(varationsDiv);
      }

      setTimeout(() => {
         const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
         if (qtyVal.innerText.length > 5) {
            btnAdd.classList.add("woosg-disabled", 'woosg-selection');
            btnAdd.disabled = true;
         }
      }, 500);

   } else {
      setTimeout(() => {
         mensaje.remove();
         const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
         if (qtyVal.innerText.length > 5) {
            btnAdd.classList.remove("woosg-disabled", 'woosg-selection');
            btnAdd.disabled = false;
         }
      }, 500);
   }

}

export function reset(e) {
   const btnAdd = document.querySelector(".single_add_to_cart_button.button.alt");
   const mensaje = document.querySelector('.alerta');
   if(mensaje) {
      mensaje.remove();
   }
   setTimeout(() => {
      const qtyVal = document.querySelector(".woocommerce-Price-amount.amount");
      if (qtyVal.innerText.length > 5) {
         btnAdd.classList.remove("woosg-disabled", 'woosg-selection');
         btnAdd.disabled = false;
      }
   }, 500);
}

export function crearUnDiv(e) {

   const divMessage = document.createElement('div');
   divMessage.innerHTML = `
       <p> Por favor seleccione las dos opciones </p>
    `
   divMessage.classList.add('alerta');
   e.appendChild(divMessage);
}

// Cerrar menu cuando doy click afuera
export function closeMenu(e) {
   tr_preventDefault.forEach((a) => {
      if(a !== e.target) {
         if(a.parentElement.classList.contains('active')) {
            a.parentElement.classList.remove('active')
         }
      } 
   });
}