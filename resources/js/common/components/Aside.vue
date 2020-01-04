 <template>
  <aside>
      <div v-if="categories">
        <div v-for="category in categories" :key="category.id" class="pb-2">
          <h6 v-b-toggle="'collapse-' + category.id" class="category-title"><span class="title">{{ category.title }}</span><font-awesome-icon :icon="['fas', 'chevron-left']" class="icon" /></h6>
          <b-collapse :id="'collapse-' + category.id">
            <div class="sub-category" v-for="subCategory in category.children" :key="subCategory.id">
              <p-check name="category" color="info" :value="subCategory.id" v-model="checkedCategory" @change="checkboxToggle()">{{ subCategory.title }}</p-check>
            </div>
          </b-collapse>
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
      // this.checkedCategory = categoryService.getIds(this.categories)
      // this.$emit('change-category', this.checkedCategory)
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
    padding: 20px 10px;
    .category-title {
      display: flex;
      justify-content: space-between;
      padding: 5px 0px;
      margin: 0;
      font-weight: 500;
      color: #343a40;
      cursor: pointer;
      .icon {
        position: relative;
        top: 3px;
        transition: transform 0.4s;
      }
      &.collapsed {
        .icon {
          transform: rotate(-90deg);
        }
      }
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