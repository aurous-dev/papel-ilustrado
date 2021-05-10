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
  nameCheckout,
  cartAPI,
  observerOptions,
} from "./selectors";

import {
  clickMenu,
  columnas,
  showHidden,
  showSearch,
  carts,
  menuMobile,
  inputValue,
  reset,
  closeMenu,
  observer,
} from "./functions.js";

class initApp {
  constructor() {
    this.App();
  }

  App() {
    //-------------- MENU DROPDOWN DESKTOP
    tr_nav.addEventListener("click", clickMenu);
    //-------------- MENU DROPDOWN DESKTOP

    //-------------- CERRAR EL MENU CUANDO LE DOY CLICK AFUERA
    window.addEventListener("click", closeMenu);
    //-------------- CERRAR EL MENU CUANDO LE DOY CLICK AFUERA

    //-------------- COLUMNAS
    document.addEventListener("DOMContentLoaded", columnas);
    document.addEventListener("DOMContentLoaded", () => {
      carts(nameCart);
      carts(nameCheckout);
    });
    //-------------- COLUMNAS

    //-------------- BTNS BURGUER AND SEARCH
    menu.addEventListener("click", showHidden);
    tr_search.addEventListener("click", showSearch);
    menu_container.addEventListener("click", menuMobile);
    //-------------- BTNS BURGUER AND SEARCH

    //-------------- PREVENIR QUE SE COMPORTE COMO UN ENLACE
    tr_preventDefault.forEach((a) => {
      a.addEventListener("click", (e) => {
        e.preventDefault();
      });
    });
    //-------------- PREVENIR QUE SE COMPORTE COMO UN ENLACE

    //-------------- COMPOSICIONES AGRUPADAS
    frameInput.forEach((element) => {
      element.addEventListener("change", inputValue);
    });
    sizeInput.forEach((element) => {
      element.addEventListener("change", inputValue);
    });
    clearBtn.forEach((element) => {
      element.addEventListener("click", reset);
    });
    //-------------- COMPOSICIONES AGRUPADAS

    //-------------- API
    if (cartAPI) {
      observer.observe(cartAPI, observerOptions);
    }
    //-------------- API
  }
}

export default initApp;
