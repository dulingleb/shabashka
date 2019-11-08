import Vue from 'vue'
import Vuex from 'vuex'
import userState from './user'

Vue.use(Vuex)

export const store = new Vuex.Store({
  state: {},
  getters: {},
  mutations: {},
  actions: {},
  modules: {
    userState
  }
})
