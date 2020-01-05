<template>
  <div>
    <div class="messages">
      <div v-for="(message, keyM) in resMessages" :key="keyM" class="message" :class="{ 'castomer-message': isCustomer(message.userId) }">

        <b-alert class="message-data" :variant="isCustomer(message.userId) ? 'success' : 'info'" show>
          <div class="user-data">
            <p class="user-name m-0">{{ getUserName(message.userId) }}</p>
            <small class="date text-secondary">{{ getTextDate(message.createdAt) }}</small>
          </div>
          <p class="text">{{ message.text }}</p>
        </b-alert>
      </div>
    </div>


    <button type="button" v-b-toggle="`setMessage-${responseId}`" class="btn btn-link pl-0 text-info">Оставить сообщение</button>
      <b-collapse :id="`setMessage-${responseId}`">
        <b-form @submit.prevent="onSubmitMessage">
          <b-form-group name="message" description="">
            <div>
              <b-form-textarea id="message" v-model="form.message" :state="validateMessage" rows="1" required placeholder="Введите сообщение" :disabled="loading || !user"></b-form-textarea>
              <b-form-invalid-feedback :state="validateMessage">Некорректное сообщение.</b-form-invalid-feedback>
            </div>
          </b-form-group>

          <app-message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></app-message>
          <app-message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

          <button v-if="user" :disabled="loading || !user" class="btn btn-outline-success mb-2">Отправить</button>
          <router-link v-if="!user" :to="{ name: 'login' }" class="btn btn-outline-info task-btn mb-2">Логин / Регистрация</router-link>

        </b-form>
      </b-collapse>

  </div>
</template>

<script lang="ts">

import taskService from '../../common/task.service'
import { User } from '../../common/model/user.model'
import { getErrTitles, getTextDate } from '../../common/utils'

export default {
  name: 'app-task-responce-messages',
  props: ['taskId', 'responseId', 'resMessages', 'customer', 'responseUser'],
  data() {
    return {
      loading: false,
      messages: [],
      errMessages: [],
      form: {
        message: ''
      },
      formDirty: false
    }
  },
  watch: {
    form: {
      handler() {
        this.formDirty = true
      },
      deep: true
    }
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    validateMessage() {
      return !this.formDirty || (this.form.message.length > 4 && this.form.message.length < 201)
    },
  },
  methods: {

    getUserName(userId) {
      return this.isCustomer(userId) ? this.user.name : this.responseUser.title
    },

    isCustomer(userId) {
      return userId !== this.responseUser.id
    },

    async onSubmitMessage() {
      this.clearMessages()
      this.loading = true
      const response = await taskService.responseMessageTask(this.taskId, this.responseId, this.form.message)
      this.loading = false
      if (response.success) {
        this.$emit('send-message', this.responseId, taskService.convertResMessage(response.data))
        return
      }
      this.errMessages = getErrTitles(response.error)
    },

    getTextDate(date: Date) {
      return getTextDate(date)
    },

    clearMessages() {
      this.messages = []
      this.errMessages = []
    }
  }
}
</script> 

<style lang="scss" scoped>
  .messages {
    max-height: 150px;
    overflow-y: auto;
    .message {
      padding: 0 0 5px 0;
      line-height: 1;
      font-size: 0.8rem;
      text-align: right;
      .message-data {
        margin: 0;
        padding: 5px 10px;
        display: inline-flex;
        flex-direction: row-reverse;
        width: 98%;
        .user-data {
          margin-left: 10px;
        }
      }
      &.castomer-message {
        text-align: left;
        .message-data {
          flex-direction: row;
          .user-data {
            margin-left: 0;
            margin-right: 10px;
          }
        }
      }
    }
  }
</style>