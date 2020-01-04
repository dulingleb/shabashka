<template>
  <div class="row">
        <div class="col-md-4 col-lg-3">
          <app-aside :categories="categories" @change-category="changeCategory"></app-aside>
        </div>
        <div class="col-md-8 col-lg-9">
           <div class="tasks">
            <app-tasks :task-options="taskOptions" :categories="categories" @change-category="changeCategory"></app-tasks>
          </div>
        </div>
      </div>
</template>

<script lang="ts">
import Aside from '../../common/components/Aside.vue'
import userService from '../../common/user.service'
import categoryService from '../../common/category.service'
import taskService from '../../common/task.service'
import { User } from 'resources/js/common/model/user.model'

export default {
  data() {
    return {
      categories: [],
      tasks: [],
      taskOptions: {
        start: 0,
        limit: 10,
        sort: 'DESC',
        checkedCategory: [],
        search: '',
        userId: null
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
  computed: {
    user(): User {
      return this.$store.getters.user
    }
  },
  methods: {
    async fetchData() {
      const categories = await categoryService.getCategories()
      this.categories = categories
      this.taskOptions.userId = this.user.id
    },

    async changeCategory(checkedCategory: string) {
      this.taskOptions.checkedCategory = checkedCategory
    },

  },
  components: {
    'app-aside': Aside,
  }
}
</script> 

<style lang="scss" scoped>

</style>