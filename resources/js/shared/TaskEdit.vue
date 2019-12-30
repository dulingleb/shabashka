 <template>
  <div>
    <div class="card">
      <div class="card-header">Создать задание</div>

      <div class="card-body">
        <p v-if="loading">Loading...</p>
        <b-form @submit.prevent="onSubmit" @reset.prevent="onReset">

          <b-form-group  name="category" description="">
            <div class="row">
              <label class="col-md-2 col-form-label text-md-right" for="category">Раздел</label>
              <div class="col-md-10">
                <multiselect
                  v-model="form.category"
                  :options="categories"
                  placeholder="Выберите категорию"
                  group-values="children" group-label="title"
                  selectLabel="Выбрать" deselectLabel="Убрать" selectedLabel="Выбрано"
                  label="title"
                  track-by="title" name="category" required>
              </multiselect>
              <b-form-invalid-feedback :state="validateCategory">Выбирите категорию</b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <b-form-group  name="title" description="">
            <div class="row">
              <label class="col-md-2 col-form-label text-md-right" for="title">Заголовок</label>
              <div class="col-md-10">
                <b-form-input class="" id="title" v-model="form.title" :state="validateTitle" type="text" placeholder="Купи слона" :disabled="loading" required></b-form-input>
                <b-form-invalid-feedback :state="validateTitle">Введите заголовок</b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <b-form-group  name="description" description="">
            <div class="row">
              <label class="col-md-2 col-form-label text-md-right" for="description">Описание</label>
              <div class="col-md-10">
                <b-form-textarea class="" id="description" v-model="form.description" :state="validateDescription" placeholder="Введите описание" :disabled="loading" required></b-form-textarea>
                <b-form-invalid-feedback :state="validateDescription">Введите описание</b-form-invalid-feedback>
              </div>
            </div>
          </b-form-group>

          <div class="row">
              <label class="col-md-2 col-form-label text-md-right" for="title">Файлы</label>
              <div class="col-md-10">
                <drag-drop-images @change-files="changeFiles" :urls="[]"></drag-drop-images>
              </div>
            </div>

          <div class="row">
            <div class="col-md-6">

              <b-form-group  name="address" description="">
                <div class="row">
                  <label class="col-md-4 col-form-label text-md-right" for="address">Адрес</label>
                  <div class="col-md-8">
                    <b-form-input class="" id="address" v-model="form.address" :state="validateAddress" type="text" placeholder="Ул. Советская, 36" :disabled="loading" required></b-form-input>
                    <b-form-invalid-feedback :state="validateAddress">Введите адрес</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>

              <b-form-group  name="date" description="">
                <div class="row">
                  <label class="col-md-4 col-form-label text-md-right" for="date">Дата</label>
                  <div class="col-md-8">
                    <b-form-input class="" id="date" v-model="form.date" :state="validateDate" type="date" placeholder="" :disabled="loading" required></b-form-input>
                    <b-form-invalid-feedback :state="validateDate">Введите дату</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>

            </div>
            <div class="col-md-6">
              
              <b-form-group  name="cost" description="">
                <div class="row">
                  <label class="col-md-3 col-form-label text-md-right" for="cost">Цена, руб.</label>
                  <div class="col-md-9">
                    <b-form-input class="" id="cost" v-model="form.cost" :state="validateCost" type="number" min="0" placeholder="0" :disabled="loading" required></b-form-input>
                    <b-form-invalid-feedback :state="validateCost">Введите цену</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>

              <b-form-group  name="phone" description="">
                <div class="row">
                  <label class="col-md-3 col-form-label text-md-right" for="phone">Телефон</label>
                  <div class="col-md-9">
                    <b-form-input v-mask="'+7 (###) ###-##-##'" id="phone" v-model="form.phone" :state="validatePhone" type="text" placeholder="+7 (___) ___-__-__" :disabled="loading"></b-form-input>
                    <b-form-invalid-feedback :state="validatePhone">Введите телефон</b-form-invalid-feedback>
                  </div>
                </div>
              </b-form-group>

            </div>
          </div>

          <message v-for="(message, key) in messages" :key="key" :message="message" @dismiss="dismissMessage(key)"></message>
          <message v-for="(error, key) in errMessages" :key="key" :error="error" @dismiss="dismissErr(key)"></message>

          <div class="btn-container mt-3 text-right">
            <button class="btn btn-info">Создать задание</button>
          </div>

        </b-form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import taskService from '../common/task.service'
import categoryService from '../common/category.service'
import { getErrTitles } from '../common/utils'

export default {
  name: 'task-edit',
  components: {},
  data() {
    return {
      loading: true,
      categories: [],
      form: {
        category: null,
        title: '',
        description: '',
        address: '',
        date: (new Date(new Date().getTime() + (24 * 60 * 60 * 1000))).toISOString().split('T')[0],
        cost: 0,
        phone: '',
        files: []
      },
      formDirty: false,
      messages: [],
      errMessages: []
    }
  },
  computed: {
    validateCategory() {
      return this.formDirty ? !!this.form.category : null
    },
    validateTitle() {
      return this.formDirty ? !!this.form.title && this.form.title.length > 4 : null
    },
    validateDescription() {
      return this.formDirty ? !!this.form.description && this.form.description.length > 19 : null
    },
    validateAddress() {
      return this.formDirty ? !!this.form.address : null
    },
    validateDate() {
      return this.formDirty ? !!this.form.date : null
    },
    validateCost() {
      return this.formDirty ? this.form.cost >= 0 : null
    },
    validatePhone() {
      return this.formDirty ? !!this.form.phone : null
    },
  },
  async created() {
    this.categories = await categoryService.getCategories()
    this.loading = false
  },
  watch: {
    form: {
      handler() {
        this.formDirty = true
      },
      deep: true
    }
  },
  methods: {
    changeFiles(files) {
      this.form.files = files
    },
    async onSubmit() {
      this.loading = true
      this.clearMessages()
      const response = await taskService.addTask(this.form.category.id, this.form.title, this.form.description, this.form.address, new Date(this.form.date), this.form.cost, this.form.phone, this.form.files)
      this.loading = false
      if (response.success) {
        this.messages = [response.message]
        this.$emit('saved-task', response)
        return
      }
      this.errMessages = getErrTitles(response.error)
    },

    dismissMessage(key) {
      this.messages.splice(key, 1)
    },
    dismissErr(key) {
      this.errMessages.splice(key, 1)
    },
    clearMessages() {
      this.messages = []
      this.errMessages = []
    }
  }
}
</script> 

<style lang="scss" scoped>
</style>