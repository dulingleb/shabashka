<template>
  <div class="row main-content">
    <div class="col-md-4 col-lg-3 aside-wrapp">
      <app-aside :categories="categories" @change-category="changeCategory"></app-aside>
    </div>
    <div class="col-md-8 col-lg-9">
        <div class="tasks">
        <app-tasks :task-options="taskOptions" :categories="categories" @change-category="changeCategory" @change-user="changeUser"></app-tasks>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import Aside from '../common/components/Aside.vue'
import userService from '../common/user.service'
import categoryService from '../common/category.service'
import taskService from '../common/task.service'
import { TaskStatus } from '../common/model/task.model'

export default {
  data() {
    return {
      categories: [],
      tasks: [],
      taskOptions: {
        start: 0,
        limit: 10,
        sort: 'DESC',
        categories: [],
        search: '',
        userId: '',
        status: TaskStatus.searchExecutor
      }
    }
  },
  created() {
    this.taskOptions.search = this.$router.currentRoute.query.search
    this.fetchData()
  },
  watch: {
    '$route.query.search'(prevVal: string, newVal: string) {
      this.taskOptions.search = this.$router.currentRoute.query.search
    }
  },
  methods: {
    async fetchData() {
      const categories = await categoryService.getCategories()
      this.categories = categories
    },

    async changeCategory(categories: string[]) {
      this.taskOptions.categories = categories
      this.taskOptions.userId = ''
    },

    async changeUser(userId: string) {
      this.taskOptions.userId = userId
      this.taskOptions.categories = []
    },

  },
  components: {
    'app-aside': Aside,
  }
}
</script> 

<style lang="scss" scoped>

</style>