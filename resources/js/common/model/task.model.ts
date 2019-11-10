import { CategoryResponse } from './category.model'

export interface Task {
  id: number
  title: string
  description: string
  price: number
  createdAt: string
  created: string
  term: string
  categoryId: number
  address: string
  executor: number
  files: string[]
  status: string
  userId: number
}

export interface TaskResponse {
  id: number
  title: string
  description: string
  price: number
  created_at: string
  term: string
  category: number
  address: string
  executor: number
  files: string[]
  status: string
  user_id: number
}
