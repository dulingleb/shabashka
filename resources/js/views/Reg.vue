 <template>
  <div class="">

    <div class="page-title">
      <h2 class="title">Регистрация</h2>
    </div>

    <div class="page-content">
      <b-form @submit="onSubmit" @reset="onReset">
        <b-form-group name="name" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="name">Имя</label>
            <div class="col-md-7">
              <b-form-input class="" id="name" v-model="form.name" :state="validateName" type="text" required placeholder="Введите имя" autofocus :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validateName">Некорректное имя. Должено быть длинее 3-х символов</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <b-form-group  name="email" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="email">Email</label>
            <div class="col-md-7">
              <b-form-input class="" id="email" v-model="form.email" :state="validateEmail" type="email" required placeholder="Введите email" :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validateEmail">Некорректный email</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <b-form-group name="password" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="password">Пароль:</label>
            <div class="col-md-7">
              <b-form-input id="password" v-model="form.password" :state="validatePassword" type="password" required placeholder="Введите пароль" :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validatePassword">Некорректный пароль. Должен быть длинее 3-х символов</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <b-form-group name="passwordConfirm" description="">
          <div class="row">
            <label class="col-md-3 col-form-label text-md-right" for="passwordConfirm">Пароль:</label>
            <div class="col-md-7">
              <b-form-input id="passwordConfirm" v-model="form.passwordConfirm" :state="validatePasswordConfirm" type="password" required placeholder="Подтвердите пароль" :disabled="loading"></b-form-input>
              <b-form-invalid-feedback :state="validatePasswordConfirm">Пароли не совпадают</b-form-invalid-feedback>
            </div>
          </div>
        </b-form-group>

        <b-form-group>
          <div class="row">
            <div class="offset-md-3 col-md-7">
              <b-button type="submit" variant="outline-info" :disabled="loading">Зарегестрировать</b-button>
              <router-link class="btn btn-link text-info" :to="{ name: 'login' }">Вход</router-link>
            </div>
          </div>
        </b-form-group>
        
      </b-form>
    </div>

  </div>
</template>

<script lang="ts">
import userService from '../common/user.service'
import appRouter from '../app.router'

export default {
  name: 'app-register',
  components: {},
  data() {
    return {
      loading: false,
      form: {
        name: '',
        email: '',
        password: '',
        passwordConfirm: ''
      },
      foods: [{ text: 'Select One', value: null }, 'Carrots', 'Beans', 'Tomatoes', 'Corn'],
      show: true
    }
  },
  computed: {
    validateName() {
      return this.form.name.length > 3
    },
    validateEmail() {
      return !!this.form.email.match(/\S+@\S+\.\S+/)
    },
    validatePassword() {
      return this.form.password.length > 5
    },
    validatePasswordConfirm() {
      return this.form.passwordConfirm === this.form.password
    }
  },
  methods: {
    async onSubmit(evt) {
      evt.preventDefault()
      this.loading = true
      const result = await userService.register(this.form.name, this.form.email, this.form.password, this.form.passwordConfirm)
      if (result) {
        await this.$store.dispatch('GET_USER')
        appRouter.push({ name: 'home' })
      }
      this.loading = false
    },
    onReset(evt) {
      evt.preventDefault()
      this.form.name = ''
      this.form.email = ''
      this.form.password = ''
      this.form.passwordConfirm = ''
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>