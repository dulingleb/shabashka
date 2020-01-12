import Vue from 'vue'

import Tasks from './Tasks.vue'
import TaskEdit from './TaskEdit.vue'
import TaskDeleteModal from './TaskDeleteModal.vue'
import TaskConfirmModal from './TaskConfirmModal.vue'
import TaskSetExecutorModal from './TaskSetExecutorModal.vue'
import TaskResponseMessages from './TaskResponseMessages.vue'

Vue.component('app-tasks', Tasks)
Vue.component('app-task-edit', TaskEdit)
Vue.component('app-delete-modal', TaskDeleteModal)
Vue.component('app-confirm-modal', TaskConfirmModal)
Vue.component('app-set-executor-modal', TaskSetExecutorModal)
Vue.component('app-task-response-messages', TaskResponseMessages)
