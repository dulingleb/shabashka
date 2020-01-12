import { User, UserResponse, Rate, RateResponse } from './model/user.model'
import { CompanyResponse, Company } from './model/company.model'

class UserHelperService {

  parseUser(data: UserResponse): User {
    if (!data) { return null }
    return {
      id: +data.id,
      name: data.name !== null ? data.name : '',
      surname: data.surname !== null ? data.surname : '',
      email: data.email !== null ? data.email : '',
      company: this.parseCompany(data.company),
      logo: data.logo !== null ? data.logo : '',
      phone: data.phone !== null ? data.phone : '',
      settings: data.settings !== null ? data.settings : '',
      rate: this.parseRate(data.rate)
    }
  }

  parseCompany(data: CompanyResponse): Company {
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

  parseRate(data: RateResponse): Rate {
    if (!data) { return {} as Rate }
    return {
      assessment: +data.assessment,
      countAssessment: +data.count_assessment,
      countDone: +data.count_done
    }
  }

  parseChangedFields(name: string, email: string, surname: string, phone: string, logo: File, password: string, cPassword: string, company: Company, companyFiles: File[], companyFilesRemoved: File[]): FormData {
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
    newCompany['is_active'] = !!company.isActive
    if (company.title && company.title.length) newCompany['title'] = company.title
    if (company.inn && company.inn.length) newCompany['inn'] = company.inn
    if (company.description && company.description.length) newCompany['description'] = company.description
    if (company.address && company.address.length) newCompany['address'] = company.address
    Object.keys(newCompany).forEach((key) => {
      bodyFormData.set(`company[${key}]`, newCompany[key])
    })
    for (let file of companyFiles) {
      bodyFormData.append('company[documents][]', file)
    }
    for (let file of companyFilesRemoved) {
      bodyFormData.append('company[documents_remove][]', file)
    }
    // bodyFormData.set('company', company)
    return bodyFormData
  }

}

const userHelperService = new UserHelperService()

export default userHelperService
