import axios from "axios";
import WC from "@woocommerce/woocommerce-rest-api";

export default class API {
  constructor(url) {
    this.baseUrl = url;
  }

  async getProduct(productId) {
    try {
      const responseACF = await axios.get(
        `${this.baseUrl}/wp-json/acf/v3/product/${productId}`
      );
      const responsePr = await axios.get(
        `${this.baseUrl}/wp-json/wc/store/products/${productId}`
      );
      const { id, images, prices, name } = responsePr.data;
      const { marcos } = responseACF.data.acf;
      const someMarcos = marcos.map((marco) => marco.marco);
      const aVerMarcos = await this.getFrames(someMarcos);
      const finalObject = {
        id,
        name,
        prices,
        images,
        // artista,
        marcos: [...aVerMarcos]
      };
      return finalObject;
    } catch (error) {
      console.log(error);
    }
  }
  async getOneFrame({ marco, precio }) {
    try {
      const response = await axios.get(
        `${this.baseUrl}/wp-json/acf/v3/posts/${marco}`
      );
      return { ...response.data.acf, id: marco, precio };
    } catch (error) {
      console.log(error);
    }
  }
  async getFrames(marcosArray) {
    try {
      const allMarcosArray = await axios.get(
        `${this.baseUrl}/wp-json/wp/v2/marco`
      );
      const marcosFiltered = allMarcosArray.data.filter((marco) =>
        marcosArray.includes(marco.id)
      );
      const marcosInfoArray = marcosFiltered.map((marco) => marco.acf);
      /* let marcosInfoArray = Promise.all(
        marcosArray.map((elem) => {
          return this.getOneFrame(elem).then((res) => res);
        })
      );
      */
      return marcosInfoArray;
    } catch (error) {
      console.log(error);
    }
  }
}
