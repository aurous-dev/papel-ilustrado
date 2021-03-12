
import {
   tr_nav, 
   tr_preventDefault, 
   menu, 
   tr_search, 
   menu_container, 
   frameInput, 
   sizeInput, 
   clearBtn
} from './selectors';

import {
   clickMenu, 
   columnas, 
   showHidden, 
   showSearch, 
   carts, 
   menuMobile,
   inputValue,
   reset
} from './functions.js'

class initApp {
   constructor() {
      this.App();
   }

   App() {
      // Menu dropdown Deskto
      tr_nav.addEventListener("click", clickMenu);

      // Columnas
      document.addEventListener("DOMContentLoaded", columnas);
      document.addEventListener("DOMContentLoaded", carts);

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
   }
}

export default initApp;