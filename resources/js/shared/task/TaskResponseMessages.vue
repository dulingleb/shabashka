<template>
  <div>
    <div class="messages" ref="messages">
      <div v-for="(message, keyM) in response.messages" :key="keyM" class="message" :class="{ 'customer-message': isCustomer(message.userId) }">

        <b-alert class="message-data" :variant="isCustomer(message.userId) ? 'success' : 'info'" show>
          <div class="user-data">
            <p class="user-name m-0">{{ getUserName(message.userId) }}</p>
            <small class="date text-secondary">{{ getTextDate(message.createdAt) }}</small>
          </div>
          <p class="text">{{ message.text }}</p>
        </b-alert>
      </div>
    </div>


    <button type="button" v-b-toggle="`setMessage-${response.id}`" class="btn btn-link pl-0 text-info">Оставить сообщение</button>
      <b-collapse :id="`setMessage-${response.id}`">
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
import taskHelperService from '../../common/task-helper.service'
import { User } from '../../common/model/user.model'
import { getErrTitles, getTextDate } from '../../common/utils'

export default {
  name: 'app-task-response-messages',
  props: ['task', 'response'],
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
  mounted() {
    this.scrollToBottom()
  },
  updated() {
    this.scrollToBottom()
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    validateMessage() {
      return !this.formDirty || !this.form.message.length || (this.form.message.length > 4 && this.form.message.length < 201)
    },
  },
  methods: {

    getUserName(userId) {
      return this.isCustomer(userId) ? this.task.userTitle : this.response.user.title
    },

    isCustomer(userId) {
      return userId !== this.response.user.id
    },

    async onSubmitMessage() {
      this.clearMessages()
      this.loading = true
      const response = await taskService.responseMessageTask(this.task.id, this.response.id, this.form.message)
      this.loading = false
      if (response.success) {
        this.form.message = ''
        this.formDirty = false
        this.$emit('send-message', this.response.id, taskHelperService.convertResMessage(response.data))
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
    },

    scrollToBottom() {
      this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
    }
  }
}
</script> 

<style lang="scss" scoped>
  .messages {
    padding: 15px;
    max-height: 250px;
    overflow-y: auto;
    background: #fff;
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
        .user-data {
          margin-left: 10px;
        }
      }
      &.customer-message {
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