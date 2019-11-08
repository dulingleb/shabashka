import './bootstrap'
import Vue from 'vue'
import { install as storageInstall } from 'vue-storage-plus'
import { NavbarPlugin, FormPlugin, FormInputPlugin, ButtonPlugin, FormGroupPlugin } from 'bootstrap-vue'
import VueTheMask from 'vue-the-mask'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import { store } from './store'
import router from './app.router'
import App from './views/App.vue'

Vue.use(storageInstall)

Vue.use(NavbarPlugin)
Vue.use(FormPlugin)
Vue.use(FormGroupPlugin)
Vue.use(FormInputPlugin)
Vue.use(ButtonPlugin)

Vue.use(VueTheMask)

const app = new Vue({
  el: '#app',
  components: { App },
  store,
  router
})
