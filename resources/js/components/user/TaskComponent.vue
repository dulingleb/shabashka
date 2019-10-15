<template>
    <form method="post" @submit="createTask">
        <div class="form-group row">
            <label class="col-md-4 col-form-label text-md-right">Раздел</label>

            <div class="col-md-6">
                <div :class="{'is-invalid' : errors.category }">
                    <multiselect
                        v-model="task.category"
                        :options="categories"
                        placeholder="Выберите категорию"
                        group-values="child" group-label="parent"
                        selectLabel="Выбрать" deselectLabel="Убрать" selectedLabel="Выбрано"
                        label="title"
                        track-by="title">
                    </multiselect>
                </div>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.category_id) != 'undefined'">
                    <strong>{{ errors.category_id[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="title" class="col-md-4 col-form-label text-md-right">Заголовок</label>

            <div class="col-md-6">
                <input id="title" type="text" class="form-control" :class="{'is-invalid' : errors.title }" name="title"
                       v-model="task.title" placeholder="Купи слона">

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.title) != 'undefined'">
                    <strong>{{ errors.title[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="description" class="col-md-4 col-form-label text-md-right">Описание</label>

            <div class="col-md-6">
                        <textarea id="description" class="form-control" :class="{'is-invalid' : errors.description }"
                                  name="description"
                                  placeholder="максимум 1000 символов" v-model="task.description"></textarea>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.description) != 'undefined'">
                    <strong>{{ errors.description[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="description" class="col-md-4 col-form-label text-md-right">Дополнительные файлы</label>

            <div class="col-md-6">
                <input type="file" id="files" class="d-none" multiple name="files" accept=".jpg,.png,.doc,.docx,.xls,xlsx,.pdf" @change="uploadFile">
                <button type="button" class="btn btn-primary" onclick="document.getElementById('files').click()"><i class="fas fa-download"></i> Выбрать файлы  <span class="spinner-grow spinner-grow-sm" v-if="loading.file"></span></button><br>
                <div class="row files mt-2 ml-0 mr-0">
                    <div class="col-md-3 p-1 d-flex align-items-center justify-content-center" v-for="file in task.files">
                        <div class="file-item w-100">
                            <img :src="file" :title="file.replace(/^.*[\\\/]/, '')" :alt="file.replace(/^.*[\\\/]/, '')" v-if="extensions.image.includes(file.substr(file.lastIndexOf('.') + 1))">

                            <div class="icon mt-1 mb-1" v-else>
                                <i class="fas fa-file-word" v-if="extensions.doc.includes(file.substr(file.lastIndexOf('.') + 1))"></i>
                                <i class="fas fa-file-excel" v-else-if="extensions.xls.includes(file.substr(file.lastIndexOf('.') + 1))"></i>
                                <i class="fas fa-file-pdf" v-else-if="extensions.pdf.includes(file.substr(file.lastIndexOf('.') + 1))"></i>
                                <br>
                                {{ file.replace(/^.*[\\\/]/, '') }}
                            </div>

                            <a href="javascript:;" class="remove-file" @click="removeFile(file)">Удалить</a>
                        </div>
                    </div>
                </div>

                <transition name="slide-fade">
                    <div class="offset-md-3 alert alert-danger" v-if="typeof(errors.file) != 'undefined'">{{ errors.file[0] }}</div>
                </transition>
            </div>
        </div>

        <div class="form-group row">
            <label for="address" class="col-md-4 col-form-label text-md-right">Адрес</label>

            <div class="col-md-6">
                <input id="address" type="text" class="form-control" :class="{'is-invalid' : errors.address }" name="address"
                       v-model="task.address" placeholder="ул. Советская, 9">

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.address) != 'undefined'">
                    <strong>{{ errors.address[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="date" class="col-md-4 col-form-label text-md-right">Дата</label>

            <div class="col-md-6">
                <datepicker
                        v-model="task.term"
                        name="date" id="date"
                        input-class="form-control bg-white"
                        :language="ru"
                        :required=true
                        :disabled-dates="state.disabledDates"
                        >
                </datepicker>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.term) != 'undefined'">
                    <strong>{{ errors.term[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="price" class="col-md-4 col-form-label text-md-right">Цена, руб.</label>

            <div class="col-md-6">
                <input id="price" type="number" class="form-control" :class="{'is-invalid' : errors.price }" name="price"
                       v-model="task.price" placeholder="5000" required>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.price) != 'undefined'">
                    <strong>{{ errors.price[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-md-4 col-form-label text-md-right">Телефон</label>

            <div class="col-md-6">
                <input id="phone" type="text" class="form-control" :class="{'is-invalid' : errors.phone }" name="phone"
                       v-model="task.phone" v-mask="'+7 (###) ###-##-##'" placeholder="+7 (___) ___-__-__" required>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.phone) != 'undefined'">
                    <strong>{{ errors.phone[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="row" v-if="Object.keys(errors).length>0">
            <div class="col-md-8 offset-md-2 text-center alert alert-danger">
                Исправьте все ошибки
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" >{{ task.id ? 'Сохранить' : 'Создать' }} задание <span class="spinner-grow spinner-grow-sm" v-if="loading.submit"></span></button>
        </div>
    </form>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import Datepicker from 'vuejs-datepicker';
    import ru from '../vuejs-datepicker/dist/locale/ru';
    import VueMask from 'v-mask'
    Vue.use(VueMask);

    export default {
        name: "TaskComponent",
        components: {
            Multiselect,
            Datepicker
        },
        props: ['categories', 'files', 'user', 'old_task'],
        mounted(){


            if(this.old_task) {
                this.task.id = this.old_task.id;
                this.task.title = this.old_task.title;
                this.task.description = this.old_task.description;
                this.task.address = this.old_task.address ? this.old_task.address : '';
                this.task.price = this.old_task.price;
                this.task.phone = this.old_task.phone;
                this.task.category.id = this.old_task.category.id;
                this.task.category.title = this.old_task.category.title;
                this.task.term = new Date(Date.parse(this.old_task.term));
                this.task.files = this.old_task.files;
            } else {
                this.task.phone = this.user.phone;
                if(this.files) this.task.files = this.files;
            }

        },
        data() {
            return {
                task: {
                    id: '',
                    category: {
                        id: '',
                        title: ''
                    },
                    title: '',
                    description: '',
                    files: [],
                    address: '',
                    term: new Date(),
                    price: 0,
                    phone: ''
                },
                extensions: {
                    image: ['jpg', 'png', 'jpeg'],
                    doc: ['doc', 'docx'],
                    xls: ['xls', 'xlsx'],
                    pdf: ['pdf', 'rtf']
                },
                errors: {},
                ru: ru,
                loading: {
                    submit: false,
                    file: false
                },
                success: false,
                state: {
                    disabledDates: {
                        to: this.currentDay()
                    }
                }
            }
        },
        methods: {
            currentDay() {
              let date = new Date();
              date.setDate(date.getDate() - 1);
              return date;
            },
            uploadFile(e){
                this.loading.file = true;
                for(let i=0;i<e.target.files.length;i++) {
                    const config = {
                        headers: { 'content-type': 'multipart/form-data' }
                    };
                    let formData = new FormData();
                    formData.append('file', e.target.files[i]);

                    axios.post('/tasks/upload-file', formData, config)
                        .then(response => {
                            this.task.files.push(response.data);
                        })
                        .catch((error) => {
                            this.errors = error.response.data.errors;
                            console.log(error.response.data.errors);
                        })
                        .finally(() => (this.loading.file = false))
                }
                document.getElementById('files').value = '';
            },
            removeFile(file){
                let index = this.task.files.indexOf(file);
                if(index > -1)
                    axios.post('/tasks/remove-file', {'file_name': file.replace(/^.*(\\|\/|\:)/, '')})
                        .then(response => {
                            console.log(response.data);
                            this.task.files.splice(index,1);
                        })
                        .catch((error) => {
                            console.log(error.response);
                            alert('При удалении произошла ошибка');
                        });


            },
            createTask(e){
                e.preventDefault();

                this.errors = [];
                this.loading.submit = true;
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                };

                let formData = new FormData();

                formData.append('id', this.task.id);
                formData.append('category_id', this.task.category.id);
                formData.append('title', this.task.title);
                formData.append('description', this.task.description);
                formData.append('files', JSON.stringify(this.task.files));
                formData.append('address', this.task.address);
                formData.append('term', this.task.term.getFullYear() + '-' + (this.task.term.getMonth() < 9 ? '0' : '') + (this.task.term.getMonth()+1)  + '-' + (this.task.term.getDate() < 10 ? '0' : '') + (this.task.term.getDate()));
                formData.append('price', this.task.price);
                formData.append('phone', this.task.phone);

                axios.post('/tasks/create', formData, config)
                    .then((response) => {
                        document.location.href = response.data;
                    })
                    .catch((error) => {
                        console.log(error.response);
                        this.errors = error.response.data.errors;
                    })
                    .finally(() => (this.loading.submit = false));
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>

</style>
