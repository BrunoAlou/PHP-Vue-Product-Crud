<template>
  <div>
    <b-toast :no-auto-hide="true"></b-toast>
    <NavigationTab exact-active-class="active" />
    <b-row class="justify-content-center mt-5">
      <b-col class="col-md-8 mt-5">
        <b-row>
          <b-col md="4" class="mb-3 mb-md-0">
            <b-form-group label="Quantidade:" label-for="quantidade">
              <b-form-input type="number" v-model="quantidade" id="quantidade"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col md="4" class="mb-3 mb-md-0">
            <b-form-group label="Produto:" label-for="produto">
              <b-form-select v-model="produtoSelecionado" id="produto" :options="produtos" value-field="id_produto"
                text-field="nome"></b-form-select>
            </b-form-group>
          </b-col>
          <b-col md="4" class="mb-3 mb-md-0 text-center align-self-center">
            <b-button variant="primary" @click.prevent="adicionarItem" :disabled="!produtoSelecionado || !quantidade">Adicionar</b-button>
          </b-col>
        </b-row>
        <b-row v-if="itensVenda.length > 0" class="mt-3">
          <b-col>
            <b-table striped hover :items="itensVenda" :fields="fields" class="text-center">
              <template #cell(produto)="data">
                {{ data.value.nome }}
              </template>
              <template #cell(quantidade)="data">
                {{ data.value }}
              </template>
              <template #cell(valor_unitario)="data">
                {{ formatPrice(data.value) }}
              </template>
              <template #cell(valor_imposto)="data">
                {{ formatPrice(((data.item.valor_imposto))) }}
              </template>
              <template #cell(valor_imposto_total)="data">
                {{ formatPrice(data.item.valor_imposto_total) }}
              </template>
              <template #cell(valor_total)="data">
                {{ formatPrice(data.item.valor_total) }}
              </template>
              <template #cell(actions)="data">
                <b-button variant="warning" size="sm" @click="data.item && openEditModal(data.item)" class="col-8">Editar</b-button>
                <b-button variant="danger" size="sm" @click="removerItem(data.index)" class="col-8">Remover</b-button>
              </template>
              <template #head(produto)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(quantidade)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(valor_unitario)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(valor_imposto)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(valor_imposto_total)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(valor_total)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
              <template #head(actions)="data">
                <div class="text-center w-100">{{ data.label }}</div>
              </template>
            </b-table>
          </b-col>
        </b-row>
        <b-row v-else class="mt-3">
          <b-col class="text-center">
            <p>Não há itens na venda</p>
          </b-col>
        </b-row>
        <b-row class="mt-3">
          <b-col class="text-center">
            <h4>Total: {{ formatPrice(total) }}</h4>
            <b-button variant="primary" class="mt-3" @click.prevent="cadastrarVenda" :disabled="itensVenda.length === 0">Cadastrar Venda</b-button>
          </b-col>
        </b-row>
      </b-col>
    </b-row>
    <b-modal id="edit-modal" title="Editar Item" @ok="updateItem">
      <b-form-group label="Quantidade:" label-for="edit-quantidade">
        <b-form-input type="number" v-model="editItem.quantidade" id="edit-quantidade"></b-form-input>
      </b-form-group>
    </b-modal>
  </div>
</template>
<script>
import axios from 'axios';
import { BFormGroup, BFormSelect, BFormInput, BButton, BTable, BModal } from 'bootstrap-vue';
import NavigationTab from '@/components/NavigationTab.vue';
import { BToast } from 'bootstrap-vue';

export default {
  components: {
    BToast,
    BFormGroup,
    BFormSelect,
    BFormInput,
    BButton,
    BTable,
    BModal,
    NavigationTab,
  },
  data() {
    return {
      produtoSelecionado: null,
      produtos: [],
      quantidade: 0,
      itensVenda: [],
      editItem: {},
      fields: [
        { key: 'produto', label: 'Produto' },
        { key: 'quantidade', label: 'Quantidade' },
        { key: 'valor_unitario', label: 'Valor Unitário' },
        { key: 'valor_imposto', label: 'Valor Imposto' },
        { key: 'valor_imposto_total', label: 'Valor Imposto Total' },
        { key: 'valor_total', label: 'Valor Total' },
        { key: 'actions', label: 'Ações' },
      ],
    }
  },
  created() {
    this.fetchProdutos();
  },
  methods: {
    fetchProdutos() {
      axios.get('/produto')
        .then((response) => {
          this.produtos = response.data;
        })
        .catch((error) => {
          console.error(error);
        });
    },
    adicionarItem() {

      const produto = this.produtos.find(p => p.id_produto === this.produtoSelecionado);
      const valorUnitario = produto.preco;
      const valorTotal = this.quantidade * valorUnitario;
      const imposto = produto.tipo_produto_perc_imposto;
      const valorImposto = (valorUnitario * (imposto / 100));
      const valorImposto_total = this.quantidade * (valorUnitario * (imposto / 100));
       const item = {
        produto,
        quantidade: this.quantidade,
        valor_unitario: valorUnitario,
        valor_imposto: valorImposto,
        valor_imposto_total: valorImposto_total,
        valor_total: valorTotal,
      };
      this.itensVenda.push(item);
      this.produtoSelecionado = null;
      this.quantidade = null;
    },
    removerItem(index) {
      this.itensVenda.splice(index, 1);
    },
    formatPrice(value) {
      const formatter = new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
      });
      return formatter.format(value);
    },
    openEditModal(item) {
      this.editItem = item;
      this.$bvModal.show('edit-modal');
    },
    updateItem() {
      const index = this.itensVenda.findIndex(item => item === this.editItem);
      if (index !== -1) {
        const produto = this.editItem.produto;
        const valorUnitario = produto.preco;
        const valorTotal = this.editItem.quantidade * valorUnitario;
        const imposto = produto.tipo_produto_perc_imposto;
        const valorImposto = valorUnitario * (imposto / 100);
        const valorImposto_total = this.editItem.quantidade * valorImposto;
        const item = {
          produto,
          quantidade: this.editItem.quantidade,
          valor_unitario: valorUnitario,
          valor_imposto: valorImposto,
          valor_imposto_total: valorImposto_total,
          valor_total: valorTotal,
        };
        this.itensVenda.splice(index, 1, item);
      }
      this.$bvModal.hide('edit-modal');    
    },
    cadastrarVenda() {
      const itens_venda = this.itensVenda.map(item => {
        const valorTotal = item.valor_total;
        const valorImposto = item.valor_imposto_total;
        return {
          id_produto: item.produto.id_produto,
          quantidade: item.quantidade,
          valor_unitario: item.valor_unitario,
          valor_imposto: item.valor_imposto,
          valor_total: valorTotal,
          valor_imposto_total: valorImposto,
        }
      });

      const valorTotal = this.itensVenda.reduce((total, item) => total + item.valor_total, 0);
      const valorImposto = this.itensVenda.reduce((total, item) => total + item.valor_imposto_total, 0);

      axios.post('/venda', {
        itens_venda: itens_venda,
        valor_total: valorTotal,
        valor_imposto: valorImposto,
      },{responseType: 'json'})
      .then(response => {
        if (response.status === 200) {
          console.log('Venda cadastrada com sucesso! ID:', response.data.id_venda);
          this.itensVenda = [];
          this.produtoSelecionado = null;
          this.quantidade = null;
          this.$bvToast.toast('Venda cadastrada com sucesso!', {
            title: 'Sucesso',
            variant: 'success',
            autoHideDelay: 5000,
          });
        } else {
          console.log('Erro ao cadastrar venda!');
          this.$bvToast.toast('Erro ao cadastrar venda!', {
            title: 'Erro',
            variant: 'danger',
            autoHideDelay: 5000,
          });
        }
      })
      .catch(error => {
        console.error(error);
        this.$bvToast.toast('Erro ao cadastrar venda!', {
          title: 'Erro',
          variant: 'danger',
          autoHideDelay: 5000,
        });
      });
    }
  },
  computed:{
    total() {
      return this.itensVenda.reduce((acc, cur) => {
        return acc + cur.valor_total;
      }, 0);
    },
  },
}
</script>

