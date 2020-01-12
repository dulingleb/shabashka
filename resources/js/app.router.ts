import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Home from './views/Home.vue'
import Login from './views/Login.vue'
import Register from './views/Reg.vue'
import Profile from './views/Profile.vue'
import Settings from './views/Settings.vue'
import Task from './views/task/Task.vue'
import NewTask from './views/task/NewTask.vue'
import MyTasks from './views/task/MyTasks.vue'
import MyTaskEdit from './views/task/MyTaskEdit.vue'
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
      path: '/profile/:id',
      name: 'profile',
      meta: { requiresAuth: true },
      component: Profile
    },
    {
      path: '/settings',
      name: 'settings',
      meta: { requiresAuth: true },
      component: Settings
    },
    {
      path: '/task/new',
      name: 'newTask',
      meta: { requiresAuth: true },
      component: NewTask
    },
    {
      path: '/task/:id',
      name: 'task',
      component: Task
    },
    {
      path: '/my-task/:id',
      name: 'myTaskEdit',
      meta: { requiresAuth: true },
      component: MyTaskEdit
    },
    {
      path: '/my-tasks',
      name: 'myTasks',
      component: MyTasks
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
