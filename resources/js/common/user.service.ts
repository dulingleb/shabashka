import User from './model/user.model'
import apiService from './api.service'

class UserService {
  private _user: User // TODO: Current auth user

  get user(): User {
    return this._user
  }

  async getTestUsers(): Promise<User[]> {
    const users = await apiService.get('users')
    if (users && users.length) {
      return users
    }
    return []
  }
}

const userService = new UserService()

export default userService
