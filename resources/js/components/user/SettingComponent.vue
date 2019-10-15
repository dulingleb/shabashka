<template>
    <form @submit="formSubmit" method="post" enctype="multipart/form-data">
        <div class="form-group row">
            <label for="logo" class="col-md-4 col-form-label text-md-right">Лого</label>

            <div class="col-md-6 text-center">
                <div class="user-logo float-left" :style="{background: 'url(' + settings.logoUrl + ') center center'}"
                     @mouseover="isHoveringLogo = true"
                     @mouseout="isHoveringLogo = false">

                    <div class="text-logo" v-if="!settings.logoUrl">
                        {{ user.name.charAt(0) }}{{ user.surname!== null ? user.surname.charAt(0) : ''}}
                    </div>

                    <transition name="fade">
                        <div class="remove d-flex" v-if="isHoveringLogo" @click="removeLogo">Удалить</div>
                    </transition>

                </div>
                <button type="button" class="btn btn-primary ml-3 mt-4" onclick="document.getElementById('logo').click()">Выбрать фотографию</button><br>
                не более 1 мб
                <input id="logo" type="file" class="d-none" name="logo" accept="image/jpeg,image/png"  @change="onLogoChange">
                <input type="hidden" name="remove_logo" value="">

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.logo) != 'undefined'">
                    <strong>{{ errors.logo[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">Имя</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control" :class="{'is-invalid': typeof(errors.name) != 'undefined'}"
                       name="name" v-model="user.name" required autocomplete="name" autofocus>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.name) != 'undefined'">
                    <strong>{{ errors.name[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="surname" class="col-md-4 col-form-label text-md-right">Фамилия</label>

            <div class="col-md-6">
                <input id="surname" type="text" class="form-control" :class="{'is-invalid': typeof(errors.surname) != 'undefined'}"
                       name="surname" v-model="user.surname" required autocomplete="surname">

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.surname) != 'undefined'">
                    <strong>{{ errors.surname[0] }}</strong>
                </span>
            </div>
        </div>

        <div class="form-group row">
            <label for="phone" class="col-md-4 col-form-label text-md-right">Телефон</label>

            <div class="col-md-6">
                <input id="phone" type="text" class="form-control" :class="{'is-invalid' : errors.phone }" name="phone"
                       v-model="settings.phone" v-mask="'+7 (###) ###-##-##'" placeholder="+7 (___) ___-__-__" required>

                <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.phone) != 'undefined'">
                    <strong>{{ errors.phone[0] }}</strong>
                </span>
            </div>
        </div>

        <!-- Юридическое лицо -->

        <div class="row mt-4">
            <div class="col-md-6 offset-md-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="company" name="is_active_company"
                           v-model="company.is_active">
                    <label class="custom-control-label" for="company">Юридическое лицо
                            <div class="badge badge-danger" v-if="company != null && company.moderate_status=='moderate'">На модерации</div>
                            <div class="badge badge-danger" v-else-if="company != null && company.moderate_status=='error'">Модерация не пройдена</div>
                            <span class="badge badge-pill badge-success" v-else-if="company != null && company.moderate_status=='success'">><i class="fas fa-check"></i></span>

                    </label>
                </div>
            </div>
        </div>


        <transition name="slide-fade">
            <div id="company-block" class="mt-3" v-if="company.is_active">
                <div class="form-group row">
                    <label for="title" class="col-md-4 col-form-label text-md-right">Наименование</label>

                    <div class="col-md-6">
                        <input id="title" type="text" class="form-control" :class="{'is-invalid' : errors.title }" name="title"
                               v-model="company.title" placeholder="ИП Иванов И.И.">

                        <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.title) != 'undefined'">
                            <strong>{{ errors.title[0] }}</strong>
                        </span>
                    </div>
                </div>


                <div class="form-group row">
                    <label for="inn" class="col-md-4 col-form-label text-md-right">ИНН</label>

                    <div class="col-md-6">
                        <input id="inn" type="text" class="form-control" :class="{'is-invalid' : errors.inn }"
                               name="inn" v-model="company.inn" placeholder="0123456789" onkeypress="return event.charCode >= 48 && event.charCode <= 57">

                        <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.inn) != 'undefined'">
                            <strong>{{ errors.inn[0] }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-md-4 col-form-label text-md-right">Описание</label>

                    <div class="col-md-6">
                        <textarea id="description" class="form-control" :class="{'is-invalid' : errors.description }"
                                  name="description"
                                  placeholder="Пару слов, чем вы занимаетесь" v-model="company.description"></textarea>

                        <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.description) != 'undefined'">
                            <strong>{{ errors.description[0] }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-md-4 col-form-label text-md-right">Категории</label>

                    <div class="col-md-6">
                        <multiselect v-model="settings.categories"
                             placeholder="Выберите категории"
                             label="title"
                             track-by="id"
                             group-values="child" group-label="parent"
                             selectLabel="Выбрать" deselectLabel="Убрать" selectedLabel="Выбрано"
                             :close-on-select="false"
                             :options="categories"
                             :multiple="true">
                        </multiselect>

                        <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.categories) != 'undefined'">
                            <strong>{{ errors.categories[0] }}</strong>
                        </span>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="address" class="col-md-4 col-form-label text-md-right">Адрес</label>

                    <div class="col-md-6">
                        <input id="address" type="text" class="form-control" :class="{'is-invalid' : errors.address }"
                               name="address" v-model="company.address"
                               placeholder="г. Магадан, ул. Юбилейная, 1">

                        <span class="invalid-feedback d-block" role="alert" v-if="typeof(errors.address) != 'undefined'">
                            <strong>{{ errors.address[0] }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="slide-fade">
            <div class="row mb-3 mt-3" v-if="success">
                <div class="col-md-7 offset-md-3 alert alert-success">{{ success }}</div>
            </div>
        </transition>

        <div class="form-group row">
            <div class="col-md-10 text-right">
                <button type="submit" class="btn btn-primary">Сохранить <span class="spinner-grow spinner-grow-sm" v-if="loading"></span></button>
            </div>
        </div>
    </form>
</template>


<script>
    import VueMask from 'v-mask';
    Vue.use(VueMask);
    import Multiselect from 'vue-multiselect';


    export default {
        name: "SettingComponent",
        components: {
            Multiselect
        },
        props: ['user', 'company_old', 'categories'],
        mounted() {
            this.settings.logoUrl = this.user.logo;
            this.settings.phone = this.user.phone;

            if(this.company_old != null){
                this.company = this.company_old;
                this.settings.categories = this.company.categories;
            }

        },
        data () {
            return {
                settings: {
                    logo: '',
                    phone: '',
                    logoUrl: '',
                    categories: [],
                    removeLogo: false
                },
                company: {
                  title: '',
                  description: '',
                  inn: '',
                  address: '',
                  is_active: false
                },
                errors: [],
                success: '',
                loading: false,
                isHoveringLogo: false
            }
        },
        methods: {
            onLogoChange (e) {
                this.settings.removeLogo = false;
                this.settings.logo = e.target.files[0];
                if(this.settings.logo.size > 1024*1000){
                    alert('Катинка больше чем 1МБ');
                    return false;
                }

                this.settings.logoUrl = URL.createObjectURL(e.target.files[0]);
            },
            removeLogo() {
                this.settings.logoUrl = null;
                this.settings.removeLogo = true;
            },
            formSubmit(e) {
                e.preventDefault();
                this.loading = true;
                this.errors = [];
                const config = {
                    headers: { 'content-type': 'multipart/form-data' }
                };

                let formData = new FormData();
                if(this.settings.logo) formData.append('logo', this.settings.logo);
                formData.append('name', this.user.name);
                formData.append('surname', this.user.surname);
                formData.append('phone', this.settings.phone);
                formData.append('remove_logo', this.settings.removeLogo);

                formData.append('is_active', this.company.is_active ? 1 : 0);
                formData.append('title', this.company.title);
                formData.append('inn', this.company.inn);
                formData.append('description', this.company.description);
                formData.append('address', this.company.address);
                formData.append('categories', JSON.stringify(this.settings.categories));

                axios.post('/settings/update', formData, config)
                    .then((response) => {
                        console.log(response);
                        this.success = response.data;
                    })
                    .catch((error) => {
                        console.log(error.response);
                        this.errors = error.response.data.errors;
                    })
                    .finally(() => (this.loading = false));
            }
        }
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
        opacity: 0;
    }

    .slide-fade-enter-active {
        transition: all .3s ease;
    }
    .slide-fade-leave-active {
        transition: all .3s ease;
    }
    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active до версии 2.1.8 */ {
        transform: translateY(10px);
        opacity: 0;
    }
</style>
