import API from "../services/api";

const localUrl = window.location.href;
const name = localUrl.split("/")[2];
const devHosts = ["localhost", "localhost:8888", "dev.trono.host"];
const fullApi = devHosts.includes(name)
  ? `http://${name}/papel-ilustrado`
  : name.includes(".devel")
  ? `http://${name}`
  : `https://${name}`;
const api = new API("http://localhost:8888/papel-ilustrado");

export const sigleProductScript = {
  el: "#sigle-product-vue",
  data: {
    selectedMarco: {},
    productID: 0,
    obra: {
      id: "",
      name: "",
      marcos: []
    },
    regs: [],
    selected: "none",
    loading: false
  },
  filters: {
    none: function(str) {
      if (str === "none") return "No marco";
      return str;
    }
  },
  computed: {
    finalPrice: function() {
      if (this.obra?.prices?.price && !this.selectedMarco?.precio)
        return this.obra.prices.price;
      if (this.selectedMarco?.precio) return this.selectedMarco.precio;
      return 0;
    },
    cssVars: function() {
      if (Object.keys(this.selectedMarco).length !== 0) {
        if (this.selectedMarco.tamano === "small") {
          return {
            "--frame-image": `url('${this.selectedMarco.imagen}') 40 40 40 40 stretch stretch`,
            "--tamano": "15px 15px 15px 15px"
          };
        }
        if (this.selectedMarco.tamano === "large") {
          return {
            "--frame-image": `url('${this.selectedMarco.imagen}') 180 180 180 180 stretch repeat`,
            "--tamano": "50px 50px 50px 50px"
          };
        }
        if (this.selectedMarco.tamano === "encajonado_con_paspartu") {
          return {
            "--frame-image": `url('${this.selectedMarco.imagen}') 130 130 130 130 stretch repeat`,
            "--tamano": "50px 50px 50px 50px"
          };
        }
        if (this.selectedMarco.tamano === "rococo_con_paspartu") {
          return {
            // "--frame-image": `url('${this.selectedMarco.imagen}') 200 200 200 200 stretch repeat`,
            "--frame-image": `url('${this.selectedMarco.imagen}') 450 450 450 450 repeat repeat`,
            "--tamano": "50px 50px 50px 50px"
          };
        }
        return {
          "--frame-image": `url('${this.selectedMarco.imagen}') 139 139 139 139 stretch stretch`,
          "--tamano": "30px 30px 30px 30px"
        };
      } else {
        return {
          "--frame-image": "none",
          "--tamano": "0px 0px 0px 0px"
        };
      }
    }
  },
  methods: {
    changeFrame: function(marcoObject, event) {
      if(event) event.preventDefault()
      this.selectedMarco = marcoObject;
    }
  },
  created: async function() {
    this.loading = true;
    let array = [];
    const bodyClasses = document.querySelector(".single-product").classList;
    const iterator = bodyClasses.values();
    for (let value of iterator) {
      array.push(value);
    }
    const postIdClass = array.find((elem) => elem.startsWith("postid"));
    const postIdClassSplit = postIdClass.split("-");

    const response = await api.getProduct(postIdClassSplit[1]);
    this.obra = { ...response };
    this.productID = +postIdClassSplit[1];
    this.loading = false;
  }
};
