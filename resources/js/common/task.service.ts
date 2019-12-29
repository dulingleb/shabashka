import { Task, TaskResponse } from './model/task.model'
import apiService from './api.service'

class TaskService {
  private _tasks: Task[]
  private _task: Task

  get tasks(): Task[] {
    return this._tasks
  }

  get task(): Task {
    return this._task
  }

  async getTasks(start = 0, limit = 10, sort = 'DESC', categories = null, search = ''): Promise<Task[]> {
    let query = `?start=${start}`
    if (limit) {
      query += `&limit=${limit}`
    }
    if (categories && categories.length) {
      query += `&categories=[${categories}]`
    }
    if (search && search.length > 2) {
      query += `&search=${search}`
    }
    const response = await apiService.get('tasks', query)
    if (response.success) {
      const resTasks: TaskResponse[] = response.data
      this._tasks = resTasks.map(dataTask => this.convertResTask(dataTask))
    }
    return this.tasks
  }

  async addTask(categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[]): Promise<void> {
    const bodyFormData = new FormData()
    bodyFormData.set('category_id', categoryId)
    bodyFormData.set('title', title)
    bodyFormData.set('description', description)
    bodyFormData.set('address', address)
    bodyFormData.set('term', term.toISOString())
    bodyFormData.set('price', price)
    bodyFormData.set('phone', phone)
    for (const file of files) {
      bodyFormData.append('files[]', file)
    }
    const response = await apiService.postFormData('task/store', bodyFormData)
    console.log('res', response)
  }

  async responseTask(id: number, text: string, price: string): Promise<void> {
    const response = await apiService.post(`task/${id}/response`, { text, price })
    console.log(response)
    if (response.success) {
      const resTask = response.data
    }
  }

  async getTask(id: number): Promise<Task> {
    const response = await apiService.get(`task/${id}`)
    if (response.success) {
      const resTask: TaskResponse = response.data
      this._task = this.convertResTask(resTask)
    }
    return this.task
  }

  private convertResTask(resTask: TaskResponse): Task {
    return {
      id: resTask.id,
      title: resTask.title,
      description: resTask.description,
      price: resTask.price,
      createdAt: resTask.created_at,
      created: resTask.term,
      categoryId: resTask.category_id,
      address: resTask.address,
      executor: resTask.executor,
      files: resTask.files,
      status: resTask.status,
      userId: resTask.user_id,
    } as Task
  }

}

const taskService = new TaskService()

export default taskService
