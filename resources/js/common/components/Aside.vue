 <template>
  <aside>
      <div v-if="categories">
        <div v-for="category in categories" :key="category.id">
          <h6 v-b-toggle="'collapse-' + category.id" class="bg-info category-title"><span class="title">{{ category.title }}</span><font-awesome-icon :icon="['fas', 'chevron-left']" class="icon" /></h6>
          <b-collapse :id="'collapse-' + category.id">
            <div class="sub-category" v-for="subCategory in category.children" :key="subCategory.id">
              <p-check name="category" color="info" :value="subCategory.id" v-model="checkedCategory" @change="checkboxToggle()">{{ subCategory.title }}</p-check>
            </div>
            {{ checkedCategory }}
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
    background: #fff;
    box-shadow: 0px 0px 10px -3px rgba(0,0,0,0.25);
    .category-title {
      display: flex;
      justify-content: space-between;
      padding: 5px 10px;
      color:white;
      font-weight: 500;
      cursor: pointer;
      .icon {
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