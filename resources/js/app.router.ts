import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Hello from './views/Hello.vue'
import Home from './views/Home.vue'
import Login from './views/Login.vue'
import Register from './views/Reg.vue'
import Profile from './views/Profile.vue'
import Task from './views/Task.vue'
import NewTask from './views/NewTask.vue'
import { store } from './store'

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/hello',
      name: 'hello',
      component: Hello
    },
    {
      path: '/login',
      name: 'login',
      meta: { guestOnly: true },
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      meta: { guestOnly: true },
      component: Register
    },
    {
      path: '/profile',
      name: 'profile',
      meta: { requiresAuth: true },
      component: Profile
    },
    {
      path: '/task/new',
      name: 'newTask',
      component: NewTask
    },
    {
      path: '/task/:id',
      name: 'task',
      component: Task
    }
  ]
})

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!store.getters.user) {
      next({ name: 'home' })
    }
  } else {
    next()
  }
  if (to.matched.some(record => record.meta.guestOnly)) {
    if (store.getters.user) {
      next({ name: 'home' })
    }
  } else {
    next()
  }
  to.matched.some(record => record.meta.isAuth)
})

export default router
