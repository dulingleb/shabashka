 <template>
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Профиль</div>

        <div class="card-body">
          <div class="row" v-if="user">
            <div class="col-md-4">
              <p><b>{{ userName }}</b></p>
              <div class="img">
                <img src="https://via.placeholder.com/300x350" alt />
              </div>
            </div>
            <div class="col-md-8">
              <b-form @submit="onSubmit" @reset="onReset">
                <b-form-group name="name" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="name">Имя</label>
                    <div class="col-md-9">
                      <b-form-input id="name" v-model="form.name" :state="validateName" type="text" required placeholder="Введите имя" autofocus :disabled="loading"></b-form-input>
                      <b-form-invalid-feedback :state="validateName">Некорректное имя. Должено быть длинее 3-х символов</b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group name="surname" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="surname">Фамилия</label>
                    <div class="col-md-9">
                      <b-form-input id="surname" v-model="form.surname" :state="validateSurname" type="text" placeholder="Введите фамилию" :disabled="loading"
                      ></b-form-input>
                      <b-form-invalid-feedback :state="true"></b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group name="email" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="email">Email</label>
                    <div class="col-md-9"><b-form-input id="email" v-model="form.email" type="email" disabled></b-form-input>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group name="password" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="password">Пароль:</label>
                    <div class="col-md-9">
                      <b-form-input id="password" v-model="form.password" :state="validatePassword" type="password" placeholder="Введите пароль" :disabled="loading"></b-form-input>
                      <b-form-invalid-feedback :state="validatePassword">Некорректный пароль. Должен быть длинее 3-х символов</b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group name="passwordConfirm" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="passwordConfirm">Пароль:</label>
                    <div class="col-md-9">
                      <b-form-input id="passwordConfirm" v-model="form.passwordConfirm" :state="validatePasswordConfirm" type="password" placeholder="Подтвердите пароль" :disabled="loading"></b-form-input>
                      <b-form-invalid-feedback :state="validatePasswordConfirm">Пароли не совпадают</b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group name="phone" description>
                  <div class="row">
                    <label class="col-md-3 col-form-label text-md-right" for="phone">Телефон:</label>
                    <div class="col-md-9">
                      <b-form-input v-mask="'+7 (###) ###-##-##'" id="phone" v-model="form.phone" :state="validatePhone" type="text" placeholder="+7 (___) ___-__-__" :disabled="loading"></b-form-input>
                      <b-form-invalid-feedback :state="validatePhone">Некорректный телефон</b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

                <b-form-group>
                  <div class="row">
                    <div class="offset-md-3 col-md-7">
                      <b-button type="submit" variant="info" :disabled="loading">Сохранить</b-button>
                    </div>
                  </div>
                </b-form-group>
              </b-form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import userService from '../common/user.service'
import User from '../common/model/user.model'

export default {
  name: 'app-login',
  data() {
    return {
      loading: false,
      form: {
        name: '',
        surname: '',
        email: '',
        password: '',
        passwordConfirm: '',
        phone: ''
      },
    }
  },
  computed: {
    validateName() {
      return this.form.name.length > 3
    },
    validatePassword() {
      return this.form.password.length > 5 || !this.form.password.length
    },
    validatePasswordConfirm() {
      return this.form.passwordConfirm === this.form.password
    },
    validatePhone() {
      return !!this.form.phone.match(/^\+7\s[(][0-9]{3}[)]\s[0-9]{3}([-][0-9]{2}){2}$/) || !this.form.phone.length
    },
    validateSurname() {
      return this.form.name.length > 3 || !this.form.name.length
    },
    user(): User {
      return this.$store.getters.user
    },
    userName() {
      return this.capitalize(this.user.name) + ' ' + this.capitalize(this.user.surname)
    }
  },
  created() {
    this.form.name = this.user.name
    this.form.surname = this.user.surname
    this.form.email = this.user.email
    this.form.phone = this.user.phone
  },
  methods: {
    async onSubmit(evt) {
      evt.preventDefault()
      this.loading = true
      this.loading = false
    },
    onReset(evt) {
      evt.preventDefault()
      this.form.name = ''
      this.form.surname = ''
      this.form.email = ''
      this.form.password = ''
      this.form.passwordConfirm = ''
      this.form.phone = ''
    },
    capitalize(s) {
      if (typeof s !== 'string' && !s.length) return ''
      return s.charAt(0).toUpperCase() + s.slice(1)
    }
  }
}
</script> 

<style lang="scss" scoped>
.img {
  border-radius: 4px;
  overflow: hidden;
  img {
    width: 100%;
  }
}
</style>