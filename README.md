# AMES - Sistema de Gestão de Atividades e Lembretes

O **AMES** é uma aplicação web focada na organização diária. O sistema permite que os usuários se cadastrem, gerenciem suas rotinas (tarefas domésticas, medicamentos, compromissos, etc.) e configurem lembretes que disparam notificações por e-mail automaticamente.

## 🚀 Funcionalidades

- **Autenticação:** Cadastro e Login de usuários de forma isolada.
- **Gestão de Atividades:** Criação, listagem, conclusão e exclusão de atividades.
- **Geolocalização:** Conversão de endereço em coordenadas (Latitude/Longitude).
- **Sistema de Lembretes:** Vinculação de lembretes a atividades com data e hora específicas.
- **Notificações por E-mail:** Envio automático de e-mail no horário agendado (via PHPMailer).
- **Logs do Sistema:** Rastreamento de ações importantes realizadas pelos usuários.

## 📋 Pré-requisitos

Para rodar este projeto localmente, você precisará de:

- Servidor web local (ex: [XAMPP](https://www.apachefriends.org/pt_br/index.html) ou WAMP).
- PHP 8.0+ (com extensão `zip` e `curl` habilitadas no `php.ini`).
- Banco de Dados MySQL / MariaDB.
- [Composer](https://getcomposer.org/) para gerenciamento das dependências.

## 🔧 Como Instalar e Rodar

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/SeuUsuario/AMES_BACKEND_P2.git](https://github.com/SeuUsuario/AMES_BACKEND_P2.git)
