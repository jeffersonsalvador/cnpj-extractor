# PROJETO EM DESENVOLVIMENTO

[comment]: <> (# CNPJ - Receita Federal - Dados Públicos)

[comment]: <> (- Fonte oficial da Receita Federal do Brasil, [aqui]&#40;https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/dados-publicos-cnpj&#41;.)

[comment]: <> (- Layout dos arquivos, [aqui]&#40;https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/arquivos/NOVOLAYOUTDOSDADOSABERTOSDOCNPJ.pdf&#41;.)

[comment]: <> (A Receita Federal do Brasil disponibiliza bases com os dados públicos do cadastro nacional de pessoas jurídicas &#40;CNPJ&#41;.)

[comment]: <> (De forma geral, nelas constam as mesmas informações que conseguimos ver no cartão do CNPJ, quando fazemos uma consulta individual, acrescidas de outros dados de Simples Nacional, sócios e etc. Análises muito ricas podem sair desses dados, desde econômicas, mercadológicas até investigações.)

[comment]: <> (## Instalação)

[comment]: <> (```)

[comment]: <> (composer install)

[comment]: <> (```)

[comment]: <> (Nesse repositório consta um processo de ETL para **i&#41;** baixar os arquivos; **ii&#41;** descompactar; **iii&#41;** ler, tratar e **iv&#41;** inserir num banco de dados relacional PostgreSQL.)

[comment]: <> (---------------------)

[comment]: <> (### Infraestrutura necessária:)

[comment]: <> (- [Python 3.8]&#40;https://www.python.org/downloads/release/python-3810/&#41;)

[comment]: <> (- [PostgreSQL 13]&#40;https://www.postgresql.org/download/&#41;)

[comment]: <> (---------------------)

[comment]: <> (### How to use:)

[comment]: <> (1. Com o Postgre instalado, inicie a instância do servidor &#40;pode ser local&#41; e crie o banco de dados conforme o arquivo `banco_de_dados.sql`.)

[comment]: <> (2. Conforme o seu ambiente, crie um arquivo `.env` no diretório `code`, conforme as variáveis de ambiente do arquivo `.env_template`:)

[comment]: <> (    - `OUTPUT_FILES_PATH`: diretório de destino para o donwload dos arquivos)

[comment]: <> (    - `EXTRACTED_FILES_PATH`: diretório de destino para a extração dos arquivos .zip)

[comment]: <> (    - `DB_USER`: usuário do banco de dados criado pelo arquivo `banco_de_dados.sql`)

[comment]: <> (    - `DB_PASSWORD`: senha do usuário do BD)

[comment]: <> (    - `DB_HOST`: host da conexão com o BD)

[comment]: <> (    - `DB_PORT`: porta da conexão com o BD)

[comment]: <> (    - `DB_NAME`: nome da base de dados na instância &#40;`Dados_RFB` - conforme arquivo `banco_de_dados.sql`&#41;)

[comment]: <> (3. Instale as bibliotecas necessárias, disponíveis em `requirements.txt`:)

[comment]: <> (```)

[comment]: <> (pip install -r requirements.txt)

[comment]: <> (```)

[comment]: <> (4. Execute o arquivo `ETL_coletar_dados_e_gravar_BD.py` e aguarde a finalização do processo.)

[comment]: <> (    - Os arquivos são grandes. Dependendo da infraestrutura isso deve levar muitas horas para conclusão.)

[comment]: <> (    - Arquivos de 08/05/2021: `4,68 GB` compactados e `17,1 GB` descompactados.)

[comment]: <> (---------------------)

[comment]: <> (### Tabelas geradas:)

[comment]: <> (- Para maiores informações, consulte o [layout]&#40;https://www.gov.br/receitafederal/pt-br/assuntos/orientacao-tributaria/cadastros/consultas/arquivos/NOVOLAYOUTDOSDADOSABERTOSDOCNPJ.pdf&#41;.)

[comment]: <> (    - `empresa`: dados cadastrais da empresa em nível de matriz)

[comment]: <> (    - `estabelecimento`: dados analíticos da empresa por unidade / estabelecimento &#40;telefones, endereço, filial, etc&#41;)

[comment]: <> (    - `socios`: dados cadastrais dos sócios das empresas)

[comment]: <> (    - `simples`: dados de MEI e Simples Nacional)

[comment]: <> (    - `cnae`: código e descrição dos CNAEs)

[comment]: <> (    - `quals`: tabela de qualificação das pessoas físicas - sócios, responsável e representante legal.)

[comment]: <> (    - `natju`: tabela de naturezas jurídicas - código e descrição.)

[comment]: <> (    - `moti`: tabela de motivos da situação cadastral - código e descrição.)

[comment]: <> (    - `pais`: tabela de países - código e descrição.)

[comment]: <> (    - `munic`: tabela de municípios - código e descrição.)


[comment]: <> (- Pelo volume de dados, as tabelas  `empresa`, `estabelecimento`, `socios` e `simples` possuem índices para a coluna `cnpj_basico`, que é a principal chave de ligação entre elas.)

[comment]: <> (### Modelo de Entidade Relacionamento:)

[comment]: <> (![alt text]&#40;https://github.com/aphonsoar/Receita_Federal_do_Brasil_-_Dados_Publicos_CNPJ/blob/master/Dados_RFB_ERD.png&#41;)
