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
  rate: Rate
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
  rate: RateResponse
}

export interface Rate {
  assessment: number
  countAssessment: number
  countDone: number
}

export interface RateResponse {
  assessment: number
  count_assessment: number
  count_done: number
}
