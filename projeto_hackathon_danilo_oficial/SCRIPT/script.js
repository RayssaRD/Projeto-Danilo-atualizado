   // Assim que abrir o index.html, ele verifica se pode estar ali
fetch('../PHP/check_session.php')
.then(res => res.json())
.then(dados => {
    if (!dados.logado) {
        // Se não estiver logado, redireciona para a tela de login
        window.location.href = 'index.html'; 
    } else {
        console.log("Bem-vindo, " + dados.nome);
    }
});
   
   const mural = document.getElementById("mural");

    const form = document.getElementById("formMensagem");

    const inputTexto = document.getElementById("texto");
    const campo = document.getElementById("texto");
    const visor = document.getElementById("contador");

    campo.addEventListener("input", function() {
        const limite = 150;
        const digitados = campo.value.length; // Pega o tamanho atual
        const restantes = limite - digitados; // Calcula a sobra

        visor.textContent = restantes + " caracteres restantes";
    });


    // 1. Função que bate no PHP e traz as mensagens (READ)

    function carregarMensagens() {

        fetch('../PHP/ler.php')

            .then(resposta => resposta.json())

            .then(dados => {

                mural.innerHTML = ''; // Limpa antes de desenhar



                dados.forEach(msg => {

                    mural.innerHTML += `

                                <div class="col-md-8 text-start p-3 rounded shadow card-msg w-75">

                                    <h5 class="m-0">${msg.texto}</h5>

                                    <small class="text-light opacity-50">Enviado às: ${msg.hora}</small>

                                    <small class="text-light opacity-50">Enviado por: ${msg.autor}</small>

                                </div>

                            `;

                });

            })

            .catch(erro => console.error("Erro ao buscar dados:", erro));

    

    }


    // 2. Intercepta o envio do formulário (CREATE)

    form.addEventListener('submit', function (e) {

        e.preventDefault();



        let formData = new FormData();

        formData.append('texto', document.getElementById ('texto').value);

        
        fetch('../PHP/salvar.php', {

            method: 'POST',

            body: formData

        })

        .then(resposta => resposta.json()) // Converte a resposta do PHP para Objeto JS

        .then(dados =>{

            //Verifica o 'status' que o PHP enviou

            if(dados.status === 'sucesso') {

                document.getElementById('texto').value = ''; //Limpa só a mensagem (mantém o nome)

                carregarMensagens(); //Atualiza o mural

            } else{

                //Se deu erro, exibe a mensagem de erro vinda do Back-end

                alert(dados.mensagem);
            }

        })

        .catch(erro => console.error("Erro na comunicação com o servidor:", erro));

    });

    // 3. Atualiza a cada 2s
    setInterval(carregarMensagens, 2000);

    // Inicia a primeira carga
    carregarMensagens();

    // --- LÓGICA DO CHAT PRIVADO ---

    // 1. Busca a lista de usuários cadastrados
    function carregarUsuarios() {
        fetch('../PHP/buscar_usuarios.php')
            .then(res => res.json())
            .then(usuarios => {
                const lista = document.getElementById('lista-usuarios');
                lista.innerHTML = ''; // Limpa a lista
                usuarios.forEach(user => {
                    lista.innerHTML += `
                        <button class="list-group-item list-group-item-action bg-secondary text-white border-dark usuario-item" 
                                onclick="abrirChat(${user.id}, '${user.autor}')">
                            ${user.autor}
                        </button>`;
                });
            });
    }

    // 2. Abre a janela de chat e define quem vai receber
    function abrirChat(id, nome) {
        document.getElementById('janela-chat').style.display = 'block';
        document.getElementById('chat-com-nome').innerText = "Chat com " + nome;
        document.getElementById('destinatario_id').value = id; // Guarda o ID no campo escondido
        atualizarChatPrivado(); // Carrega as mensagens na hora
    }

    function fecharChat() {
        document.getElementById('janela-chat').style.display = 'none';
    }

    // 3. Busca as mensagens entre você e o contato
    function atualizarChatPrivado() {
        const idContato = document.getElementById('destinatario_id').value;
        if (!idContato) return;

        fetch(`../PHP/ler_privado.php?contato_id=${idContato}`)
            .then(res => res.json())
            .then(mensagens => {
                const corpo = document.getElementById('corpo-chat');
                corpo.innerHTML = '';
                mensagens.forEach(msg => {
                    const alinhamento = msg.remetente_id == idContato ? 'text-start' : 'text-end';
                    const cor = msg.remetente_id == idContato ? 'text-info' : 'text-warning';
                    corpo.innerHTML += `<p class="${alinhamento} mb-1"><small class="${cor}">${msg.hora}</small>: ${msg.mensagem}</p>`;
                });
                corpo.scrollTop = corpo.scrollHeight; // Rola para o fim
            });
    }

// 4. Envia a mensagem privada (Intercepta o form do chat)
document.getElementById('formChatPrivado').addEventListener('submit', function(e) {
    e.preventDefault();
    const dados = new FormData();
    dados.append('destinatario_id', document.getElementById('destinatario_id').value);
    dados.append('mensagem', document.getElementById('msg-privada').value);

    fetch('../PHP/enviar_mensagem.php', { method: 'POST', body: dados })
        .then(res => res.json())
        .then(retorno => {
            if(retorno.status === 'sucesso') {
                document.getElementById('msg-privada').value = '';
                atualizarChatPrivado();
            } else {
                alert(retorno.mensagem);
            }
        });
});

// 5. Inicia as atualizações automáticas
setInterval(carregarUsuarios, 5000); // Atualiza lista de quem está online a cada 5s
setInterval(atualizarChatPrivado, 2000); // Atualiza as mensagens do chat aberto a cada 2s

// Chama a carga inicial
carregarUsuarios();
