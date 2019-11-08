import User from './model/user.model'
import apiService from './api.service'
import jwtService from './jwt.service'

class UserService {
  async getTestUser(): Promise<User> {
    const response = await apiService.get('user/me')
    if (response.success) {
      return response.data
    }
    return null
  }

  async auth(email: string, password: string): Promise<boolean> {
    const response = await apiService.post('oauth/login', { email, password })
    if (response.success) {
      jwtService.saveToken(response.token)
      const user = await this.getTestUser()
      return !!user
    }
    return false
  }

  async register(name: string, email: string, password: string, Cpassword: string): Promise<boolean> {
    const response = await apiService.post('oauth/register', { name, email, password, c_password: Cpassword })
    if (response.success) {
      jwtService.saveToken(response.token)
      const user = await this.getTestUser()
      return !!user
    }
    return false
  }

  logout(): void {
    jwtService.clearToken()
  }

}

const userService = new UserService()

export default userService
