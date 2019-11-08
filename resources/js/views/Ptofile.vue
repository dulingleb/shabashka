 <template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Профиль</div>

        <div class="card-body">
          {{ user }}
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import userService from "../common/user.service"

export default {
  name: "app-login",
  data() {
      return {
        form: {
          email: '',
          password: ''
        }
      }
    },
    computed: {
      user() {
        return this.$store.getters.user
      }
    },
    methods: {
    }
};
</script> 

<style lang="scss" scoped>
</style>