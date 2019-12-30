<template>
  <div>
    <div class="loading" v-if="loading">
      Loading...
    </div>
    <div v-if="!loading && tasks" class="tasks container">
      <section class="row task border-bottom" v-for="(task, index) in tasks" :key="index">
        <div class="col-md-9">
          <h3 class="title">
            <router-link :to="{ name: 'task', params: { id: task.id } }" v-if="!user || user.id !== task.userId" class="text-decoration-none text-info">{{ task.title }}</router-link>
            <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" v-if="user && user.id === task.userId" class="text-decoration-none text-info">{{ task.title }}</router-link>
            <small class="text-secondary">{{ task.createdAt }}</small>
          </h3>
          <p class="description">{{ task.description }}</p>
          <footer class="info-footer">
            <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />{{ task.term }}<font-awesome-icon :icon="['fa', 'folder']" class="ml-3 text-secondary" />
            <span class="btn btn-link text-info category-link" @click="changeCategory(task.categoryId)">{{ getCategoryName(task.categoryId) }}</span>
          </footer>
        </div>
        <div class="col-md-3 text-center text-secondary">
          <p class="price">{{ task.price }} руб.</p>
          <router-link :to="{ name: 'task', params: { id: task.id } }" v-if="!user || user.id !== task.userId" class="btn btn-info text-light task-btn">Откликнуться</router-link>
          <router-link :to="{ name: 'myTaskEdit', params: { id: task.id } }" v-if="user && user.id === task.userId" class="btn btn-info text-light task-btn">Редактировать</router-link>
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

export default {
  name: 'tasks',
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
    async changeCategory(checkedCategory: string) {
      this.$emit('change-category', checkedCategory)
    },

    getTasks: _.debounce(async(self) => {
      self.loading = true
      self.tasks = await taskService.getTasks(self.taskOptions.start, self.taskOptions.limit, self.taskOptions.sort, self.taskOptions.checkedCategory, self.taskOptions.search, self.taskOptions.userId)
      self.$forceUpdate()
      await self.$nextTick()
      self.loading = false
    }, 500),

    getCategoryName(id: number) {
      return categoryService.getCategoryName(this.categories, id)
    }
  }
}
</script> 

<style lang="scss" scoped>
  .tasks {
    background:#fff;
    box-shadow: 0px 0px 10px -3px rgba(0,0,0,0.25);
    .task {
      padding: 10px 0;
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
        font-weight: 600;
      }
      .task-btn {
        width: 140px;
      }
    }
  }

</style>