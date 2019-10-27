
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'

export default class ApiService {
    private _apiPrefix = 'api'

    init(): void {
        Vue.use(VueAxios, axios)
    }

    get apiPrefix(): string {
        return this._apiPrefix
    }

    // TODO: Add token in headers
    setHeader() {
        // Vue.axios.defaults.headers.common[
        //   "Authorization"
        // ] = `Token ${JwtService.getToken()}`;
      }
    
      async query(resource: string, params: any) {
        try {
              return Vue.axios.get(resource, params);
          }
          catch (error) {
              console.log(`[RWV] QUERY ApiService ${error}`);
          }
      }
    
      async get(resource: string, slug = "") {
        try {
              return Vue.axios.get(`${resource}/${slug}`);
          }
          catch (error) {
            console.log(`[RWV] GET ApiService ${error}`);
          }
      }
    
      post(resource: string, params: any) {
        try {
            return Vue.axios.post(`${resource}`, params);
        }
        catch (error) {
            console.log(`[RWV] POST ApiService ${error}`)
        }
      }
    
      update(resource: string, slug: string, params:any) {
        try {
            return Vue.axios.put(`${resource}/${slug}`, params);
        }
        catch (error) {
            console.log(`[RWV] UPDATE ApiService ${error}`)
        }
      }
    
      put(resource: string, params: any) {
        try {
            return Vue.axios.put(`${resource}`, params);
        }
        catch (error) {
            console.log(`[RWV] PUT ApiService ${error}`)
        }
      }
    
      delete(resource: string) {
        try {
            return Vue.axios.delete(resource);
        }
        catch (error) {
            console.log(`[RWV] DELETE ApiService ${error}`)
        }
      }

}