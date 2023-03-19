<template>
  <div>
    <NavigationTab exact-active-class="active" />
    <b-col class="text-center mt-5" align-self="center">
      <b-table class="mt-5" striped hover :items="produtos" :fields="fields" @row-clicked="selecionaProduto" />
    </b-col>
  </div>
</template>

<script>
import NavigationTab from '@/components/NavigationTab.vue'
import axios from 'axios'
import { BTable } from 'bootstrap-vue'

export default {
  components: {
    NavigationTab,
    BTable
  },
  data() {
    return {
      produtos: [],
      fields: [
        { key: 'nome', label: 'Nome' },
        { key: 'preco', label: 'Preço (R$)', formatter: this.precoFormatter },
        { key: 'tipo_produto_perc_imposto', label: 'Imposto (%)', formatter: this.impostoFormatter }
      ]
    }
  },
  mounted() {
    this.carregaProdutos()
  },
  methods: {
    carregaProdutos() {
      axios.get('/produto').then(response => {
        this.produtos = response.data
      })
    },
    selecionaProduto(item) {
      console.log(`Produto selecionado: ${item.nome}`)
    }
  },
  filters: {
    precoFormatter(value) {
      return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
    },
    impostoFormatter(value) {
      return new Intl.NumberFormat('pt-BR', { style: 'percent', minimumFractionDigits: 2 }).format(value)
    }
  }
}
</script>
