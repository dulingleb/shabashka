 <template>
  <div class="">
    <task-edit @saved-task="savedTask"></task-edit>
  </div>
</template>

<script lang="ts">
import taskService from '../../common/task.service'
import categoryService from '../../common/category.service'

export default {
  name: 'app-register',
  data() {
    return {

    }
  },

  methods: {
    savedTask(task) {
      setTimeout(() => this.$router.push('/'), 1000)
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>