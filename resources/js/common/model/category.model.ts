export interface Category {
  id: number
  title: string
  children: Category[]
}

export interface CategoryResponse {
  id: number
  title: string
  parent: number
}
