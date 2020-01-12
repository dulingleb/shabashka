import { User, Review } from './model/user.model'
import userHelperService from './user-helper.service'
import apiService from './api.service'
import jwtService from './jwt.service'
import { CompanyResponse, Company } from './model/company.model'
import { ResponseApiAuth, ResponseApi } from './model/api.model'

class UserService {

  async getTestUser(): Promise<User> {
    const response = await apiService.get('user/me')
    if (response.success) {
      return userHelperService.parseUser(response.data)
    }
    return null
  }

  async getUserById(id: number): Promise<User> {
    const response = await apiService.get(`user/${id}`)
    if (response.success) {
      return userHelperService.parseUser(response.data)
    }
    return null
  }

  async auth(email: string, password: string): Promise<boolean> {
    const response: ResponseApiAuth = await apiService.post('oauth/login', { email, password })
    if (response.success) {
      jwtService.saveToken(response.token)
      const user = await this.getTestUser()
      return !!user
    }
    return false
  }

  async register(name: string, email: string, password: string, Cpassword: string): Promise<boolean> {
    const response: ResponseApiAuth = await apiService.post('oauth/register', { name, email, password, c_password: Cpassword })
    if (response.success) {
      jwtService.saveToken(response.token)
      const user = await this.getTestUser()
      return !!user
    }
    return false
  }

  async editUser(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company: Company, companyFiles: File[], companyFilesRemoved: File[]): Promise<ResponseApi> {
    const bodyFormData = userHelperService.parseChangedFields(name, email, surname, phone, logo, password, cPassword, company, companyFiles, companyFilesRemoved)
    const response: ResponseApi = await apiService.postFormData('user/me', bodyFormData)
    return response
  }

  async getReviews(id: number): Promise<{ reviews: Review[], total: number }> {
    const response: ResponseApi = await apiService.get(`user/${id}/reviews`)
    if (response.success) {
      return { reviews: response.data, total: response.total }
    }
    return { reviews: [], total: -1 }
  }

  logout(): void {
    jwtService.clearToken()
  }

}

const userService = new UserService()

export default userService
