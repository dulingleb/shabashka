
import { ls } from 'vue-storage-plus'

class JwtService {
  private _token: string

  get token(): string {
    return this._token
  }

  init() {
    this._token = ls.get('token')
  }

  saveToken(token: string) {
    this._token = token
    ls.set('token', token)
  }

  resetToken() {
    this._token = ''
    ls.remove('token')
  }

}

const jwtService = new JwtService()

export default jwtService
