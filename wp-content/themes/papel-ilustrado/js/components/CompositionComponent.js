import axios from "axios";

const baseUrl = "http://localhost:8888/papel-ilustrado";

// Compositions list
const COMPOSITIONS = [
  {
    id: 1,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 2,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/instagram1.png",
  },
  {
    id: 3,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/nosotros.png",
  },
  {
    id: 4,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 5,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 6,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 7,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 8,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 9,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 10,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 11,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 12,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 13,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 14,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
  {
    id: 15,
    obras: ["50x70", "50x70", "50x70"],
    image: "/img/png/medida.png",
  },
];

export const compositionComponentScript = {
  el: "#composition-component",
  data: {
    selectedComposition: {},
    compositions: [],
    products: [],
  },
  created: async function() {
    await this.callCompositions(1);
    this.selectedComposition = this.compositions[0];
    await this.callProducts(1);
    await this.callACF(1);
  },
  computed: {},
  methods: {
    async callCompositions(page) {
      /* const response = await axios.get(`${baseUrl}/wp-json/wc/store/products`, {
        params: {
          per_page: 100,
          page,
        },
      }); */
      const response = { data: COMPOSITIONS };
      this.compositions = [...this.compositions, ...response.data];
      if (response.data.length === 100) {
        await this.callCompositions(page + 1);
      } else {
        return null;
      }
    },
    async callProducts(page) {
      const response = await axios.get(`${baseUrl}/wp-json/wc/store/products`, {
        params: {
          per_page: 100,
          page,
        },
      });
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
    },
  },
};
