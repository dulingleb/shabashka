 <template>
  <div>
    <app-task-edit v-if="!loading" :task="task" @saved-task="savedTask"></app-task-edit>
  </div>
</template>

<script lang="ts">
import taskService from '../../common/task.service'

export default {
  name: 'app-register',
  components: {},
  data() {
    return {
      loading: true,
      task: {}
    }
  },
  async mounted() {
    this.task = await taskService.getTask(this.$route.params.id)
    this.loading = false
    if (!this.task) {
      this.$router.push('/my-tasks')
    }
  },
  methods: {
    savedTask(task) {
      setTimeout(() => this.$router.push('/my-tasks'), 1000)
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>