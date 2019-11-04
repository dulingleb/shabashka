 <template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Регистрация</div>

        <div class="card-body">
          <b-form @submit="onSubmit" @reset="onReset">
            <b-form-group  name="email" description="">
              <div class="row">
                <label class="col-md-3 col-form-label text-md-right" for="email">Email</label>
                <div class="col-md-7">
                  <b-form-input class="" id="email" v-model="form.email" :state="validateEmail" type="email" required placeholder="Введите email" autofocus></b-form-input>
                  <b-form-invalid-feedback :state="validateEmail">Некорректный email</b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <b-form-group name="password" description="">
              <div class="row">
                <label class="col-md-3 col-form-label text-md-right" for="password">Пароль:</label>
                <div class="col-md-7">
                  <b-form-input id="password" v-model="form.password" :state="validatePassword" type="password" required placeholder="Введите пароль"></b-form-input>
                  <b-form-invalid-feedback :state="validatePassword">Некорректный пароль. Должен быть длинее 3-х символов</b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <b-form-group name="passwordConfirm" description="">
              <div class="row">
                <label class="col-md-3 col-form-label text-md-right" for="passwordConfirm">Пароль:</label>
                <div class="col-md-7">
                  <b-form-input id="passwordConfirm" v-model="form.passwordConfirm" :state="validatePasswordConfirm" type="password" required placeholder="Подтвердите пароль"></b-form-input>
                  <b-form-invalid-feedback :state="validatePasswordConfirm">Пароли не совпадают</b-form-invalid-feedback>
                </div>
              </div>
            </b-form-group>

            <b-form-group>
              <div class="row">
                <div class="offset-md-3 col-md-7">
                  <b-button type="submit" variant="info">Зарегестрировать</b-button>
                  <router-link class="btn btn-link text-info" :to="{ name: 'login' }">Вход</router-link>
                </div>
              </div>
            </b-form-group>
            
          </b-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: "app-register",
  components: {},
  data() {
      return {
        form: {
          email: '',
          password: '',
          passwordConfirm: ''
        },
        foods: [{ text: 'Select One', value: null }, 'Carrots', 'Beans', 'Tomatoes', 'Corn'],
        show: true
      }
    },
    computed: {
      validateEmail() {
        return this.form.email.length > 4
      },
      validatePassword() {
        return this.form.password.length > 3
      },
      validatePasswordConfirm() {
        return this.form.passwordConfirm === this.form.password
      }
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault()
        alert(JSON.stringify(this.form))
      },
      onReset(evt) {
        evt.preventDefault()
        // Reset our form values
        this.form.email = ''
        this.form.password = ''
        this.form.passwordConfirm = ''
        // Trick to reset/clear native browser form validateEmail state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      }
    }
};
</script> 

<style lang="scss" scoped>
</style>