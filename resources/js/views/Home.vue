<template>
  <div class="row">
        <div class="col-md-4 col-lg-3">
          <app-aside :categories="categories" @change-category="changeCategory"></app-aside>
        </div>
        <div class="col-md-8 col-lg-9">
           <div class="tasks">
            <div class="loading" v-if="loading">
              Loading...
            </div>

            <tasks v-if="!loading && tasks" :tasks="tasks" :categories="categories" @change-category="changeCategory"></tasks>

          </div>
        </div>
      </div>
</template>

<script lang="ts">
import Aside from '../common/components/Aside.vue'
import userService from '../common/user.service'
import categoryService from '../common/category.service'
import taskService from '../common/task.service'

export default {
  data() {
    return {
      loading: false,
      categories: [],
      tasks: [],
      taskOptions: {
        start: 0,
        limit: 10,
        sort: 'DESC',
        checkedCategory: [],
        search: ''
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
      this.getTasks()
    }
  },
  methods: {
    async fetchData() {
      this.loading = true
      const categories = await categoryService.getCategories()
      this.categories = categories
      await this.getTasks()
    },

    async changeCategory(checkedCategory: string) {
      this.taskOptions.checkedCategory = [checkedCategory]
      await this.getTasks()
    },

    async getTasks() {
      this.loading = true
      this.tasks = await taskService.getTasks(this.taskOptions.start, this.taskOptions.limit, this.taskOptions.sort, this.taskOptions.checkedCategory, this.taskOptions.search)
      this.$forceUpdate()
      await this.$nextTick()
      this.loading = false
    }
  },
  components: {
    'app-aside': Aside,
  }
}
</script> 

<style lang="scss" scoped>

</style>