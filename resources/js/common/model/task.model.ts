import { CategoryResponse } from './category.model'
import { Rate, RateResponse } from './user.model'

export interface Task {
  id: number
  title: string
  description: string
  price: number
  phone: string
  createdAt: Date
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
  createdAt: Date
  user: {
    id: number
    title: string
    logo: string,
    rate: Rate
  }
  messages: TaskResMessage[]
}

export interface TaskResResponse {
  id: number
  text: string
  price: number
  created_at: string
  user: {
    id: number
    title: string
    logo: string,
    rate: RateResponse
  }
  messages: TaskResMessageResponse[]
}

export interface TaskResMessage {
  id: number
  responseId: number
  userId: number
  text: string
  createdAt: Date
}

export interface TaskResMessageResponse {
  id: number
  response_id: number
  user_id: number
  text: string
  created_at: string
}

export interface TaskOptions {
  start: number
  limit: number
  sort: string
  categories: number[]
  search: string
  userId: number
}
