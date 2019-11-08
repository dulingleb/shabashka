import { Task, TaskResponse } from './model/task.model'
import apiService from './api.service'

class TaskService {
  private _tasks: Task[]

  get tasks(): Task[] {
    return this._tasks
  }

  async getTaks(start = 0, limit = 10, sort = 'DESC', categories = []): Promise<Task[]> {
    const response = await apiService.get('tasks')
    if (response.success) {
      const resCategories: TaskResponse[] = response.data
      this._tasks = resCategories.map(dataTask => this.convertResTask(dataTask))
    }
    return this.tasks
  }

  async getTask(id: number): Promise<Task> {
    return { id, title: 'TEST. TODO: Get task by id' } as Task
  }

  private convertResTask(resTask: TaskResponse): Task {
    return {
      id: resTask.id,
      title: resTask.title,
      description: resTask.description,
      price: resTask.price,
      createdAt: resTask.created_at,
      created: resTask.term,
      categoryId: resTask.category ? resTask.category.id : null
    } as Task
  }

}

const taskService = new TaskService()

export default taskService
