 <template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Todo: Add Edit/remove btns if userId === currentUserId</div>

        <div class="card-body">
          <p v-if="loading">Loading...</p>
          {{ task }}
        </div>
      </div>
    </div>
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
  },
  methods: {
  }
}
</script> 

<style lang="scss" scoped>
</style>