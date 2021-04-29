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
    selectedComposition: undefined,
    selectedDimensions: {},
    selectedArtworks: [],
    selectedArtworksMap: new Map(),
    compositions: [],
    marcos: [],
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
  },
  created: async function() {
    this.isLoading = true;
    await this.callCompositions(1);
    await this.slider();
    window.addEventListener("resize", this.unslike);
    await this.callMarcos(1);
    await this.callProducts(1);
    // await this.callACF(1);
    this.isLoading = false;
  },
  computed: {
    filteredProducts() {
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

          return haveDimention;
        });
        return finalProducts;
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
        const productsIds = this.finalArtworksInfo.join(",");
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
      const response = await axios.get(
        `${baseUrl}/wp-json/wp/v2/composicion?_fields=id,acf`,
        {
          params: {
            per_page: 100,
            page,
          },
        }
      );

      const compositions = response.data.map((composition) => ({
        id: composition.id,
        icono: composition.acf.icono,
        imagen: composition.acf.imagen,
        obras: composition.acf.obras.map((obra) => obra.tamano),
      }));

      this.compositions = [...this.compositions, ...compositions];
      if (response.data.length === 100) {
        await this.callCompositions(page + 1);
      } else {
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
        $(".slider-nav").slick('unslick')
        // const sliderComposition = document.querySelector(".slider-nav");
        // await sliderComposition.classList.add("slider_mobile");
        // sliderComposition.classList.remove(
        //   "slider-nav",
        //   "slick-initialized",
        //   "slick-slider"
        // );
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
    async callProducts(page) {
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
      const onlyVariableProducts = response.data.filter(
        (product) => product.type === "variable"
      );
      this.products = [...this.products, ...onlyVariableProducts];
      if (response.data.length === 100) {
        await this.callProducts(page + 1);
      } else {
        return null;
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
      this.changeStep(1);
    },
    setDimensions(dimensions, selectedIndex) {
      this.selectedDimensions = { dimensions, index: selectedIndex };
    },
    touchArtwork(product) {
      if (this.selectedArtworksMap.has(product.id)) {
        console.log(product.id, "Estaba seleccionado");
        if (
          this.selectedArtworksMap.get(product.id) !==
          this.selectedDimensions.index
        ) {
          console.log(product.id, "Era de otro");
          this.removeArtwork(this.selectedArtworksMap.get(product.id));
          this.selectArtwork(product);
        } else {
          this.removeArtwork(this.selectedDimensions.index);
        }
      } else {
        this.selectArtwork(product);
      }
    },
    selectArtwork(product) {
      const newSelectedArtworks = [...this.selectedArtworks];
      newSelectedArtworks[this.selectedDimensions.index] = product;
      for (let [k, v] of this.selectedArtworksMap) {
        if (v === this.selectedDimensions.index) {
          console.log(v, "el index ya estaba ocupado");
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
          console.log(v, "el index ya estaba ocupado");
          this.selectedArtworksMap.delete(k);
        }
      }
      this.selectedArtworksMap.delete(this.selectedArtworks[index].id);
      newSelectedArtworks[index] = undefined;
      this.selectedArtworks = [...newSelectedArtworks];
    },
    changeStep(step) {
      this.step = step;
      this.stepInfo = stepsDescription[this.step];
      console.log(step);
      if (step >= 0) {
        this.mobile = true;
      }
    },
    unslike() {
      if (window.outerWidth < 500) {
        $(".slider-nav").slick('unslick')
        // const sliderComposition = document.querySelector(".slider-nav");
        // sliderComposition.classList.add("slider_mobile");
        // sliderComposition.classList.remove(
        //   "slider-nav",
        //   "slick-initialized",
        //   "slick-slider"
        // );
      } else {
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
        // const sliderComposition = document.querySelector(".slider_mobile");
        // if (sliderComposition) {
        //   sliderComposition.classList.add(
        //     "slider-nav",
        //     "slick-initialized",
        //     "slick-slider"
        //   );
        //   sliderComposition.classList.remove("slider_mobile");
        // }
      }
    },
  },
};
