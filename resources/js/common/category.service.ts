import { CategoryResponse, Category } from './model/category.model'
import apiService from './api.service'

class CategoryService {
  private _categories: Category[]

  get categories(): Category[] {
    return this._categories
  }

  async getCategories(): Promise<Category[]> {
    const response = await apiService.get('categories')
    if (response.success) {
      const resCategories: CategoryResponse[] = response.data

      this._categories = resCategories.map(dataCategory => {
        if (!dataCategory.parent) {
          return this.convertResCategory(dataCategory)
        }
      }).filter(category => category)

      for (const dataCategory of resCategories) {
        if (!dataCategory.parent) { continue }
        const index = this._categories.findIndex((category => category.id === dataCategory.parent))
        if (index === -1) { continue }
        this._categories[index].children.push(this.convertResCategory(dataCategory))
      }
    }
    return this.categories
  }

  getCategoryName(categories: Category[], id: number): string {
    for (const categoryParent of categories) {
      const category = categoryParent.children.find(category => category.id === id)
      if (category) {
        return category.title
      }
    }
  }

  getIds(categories: Category[]): string[] {
    const ids = []
    for (const categoryParent of categories) {
      ids.push(...categoryParent.children.map(category => category.id))
    }
    return ids
  }

  private convertResCategory(resCategory: CategoryResponse): Category {
    return {
      id: resCategory.id,
      title: resCategory.title,
      children: []
    } as Category
  }

}

const categoryService = new CategoryService()

export default categoryService
