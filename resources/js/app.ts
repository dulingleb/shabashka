import './bootstrap'
import Vue from 'vue'
import { NavbarPlugin, FormPlugin, FormInputPlugin, ButtonPlugin } from 'bootstrap-vue'


import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import router from './app.router'
import apiService from './common/api.service'
import App from './views/App.vue'

Vue.use(NavbarPlugin)
Vue.use(FormPlugin)
Vue.use(FormInputPlugin)
Vue.use(ButtonPlugin)

apiService.init();

const app = new Vue({
  el: '#app',
  components: { App },
  router,
}); 
