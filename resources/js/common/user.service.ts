import User from './model/user.model'
import apiService from './api.service'
import jwtService from './jwt.service'

class UserService {
  async getTestUser(): Promise<User> {
    const response = await apiService.get('user/me')
    if (response.success) {
      console.log(this.parseUser(response.data))
      return this.parseUser(response.data)
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

  private parseUser(data: any): User {
    if (!data) { return null }
    return {
      id: +data.id,
      name: data.name !== null ? data.name : '',
      surname: data.surname !== null ? data.surname : '',
      email: data.email !== null ? data.email : '',
      avatar: data.avatar !== null ? data.avatar : '',
      company: data.company !== null ? data.company : '',
      emailvverifiedAt: data.email_verified_at,
      logo: data.logo !== null ? data.logo : '',
      phone: data.phone !== null ? data.phone : '',
      role_id: data.role_id !== null ? data.role_id : '',
      settings: data.settings !== null ? data.settings : '',
      createdAt: data.created_at,
      updatedAt: data.updated_at
    }
  }

}

const userService = new UserService()

export default userService
