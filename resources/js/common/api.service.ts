
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import jwtService from './jwt.service'

class ApiService {
  private _api = 'api'

  init(csrfToken = ''): void {
    Vue.use(VueAxios, axios)
    jwtService.init()

    csrfToken
      ? Vue.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken
      : console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
  }

  get api(): string {
    return this._api
  }

  // TODO: Add token in headers
  setHeader() {
    // Vue.axios.defaults.headers.common[
    //   "Authorization"
    // ] = `Token ${JwtService.getToken()}`;
  }

  async query(resource: string, params: any) {
    try {
      const res = await Vue.axios.get(`${this.api}/${resource}`, params)
      return res.data
    } catch (error) {
      throw new Error(`[RWV] QUERY ApiService ${error}`)
    }
  }

  async get(resource: string, slug = '') {
    try {
      const res = await Vue.axios.get(`${this.api}/${resource}/${slug}`, this.getConfig())
      return res.data
    } catch (error) {
      throw new Error(`[RWV] GET ApiService ${error}`)
    }
  }

  async post(resource: string, params: any) {
    try {
      const res = await Vue.axios.post(`${this.api}/${resource}`, params, this.getConfig())
      return res.data
    } catch (error) {
      throw new Error(`[RWV] POST ApiService ${error}`)
    }
  }

  async update(resource: string, slug: string, params: any) {
    try {
      const res = await Vue.axios.put(`${this.api}/${resource}/${slug}`, params, this.getConfig())
      return res.data
    } catch (error) {
      throw new Error(`[RWV] UPDATE ApiService ${error}`)
    }
  }

  async put(resource: string, params: any) {
    try {
      const res = await Vue.axios.put(`${this.api}/${resource}`, params, this.getConfig())
      return res.data
    } catch (error) {
      throw new Error(`[RWV] PUT ApiService ${error}`)
    }
  }

  async delete(resource: string) {
    try {
      const res = await Vue.axios.delete(`${this.api}/${resource}`, this.getConfig())
      return res.data
    } catch (error) {
      throw new Error(`[RWV] DELETE ApiService ${error}`)
    }
  }

  private getConfig() {
    return jwtService.token ? {
      headers:  { Authorization: `Bearer ${jwtService.token}` }
    } : {}
  }

}

const apiService = new ApiService()
export default apiService
