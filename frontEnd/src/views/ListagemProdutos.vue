<template>
    <div>
      <NavigationTab exact-active-class="active" />
        <b-col class="text-center mt-5" align-self="center">
          <b-table class="mt-5" striped hover :items="produtos" :fields="fields" :per-page="perPage" :current-page="currentPage" @row-clicked="selecionaProduto" />
          <b-pagination :total-rows="totalProdutos" :per-page="perPage" v-model="currentPage" class="my-3" />
        </b-col>
    </div>
  </template>
  <script>
  import NavigationTab from '@/components/NavigationTab.vue'
  import axios from 'axios'
  import { BTable, BPagination } from 'bootstrap-vue'
  
  export default {
    components: {
      NavigationTab,
      BTable,
      BPagination
    },
    data() {
      return {
        produtos: [],
        fields: [
          { key: 'id', label: 'ID' },
          { key: 'nome', label: 'Nome' },
          { key: 'preco', label: 'Preço' }
        ],
        totalProdutos: 0,
        perPage: 10,
        currentPage: 1
      }
    },
    mounted() {
      this.carregaProdutos()
    },
    methods: {
      carregaProdutos() {
        axios.get('http://localhost:8080/produto').then(response => {
          this.produtos = response.data
          this.totalProdutos = this.produtos.length
        })
      },
      selecionaProduto(item) {
        console.log(`Produto selecionado: ${item.nome}`)
        // implemente aqui a lógica para selecionar o produto e redirecionar para a tela de detalhes
      }
    }
  }
  </script>