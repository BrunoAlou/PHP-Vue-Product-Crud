<template>
  <div>
    <b-toast :no-auto-hide="true"></b-toast>
    <NavigationTab exact-active-class="active" />
    <b-row class="justify-content-center text-center mt-5">
      <b-col class="text-center" md="6" lg="4" align-self="center">
        <b-form style="margin: 25px;" ref="form" @submit.prevent="cadastrarProduto">
          <b-form-group id="input-nome-produto" label="Produto:" label-for="input-nome">
            <b-form-input id="input-nome" v-model="nomeProduto" required class="w-100" />
          </b-form-group>
          <b-form-group id="input-descricao-produto" label="Descrição:" label-for="input-descricao">
            <b-form-input id="input-descricao" v-model="descricaoProduto" />
          </b-form-group>
          <b-form-group id="input-preco-produto" label="Preço:" label-for="input-preco">
            <b-input-group id="input-group-preco" label="Preço:" prepend="R$" append=",00" label-for="input-preco">
              <b-form-input id="input-preco" type="number" v-model="precoProduto" required/>
            </b-input-group>
          </b-form-group>
          <b-form-group id="input-tipo-produto" label="Tipo:" label-for="input-tipo">
            <b-form-select id="input-tipo" class="w-100" v-model="tipoProdutoSelecionado" :options="tiposOptions"
              required />
          </b-form-group>
          <b-button-group class="w-100">
            <b-button variant="dark" @click="exibirModalCadastroTipo = true">Cadastrar Tipo de Produto</b-button>
            <b-button :disabled="!nomeProduto || precoProduto === 0 || !tipoProdutoSelecionado" type="submit" variant="primary">Cadastrar Produto</b-button>
          </b-button-group>
          <b-modal v-model="exibirModalCadastroTipo" title="Cadastrar Tipo de Produto" hide-footer>
            <form>
              <b-form-group label="Nome Tipo:">
                <b-form-input v-model="nomeTipo" required />
              </b-form-group>
              <b-form-group label="Percentual de Imposto:">
              <b-input-group append="%">
                <b-form-input type="number" v-model="percImpostoTipo" required/>
              </b-input-group>
            </b-form-group>
              <b-button-group class="mt-2 btn-group w-100">
                <b-button :disabled="!nomeTipo || !percImpostoTipo" variant="primary" @click="cadastrarTipo">Cadastrar</b-button>
                <b-button variant="secondary" @click="exibirModalCadastroTipo = false">Cancelar</b-button>
              </b-button-group>
            </form>
          </b-modal>
        </b-form>
      </b-col>
    </b-row>
  </div>
</template>
<script>
import axios from 'axios';
import NavigationTab from '@/components/NavigationTab.vue'
import { BToast } from 'bootstrap-vue';


export default {
  name: 'CadastroProduto',
  components: {
    NavigationTab,
    BToast
  },
  data() {
    return {
      nomeProduto: '',
      descricaoProduto: '',
      precoProduto: 0,
      tipoProdutoSelecionado: '',
      produtoCadastrado: false,
      tipos: [],
      exibirModalCadastroTipo: false,
      nomeTipo: '',
      percImpostoTipo: 0,
      exibirModal: false,
    }
  },
  mounted() {
    this.atualizarTipos();
  },
  computed: {
    tiposOptions() {
      let options = [{ value: '', text: 'Selecione um tipo' }];
      this.tipos.forEach(tipo => {
        options.push({ value: tipo.id_tipo_produto, text: tipo.nome });
      });
      return options;
    }
  },
  methods: {
    atualizarTipos(tipoCadastrado) {
      axios.get(`/tipo_produto`)
        .then(response => {
          this.tipos = response.data;
          if (tipoCadastrado) {
            this.tipoProdutoSelecionado = tipoCadastrado.id_tipo_produto;
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    cadastrarTipo() {
      axios.post(`/tipo_produto`, {
        nome: this.nomeTipo,
        perc_imposto: this.percImpostoTipo
      })
        .then(response => {
          this.atualizarTipos(response.data);
          this.exibirModalCadastroTipo = false;
          this.nomeTipo = '';
          this.percImpostoTipo = 0;
          console.log(response.data);
          this.$bvToast.toast('Tipo do produto cadastrado com sucesso!', {
            title: 'Sucesso',
            variant: 'success',
            autoHideDelay: 5000,
          });
        })
        .catch(error => {
          console.log(error);
          this.$bvToast.toast('Erro ao cadastrar o tipo do produto!', {
            title: 'Erro',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        });
    },
    cadastrarProduto() {
      axios.post(`/produto`, {
        nome: this.nomeProduto,
        descricao: this.descricaoProduto,
        preco: this.precoProduto,
        id_tipo_produto: this.tipoProdutoSelecionado
      })
        .then(response => {
          console.log(response.data);
          this.$bvToast.toast('Produto cadastrado com sucesso!', {
            title: 'Sucesso',
            variant: 'success',
            autoHideDelay: 5000,
          });
        })
        .catch(error => {
          console.error(error);
            this.$bvToast.toast('Erro ao cadastrar o produto!', {
            title: 'Erro',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        });
    },
  }
}
</script>
<style>
/* Define as colunas ocupando todo o espaço disponível em telas pequenas */
.b-col {
  flex-basis: 100%;
  max-width: 100%;
}
</style>