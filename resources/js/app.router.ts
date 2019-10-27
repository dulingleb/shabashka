import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import Hello from './views/Hello.vue'
import Home from './views/Home.vue'

export default new VueRouter({
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
      component: Hello,
    },
  ],
});
