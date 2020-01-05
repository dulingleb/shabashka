import Vue from 'vue'

import Tasks from './Tasks.vue'
import TaskEdit from './TaskEdit.vue'
import TaskDeleteModal from './TaskDeleteModal.vue'
import TaskResponceMessages from './TaskResponceMessages.vue'

Vue.component('app-tasks', Tasks)
Vue.component('app-task-edit', TaskEdit)
Vue.component('app-delete-modal', TaskDeleteModal)
Vue.component('app-task-responce-messages', TaskResponceMessages)
