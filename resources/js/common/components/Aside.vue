 <template>
  <aside>
      <div v-if="categories">
        <div v-for="category in categories" :key="category.id">
          <h6 class="bg-info category-title">{{ category.title }}</h6>
          <div class="sub-category" v-for="subCategory in category.children" :key="subCategory.id">
            <p-check name="category" color="info" :value="subCategory.id" v-model="checkedCategory" @change="checkboxToggle()">{{ subCategory.title }}</p-check>
          </div>
          {{ checkedCategory }}
        </div>
      </div>
  </aside>
</template>

<script lang="ts">
import { Category } from '../model/category.model'
import categoryService from '../category.service'
export default {
  name: 'app-aside',
  props: ['categories'],
  data() {
    return {
      checkedCategory: [],
      aPropFrom: true
    }
  },
  watch: {
    categories() {
      this.checkedCategory = categoryService.getIds(this.categories)
      this.$emit('change-category', this.checkedCategory)
    }
  },
  methods: {
    checkboxToggle() {
      this.$emit('change-category', this.checkedCategory)
    }
  }
}
</script> 

<style lang="scss" scoped>
  aside {
    background: #fff;
    box-shadow: 0px 0px 10px -3px rgba(0,0,0,0.25);
    .category-title {
      padding: 5px 10px;
      color:white;
      font-weight: 500;
    }
    .sub-category {
      padding: 0 10px;
      label {
        cursor: pointer;
        input {
          margin-right: 5px;
        }
      }
    }
  }
</style>