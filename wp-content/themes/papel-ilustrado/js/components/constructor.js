
import {tr_nav, tr_preventDefault, menu, tr_search, menu_container} from './selectors';
import {clickMenu, columnas, showHidden, showSearch, carts, menuMobile} from './functions.js'

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
   }
}

export default initApp;