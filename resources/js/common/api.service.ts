
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'


class ApiService {
    private _api = 'api';

    init(): void {
        Vue.use(VueAxios, axios)
    }

    get api(): string {
        return this._api;
    }

    // TODO: Add token in headers
    setHeader() {
        // Vue.axios.defaults.headers.common[
        //   "Authorization"
        // ] = `Token ${JwtService.getToken()}`;
      }
    
      async query(resource: string, params: any) {
        try {
              return Vue.axios.get(`${this.api}/${resource}`, params);
          }
          catch (error) {
              console.log(`[RWV] QUERY ApiService ${error}`);
          }
      }
    
      async get(resource: string, slug = "") {
        try {
            const res = await Vue.axios.get(`${this.api}/${resource}/${slug}`);
            return res.data;
          }
          catch (error) {
            console.log(`[RWV] GET ApiService ${error}`);
          }
      }
    
      post(resource: string, params: any) {
        try {
            return Vue.axios.post(`${this.api}/${resource}`, params);
        }
        catch (error) {
            console.log(`[RWV] POST ApiService ${error}`)
        }
      }
    
      update(resource: string, slug: string, params:any) {
        try {
            return Vue.axios.put(`${this.api}/${resource}/${slug}`, params);
        }
        catch (error) {
            console.log(`[RWV] UPDATE ApiService ${error}`)
        }
      }
    
      put(resource: string, params: any) {
        try {
            return Vue.axios.put(`${this.api}/${resource}`, params);
        }
        catch (error) {
            console.log(`[RWV] PUT ApiService ${error}`)
        }
      }
    
      delete(resource: string) {
        try {
            return Vue.axios.delete(`${this.api}/${resource}`);
        }
        catch (error) {
            console.log(`[RWV] DELETE ApiService ${error}`)
        }
      }

}

const apiService = new ApiService();
export default apiService;