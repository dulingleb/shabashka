 <template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Создать задание</div>

        <div class="card-body">
          <p v-if="loading">Loading...</p>
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
  methods: {
  }
}
</script> 

<style lang="scss" scoped>
</style>