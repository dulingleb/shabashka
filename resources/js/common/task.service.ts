import { Task, TaskResponse, TaskOptions, TaskResResponse, TaskRes, TaskResMessage, TaskResMessageResponse } from './model/task.model'
import apiService from './api.service'
import taskHelperService from './task-helper.service'
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
      this._tasks = resTasks.map(dataTask => taskHelperService.convertResTask(dataTask))
    }
    return this.tasks
  }

  async getTask(id: number): Promise<Task> {
    const response = await apiService.get(`task/${id}`)
    if (response.success) {
      const resTask: TaskResponse = response.data
      return taskHelperService.convertResTask(resTask)
    }
    return null
  }

  async addTask(categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[]): Promise<ResponseApi> {
    const bodyFormData = taskHelperService.parseChangedFields(categoryId, title, description, address, term, price, phone, files)
    const response: ResponseApi = await apiService.postFormData('task/store', bodyFormData)
    return response
  }

  async editTask(id: string, categoryId: string, title: string, description: string, address: string, term: Date, price: string, phone: string, files: File[], filesRemoved: File[]): Promise<ResponseApi> {
    const bodyFormData = taskHelperService.parseChangedFields(categoryId, title, description, address, term, price, phone, files, filesRemoved)
    const response: ResponseApi = await apiService.postFormData(`task/${id}`, bodyFormData)
    return response
  }

  async responseTask(id: number, text: string, price: string): Promise<ResponseApi> {
    const response: ResponseApi = await apiService.post(`task/${id}/response`, { text, price })
    return response
  }

  async responseMessageTask(taskId: number, responseId: number, text: string): Promise<ResponseApi> {
    const response: ResponseApi = await apiService.post(`task/${taskId}/message`, { response_id: responseId, text })
    return response
  }

  async setExecutor(taskId: number, executorId: number): Promise<ResponseApi> {
    const response: ResponseApi = await apiService.post(`task/${taskId}/set-executor`, { executor_id: executorId })
    return response
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
        responses.push(taskHelperService.convertRes(res))
      }
      return { responses, total: response.total }
    }
    return { responses: [], total: -1 }
  }

}

const taskService = new TaskService()

export default taskService
