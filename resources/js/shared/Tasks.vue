<template>
  <div>
    
    <app-loading v-if="loading"></app-loading>

    <div v-if="!loading && tasks" class="tasks container">
      <p v-if="!tasks.length" class="not-found">Нет заданий в этой категории</p>
      <section class="task" v-for="(task, index) in tasks" :key="index">
        <div class="task-data-wrapp">
          <div class="task-data">
            <div class="logo-wrapp">
              <app-avatar :title="task.title" :image="getFirstImage(task.files)" :font-size="'42px'"></app-avatar>
            </div>
            <div class="data">
              <h5 class="title">
              <router-link :to="{ name: 'task', params: { id: task.id } }" class="text-decoration-none text-info">{{ task.title }}</router-link>
              <small class="text-secondary">{{ getTextDate(task.createdAt) }}</small>
              </h5>
              <p class="description">{{ task.description }}</p>
            </div>
          </div>
          <footer class="info-footer">
            <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />До {{ getTextDate(task.term) }}
            <font-awesome-icon :icon="['fa', 'folder']" class="ml-3 text-secondary" />
            <span class="btn btn-link text-info category-link" @click="changeCategory(task.categoryId)">{{ getCategoryName(task.categoryId) }}</span>
            <font-awesome-icon :icon="['fas', 'user']" class="ml-3 text-secondary" />
            <span class="btn btn-link text-info category-link" @click="changeUser(task.userId)">{{ task.userTitle }}</span>
          </footer>
        </div>
        <div class="task-btns text-center text-secondary">
          <p class="price">{{ task.price }} руб.</p>
          <router-link :to="{ name: 'task', params: { id: task.id } }" v-if="!user || user.id !== task.userId" class="btn btn-outline-info task-btn">Откликнуться</router-link>
          <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" v-if="user && user.id === task.userId" class="btn btn-outline-info task-btn">Редактировать</router-link>
        </div>
      </section>
    </div>
  </div>
</template>

<script lang="ts">
import _ from 'lodash'

import userService from '../common/user.service'
import categoryService from '../common/category.service'
import taskService from '../common/task.service'
import { User } from '../common/model/user.model'
import { getTextDate, isImage } from '../common/utils'

export default {
  name: 'app-tasks',
  props: ['taskOptions', 'categories'],
  data() {
    return {
      loading: true
    }
  },
  watch: {
    taskOptions: {
      handler() {
        this.getTasks(this)
      },
      deep: true
    },
    categories() {
      this.getTasks(this)
    }
  },
  computed: {
    user(): User {
      return this.$store.getters.user
    }
  },
  methods: {
    async changeCategory(category: string) {
      this.$emit('change-category', [category])
    },
    async changeUser(userId: string) {
      this.$emit('change-user', userId)
    },

    getTasks: _.debounce(async(self) => {
      self.loading = true
      self.tasks = await taskService.getTasks(self.taskOptions)
      self.$forceUpdate()
      await self.$nextTick()
      self.loading = false
    }, 500),

    getCategoryName(id: number) {
      return categoryService.getCategoryName(this.categories, id)
    },

    getTextDate(date: Date) {
      return getTextDate(date)
    },

    getFirstImage(files: string[]) {
      return files.find(file => isImage(file))
    },

  }
}
</script> 

<style lang="scss" scoped>
  .tasks {
    padding: 5px 15px;
    .not-found {
      padding: 15px 5px;
      text-align: center;
    }
    .task {
      display: flex;
      padding: 20px 0;
      border-bottom: 1px solid #dee2e6;
      .task-data-wrapp {
        width: 100%;
      }
      .task-btns {

      }
      @media screen and (max-width: 570px){
        display: block;
      }
      .task-data {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        .logo-wrapp {
          margin-right: 10px;
          width: 70px;
        }
      }
      small {
        font-size: 12px;
      }
      .info-footer {
        display: flex;
        align-items: center;
        font-size: 12px;
        .category-link {
          padding: 0 0 0 5px;
          font-size: 12px;
          line-height: 1;
          cursor: pointer;
        }
      }
      .price {
        font-size: 20px;
        font-weight: 500;
      }
      .task-btn {
        width: 140px;
      }
      &:last-child {
        border-bottom: none;
      }
    }
  }

</style>