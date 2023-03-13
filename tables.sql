CREATE TABLE tipo_produto (
id_tipo_produto SERIAL PRIMARY KEY,
nome VARCHAR(100) NOT NULL,
perc_imposto DECIMAL(10, 2) NOT NULL
);

CREATE TABLE produto (
id_produto SERIAL PRIMARY KEY,
nome VARCHAR(100) NOT NULL,
preco DECIMAL(10, 2) NOT NULL,
id_tipo_produto INTEGER NOT NULL,
CONSTRAINT fk_id_tipo_produto FOREIGN KEY (id_tipo_produto) REFERENCES tipo_produto(id_tipo_produto)
);



CREATE TABLE venda (
id_venda SERIAL PRIMARY KEY,
data_hora TIMESTAMP NOT NULL DEFAULT NOW(),
valor_total DECIMAL(10, 2) NOT NULL,
valor_imposto DECIMAL(10, 2) NOT NULL
);

CREATE TABLE item_venda (
id_item_venda SERIAL PRIMARY KEY,
id_venda INTEGER NOT NULL,
id_produto INTEGER NOT NULL,
quantidade INTEGER NOT NULL,
valor_unitario DECIMAL(10, 2) NOT NULL,
valor_imposto DECIMAL(10, 2) NOT NULL,
CONSTRAINT fk_id_venda FOREIGN KEY (id_venda) REFERENCES venda(id_venda),
CONSTRAINT fk_id_produto FOREIGN KEY (id_produto) REFERENCES produto(id_produto)
);