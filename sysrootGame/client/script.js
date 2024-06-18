document.addEventListener('DOMContentLoaded', (event) => {

    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu');
    const links = document.querySelectorAll('.menu-link');
    const sections = document.querySelectorAll('main section');
    const gameCards = document.querySelectorAll('.card.game');
    const userCards = document.querySelectorAll('.card.user');

    // Função para mostrar a seção ativa
    function showSection(sectionId) {
        console.log("Seção atual: " + sectionId);
        sections.forEach(section => {
            section.classList.remove('active-section');
            if (section.id === sectionId) {
                section.classList.add('active-section');
            }
        });
    }

    // Toggle do menu
    menuToggle.addEventListener('click', function () {
        menu.classList.toggle('active');
    });

    // Mostrar a seção correspondente ao clicar no link do menu
    links.forEach(link => {
        link.addEventListener('click', function (event) {
            console.log("Clique em um link do menu detectado");
            event.preventDefault();
            const sectionId = this.getAttribute('data-section');
            /*if (sectionId == 'configuracoes') {
                window.location.href = "/err/main.php?code=403";
            }*/
            showSection(sectionId);
            if (window.matchMedia("(max-width: 600px)").matches) {
                menu.classList.remove('active');
            }
        });
    });

    gameCards.forEach(link => {
        link.addEventListener('click', function (event) {
            console.log("Clique em game card detectado");
            event.preventDefault();
            // Extrair o ID do elemento li pai
            const gameId = this.closest('li').id;
            console.log("ID do game card:", gameId);
            // Enviar o ID ao servidor usando fetch
            fetch('home.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'game_id=' + encodeURIComponent(gameId)
            })
            .then(response => response.text())
            .then(data => {
                console.log('Response from server:', data);
                showSection('jogo'); // Chama a função showSection após receber a resposta
            })
            .catch(error => console.error('Error:', error));
        });
    });

    userCards.forEach(link => {
        link.addEventListener('click', function (event) {
            console.log("Clique em user card detectado");
            event.preventDefault();
            //window.location.href = "/err/main.php?code=404";
            showSection('personagem');
        });
    });

    // Mostrar a seção "Jogos" por padrão
    showSection('jogos');

    // Verificar o tamanho da tela
    if (window.matchMedia("(max-width: 600px)").matches) {
        // Se a tela for menor que 600px, desative o menu
        menu.classList.remove('active');
        console.log("A tela do dispositivo é pequena, adaptando o menu...")
    }

    document.getElementById('okButton').addEventListener('click', function(e) {
        e.preventDefault();
        var tooltipText = document.querySelector('.tooltiptext');
        tooltipText.style.display = 'none'; // Esconde o balão de sobreposição
    });

});
