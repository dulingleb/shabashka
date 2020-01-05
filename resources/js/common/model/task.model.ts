import { CategoryResponse } from './category.model'
import { Rate, RateResponse } from './user.model'

export interface Task {
  id: number
  title: string
  description: string
  price: number
  phone: string
  createdAt: Date
  created: string
  term: Date
  categoryId: number
  address: string
  executorId: number
  files: string[]
  status: string
  userId: number
  userTitle: string
}

export interface TaskResponse {
  id: number
  title: string
  description: string
  price: number
  phone: string
  created_at: string
  term: string
  category_id: number
  address: string
  executor_id: number
  files: string[]
  status: string
  user_id: number
  user_title: string
}

export interface TaskRes {
  id: number
  text: string
  price: number
  userId: number
  createdAt: Date
  user: {
    id: number
    title: string
    logo: string,
    rate: Rate
  }
}

export interface TaskResResponse {
  id: number
  text: string
  price: number
  user_id: number
  created_at: string
  user: {
    id: number
    title: string
    logo: string,
    rate: RateResponse
  }
}

export interface TaskOptions {
  start: number
  limit: number
  sort: string
  categories: number[]
  search: string
  userId: number
}
