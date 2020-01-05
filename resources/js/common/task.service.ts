import { Task, TaskResponse, TaskOptions, TaskResResponse, TaskRes } from './model/task.model'
import apiService from './api.service'
import { ResponseApi } from './model/api.model'

class TaskService {
  private _tasks: Task[]
  private _task: Task

  get tasks(): Task[] {
    return this._tasks
  }

  get task(): Task {
    return this._task
  }

  async getTasks(taskOptions: TaskOptions): Promise<Task[]> {
    let query = `?start=${taskOptions.start}`
    if (taskOptions.limit) {
      query += `&limit=${taskOptions.limit}`
    }
    if (taskOptions.categories && taskOptions.categories.length) {
      query += `&categories=[${taskOptions.categories}]`
    }
    if (taskOptions.search && taskOptions.search.length > 2) {
      query += `&search=${taskOptions.search}`
    }
    if (taskOptions.userId || taskOptions.userId === 0) {
      query += `&user_id=${taskOptions.userId}`
    }
    const response = await apiService.get('tasks', query)
    if (response.success) {
      const resTasks: TaskResponse[] = response.data
      this._tasks = resTasks.map(dataTask => this.convertResTask(dataTask))
    }
    return this.tasks
  }

  async getTask(id: number): Promise<Task> {
    const response = await apiService.get(`task/${id}`)
    if (response.success) {
      const resTask: TaskResponse = response.data
      return this.convertResTask(resTask)
    }
    return null
  }

  async addTask(categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[]): Promise<ResponseApi> {
    const bodyFormData = this.parseChangedFields(categoryId, title, description, address, term, price, phone, files)
    const response: ResponseApi = await apiService.postFormData('task/store', bodyFormData)
    return response
  }

  async editTask(id: string, categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[], filesRemoved: File[]): Promise<ResponseApi> {
    const bodyFormData = this.parseChangedFields(categoryId, title, description, address, term, price, phone, files, filesRemoved)
    const response: ResponseApi = await apiService.postFormData(`task/${id}`, bodyFormData)
    return response
  }

  async responseTask(id: number, text: string, price: string): Promise<void> {
    const response = await apiService.post(`task/${id}/response`, { text, price })
    if (response.success) {
      const resTask = response.data
    }
  }

  async deleteTask(id: number): Promise<ResponseApi> {
    const response: ResponseApi = await apiService.delete(`task/${id}`)
    return response
  }

  async getResponses(id: number): Promise<{ responses: TaskRes[], total: number }> {
    const response: ResponseApi = await apiService.get(`task/${id}/responses`)
    if (response.success) {
      const responses: TaskRes[] = []
      for (let res of response.data) {
        responses.push(this.convertRes(res))
      }
      return { responses, total: response.total }
    }
    return { responses: [], total: -1 }
  }

  parseChangedFields(categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[], filesRemoved: File[] = []): FormData {
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
    for (const file of filesRemoved) {
      bodyFormData.append('files_remove[]', file)
    }
    return bodyFormData
  }

  private convertResTask(resTask: TaskResponse): Task {
    return {
      id: resTask.id,
      title: resTask.title,
      description: resTask.description,
      price: resTask.price,
      phone: resTask.phone,
      createdAt: new Date(resTask.created_at),
      term: new Date(resTask.term),
      categoryId: resTask.category_id,
      address: resTask.address,
      executorId: resTask.executor_id,
      files: resTask.files || [],
      status: resTask.status,
      userId: resTask.user_id,
      userTitle: resTask.user_title,
    } as Task
  }

  private convertRes(res: TaskResResponse): TaskRes {
    const user = {
      id: res.user.id,
      name: res.user.name,
      logo: res.user.logo,
      rate: {
        assessment: res.user.rate.assessment,
        countAssessment: res.user.rate.count_assessment,
        countDone: res.user.rate.count_done
      }
    }
    return {
      id: res.id,
      text: res.text,
      price: res.price,
      userId: res.user_id,
      createdAt: new Date(res.created_at),
      user
    } as TaskRes
  }

}

const taskService = new TaskService()

export default taskService
