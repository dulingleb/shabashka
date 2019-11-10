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

            <div class="tasks container" v-if="!loading && tasks">
              <section class="row task border-bottom" v-for="(task, index) in tasks" :key="index">
                <div class="col-md-9">
                  <h3 class="title"><router-link :to="{ name: 'task', params: { id: task.id } }" class="text-decoration-none text-info">{{ task.title }}</router-link> <small class="text-secondary">{{ task.createdAt }}</small></h3>
                  <p class="description">{{ task.description }}</p>
                  <footer class="info-footer">
                    <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />{{ task.created }}<font-awesome-icon :icon="['fa', 'folder']" class="ml-3 text-secondary" /> <span>{{ getCategoryName(task.categoryId) }}</span>
                  </footer>
                </div>
                <div class="col-md-3 text-center text-secondary">
                  <p class="price">{{ task.price }} руб.</p><router-link :to="{ name: 'task', params: { id: task.id } }" class="btn btn-info text-light task-btn">Откликнуться</router-link>
                </div>
              </section>
            </div>

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
      users: null,
      error: null,
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
      this.error = this.users = null
      this.loading = true
      const categories = await categoryService.getCategories()
      this.categories = categories

      this.getTasks()
    },

    async changeCategory(checkedCategory: string[]) {
      this.taskOptions.checkedCategory = checkedCategory
      this.getTasks()
    },

    async getTasks() {
      this.loading = true
      this.tasks = await taskService.getTasks(this.taskOptions.start, this.taskOptions.limit, this.taskOptions.sort, this.taskOptions.checkedCategory, this.taskOptions.search)
      this.$forceUpdate()
      await this.$nextTick()
      this.loading = false
    },

    getCategoryName(id: number) {
      return categoryService.getCategoryName(this.categories, id)
    }
  },
  components: {
    'app-aside': Aside,
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
        font-size: 12px;
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