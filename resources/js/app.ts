import './bootstrap'
import Vue from 'vue'
import { install as storageInstall } from 'vue-storage-plus'
import { NavbarPlugin, FormPlugin, FormInputPlugin, FormTextareaPlugin, ButtonPlugin, FormGroupPlugin, AlertPlugin, SpinnerPlugin, ModalPlugin } from 'bootstrap-vue'
import VueTheMask from 'vue-the-mask'

// import 'bootstrap/dist/css/bootstrap.css'
// import 'bootstrap-vue/dist/bootstrap-vue.css'

import { library } from '@fortawesome/fontawesome-svg-core'
import { faClock, faFolder, faChevronUp, faChevronLeft, faMapMarkedAlt, faFolderOpen, faTimes, faUser, faCog, faTasks, faSignOutAlt, faFileAlt, faEdit, faTrashAlt, faDollarSign, faCheck } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import StarRating from 'vue-star-rating'

import PrettyCheckbox from 'pretty-checkbox-vue'
import 'pretty-checkbox/src/pretty-checkbox.scss'

import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'

import { store } from './store'
import router from './app.router'
import App from './views/App.vue'

import './shared/index'

Vue.use(storageInstall)

Vue.use(NavbarPlugin)
Vue.use(FormPlugin)
Vue.use(FormGroupPlugin)
Vue.use(FormInputPlugin)
Vue.use(FormTextareaPlugin)
Vue.use(ButtonPlugin)
Vue.use(AlertPlugin)
Vue.use(SpinnerPlugin)
Vue.use(ModalPlugin)

Vue.use(VueTheMask)

library.add(faClock, faFolder, faChevronUp, faChevronLeft, faMapMarkedAlt, faFolderOpen, faTimes, faUser, faCog, faTasks, faSignOutAlt, faFileAlt, faEdit, faTrashAlt, faDollarSign, faCheck)
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('star-rating', StarRating)
Vue.config.productionTip = false

Vue.component('multiselect', Multiselect)

Vue.use(PrettyCheckbox)

const app = new Vue({
  el: '#app',
  components: { App },
  store,
  router
})
