import './bootstrap'
import Vue from 'vue'
import { install as storageInstall } from 'vue-storage-plus'
import { NavbarPlugin, FormPlugin, FormInputPlugin, ButtonPlugin, FormGroupPlugin } from 'bootstrap-vue'
import VueTheMask from 'vue-the-mask'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faClock, faFolder } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

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

library.add(faClock, faFolder)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.config.productionTip = false

const app = new Vue({
  el: '#app',
  components: { App },
  store,
  router
})
