import User from './model/user.model'
import apiService from './api.service'
import jwtService from './jwt.service'

class UserService {
  private _user: User // TODO: Current auth user

  get user(): User {
    return this._user
  }

  async init() {
    await this.getTestUser()
  }

  async getTestUser(): Promise<User> {
    const response = await apiService.get('user')
    console.log('getTestUser', response)
    return null
  }

  async auth(email: string, password: string): Promise<boolean> {
    const response = await apiService.post('oauth/login', { email, password })
    if (response.success) {
      jwtService.saveToken(response.token)
      const user = await this.getTestUser()
    }
    console.log('user', response)
    return false
  }

}

const userService = new UserService()

export default userService
