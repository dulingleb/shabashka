<template>
    <div class="row">
        <div class="col-md-4 col-lg-3">
            <div class="filters bg-white rounded border border-light p-2">
                <div class="category" v-for="(category, category_key) in categories">
                    <a :href="'#category_' + category_key" class="text-decoration-none title collapsed" data-toggle="collapse">{{ category.parent }}</a>
                    <div class="child-category ml-2 collapse" :id="'category_' + category_key" :class>
                        <div v-for="(child, child_key) in category.child">
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input" type="checkbox" :value="child.id"
                                       v-model="filter_category" :id="'category_'+category_key+'_'+child_key"
                                       @change="setFilters('cat', filter_category)">
                                <label :for="'category_'+category_key+'_'+child_key" class="custom-control-label">{{ child.title }}</label>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-8 col-lg-9">
            <div class="tasks bg-white rounded border border-light p-2">
                <div class="row task border-bottom m-0" v-for="task in tasks.data">
                    <div class="col-md-9">
                        <h3 class="title"><a :href="'/tasks/'+task.id" class="text-decoration-none">{{ task.title }}</a> <small>{{ task.created_at }}</small></h3>
                        <p class="description">{{ (task.description.length>150) ? task.description.substr(0,150) + '...' : task.description }}</p>
                        <div class="info">
                            <i class="far fa-clock"></i> до {{ formatDate(new Date(Date.parse(task.term))) }}
                            <i class="far fa-folder ml-3"></i> {{ task.category.title }}
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <span class="price">{{ task.price > 0 ? task.price.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ') + ' руб.' : 'договорная'  }}</span>
                        <a :href="'/tasks/'+task.id" class="btn btn-primary" v-if="task.executor==null">Откликнуться</a>
                        <span class="text-success" v-else>Исполнитель найден</span>
                    </div>
                </div>
                <h3 class="text-center mt-3 mb-3" v-if="typeof tasks.data === 'undefined' || Object.keys(tasks.data).length==0">Заданий в этих категориях нет</h3>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ShowComponent",
        props: ['categories'],
        mounted() {
            this.filter_category = new URL(window.location.href).searchParams.getAll("cat[]");
            this.filter_category.forEach(function (value) {
               $('input[value='+value+']').parent().parent().parent().addClass('show').parent().find('.title').removeClass('collapsed');
            });
            this.getTasks();

        },
        data() {
            return {
                tasks: [],
                filter_category: []
            }
        },
        methods: {
            getTasks() {
                let url = new URL(window.location.href);
                let categories = url.searchParams.getAll("cat[]");

                axios.post('/tasks/list', categories.length>0 ? {'categories': categories} : null)
                    .then((response) => {
                        this.tasks = response.data;
                    })
                    .catch((error) => {
                        console.log(error.response.data);
                    })
            },
            formatDate(date) {
                let monthNames = [
                    "Января", "Февраля", "Марта",
                    "Апреля", "Мая", "Июня", "Июля",
                    "Августа", "Сентября", "Октября",
                    "Ноября", "Декабря"
                ];

                return date.getDate() + ' ' + monthNames[date.getMonth()] + ' ' + date.getFullYear();
            },
            setFilters(key, arr){
                if(arr.length > 0) {
                    arr = arr.map(encodeURIComponent);
                    let url = '?' + key + encodeURIComponent('[]') + '=' + arr.join('&' + key + encodeURIComponent('[]') + '=');
                    history.pushState( {}, null, url );
                } else {
                    history.pushState( {}, null, '/' );
                }

                this.getTasks();
            }
        }
    }
</script>

<style scoped>

</style>
