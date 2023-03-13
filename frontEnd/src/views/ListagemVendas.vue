<template>
    <div>
      <NavigationTab exact-active-class="active"/>
        <b-col class="text-center mt-5" align-self="center">
          <b-table :items="vendas" striped hover responsive class="mt-5">
            <template v-slot:cell(id)="data">
              {{ data.value }}
            </template>
            <template v-slot:cell(produto)="data">
              {{ data.value }}
            </template>
            <template v-slot:cell(quantidade)="data">
              {{ data.value }}
            </template>
            <template v-slot:cell(data)="data">
              {{ data.value }}
            </template>
          </b-table>
        </b-col>
    </div>
  </template>
  
  <script>
  import { BTable } from 'bootstrap-vue';
  import NavigationTab from '@/components/NavigationTab.vue';
  import axios from 'axios';
  
  export default {
    components: {
      BTable,
      NavigationTab,
    },
    data() {
      return {
        vendas: [],
      };
    },
    mounted() {
      axios
        .get('http://localhost:8080/venda')
        .then((response) => {
          this.vendas = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  };
  </script>
  
  <style scoped>
  </style>