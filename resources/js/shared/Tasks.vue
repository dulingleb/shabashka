<template>
  <div>
    <div class="tasks container">
      <section class="row task border-bottom" v-for="(task, index) in tasks" :key="index">
        <div class="col-md-9">
          <h3 class="title"><router-link :to="{ name: 'task', params: { id: task.id } }" class="text-decoration-none text-info">{{ task.title }}</router-link> <small class="text-secondary">{{ task.createdAt }}</small></h3>
          <p class="description">{{ task.description }}</p>
          <footer class="info-footer">
            <font-awesome-icon :icon="['fa', 'clock']" class="mr-1 text-secondary" />{{ task.created }}<font-awesome-icon :icon="['fa', 'folder']" class="ml-3 text-secondary" />
            <span class="btn btn-link text-info category-link" @click="changeCategory(task.categoryId)">{{ getCategoryName(task.categoryId) }}</span>
          </footer>
        </div>
        <div class="col-md-3 text-center text-secondary">
          <p class="price">{{ task.price }} руб.</p><router-link :to="{ name: 'task', params: { id: task.id } }" class="btn btn-info text-light task-btn">Откликнуться</router-link>
        </div>
      </section>
    </div>
  </div>
</template>

<script lang="ts">
import Aside from '../common/components/Aside.vue'
import userService from '../common/user.service'
import categoryService from '../common/category.service'
import taskService from '../common/task.service'

export default {
  name: 'tasks',
  props: ['tasks', 'categories'],
  data() {
    return {
    }
  },
  methods: {
    async changeCategory(checkedCategory: string) {
      this.$emit('change-category', checkedCategory)
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