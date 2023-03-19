<template>
  <div>
    <b-toast :no-auto-hide="true"></b-toast>
    <template>
      <NavigationTab exact-active-class="active" />
      <div>
        <b-table :items="items" :fields="fields" striped responsive="sm" class="mt-5">
          <template #cell(show_details)="row">
            <b-button size="sm" @click="carregaDetalhesVenda(row.item, row.index)">
              {{ row._showDetails ? 'Esconder' : 'Mostrar' }} Detalhes
            </b-button>
          </template>
          <template #row-details="row">
            <b-card>
              <b-card v-for="itemVenda in row.item.itens_venda" :key="itemVenda.id_item_venda" class="mb-3">
                <b-row>
                  <b-col sm="3" class="text-sm-right"><b>Produto:</b></b-col>
                  <b-col>{{ itemVenda.id_produto }}</b-col>
                </b-row>
                <b-row>
                  <b-col sm="3" class="text-sm-right"><b>Quantidade:</b></b-col>
                  <b-col>{{ itemVenda.quantidade }}</b-col>
                </b-row>
                <b-row>
                  <b-col sm="3" class="text-sm-right"><b>Valor Imposto:</b></b-col>
                  <b-col>{{ itemVenda.valor_imposto }}</b-col>
                </b-row>
                <b-row>
                  <b-col sm="3" class="text-sm-right"><b>Valor  Unitario:</b></b-col>
                  <b-col>{{ itemVenda.valor_unitario }}</b-col>
                </b-row>
              </b-card>
              <b-button size="sm" @click="row.toggleDetails">Esconder Detalhes</b-button>
            </b-card>
          </template>
        </b-table>
      </div>
    </template>
  </div>
</template>

<script>
import NavigationTab from '@/components/NavigationTab.vue'
import axios from 'axios'
import Vue from 'vue';
import { BToast } from 'bootstrap-vue';

  export default {
    components:{
      NavigationTab,
      BToast
    },
    data() {
      return {
        fields: ['id_venda', 'valor_total', 'valor_imposto','data_hora','show_details'],
        items: [],
        selectedItems: []
      }
    },
    created() {
      this.carregaVendas()
    },
    methods:{
      carregaVendas() {
        axios.get('/vendas').then(response => {
          this.items = response.data.map(item => {
            return {...item, _showDetails: false};
          });
        })
      },
      carregaDetalhesVenda(item, index) {
        const isSelected = this.selectedItems.includes(index);
        if (isSelected) {
          this.selectedItems.splice(this.selectedItems.indexOf(index), 1);
        } else {
          this.selectedItems.push(index);
        }
        this.items[index]._showDetails = isSelected ? false : true;
        if (!isSelected && (!this.items[index].itens_venda || this.items[index].itens_venda.length === 0)) {
          axios.get(`/venda/${item.id_venda}`)
          .then(response => {
            console.log(response.data.itens_venda);
            if (response.data.itens_venda.length > 0) {
              Vue.set(this.items[index], 'itens_venda', response.data.itens_venda);
            }
          })
          .catch(error => {
            console.log(error)
            this.$bvToast.toast('Não foram encontrados itens para essa venda', {
            title: 'Erro',
            variant: 'danger',
            autoHideDelay: 5000,
          });
          });
        }
      },
    },
  }
</script>
