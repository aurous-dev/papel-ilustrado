
import {
   tr_nav,
   tr_preventDefault,
   menu,
   tr_search,
   menu_container,
   frameInput,
   sizeInput,
   clearBtn,
   nameCart,
   nameCheckout
} from './selectors';

import {
   clickMenu,
   columnas,
   showHidden,
   showSearch,
   carts,
   menuMobile,
   inputValue,
   reset,
   closeMenu
} from './functions.js'

class initApp {
   constructor() {
      this.App();
   }

   App() {
      // Menu dropdown Deskto
      tr_nav.addEventListener("click", clickMenu);

      // Cerrar el menu cuando le doy click afuera
      window.addEventListener('click', closeMenu);

      // Columnas
      document.addEventListener("DOMContentLoaded", columnas);
      document.addEventListener("DOMContentLoaded", () => {
         carts(nameCart)
         carts(nameCheckout)
      });

      // btns burguer and search
      menu.addEventListener("click", showHidden);
      tr_search.addEventListener("click", showSearch);

      // btns burguer and search
      menu_container.addEventListener("click", menuMobile);

      // Prevenir que se comporte como un enlace
      tr_preventDefault.forEach((a) => {
         a.addEventListener("click", (e) => {
            e.preventDefault();
         });
      });

      // Composiciones Agrupadas
      frameInput.forEach((element) => {
         element.addEventListener('change', inputValue)
      })
      sizeInput.forEach((element) => {
         element.addEventListener('change', inputValue)
      })
      clearBtn.forEach((element) => {
         element.addEventListener('click', reset)
      })

      // Input Cart disabled
      nameCheckout.forEach(e => {
         const productName = e.outerText.indexOf(', ');
         const total = e.outerText.length;
         const onlyNumber = e.outerText.substring((productName + 2), total);

         const firstSize = onlyNumber.indexOf('X')
         const secondSize = onlyNumber.indexOf(' ×')

         const firstNumber = parseInt(onlyNumber.substring(0, firstSize))
         const SecondNumber = parseInt(onlyNumber.substring((firstSize + 1), secondSize))

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
}

export default initApp;