<template>
  <div>
    <NavigationTab exact-active-class="active" />
    <b-row class="justify-content-center text-center mt-5">
      <b-col class="text-center col-4 mt-5" align-self="center">
        <b-form>
          <b-form-group label="Quantidade:" label-for="quantidade">
            <b-form-input type="number" v-model="quantidade" id="quantidade"></b-form-input>
          </b-form-group>
          <b-form-group label="Produto:" label-for="produto">
            <b-form-select v-model="produtoSelecionado" id="produto" :options="produtos" value-field="id_produto"
              text-field="nome"></b-form-select>
          </b-form-group>
          <b-button variant="primary" @click.prevent="cadastrarVenda">Cadastrar Venda</b-button>
        </b-form>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import axios from 'axios';
import { BForm, BFormGroup, BFormSelect, BFormInput, BButton } from 'bootstrap-vue';
import NavigationTab from '@/components/NavigationTab.vue';


export default {
  components: {
    BForm,
    BFormGroup,
    BFormSelect,
    BFormInput,
    BButton,
    NavigationTab,
  },
  data() {
    return {
      produtoSelecionado: null,
      quantidade: null,
      produtos: []
    }
  },
  created() {
    // Carrega a lista de produtos para exibir no dropdown
    this.carregarProdutos();
  },
  methods: {
    carregarProdutos() {
      axios.get(`produto`)
        .then(response => {
          this.produtos = response.data;
        })
        .catch(error => {
          console.log(error);
        });
    },
    cadastrarVenda() {
      axios.get(`/produto/${this.produtoSelecionado}`)
        .then(response => {
          const produto = response.data;
          if (produto && this.quantidade > 0) {
            axios.post(`/venda`, {
              id_produto: produto.id_produto,
              quantidade: this.quantidade,
              valor_imposto: this.quantidade * (produto.preco * (produto.tipo_produto_perc_imposto / 100)),
              valor_total: this.quantidade * produto.preco + this.quantidade * (produto.preco * (produto.tipo_produto_perc_imposto / 100))
            })
            .then(response => {
              console.log(response.data);
            })
            .catch(error => {
              console.log(error);
            });
          } else {
            console.log("Produto inválido ou quantidade insuficiente.");
          }
        })
        .catch(error => {
          console.log(error);
        });
    } 
  }
}
</script>
