import { menu, tr_header, tr_preventDefault } from './selectors.js';

//------------------- MENU DROPDOWN DESKTOP
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
//------------------- MENU DROPDOWN DESKTOP

//------------------- COLUMNAS PARA EL MENU
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
//------------------- COLUMNAS PARA EL MENU

//------------------- OCULTAR Y MOSTRAR MENU Y BUQUEDA (MOBILE)
export function showHidden() {
   menu.classList.toggle("active");
   tr_header.classList.toggle("active");
}
export function showSearch(e) {
   e.preventDefault();
   const menu_search = document.querySelector(".header__menu--search");
   menu_search.classList.toggle("srch");
}
//------------------- OCULTAR Y MOSTRAR MENU Y BUQUEDA (MOBILE)


//------------------- SEPARAR PALABRAS EN CARRITO
export function carts(element) {
   element.forEach((e) => {
      const number = e.textContent.indexOf(" - ");
      const final = e.textContent.length;
      e.innerHTML =
      `
         ${e.textContent.substring(0, number)} <br>
         <span> ${e.textContent.substring( number + 3, final)} </span>
      `;
   });
}
// API
export const observer = new MutationObserver( mutationListener => {
   mutationListener.forEach( mutation => {
      const {addedNodes,target} = mutation
      
      //Carrito de compra
      if(addedNodes.length && addedNodes[0].classList) {
         const cartTable = addedNodes[0].classList[1];
         const shopTable = addedNodes[0].classList[0];

         if (cartTable === 'w-cart__form') {
            const cartTd = document.querySelectorAll(".w-cart__table--name a");
            carts(cartTd)
            return
         } 

         if (shopTable === 'shop_table') {
            const shopTd = document.querySelectorAll('table.shop_table tbody td.product-name');
            carts(shopTd)
            inputDisabled(shopTd)
            return
         }
      }

      // Producto Simple
      if(addedNodes.length > 1) {
         if(target.classList.contains('single_variation')) {
            const price = document.querySelector('.price.simple-product__price');
            price.classList.add('opacity')
            return;
         }
      }

   } )
})
//------------------- SEPARAR PALABRAS EN CARRITO

//-------------- INPUT CART DISABLED
function inputDisabled(element) {
   element.forEach(e => {
      const productName = e.children[1].outerText.indexOf(', ');
      const total = e.children[1].outerText.length;
      const onlyNumber = e.children[1].outerText.substring((productName + 2), total);
   
      const firstSize = onlyNumber.indexOf('x');
      const secondSize = onlyNumber.indexOf(' ×');
   
      const firstNumber = parseInt(onlyNumber.substring(0, firstSize));
      const SecondNumber = parseInt(onlyNumber.substring((firstSize + 1), secondSize));
   
      if (firstNumber > 65 || SecondNumber > 90) {
         const inputCity = document.querySelector('#billing_city');
         const inputRegion = document.querySelector('#billing_state');
   
         inputCity.value = 'Santiago'
         inputCity.disabled = true
         inputRegion.value = 'Metropolitana'
         inputRegion.disabled = true
      }
   
   })
}
//-------------- INPUT CART DISABLED

//------------------- FUNCION DE CERRAR Y ABRIR EN MENU MOBILE
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
//------------------- FUNCION DE CERRAR Y ABRIR EN MENU MOBILE

//------------------ COMPOSICIONES AGRUPADAS
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

   // Confirma que el value de los dos inputs no sean vacios
   if (inputDiv1 === '' || inputDiv2 === '') {
      const price = document.querySelector('.price.simple-product__price');

      // Remover clase de opacity agregado en la funcion API 
      if(price) {
         if(price.classList.contains('opacity')) {
            price.classList.remove('opacity')
         }
      }

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
         if(mensaje) {
            mensaje.remove();
         }
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
   const price = document.querySelector('.price.simple-product__price');

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

   // Remover clase de opacity agregado en la funcion API 
   if(price) {
      if(price.classList.contains('opacity')) {
         price.classList.remove('opacity')
         return
      }
   }
}
export function crearUnDiv(e) {

   const divMessage = document.createElement('div');
   divMessage.innerHTML = `
       <p> Por favor seleccione las dos opciones </p>
    `
   divMessage.classList.add('alerta');
   e.appendChild(divMessage);
}
//------------------ COMPOSICIONES AGRUPADAS

//------------------ CERRAR MENU CUANDO DOY CLICK AFUERA
export function closeMenu(e) {
   tr_preventDefault.forEach((a) => {
      if(a !== e.target) {
         if(a.parentElement.classList.contains('active')) {
            a.parentElement.classList.remove('active')
         }
      } 
   });
}
//------------------ CERRAR MENU CUANDO DOY CLICK AFUERA