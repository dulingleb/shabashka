<template>
  <div v-if="task" class="d-inline-block">
    <button v-if="!fullBtn" class="btn btn-link text-danger" v-b-modal.modal-prevent-closing><font-awesome-icon :icon="['fas', 'trash-alt']" class="icon" /> Удалить</button>
    <button v-if="fullBtn" class="btn btn-outline-danger" v-b-modal.modal-prevent-closing><font-awesome-icon :icon="['fas', 'trash-alt']" class="icon" /> Удалить</button>

    <b-modal id="modal-prevent-closing" ref="modal" title="Удаление задания" hide-footer @hide="hiddenModal">
      <p class="mt-2 mb-4">Вы действительно хотите удалить "{{ task.title }}"?</p>

      <app-message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></app-message>
      <app-message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

      <div class="text-right btns">
        <button class="btn btn-outline-danger" @click="handleOk" :disabled="loading"><font-awesome-icon :icon="['fas', 'trash-alt']" class="icon" /> Удалить</button>
        <button class="btn btn-outline-info" @click="cancelModal" :disabled="loading">Отмена</button>
      </div>
    </b-modal>

  </div>
</template>

<script lang="ts">

import taskService from '../common/task.service'
import { getErrTitles } from '../common/utils'

export default {
  name: 'app-delete-modal',
  props: ['task', 'fullBtn'],
  data() {
    return {
      loading: false,
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
      const response = await taskService.deleteTask(this.task.id)
      if (response.success) {
        this.messages = ['Задание удалено.']
        setTimeout(() => {
          this.$refs['modal'].hide()
          this.$emit('delete-task', this.task.id)
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