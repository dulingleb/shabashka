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

  async editUser(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company): Promise<void> {
    const bodyFormData = this.parseChangedFields(name, email, surname, phone, logo, password, cPassword, company)
    const response = await apiService.postFormData('user/me', bodyFormData)
    console.log('res', response)
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

  private parseChangedFields(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company): FormData {
    const bodyFormData = new FormData()
    if (name && name.length) bodyFormData.set('name', name)
    if (email && email.length) bodyFormData.set('email', email)
    if (surname && surname.length) bodyFormData.set('surname', surname)
    if (phone && phone.length) bodyFormData.set('phone', phone)
    if (!logo) bodyFormData.set('logo', logo)
    if (password && password.length && cPassword && cPassword.length && cPassword.length === password.length) {
      bodyFormData.set('password', password)
      bodyFormData.set('c_password', cPassword)
    }
    return bodyFormData
  }

}

const userService = new UserService()

export default userService
