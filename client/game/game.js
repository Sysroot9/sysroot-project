document.addEventListener("DOMContentLoaded", function() {

    let disconnectReason = null;

    showSection('waitingGameStart');  // Sessão padrão

    /*
    const filePath = './server.json';
    // Lê o arquivo JSON de forma assíncrona
    fs.readFile(filePath, 'utf8', (err, data) => {
        if (err) {
        console.error('Erro ao ler o arquivo JSON:', err);
        return;
        }
        
        try {
        // Faz o parsing dos dados JSON
        const config = JSON.parse(data);
        
        // Exibe os dados lidos do arquivo JSON
        console.log('Endereço do servidor:', config.address);
        console.log('Porta do servidor:', config.port);
        
        // Aqui você pode usar config.address e config.port como necessário
        // Exemplo: conectar-se ao servidor WebSocket
        } catch (err) {
        console.error('Erro ao fazer parsing do JSON:', err);
        }
    });*/
    
    function startGame() {
        console.log('O jogo está ativo!');
        // Código para iniciar o jogo
    }

    function pauseGame() {
        console.log('O jogo está pausado!');
        // Código para pausar o jogo
    }

    function showSection(sectionId) {
        document.querySelectorAll('div').forEach(section => {
            section.classList.add('hidden');
        });
        document.getElementById(sectionId).classList.remove('hidden');
    }

    const socket = new WebSocket('wss://6.tcp.ngrok.io:12573');

    socket.onopen = function(event) {
        console.log('Conectado ao servidor WebSocket');
        let data = {
            'type': 'connection',
            'playerId': playerId,
            'gameId': gameId,
            'is_online': true
        };
        socket.send(JSON.stringify(data));
    };

    socket.onmessage = function(event) {
        let data = JSON.parse(event.data);
        console.log('Dados recebidos do servidor: ', data);
    
        if (data.message === "game.start") {
            console.log("O jogo está iniciando...");
            showSection('gameStart');
    
        } else if (data["type"] === "disconnected") {
            if (data["err"] === "player.id.use") {
                console.log("A conexão foi recusada, este usuário já está conectado em outra sessão");
                disconnectReason = 'alreadyInUse';
                socket.close();
            }
        }
    };

    socket.onclose = function(event) {
        console.log('Desconectado do servidor WebSocket');
        if (disconnectReason === 'alreadyInUse') {
            showSection('alreadyInUse');
        } else {
            showSection('disconnected');
        }
    };

    socket.addEventListener('error', function (event) {
        console.log('Erro na conexão WebSocket:', event);
    });

    // TODO O CÓDIGO LEGADO FICARÁ DAQUI PARA BAIXO.

     /* Verifica o estado do jogo a cada 3 segundos (LEGADO)
    setInterval(() => {
        fetch('check_game_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.state === 'active') {
                    startGame();
                } else if (data.state === 'paused') {
                    pauseGame();
                } else if (data.state === 'waiting') {
                    waitingGame();
                }
            })
            .catch(error => console.error('Erro ao verificar o estado do jogo:', error));
    }, 3000);

    // Detecta desconexão do jogador
    window.addEventListener('beforeunload', () => {
        navigator.sendBeacon('update_player_status.php', JSON.stringify({
            'playerId': playerId,
            'gameId': gameId,
            'isOnline': false
        }));
    });*/

});
