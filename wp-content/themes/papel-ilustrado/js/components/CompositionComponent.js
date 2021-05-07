import axios from "axios";
window.jQuery = $;
window.$ = $;

// const baseUrl = "http://localhost:8888/papel-ilustrado";
// const baseUrl = "http://localhost/papel-ilustrado";
const baseUrl = "http://aurouslabs.cl/papelilustrado";

const stepsDescription = [
  {
    title: "PASO 1: Selecciona diagramación",
    description:
      "Elige la diagramación que quieras colocar en tu espacio favorito, tenemos muchas combinaciones.",
  },
  {
    title: "PASO 2: Selecciona las obras",
    description:
      "Elige en orden las obras que desees para tu composición, éstas se enumerarán de la primera a la última dependiendo de la cantidad de la diagramación.",
  },
  {
    title: "PASO 3: Selecciona el marco y agrega al carrito",
    description:
      "Elige el marco que desees para las obras de tu composición, al hacerlo se mostrará el precio total.",
  },
];

export const compositionComponentScript = {
  el: "#composition-component",
  data: {
    listOfCategories: [],
    selectedCategory: "",
    selectedComposition: undefined,
    selectedDimensions: {},
    selectedArtworks: [],
    selectedArtworksMap: new Map(),
    compositions: [],
    marcos: [],
    filteredMarcos: [],
    search: "",
    products: [],
    isLoading: false,
    selectedMarco: "Sin marco",
    finalArts: [],
    storedArtsData: new Map(),
    /**
     * 0 - Select Composition.
     * 1 - Select Artworks.
     * 2 - Select Marco and sent to Cart.
     */
    step: 0,
    stepInfo: stepsDescription[0],
    mobile: true,
    productPage: 1,
    haveMore: true,
    repisasIds: [],
    repisas: new Map(),
    availableRepisas: [],
    selectedRepisas: [],
  },
  created: async function() {
    this.isLoading = true;
    await this.callCompositions(1);
    await this.callRepisas();
    await this.slider();
    window.addEventListener("resize", this.unslike);
    await this.callMarcos(1);
    await this.callCategories();
    await this.callProducts(1);
    // await this.callACF(1);
    this.isLoading = false;
  },
  computed: {
    filteredProductsBySize() {
      if (
        !this.isLoading &&
        this.selectedDimensions?.dimensions &&
        this.products.length > 0
      ) {
        const finalProducts = this.products.filter((product) => {
          const sizes = product.attributes.find(
            (attr) => attr.name === "Tamaño"
          );

          if (!sizes || sizes?.terms?.length < 0) return false;

          const haveDimention = sizes.terms.find(
            (dimension) => dimension.name === this.selectedDimensions.dimensions
          );

          let isDecoration = product.categories.some(
            (productCategory) =>
              productCategory.slug.includes("decoracion") ||
              productCategory.slug.includes("repisa") ||
              productCategory.slug.includes("espejo") ||
              productCategory.slug.includes("murales") ||
              productCategory.slug.includes("uncategorized")
          );

          return haveDimention && !isDecoration;
        });
        return finalProducts;
      }
      return [];
    },
    finalFilteredProducts() {
      if (this.filteredProductsBySize.length > 0) {
        let filteredProducts = this.filteredProductsBySize.filter((product) => {
          // ! Filter by category
          let haveCategorySelected = product.categories.some(
            (productCategory) =>
              productCategory.slug.includes(this.selectedCategory)
          );

          // ! Search by name
          // let nameIsInSearch = product.name.includes(this.search);
          let nameIsInSearch = product.name
            .toLowerCase()
            .normalize("NFKD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/&#038;/g, "&")
            .replace(/\\u[\dA-F]{4}/gi, (match) =>
              String.fromCharCode(parseInt(match.replace(/\\u/g, ""), 16))
            )
            .includes(
              this.search
                .toLowerCase()
                .normalize("NFKD")
                .replace(/[\u0300-\u036f]/g, "")
            );

          // ? Final return with al filters
          return haveCategorySelected && nameIsInSearch;
        });

        if (filteredProducts.length === 0 && this.haveMore)
          this.callProducts(this.productPage);

        return filteredProducts;
      }
      return [];
    },
    areAllArtsSelected() {
      if (this.selectedArtworks.length > 0) {
        if (this.selectedArtworks.includes(undefined)) {
          this.changeStep(1);
          return false;
        } else {
          this.changeStep(2);
          return true;
        }
      }
      return false;
    },
    finalArtworksInfo() {
      if (this.areAllArtsSelected && this.selectedMarco !== "Sin marco") {
        const variationsInfo = this.selectedArtworks.map((art, index) => {
          const variationFound = art.variations.find(
            (variation) =>
              variation.attributes[0].value === this.selectedMarco &&
              variation.attributes[1].value ===
                this.selectedComposition.obras[index]
          );
          return variationFound;
        });

        const variationsIds = variationsInfo.map((vari) => vari.id);

        this.callVariations(variationsIds);

        return variationsIds;
      }
      return undefined;
    },
    totalPrice() {
      if (this.areAllArtsSelected && this.selectedMarco === "Sin marco") {
        return this.selectedArtworks.reduce(
          (a, b) => a + Number(b.prices.price),
          0
        );
      }
      if (this.areAllArtsSelected && this.finalArts.length > 0) {
        return this.finalArts.reduce((a, b) => a + Number(b.prices.price), 0);
      }
      return 0;
    },
    urlToCart() {
      if (this.finalArtworksInfo && this.finalArtworksInfo.length > 0) {
        let productsIds = this.finalArtworksInfo.join(",");
        if (
          this.selectedRepisas.length > 0 &&
          this.selectedRepisas.includes(undefined)
        ) {
          return "#";
        }
        if (
          this.selectedRepisas.length > 0 &&
          !this.selectedRepisas.includes(undefined)
        ) {
          let repisas = this.selectedRepisas.join(",");
          productsIds = `${productsIds},${repisas}`;
        }
        return `${baseUrl}/cart/?add-to-cart=${productsIds}`;
      }
      return "#";
    },
  },
  methods: {
    console(title, info) {
      console.groupCollapsed(title);
      console.log(info);
      console.groupEnd();
    },
    async callCompositions(page) {
      try {
        const response = await axios.get(
          `${baseUrl}/wp-json/wp/v2/composicion?_fields=id,acf`,
          {
            params: {
              per_page: 100,
              page,
            },
          }
        );

        const compositions = response.data.map((composition) => {
          let simpleComposition = {
            id: composition.id,
            icono: composition.acf.icono,
            imagen: composition.acf.imagen,
            obras: composition.acf.obras.map((obra) => obra.tamano),
          };
          let validateRepisa =
            composition.acf?.repisa === "false"
              ? false
              : !!composition.acf?.repisa;
          if (validateRepisa) {
            let repisasToAdd = composition.acf.repisas.map(
              (repisa) => repisa.repisa
            );
            this.repisasIds = [...this.repisasIds, ...repisasToAdd];
            simpleComposition = {
              ...simpleComposition,
              repisas: [...repisasToAdd],
            };
          }
          return simpleComposition;
        });

        this.compositions = [...this.compositions, ...compositions];
        if (response.data.length === 100) {
          await this.callCompositions(page + 1);
        } else {
          return null;
        }
      } catch (error) {
        console.error(error);
        return null;
      }
    },
    async slider() {
      $(".slider-nav").slick({
        infinite: true,
        slidesToShow: 10,
        slidesToScroll: 1,
        dots: false,
        arrows: true,
        responsive: [
          {
            breakpoint: 770,
            settings: {
              slidesToShow: 5,
              slidesToScroll: 1,
            },
          },
        ],
      });
      if (window.outerWidth < 500) {
        $(".slider-nav").slick("unslick");
      }
    },
    async callCategories() {
      try {
        const response = await axios.get(
          `${baseUrl}/wp-json/wc/store/products/categories?_fields=id,name,slug`
        );
        const categories = response.data;
        const filteredCategories = categories.filter(
          (category) =>
            !category.slug.includes("decoracion") &&
            !category.slug.includes("repisa") &&
            !category.slug.includes("espejo") &&
            !category.slug.includes("murales") &&
            !category.slug.includes("uncategorized")
        );
        this.listOfCategories = [...filteredCategories];
      } catch (error) {
        console.error(error);
      }
    },
    async callMarcos(page) {
      const response = await axios.get(
        `${baseUrl}/wp-json/wp/v2/marco?_fields=id,acf,slug`,
        {
          params: {
            per_page: 100,
            page,
          },
        }
      );

      const marcos = response.data.map((marco) => ({
        id: marco.id,
        slug: marco.slug,
        name: marco.acf.nombre_de_marco,
        description: marco.acf.descripcion,
        tamano: marco.acf.tamano,
        icono: marco.acf.icono,
        imagen: marco.acf.imagen,
      }));

      this.marcos = [...this.marcos, ...marcos];
      if (response.data.length === 100) {
        await this.callMarcos(page + 1);
      } else {
        return null;
      }
    },
    async callRepisas() {
      const repisasList = new Set(this.repisasIds);
      try {
        const promises = [...repisasList].map(async (id) => {
          if (this.repisas.has(id)) return this.repisas.get(id);

          const response = await axios.get(
            `${baseUrl}/wp-json/wc/store/products/${id}?_fields=id,name,variations,prices`
          );

          this.repisas.set(id, response.data);

          return response.data;
        });

        Promise.all(promises);
      } catch (err) {
        console.error(err);
      }
    },
    async callProducts(page) {
      if (!this.haveMore) return;

      try {
        const response = await axios.get(
          `${baseUrl}/wp-json/wc/store/products?_fields=id,name,attributes,type,variations,categories,images,prices`,
          {
            params: {
              per_page: 100,
              page,
              type: "variable",
              stock_status: "instock",
            },
          }
        );
        this.productPage += 1;

        const onlyVariableProducts = response.data.filter(
          (product) => product.type === "variable"
        );

        this.products = [...this.products, ...onlyVariableProducts];

        if (onlyVariableProducts.length === 0 && this.haveMore)
          await this.callProducts(this.productPage);

        if (response.data.length !== 100) {
          this.haveMore = false;
        }
      } catch (err) {
        console.error(err);
      }
    },
    async callACF(page) {
      let acf = [];
      const response = await axios.get(`${baseUrl}/wp-json/acf/v3/product`, {
        params: {
          per_page: 100,
          page,
        },
      });
      acf = [...acf, ...response.data];
      this.products.forEach((product, index) => {
        let theProd = acf.find((fields) => product.id === fields.id);
        this.products[index] = { ...this.products[index], ...theProd };
      });
      if (response.data.length === 100) {
        await this.callACF(page + 1);
      } else {
        return null;
      }
    },
    async callVariations(ids) {
      try {
        const promises = ids.map(async (id) => {
          if (this.storedArtsData.get(id)) return this.storedArtsData.get(id);

          const response = await axios.get(
            `${baseUrl}/wp-json/wc/store/products/${id}?_fields=id,name,attributes,prices`
          );

          this.storedArtsData.set(id, response.data);

          return response.data;
        });

        Promise.all(promises).then((values) => {
          this.finalArts = values;
        });
      } catch (error) {
        console.error(error);
      }
    },
    setComposition(index) {
      this.selectedComposition = this.compositions[index];
      this.selectedArtworks = new Array(this.selectedComposition.obras.length);
      if (this.selectedComposition.repisas) {
        let availableRepisas = this.selectedComposition.repisas.map((id) =>
          this.repisas.get(id)
        );
        console.log(new Array(availableRepisas.length));
        this.selectedRepisas = new Array(availableRepisas.length);
        this.availableRepisas = [...availableRepisas];
      } else {
        this.availableRepisas = [];
      }
      this.changeStep(1);
    },
    setDimensions(dimensions, selectedIndex) {
      this.selectedDimensions = { dimensions, index: selectedIndex };
    },
    touchArtwork(product) {
      if (this.selectedArtworksMap.has(product.id)) {
        if (
          this.selectedArtworksMap.get(product.id) !==
          this.selectedDimensions.index
        ) {
          this.removeArtwork(this.selectedArtworksMap.get(product.id));
          this.selectArtwork(product);
        } else {
          this.removeArtwork(this.selectedDimensions.index);
        }
      } else {
        this.selectArtwork(product);
      }
      this.updateMarcos();
    },
    selectArtwork(product) {
      const newSelectedArtworks = [...this.selectedArtworks];
      newSelectedArtworks[this.selectedDimensions.index] = product;
      for (let [k, v] of this.selectedArtworksMap) {
        if (v === this.selectedDimensions.index) {
          this.selectedArtworksMap.delete(k);
        }
      }
      this.selectedArtworksMap.set(product.id, this.selectedDimensions.index);
      this.selectedArtworks = [...newSelectedArtworks];
      if (this.areAllArtsSelected) {
        this.changeStep(2);
      }
    },
    removeArtwork(index) {
      const newSelectedArtworks = [...this.selectedArtworks];
      for (let [k, v] of this.selectedArtworksMap) {
        if (v === index) {
          this.selectedArtworksMap.delete(k);
        }
      }
      this.selectedArtworksMap.delete(this.selectedArtworks[index].id);
      newSelectedArtworks[index] = undefined;
      this.selectedArtworks = [...newSelectedArtworks];
    },
    updateMarcos() {
      let marcos = [];
      this.selectedArtworks.forEach((art, index) => {
        if (art) {
          let variations = art.variations.filter((vari) => {
            let tamanos = vari.attributes.find((att) => att.name === "Tamaño");
            return this.selectedComposition.obras[index] === tamanos.value;
          });
          let marcosToAdd = variations.map((vari) => {
            let marcosAtt = vari.attributes.find((att) => att.name === "Marco");
            return marcosAtt.value;
          });

          if (marcos.length === 0) {
            marcos = [...marcosToAdd];
          } else {
            marcos = marcos.filter((marco) => marcosToAdd.includes(marco));
          }
        }
      });

      if (marcos.length > 0) {
        this.filteredMarcos = this.marcos.filter((marco) =>
          marcos.includes(marco.slug)
        );
      }
    },
    changeStep(step) {
      this.step = step;
      this.stepInfo = stepsDescription[this.step];
      if (step >= 0) {
        this.mobile = true;
      }
    },
    unslike() {
      if (window.outerWidth < 500) {
        $(".slider-nav").slick("unslick");
      }
    },
  },
  filters: {
    formatName(slug) {
      let words = slug.split("-");
      words = words.map((word) => {
        word = word.split("");
        word[0] = word[0].toUpperCase();
        return word.join("");
      });
      return words.join(" ");
    },
    milesSeparator(num) {
      if (num < 1000) return num;
      let numArrayReversed = `${num}`.split("").reverse();
      let resultArrayReversed = [];
      for (let i = 0; i < numArrayReversed.length; i++) {
        if (i > 0 && i % 3 === 0) resultArrayReversed.push(".");
        resultArrayReversed.push(numArrayReversed[i]);
      }
      return resultArrayReversed.reverse().join("");
    },
  },
};
