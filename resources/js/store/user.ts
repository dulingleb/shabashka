import userService from '../common/user.service'
import { User } from '../common/model/user.model'

class UserState {
  user: User
  constructor() {
    this.user = {} as User
  }
}

const state = new UserState()

const getters = {
  user: (state: UserState) => {
    return state.user.id !== undefined && state.user
  },
}

const mutations = {
  SET_USER: (state: UserState, payload: User) => {
    state.user = payload
  }
}

const actions = {
  GET_USER: async(context: any, payload: any) => {
    const user = await userService.getTestUser()
    context.commit('SET_USER', user)
  },

  SET_USER: async(context: any, payload: any) => {
    context.commit('SET_USER', payload)
  },

  LOGOUT: async(context: any, payload: any) => {
    userService.logout()
    context.commit('SET_USER', {})
  }
}

export default {
  state,
  getters,
  mutations,
  actions,
}
