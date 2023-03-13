<template>
  <div>
    <NavigationTab exact-active-class="active"/>
    <b-row class="justify-content-center text-center mt-5">
      <b-col class="text-center col-4" align-self="center">
        <b-form style="margin: 25px;">
          <b-form-group id="input-group-2" label="Produto:" label-for="input-2">
            <b-form-input id="input-2" v-model="nomeProduto" required />
          </b-form-group>
          <b-form-group id="input-group-3" label="Descrição:" label-for="input-3">
            <b-form-input id="input-3" v-model="descricaoProduto" />
          </b-form-group>
          <b-form-group id="input-group-4" label="Preço:" label-for="input-4">
            <b-input-group id="input-group-4" label="Preço:" prepend="R$" append=",00" label-for="input-4">
            <b-form-input id="input-4" type="number" v-model="precoProduto" />
            </b-input-group>          
          </b-form-group>
          <b-form-group id="input-group-5" label="Tipo:" label-for="input-5">
            <b-form-select class="w-100" v-model="tipoProdutoSelecionado" :options="tiposOptions" required />
          </b-form-group>
          <b-button-group>
            <b-button variant="dark" @click="exibirModalCadastroTipo = true">Cadastrar Tipo de Produto</b-button>
            <b-button type="submit" variant="primary" @click="cadastrarProduto">Cadastrar Produto</b-button>
          </b-button-group>
          <b-modal v-model="exibirModalCadastroTipo" title="Cadastrar Tipo de Produto">
            <form ref="form" @submit.stop.prevent="handleSubmit">
              <b-form-group label="Nome:">
                <b-form-input v-model="nomeTipo" />
              </b-form-group>
              <b-input-group label="Percentual de Imposto:" append="%">
                <b-form-input type="number" v-model="percImpostoTipo" />
              </b-input-group>
              <b-button variant="primary" @click="cadastrarTipo">Cadastrar</b-button>
              <b-button variant="secondary" @click="exibirModalCadastroTipo = false">Cancelar</b-button>
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


export default {
  name: 'CadastroProduto',
  components: {
    NavigationTab,
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

    axios.interceptors.response.use(
      response => {
        return response;
      },
      error => {
        if (error.response.status === 401) {
          this.$router.push({ name: 'Login' });
        }
        return Promise.reject(error);
      }
    );
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
        })
        .catch(error => {
          console.log(error);
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
          this.produtoCadastrado = true;
        })
        .catch(error => {
          console.log(error);
        });
    },
    fecharModal() {
      this.exibirModal = false;
      this.$emit('fechar');
    },
    salvarTipo() {
      axios.post(`/tipo_produto`, {
          nome: this.nomeTipo
        })
        .then(response => {
          this.$emit('tipo-cadastrado', response.data);
          this.nomeTipo = '';
          this.exibirModal = false;
        })
        .catch(error => {
          console.log(error);
        });
    }
  }
}
</script>
