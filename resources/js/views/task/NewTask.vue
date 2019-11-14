 <template>
  <div class="">
    <div class="">
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

            <p>Файлы</p>

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
                      <b-form-input class="" id="date" v-model="form.date" :state="validateAddress" type="date" placeholder="" :disabled="loading" required></b-form-input>
                      <b-form-invalid-feedback :state="validateAddress">Введите дату</b-form-invalid-feedback>
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
                    <label class="col-md-3 col-form-label text-md-right" for="phone">Адрес</label>
                    <div class="col-md-9">
                      <b-form-input class="" id="phone" v-model="form.phone" :state="validatePhone" type="text" placeholder="Введите заголовок" :disabled="loading" required></b-form-input>
                      <b-form-invalid-feedback :state="validatePhone">Введите телефон</b-form-invalid-feedback>
                    </div>
                  </div>
                </b-form-group>

              </div>
            </div>

          </b-form>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import taskService from '../../common/task.service'
import categoryService from '../../common/category.service'

export default {
  name: 'app-register',
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
        date: new Date(),
        cost: 0,
        phone: '',
      },
      formDirty: false
    }
  },
  computed: {
    validateCategory() {
      return this.formDirty ? !!this.category : null
    },
    validateTitle() {
      return this.formDirty ? !!this.form.title : null
    },
    validateDescription() {
      return this.formDirty ? !!this.form.description : null
    },
    validateAddress() {
      return this.formDirty ? !!this.form.address : null
    },
    validateDate() {
      return this.formDirty ? !!this.form.date : null
    },
    validateCost() {
      return this.formDirty ? this.form.cost > 0 : null
    },
    validatePhone() {
      return this.formDirty ? !!this.form.phone : null
    },
  },
  async created() {
    this.categories = await categoryService.getCategories()
    this.loading = false
  },
  methods: {
  }
}
</script> 

<style lang="scss" scoped>
</style>