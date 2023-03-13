import Vue from 'vue'
import VueRouter from 'vue-router'
import ListagemProdutos from './views/ListagemProdutos.vue'
import CadastroProduto from './views/CadastroProduto.vue'
import ListagemVendas from './views/ListagemVendas.vue'
import CadastroVenda from './views/CadastroVenda.vue'

Vue.use(VueRouter)

const routes = [
  { path: '/',  component: CadastroProduto },
  { path: '/listagem-produtos', component: ListagemProdutos },
  { path: '/cadastro-produto', component: CadastroProduto },
  { path: '/listagem-vendas', component: ListagemVendas },
  { path: '/cadastro-venda', component: CadastroVenda },
]

const router = new VueRouter({
  mode: 'history',
  routes
})

export default router