    const mural = document.getElementById("mural");

    const form = document.getElementById("formMensagem");

    const inputTexto = document.getElementById("texto");

    const nome = document.getElementById("autor");
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

        formData.append('autor',document.getElementById ('autor').value);

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

