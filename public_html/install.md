# CHURROS FLOW - Instalacao (HostGator/cPanel)

## 1) Criar banco e usuario MySQL
1. No cPanel, abra **MySQL Databases**.
2. Crie o banco (ex: `churrosflow`).
3. Crie usuario e senha forte.
4. Adicione o usuario ao banco com **ALL PRIVILEGES**.

## 2) Importar SQL
### Instalacao nova
- Importe `public_html/sql/install.sql`.

### Projeto ja instalado (upgrade)
- Importe `public_html/sql/update_restaurant_theme.sql`.
- Esse script adiciona logo e configuracao de tema por restaurante.

## 3) Configurar `public_html/config.php`
Edite:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_PORT`
- `BASE_URL`
- `DEBUG` (false em producao)

Para seu caso:
- `BASE_URL = https://temartes.com/ChurrosFlow/public_html`

## 4) Upload correto
Suba os arquivos para a pasta da aplicacao e mantenha a estrutura do projeto.

## 5) Permissoes de escrita
Garanta permissao para:
- `public_html/pdf`
- `public_html/storage/exports`
- `public_html/storage/logos`

## 6) Testes
- Login: `index.php?r=auth/login`
- Configuracoes: `index.php?r=settings/index`
- Upload de logo e troca de cores
- Geracao de PDF e envio WhatsApp

## 7) Seguranca recomendada
- `DEBUG=false`
- SSL/HTTPS ativo
- Backup periodico do banco
