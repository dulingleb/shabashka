<template>
  <div class="row">
        <div class="col-md-4 col-lg-3">
          <app-aside :categories="categories" ></app-aside>
        </div>
        <div class="col-md-8 col-lg-9">
           <div class="tasks">
            <div class="loading" v-if="loading">
              Loading...
            </div>

            <div class="tasks" v-if="!loading">
            {{ tasks }}
            </div>


            <ul v-if="users">
              <li v-for="{ name, email } in users">
                <strong>Name:</strong> {{ name }},
                <strong>Email:</strong> {{ email }}
              </li>
            </ul>
          </div>
        </div>
      </div>
</template>

<script lang="ts">
import Aside from "../common/components/Aside.vue"
import userService from "../common/user.service"
import categoryService from "../common/category.service"
import taskService from "../common/task.service"

export default {
  data() {
    return {
      loading: false,
      categories: [],
      tasks: [],
      users: null,
      error: null,
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      this.error = this.users = null
      this.loading = true
      const categories = await categoryService.getCategories()
      this.categories = categories
      console.log('categories', categories)

      const tasks = await taskService.getTaks()
      this.tasks = tasks
      // this.users = users
      this.loading = false
    }
  },
  components: {
    "app-aside": Aside,
  }
}
</script> 

<style lang="scss" scoped>
  .tasks {
    background:#fff;
    box-shadow: 0px 0px 10px -3px rgba(0,0,0,0.25);
  }

</style>