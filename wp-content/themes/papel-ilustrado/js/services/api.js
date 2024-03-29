import axios from "axios";

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
        marcos: [...aVerMarcos],
      };
      return finalObject;
    } catch (error) {
      console.error(error);
    }
  }
  async getOneFrame({ marco, precio }) {
    try {
      const response = await axios.get(
        `${this.baseUrl}/wp-json/acf/v3/posts/${marco}`
      );
      return { ...response.data.acf, id: marco, precio };
    } catch (error) {
      console.error(error);
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
      const marcosInfoArray = marcosFiltered.map((marco) => ({
        id: marco.id,
        ...marco.acf,
      }));
      return marcosInfoArray;
    } catch (error) {
      console.error(error);
    }
  }
}
