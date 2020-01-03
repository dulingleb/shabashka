<template>
<div class="">
  <div class="card">
    <div class="card-header" v-if="task">{{ task.title }}, <small class="text-secondary">{{ getTextDate(task.createdAt) }}</small></div>

    <div class="card-body">
      <p v-if="loading">Loading...</p>
      <div v-if="!loading" class="row">
        <div class="col-md-8">
          <div class="user-wrapp">
            <div class="logo-wrapp">
              <avatar v-if="customer" :title="userName" :image="customer.logo" :mini="true"></avatar>
            </div>
            <div class="user-data">
                <p class="m-0 name">{{ userName }}</p>
                <star-rating v-model="rating" :read-only="true" :show-rating="false" :star-size="20"></star-rating>
                <p class="m-0 rating"> <a href="" class="text-secondary">нет отзывов</a></p>
            </div>
          </div>
          <p>{{ task.description }}</p>
          <div v-if="task.files" class="files">
            <a class="file" v-for="(file, index) in task.files" :key="index" :href="file" :title="getFileNameByUrl(file)" target="_blank">
              <div v-if="isImage(file)" class="img">
                <img :src="file" alt="">
              </div>
              <div v-if="!isImage(file)" class="doc">
                <font-awesome-icon :icon="['fas', 'file-alt']" class="icon text-secondary" />
              </div>
            </a>
          </div>

        </div>
        <div class="col-md-4">
          <p class="mb-0">
            <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />{{ getTextDate(task.term) }}</p>
          <p class="mb-0">
            <font-awesome-icon :icon="['fa', 'folder']" class="mr-1 text-secondary" />{{ getCategoryName(task.categoryId) }}</p>
          <p class="mb-0">
            <font-awesome-icon :icon="['fas', 'map-marked-alt']" class="mr-1 text-secondary" />{{ task.address }}</p>
        </div>
      </div>
    </div>

  </div>

  <div class="card mt-4" v-if="!loading">
    <div class="card-header">{{ (user && task.userId === user.id) ? 'Управлеине' : 'Откликнуться' }}</div>

    <div class="card-body" v-if="user && task.userId !== user.id">
      <b-form @submit.prevent="onSubmitResponse" @reset.prevent="onReset">

        <b-form-group name="description" description="">
          <div>
            <b-form-textarea id="description" v-model="form.description" :state="validateDescription" rows="4" required placeholder="Описание, максимум 1000 символов" :disabled="loading"></b-form-textarea>
            <b-form-invalid-feedback :state="validateDescription">Некорректное описание</b-form-invalid-feedback>
          </div>
        </b-form-group>

        <b-form-group name="price" description="">
          <div class="row">
            <label class="col-md-2 col-lg-1 col-form-label" for="price">Цена:</label>
            <div class="col-md-10 col-lg-11">
              <b-form-input id="price" v-model="form.price" :state="validatePrice" type="number" min="0" required placeholder="Цена" :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validatePrice">Некорректная цена</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <input type="hidden" name="task_id" value="1">
        <button class="btn btn-success mt-3">Откликнуться</button>
      </b-form>

    </div>

    <div class="card-body" v-if="user && task.userId === user.id">
      <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" class="btn btn-info text-light task-btn">Редактировать</router-link>
      <button class="btn btn-danger" @click="deleteTask">Удалить</button>
    </div>

    <div v-if="!user" class="card-body">
      <router-link :to="{ name: 'login' }" class="btn btn-info text-light task-btn">Логин / Регистрация</router-link>
    </div>

  </div>

  <div class="card mt-4" v-if="!loadingResponses">
    <div class="card-header">Откликнулись ({{ responsesCount }})</div>

    <div class="card-body">
      {{ responses }}
    </div>

  </div>
</div>
</template>

<script lang="ts">
import { Task } from '../../common/model/task.model'
import { Category } from '../../common/model/category.model'
import { User } from 'resources/js/common/model/user.model'

import taskService from '../../common/task.service'
import categoryService from '../../common/category.service'
import { getFileNameByUrl, getTextDate, capitalizeFirst, isImage } from '../../common/utils'
import userService from '../../common/user.service'

export default {
  name: 'app-register',
  components: {},
  data() {
    return {
      loading: true,
      loadingResponses: true,
      task: null,
      customer: null,
      categories: [] as Category[],
      form: {
        price: 0,
        description: ''
      },
      formDirty: false,
      responses: [],
      responsesCount: [],
      rating: 3
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
  async mounted() {
    this.task = await taskService.getTask(this.$route.params.id)
    if (!this.task) {
      this.$router.push('/')
    }
    this.categories = await categoryService.getCategories()
    this.customer = this.task.userId === this.user.id ? this.user : await userService.getUserById(this.task.userId)
    if (!this.customer) {
      this.$router.push('/')
    }
    this.loading = false
    const res = await taskService.getResponses(this.task.id)
    this.responses = res.responses
    this.responsesCount = res.total
    this.loadingResponses = false
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    validatePrice() {
      const price = +this.form.price
      return !this.formDirty || (typeof price === 'number' && price >= 0)
    },
    validateDescription() {
      return !this.formDirty || (this.form.description.length > 19)
    },
    userName() {
      return capitalizeFirst(this.customer.name) + ' ' + capitalizeFirst(this.customer.surname)
    }
  },
  methods: {
    getCategoryName(id: number) {
      return categoryService.getCategoryName(this.categories, id)
    },

    async onSubmit(evt) {
      this.loading = true
      this.loading = false
    },

    onReset(evt) {
      this.form.email = ''
      this.form.password = ''
    },

    onSubmitResponse() {
      taskService.responseTask(this.task.id, this.form.description, this.form.price)
    },

    async deleteTask() {
      const response = await taskService.deleteTask(this.task.id)
      this.$router.push('/my-tasks')
    },

    isImage(url: string) {
      return isImage(url)
    },

    getTextDate(date: Date) {
      return getTextDate(date)
    },

    getFileNameByUrl(url: string) {
      return getFileNameByUrl(url)
    }
  }
}
</script> 

<style lang="scss" scoped>
.user-wrapp {
  padding-bottom: 10px;
  display: flex;
  .logo-wrapp {
    margin-right: 10px;
    width: 70px;
  }
  .user-data {

  }
}
.files {
  margin: 0 -5px;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  .file {
    display: inline-block;
    margin: 0 5px;
    .img {
      display: flex;
      max-height: 70px;
      img {
        max-height: 100%;
      }
    }
    .doc {
      font-size: 58px;
      line-height: 1;
    }
  }
}
</style>
