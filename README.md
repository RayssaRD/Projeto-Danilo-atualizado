# Documentação do Sistema - Módulo de Persistência

Este projeto conta com um backend em PHP para o processamento e armazenamento de mensagens enviadas pelos usuários. Abaixo, detalhamos o funcionamento do arquivo principal de salvamento.

## 📄 Arquivo: `salvar.php`

### 1. Objetivo Geral
O arquivo `salvar.php` funciona como uma ponte entre o formulário do site (Front-end) e o Banco de Dados (Back-end). Ele é responsável por:
* Receber os dados enviados via método POST (`autor` e `texto`).
* Validar se as informações são reais e seguras.
* Gravar as mensagens permanentemente no banco de dados.

---

### 2. Segurança e Tratamento de Dados
Para garantir a integridade do sistema e a proteção contra ataques, foram implementadas as seguintes camadas:

* **Proteção Anti-XSS (Sanitização):**
    * Utilizamos a função `htmlspecialchars()`. Ela transforma caracteres especiais (como `<` e `>`) em códigos de texto inofensivos. Isso impede que usuários mal-intencionados executem scripts maliciosos no navegador de outros visitantes.
* **Validação de Dados Vazios:**
    * **Limpeza:** Usamos o `trim()` para remover espaços em branco desnecessários no início e no fim das frases.
    * **Verificação:** O sistema utiliza `empty()` para garantir que ninguém envie mensagens em branco ou apenas com espaços. Se o campo estiver vazio após a limpeza, a gravação é interrompida.
* **Segurança no Banco (Prepared Statements):**
    * Os dados são inseridos usando "comandos preparados", o que evita que códigos SQL invasivos sejam executados por acidente ou ataque.

---

### 3. Comunicação com o Front-end (JSON)
O script comunica o resultado da operação de volta para o site usando o formato **JSON** (um padrão de texto leve que o JavaScript entende facilmente).

**Possíveis retornos:**
1. **Sucesso:** `{"status": "sucesso"}` - Quando a mensagem é gravada corretamente.
2. **Erro de Preenchimento:** `{"status": "erro", "mensagem": "Atenção: Os campos não podem estar vazios."}` - Quando o usuário tenta enviar campos sem conteúdo.
3. **Erro de Conexão:** `{"status": "erro", "mensagem": "Erro interno..."}` - Caso ocorra uma falha técnica com o servidor ou banco de dados.

---
*Documentação gerada para auxílio ao desenvolvimento do projeto.*
