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
}

export interface TaskResponse {
  id: number
  title: string
  description: string
  price: number
  created_at: string
  term: string
  category: number
}
