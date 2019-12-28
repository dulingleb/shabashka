export interface Company {
  id: number
  isActive: boolean
  title: string
  inn: string
  address: string
  description: string
  moderateStatus: ComapnyStatus
  documents: string[]
  categories: number[]
}

export interface CompanyResponse {
  id: number
  is_active: boolean
  title: string
  inn: string
  address: string
  description: string
  moderate_status: ComapnyStatus
  documents: string[]
  categories: number[]
}

export enum ComapnyStatus {
  moderate = 'moderate',
  active = 'active',
  dismiss = 'dismiss',
}
