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
  executorId: number
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
  category_id: number
  address: string
  executor_id: number
  files: string[]
  status: string
  user_id: number
}

export interface TaskRes {
  id: number
  text: string
  price: number
  userId: number
}

export interface TaskResResponse {
  id: number
  text: string
  price: number
  user_id: number
}
