 <template>
  <aside>
      {{ categories  }}
  </aside>
</template>

<script lang="ts">
export default {
  name: "app-aside",
  props: ['categories'],
  components: {}
};
</script> 

<style lang="scss" scoped>
  aside {
    background: #fff;
    box-shadow: 0px 0px 10px -3px rgba(0,0,0,0.25);
  }
</style>