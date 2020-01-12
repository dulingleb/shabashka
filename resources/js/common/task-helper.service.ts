import { Task, TaskResponse, TaskResResponse, TaskRes, TaskResMessage, TaskResMessageResponse } from './model/task.model'

class TaskHelperService {

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

  convertResMessage(message: TaskResMessageResponse): TaskResMessage {
    return {
      id: +message.id,
      createdAt: new Date(message.created_at),
      responseId: message.response_id,
      text: message.text,
      userId: message.user_id
    }
  }

  convertResTask(resTask: TaskResponse): Task {
    return {
      id: +resTask.id,
      title: resTask.title,
      description: resTask.description,
      price: +resTask.price,
      phone: resTask.phone,
      createdAt: new Date(resTask.created_at),
      term: new Date(resTask.term),
      categoryId: +resTask.category_id,
      address: resTask.address,
      executorId: +resTask.executor_id,
      files: resTask.files || [],
      status: resTask.status,
      userId: +resTask.user_id,
      userTitle: resTask.user_title
    }
  }

  convertRes(res: TaskResResponse): TaskRes {
    const user = {
      id: +res.user.id,
      title: res.user.title,
      logo: res.user.logo,
      rate: {
        assessment: +res.user.rate.assessment,
        countAssessment: +res.user.rate.count_assessment,
        countDone: +res.user.rate.count_done
      }
    }
    return {
      id: +res.id,
      text: res.text,
      price: +res.price,
      createdAt: new Date(res.created_at),
      user,
      messages: this.convertResMessages(res.messages)
    }
  }

  convertResMessages(messages: TaskResMessageResponse[]): TaskResMessage[] {
    const result = []
    for (let message of messages) {
      result.push(this.convertResMessage(message))
    }
    return result
  }

}

const taskHelperService = new TaskHelperService()

export default taskHelperService
