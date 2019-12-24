<template>
<div class="">
  <div class="card">
    <div class="card-header">{{ task ? task.title : '' }}</div>

    <div class="card-body">
      <p v-if="loading">Loading...</p>
      <div v-if="!loading" class="row">
        <div class="col-md-8">
          <p>{{ task.description }}</p>
          <div v-if="task.files">
            <div class="file" v-for="(file, index) in task.files" :key="index">
              <img :src="file" alt="">
            </div>
          </div>


        </div>
        <div class="col-md-4">
          <p class="mb-0">
            <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />{{ task.created }}</p>
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
      <b-form @submit.prevent="onSubmit" @reset.prevent="onReset">
        <textarea class="form-control" rows="4" name="text" placeholder="Описание, максимум 1000 символов" required></textarea>

        <b-form-group name="price" description="">
          <label class="float-left mt-2 mr-3" for="price">Цена:</label>
          <div>
            <b-form-input id="price" v-model="form.price" :state="validatePrice" type="text" required placeholder="" :disabled="loading"></b-form-input>
            <b-form-invalid-feedback :state="validatePrice">Некорректная цена</b-form-invalid-feedback>
          </div>
        </b-form-group>

        <input type="hidden" name="task_id" value="1">
        <button class="btn btn-success mt-3">Откликнуться</button>
      </b-form>

    </div>

    <div class="card-body" v-if="user && task.userId === user.id">
      <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" class="btn btn-info text-light task-btn">Редактировать</router-link>
      <button class="btn btn-danger">Удалить</button>
    </div>

    <div v-if="!user" class="card-body">
      <router-link :to="{ name: 'login' }" class="btn btn-info text-light task-btn">Логин / Регистрация</router-link>
    </div>

  </div>

  <div class="card mt-4" v-if="!loading">
    <div class="card-header">Откликнулись (0)</div>

    <div class="card-body">
    </div>

  </div>
</div>
</template>

<script lang="ts">
import {
  Task
} from '../../common/model/task.model'
import {
  Category
} from '../../common/model/category.model'
import User from 'resources/js/common/model/user.model'

import taskService from '../../common/task.service'
import categoryService from '../../common/category.service'

export default {
  name: 'app-register',
  components: {},
  data() {
    return {
      loading: true,
      task: {} as Task,
      categories: [] as Category[],
      form: {
        price: 0
      }
    }
  },
  async mounted() {
    this.task = await taskService.getTask(this.$route.params.id)
    this.categories = await categoryService.getCategories()
    this.loading = false
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    validatePrice() {
      const price = +this.form.price
      return typeof price === 'number' && price >= 0
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
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>
