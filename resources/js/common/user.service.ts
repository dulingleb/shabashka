import { User, UserResponse } from './model/user.model'
import apiService from './api.service'
import jwtService from './jwt.service'
import { CompanyResponse, Company } from './model/company.model'

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

  async editUser(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company: Company, companyFiles: File[]): Promise<void> {
    const bodyFormData = this.parseChangedFields(name, email, surname, phone, logo, password, cPassword, company, companyFiles)
    const response = await apiService.postFormData('user/me', bodyFormData)
    console.log('res', response)
  }

  logout(): void {
    jwtService.clearToken()
  }

  private parseUser(data: UserResponse): User {
    if (!data) { return null }
    return {
      id: +data.id,
      name: data.name !== null ? data.name : '',
      surname: data.surname !== null ? data.surname : '',
      email: data.email !== null ? data.email : '',
      company: this.parseCompany(data.company),
      logo: data.logo !== null ? data.logo : '',
      phone: data.phone !== null ? data.phone : '',
      settings: data.settings !== null ? data.settings : ''
    }
  }

  private parseCompany(data: CompanyResponse): Company {
    if (!data) { return {} as Company }
    return {
      id: +data.id,
      isActive: !!data.is_active,
      title: data.title !== null ? data.title : '',
      inn: data.inn !== null ? data.inn : '',
      address: data.address !== null ? data.address : '',
      description: data.description !== null ? data.description : '',
      moderateStatus: data.moderate_status,
      documents: data.documents !== null ? data.documents : [],
      categories: data.categories !== null ? data.categories : []
    }
  }

  private parseChangedFields(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company: Company, companyFiles: File[]): FormData {
    const bodyFormData = new FormData()
    if (name && name.length) bodyFormData.set('name', name)
    if (email && email.length) bodyFormData.set('email', email)
    if (surname && surname.length) bodyFormData.set('surname', surname)
    if (phone && phone.length) bodyFormData.set('phone', phone)
    if (logo) bodyFormData.set('logo', logo)
    if (password && password.length && cPassword && cPassword.length && cPassword.length === password.length) {
      bodyFormData.set('password', password)
      bodyFormData.set('c_password', cPassword)
    }
    if (!company) return bodyFormData
    const newCompany = []
    try {
      newCompany['is_active'] = company.isActive
      if (company.title && company.title.length) newCompany['title'] = company.title
      if (company.inn && company.inn.length) newCompany['inn'] = company.inn
      if (company.description && company.description.length) newCompany['description'] = company.description
      if (company.address && company.address.length) newCompany['address'] = company.address
      console.log('newCompany', newCompany)
      Object.keys(newCompany).forEach((key) => {
        bodyFormData.set(`company[${key}]`, newCompany[key])
      })
      for (let file of companyFiles) {
        bodyFormData.append('company[documents][]', file)
      }
    // tslint:disable-next-line: no-empty
    } catch (e) { }
    // bodyFormData.set('company', company)
    return bodyFormData
  }

}

const userService = new UserService()

export default userService
