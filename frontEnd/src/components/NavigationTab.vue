<template>
  <b-navbar type="dark" variant="dark" fixed="top" toggleable="md">
    <div class="d-flex align-items-center">
      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
      <b-navbar-brand href="/">
        <img src="logo.png" width="100" height="30" class="d-inline-block align-top" alt="Logo">
      </b-navbar-brand>
    </div>
    <b-collapse is-nav id="nav-collapse">
      <b-navbar-nav>
        <b-nav-item exact-active-class="active" :to="{ path: '/cadastro-produto' }">Cadastro de Produto</b-nav-item>
        <b-nav-item exact-active-class="active" :to="{ path: '/cadastro-venda' }">Cadastro de Venda</b-nav-item>
        <b-nav-item exact-active-class="active" :to="{ path: '/listagem-produtos' }">Listagem de Produtos</b-nav-item>
        <b-nav-item exact-active-class="active" :to="{ path: '/listagem-vendas' }">Listagem de Vendas</b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>

<script>
export default {
  computed: {
    rotaAtual() {
      return this.$route.path
    }
  },
  methods: {
    isActive(rota) {
      return this.rotaAtual === rota
    }
  },
  mounted() {
    console.log('Rota atual:', this.rotaAtual)
  }
}
</script>

<style>
.b-nav-item.active {
  background-color: #007bff;
  color: #fff;
}

.b-navbar {
  display: flex;
  justify-content: center;
}

.b-navbar-brand,
.b-navbar-nav,
.b-nav-item-dropdown {
  max-width: 800px;
  width: 100%;
}
</style>
