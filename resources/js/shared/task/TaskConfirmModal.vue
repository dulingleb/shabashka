<template>
  <div v-if="task" class="d-inline-block">
    <button v-if="!fullBtn" class="btn btn-link text-info" v-b-modal.modal-confirm><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Подтвердить</button>
    <button v-if="fullBtn" class="btn btn-outline-info" v-b-modal.modal-confirm><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Подтвердить</button>

    <b-modal id="modal-confirm" ref="modal" title="Подтверждение задания" hide-footer @hide="hiddenModal">

      <b-form @submit.prevent="handleOk">

        <b-form-group name="rating" description="">
          <div class="rating-wrapp">
            <label class="rating-label" for="rating">Оценка:</label>
            <div class="rating-stars">
              <star-rating v-model="form.rating" :read-only="false" :show-rating="false" :star-size="20"></star-rating>
            </div>
          </div>
        </b-form-group>

        <b-form-group name="text" description="">
          <div>
            <b-form-textarea id="text" v-model="form.text" rows="1" placeholder="Пару слов про исполнителя..." :disabled="loading"></b-form-textarea>
          </div>
        </b-form-group>

      <app-message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></app-message>
      <app-message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

      <div class="text-right btns">
        <button class="btn btn-outline-danger" :disabled="loading"><font-awesome-icon :icon="['fas', 'check']" class="icon" /> Подтвердить</button>
        <button class="btn btn-outline-info" type="button" @click="cancelModal" :disabled="loading">Отмена</button>
      </div>

      </b-form>

      
    </b-modal>

  </div>
</template>

<script lang="ts">

import taskService from '../../common/task.service'
import taskHelperService from '../../common/task-helper.service'
import { getErrTitles } from '../../common/utils'

export default {
  name: 'app-confirm-modal',
  props: ['task', 'fullBtn'],
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
      const response = await taskService.confirmTask(this.task.id, this.form.rating, this.form.text)
      this.loading = false
      if (response.success) {
        this.messages = ['Задание подтверждено.']
        setTimeout(() => {
          this.$refs['modal'].hide()
          this.$emit('confirm-task', taskHelperService.convertResTask(response.data))
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

  .rating-wrapp {
    margin-top: -4px;
    display: flex;
    align-items: center;
    .rating-label {
      margin: 4px 5px 0 0;
    }
    .rating-stars {

    }
  }

</style>