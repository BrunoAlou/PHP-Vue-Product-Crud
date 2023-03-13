<template class="d-flex justify-content-center">
  <b-navbar class="justify-content-center" type="dark" variant="dark" fixed="top">
    <div class="d-flex align-items-center">
      <b-navbar-brand href="/">
        <img src="logo.png" width="100" height="30" class="d-inline-block align-top" alt="Logo">
      </b-navbar-brand>
    </div>
    <b-navbar-nav>
      <b-nav-item exact-active-class="active" :to="{ path: '/cadastro-produto' }">Cadastro de Produto</b-nav-item>
      <b-nav-item exact-active-class="active" :to="{ path: '/cadastro-venda' }">Cadastro de Venda</b-nav-item>
      <b-nav-item exact-active-class="active" :to="{ path: '/listagem-produtos' }">Listagem de Produtos</b-nav-item>
      <b-nav-item exact-active-class="active" :to="{ path: '/listagem-vendas' }">Listagem de Vendas</b-nav-item>
    </b-navbar-nav>
  </b-navbar>
</template>
<script>
export default {
  computed: {
    // Obtém a rota atual
    rotaAtual() {
      return this.$route.path
    }
  },
  methods: {
    // Verifica se a rota atual corresponde à rota do item de navegação
    isActive(rota) {
      return this.rotaAtual === rota
    }
  },
  mounted() {
    // Debug da rota atual no console
    console.log('Rota atual:', this.rotaAtual)
  }
}
</script>
<style>
/* Adiciona a classe active ao item de navegação correspondente à rota atual */
.b-nav-item.active {
  background-color: #007bff;
  color: #fff;
}

/* Centraliza o conteúdo da navbar */
.b-navbar {
  display: flex;
  justify-content: center;
}

/* Define uma largura máxima para a navbar */
.b-navbar-brand,
.b-navbar-nav,
.b-nav-item-dropdown {
  max-width: 800px;
  width: 100%;
}

</style>