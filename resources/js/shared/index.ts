import './task/index'
import './messages/index'
import Vue from 'vue'

import DragDropImages from './DragDropImages.vue'
import Avatar from './Avatar.vue'
import Loading from './Loading.vue'

Vue.component('app-drag-drop-images', DragDropImages)
Vue.component('app-avatar', Avatar)
Vue.component('app-loading', Loading)
