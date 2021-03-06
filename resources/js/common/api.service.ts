
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import jwtService from './jwt.service'

class ApiService {
  private _api = '/api'

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
  // setHeader() {
  //   Vue.axios.defaults.headers.common[
  //     "Authorization"
  //   ] = `Token ${JwtService.getToken()}`;
  // }

  async query(resource: string, params: any) {
    try {
      const res = await Vue.axios.get(`${this.api}/${resource}`, params)
      return res.data
    } catch (error) {
      console.error(`[RWV] QUERY ApiService`, error.response.data)
      return error.response.data
    }
  }

  async get(resource: string, slug = '') {
    try {
      const res = await Vue.axios.get(`${this.api}/${resource}/${slug}`, this.getConfig())
      return res.data
    } catch (error) {
      console.error(`[RWV] GET ApiService`, error.response.data)
      return error.response.data
    }
  }

  async post(resource: string, params: any) {
    try {
      const res = await Vue.axios.post(`${this.api}/${resource}`, params, this.getConfig())
      return res.data
    } catch (error) {
      console.error(`[RWV] POST ApiService`, error.response.data)
      return error.response.data
    }
  }

  async update(resource: string, slug: string, params: any) {
    try {
      const res = await Vue.axios.put(`${this.api}/${resource}/${slug}`, params, this.getConfig())
      return res.data
    } catch (error) {
      console.error(`[RWV] UPDATE ApiService`, error.response.data)
      return error.response.data
    }
  }

  async put(resource: string, params: any) {
    try {
      const res = await Vue.axios.put(`${this.api}/${resource}`, params, this.getConfig())
      return res.data
    } catch (error) {
      console.error(`[RWV] PUT ApiService`, error.response.data)
      return error.response.data
    }
  }

  async delete(resource: string) {
    try {
      const res = await Vue.axios.delete(`${this.api}/${resource}`, this.getConfig())
      return res.data
    } catch (error) {
      console.error(`[RWV] DELETE ApiService`, error.response.data)
      return error.response.data
    }
  }

  async postFormData(resource: string, bodyFormData: FormData) {
    const config = this.getConfig()
    config.headers['Content-Type'] = 'multipart/form-data'
    try {
      const res = await Vue.axios({
        method: 'post',
        url: `${this.api}/${resource}`,
        data: bodyFormData,
        headers: config.headers
      })
      return res.data
    } catch (error) {
      console.error(`[RWV] POST ApiService`, error.response.data)
      return error.response.data
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
