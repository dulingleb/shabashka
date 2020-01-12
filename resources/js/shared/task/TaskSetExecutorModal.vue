<template>
  <div v-if="task" class="d-inline-block">
    <button v-if="!fullBtn" class="btn btn-link text-info" v-b-modal.modal-set-executor><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Назначить</button>
    <button v-if="fullBtn" class="btn btn-outline-info" v-b-modal.modal-set-executor><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Назначить</button>

    <b-modal id="modal-set-executor" ref="modal" title="Назначение исполнителем" hide-footer @hide="hiddenModal">

      <p class="mt-2 mb-4">Вы действительно хотите назначить <b><i>{{ response.user.title }}</i></b> исполнителем задания <b><i>{{ task.title }}</i></b>?</p>

      <app-message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></app-message>
      <app-message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

      <div class="text-right btns">
        <button class="btn btn-outline-danger" @click="handleOk" :disabled="loading"><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Назначить</button>
        <button class="btn btn-outline-info" type="button" @click="cancelModal" :disabled="loading">Отмена</button>
      </div>

    </b-modal>

  </div>
</template>

<script lang="ts">

import taskService from '../../common/task.service'
import taskHelperService from '../../common/task-helper.service'
import { getErrTitles } from '../../common/utils'

export default {
  name: 'app-set-executor-modal',
  props: ['task', 'response', 'fullBtn'],
  data() {
    return {
      loading: false,
      form: {
        rating: 5,
        text: ''
      },
      messages: [],
      errMessages: [],
    }
  },
  methods: {
    hiddenModal() {
      this.clearMessages()
      this.loading = false
    },
    cancelModal() {
      this.clearMessages()
      this.$refs['modal'].hide()
    },
    async handleOk(bvModalEvt) {
      this.clearMessages()
      this.loading = true
      const response = await taskService.setExecutor(this.task.id, this.response.user.id)
      this.loading = false

      if (response.success) {
        this.messages = ['Исполнитель назначен.']
        setTimeout(() => {
          this.$refs['modal'].hide()
          this.$emit('execut-task', taskHelperService.convertResTask(response.data))
        }, 1000)
        return
      }
      this.errMessages = getErrTitles(response.error)
    },

    clearMessages() {
      this.messages = []
      this.errMessages = []
    }
  }
}
</script> 

<style lang="scss" scoped>
  .loading {
    padding: 15px;
    text-align: center;
    .spinner {
      width: 3rem;
      height: 3rem;
    }
  }
</style>