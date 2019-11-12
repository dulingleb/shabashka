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

  async getTasks(start = 0, limit = 10, sort = 'DESC', categories = [], search = ''): Promise<Task[]> {
    const response = await apiService.get('tasks',
      `?start=${start}` +
      `&limit=${limit}` +
      `&categories=[${categories}]` +
      `&search=${search.length > 2 ? search : ''}`
    )
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
      categoryId: resTask.category,
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
