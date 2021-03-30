import axios from "axios";

const baseUrl = "http://localhost:8888/papel-ilustrado";

export const compositionComponentScript = {
  el: "#composition-component",
  data: {
    selectedComposition: undefined,
    selectedDimensions: {},
    selectedArtworks: [],
    compositions: [],
    products: [],
    isLoading: false,
    /**
     * 0 - Select Composition.
     * 1 - Select Artworks.
     * 3 - Sent to Cart.
     */
    step: 0,
  },
  created: async function() {
    this.isLoading = true;
    await this.callCompositions(1);
    // this.setComposition(0);
    // this.setDimensions(0, 0);
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
        if (this.selectedArtworks.includes(undefined)) return false;
        else return true;
      }
      return false;
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
    async callProducts(page) {
      const response = await axios.get(
        `${baseUrl}/wp-json/wc/store/products?_fields=id,name,attributes,variations`,
        {
          params: {
            per_page: 100,
            page,
            // stock_status: "instock",
          },
        }
      );
      this.products = [...this.products, ...response.data];
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
    setComposition(index) {
      this.selectedComposition = this.compositions[index];
      this.selectedArtworks = new Array(this.selectedComposition.obras.length);
      this.step = 1;
    },
    setDimensions(dimensions, selectedIndex) {
      this.selectedDimensions = { dimensions, index: selectedIndex };
    },
    selectArtwork(product) {
      const newSelectedArtworks = [...this.selectedArtworks];
      newSelectedArtworks[this.selectedDimensions.index] = product;
      this.selectedArtworks = [...newSelectedArtworks];
    },
    removeArtwork(index) {
      const newSelectedArtworks = [...this.selectedArtworks];
      newSelectedArtworks[index] = undefined;
      this.selectedArtworks = [...newSelectedArtworks];
    },
  },
};
