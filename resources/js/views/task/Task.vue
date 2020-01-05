<template>
<div class="">
  <!-- <div class="card-header" v-if="task">{{ task.title }}, <small class="text-secondary">{{ getTextDate(task.createdAt) }}</small></div> -->

  <div class="page-title">
    <h2 class="title" v-if="task">{{ task.title }}, <small class="text-secondary">{{ getTextDate(task.createdAt) }}</small></h2>
    <div class="btns" v-if="task && user && task.userId === user.id">
        <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" class="btn btn-link text-info"><font-awesome-icon :icon="['fas', 'edit']" class="icon" /> Редактировать</router-link>
        <!-- <button class="btn btn-link text-danger" @click="deleteTask"><font-awesome-icon :icon="['fas', 'trash-alt']" class="icon" /> Удалить</button> -->
        <app-delete-modal :task="task" @delete-task="deleteTask"></app-delete-modal>
    </div>
  </div>

  <div class="page-content">

    <app-loading v-if="loading"></app-loading>

    <div v-if="!loading" class="row">
      <div class="col-md-8">
        <div class="user-wrapp">
          <div class="logo-wrapp">
            <app-avatar v-if="customer" :title="userName" :image="customer.logo" :font-size="'35px'"></app-avatar>
          </div>
          <div class="user-data">
              <p class="m-0 name">{{ userName }}</p>
              <div class="user-data-info">
                <star-rating v-if="customer" v-model="customer.rate.assessment" :read-only="true" :show-rating="false" :star-size="20"></star-rating>
                <p class="m-0 rating"> <a href="" class="text-secondary">{{ getAssessmentTitle(customer.rate.countAssessment) }}</a></p>
              </div>
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


  <div class="mt-4" v-if="!loading && !hideResponceForm && (!user || (user && task.userId !== user.id))">
    <div class="page-title sub-title">
      <h3 class="title">Откликнуться</h3>
    </div>

    <div class="page-content">

      <b-form @submit.prevent="onSubmitResponse" @reset.prevent="onReset">

        <b-form-group name="description" description="">
          <div>
            <b-form-textarea id="description" v-model="form.description" :state="validateDescription" rows="4" required placeholder="Описание, максимум 1000 символов" :disabled="loading || loadingResponse || !user"></b-form-textarea>
            <b-form-invalid-feedback :state="validateDescription">Некорректное описание. Должно быть не менее 20 символов.</b-form-invalid-feedback>
          </div>
        </b-form-group>

        <b-form-group name="price" description="">
          <div class="row">
            <label class="col-md-2 col-lg-1 col-form-label" for="price">Цена:</label>
            <div class="col-md-10 col-lg-11">
              <b-form-input id="price" v-model="form.price" :state="validatePrice" type="number" min="1" required placeholder="Цена" :disabled="loading || loadingResponse || !user"></b-form-input>
              <b-form-invalid-feedback :state="validatePrice">Некорректная цена</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <app-message v-for="(error, key) in errReponseMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></app-message>

        <button v-if="user" :disabled="loading || loadingResponse || !user" class="btn btn-outline-success mt-3">Откликнуться</button>
        <router-link v-if="!user" :to="{ name: 'login' }" class="btn btn-outline-info task-btn mt-3">Логин / Регистрация</router-link>
      </b-form>

    </div>

  </div>

    <app-loading v-if="!loading && loadingResponses"></app-loading>

    <div class="page-title sub-title" v-if="!loadingResponses">
      <h3 class="title">Откликнулись ({{ responsesCount }})</h3>
    </div>

    <div class="page-content" v-if="!loadingResponses">

      <div class="responses">
        <section v-for="(responce, key) in responses" :key="key" class="response">
          <div class="user-wrapp">
            <div class="logo-wrapp">
              <app-avatar :title="responce.user.title" :image="responce.user.logo" :font-size="'35px'"></app-avatar>
            </div>
            <div class="user-data">
                <p class="m-0 name">{{ responce.user.title }}</p>
                <div class="user-data-info">
                  <star-rating v-model="responce.user.assessment" :read-only="true" :show-rating="false" :star-size="20"></star-rating>
                  <p class="m-0 rating">
                    <a href="" class="text-secondary">{{ getAssessmentTitle(responce.user.countAssessment) }}, {{ getOrderDoneTitle(responce.user.countAssessment) }}</a>
                  </p>
                </div>
            </div>
            <p class="price">{{ responce.price }} р.</p>
          </div>
            <p>{{ responce.text }}</p>

          <p>{{ responce.description }}</p>
        </section>
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
import { getFileNameByUrl, getTextDate, capitalizeFirst, isImage, getErrTitles, getAssessmentTitle, getOrderDoneTitle } from '../../common/utils'
import userService from '../../common/user.service'

export default {
  name: 'app-register',
  components: {},
  data() {
    return {
      loading: true,
      loadingResponse: false,
      loadingResponses: true,
      task: null,
      customer: null,
      categories: [] as Category[],
      form: {
        price: 1,
        description: ''
      },
      hideResponceForm: false,
      formDirty: false,
      responses: [],
      responsesCount: [],
      rating: 3,
      errReponseMessages: [],
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

    this.customer = this.user && this.task.userId === this.user.id ? this.user : await userService.getUserById(this.task.userId)
    if (!this.customer) {
      this.$router.push('/')
    }

    this.loading = false
    const res = await taskService.getResponses(this.task.id)
    this.responses = res.responses
    this.responsesCount = res.total
    this.loadingResponses = false
    if (!this.user || !this.responsesCount) { return }
    for (let response of this.responses) {
      if (response.user.id === this.user.id) {
        this.hideResponceForm = true
        break
      }
    }
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    },
    validatePrice() {
      const price = +this.form.price
      return !this.formDirty || (typeof price === 'number' && price > 0)
    },
    validateDescription() {
      return !this.formDirty || (this.form.description.length > 19)
    },
    userName() {
      return this.customer ? capitalizeFirst(this.customer.name) + ' ' + capitalizeFirst(this.customer.surname) : ''
    }
  },
  methods: {
    getCategoryName(id: number) {
      return categoryService.getCategoryName(this.categories, id)
    },

    onReset(evt) {
      this.form.email = ''
      this.form.password = ''
    },

    async onSubmitResponse() {
      this.loadingResponse = true
      this.errReponseMessages = []
      const response = await taskService.responseTask(this.task.id, this.form.description, this.form.price)
      this.loadingResponse = false
      if (response.success) {
        this.hideResponceForm = true
        this.responsesCount++
        this.responses.unshift(response.data)
        return
      }
      this.errReponseMessages = getErrTitles(response.error)
    },

    deleteTask(idTask) {
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
    },

    getAssessmentTitle(countRate) {
      return getAssessmentTitle(countRate)
    },

    getOrderDoneTitle(countRate) {
      return getOrderDoneTitle(countRate)
    },

  }
}
</script> 

<style lang="scss" scoped>
.user-wrapp {
  padding-bottom: 10px;
  display: flex;
  align-items: center;
  .logo-wrapp {
    margin-right: 10px;
    width: 70px;
  }
  .user-data {
    flex: 1;
    .user-data-info {
      padding-top: 5px;
      display: flex;
      align-items: center;
      font-size: 14px;
      ::v-deep .vue-star-rating {
        margin-right: 5px;
        position: relative;
        top: -1px;
      }
    }
    .price {
      margin: 0 0 0 5px;
    }
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
      height: 70px;
      img {
        max-height: 100%;
        max-width: 100%;
      }
    }
    .doc {
      font-size: 58px;
      line-height: 1;
    }
  }
}

.responses {
  .response {
    margin-bottom: 20px;
    border-bottom: 1px solid#dee2e6;
    &:last-child {
      margin-bottom: 0;
      border-bottom: none;
    }
  }
}
</style>
