import { CompanyResponse, Company } from './company.model'

export interface User {
  id: number
  name: string
  surname: string
  email: string
  company: Company
  logo: string
  phone: string
  settings: string
}

export interface UserResponse {
  id: number
  name: string
  surname: string
  email: string
  company: CompanyResponse
  logo: string
  phone: string
  settings: string
}
