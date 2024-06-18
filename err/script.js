document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function(e) {
        e.preventDefault(); // Impede o envio tradicional do formulário

        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);

        xhr.open("POST", "/err/main.php", true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Faz uma nova solicitação para obter a resposta armazenada na sessão
                    const xhr2 = new XMLHttpRequest();
                    xhr2.open("GET", "/err/response.php", true);
                    xhr2.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        
                    xhr2.onreadystatechange = function() {
                        if (xhr2.readyState === XMLHttpRequest.DONE) {
                            if (xhr2.status === 200) {
                                try {
                                    const response = JSON.parse(xhr2.responseText);
                                    // Agora você pode manipular a resposta como quiser
                                    console.log(response.message);
                                    // Atualiza a URL da barra de endereços e redireciona
                                    window.history.pushState({}, '', response.redirect);
                                    window.location.href = response.redirect;
                                } catch (e) {
                                    console.error('Erro ao processar a resposta: ' + e.message);
                                }
                            } else {
                                console.error('Erro na requisição: ' + xhr2.status);
                            }
                        }
                    };
                    xhr2.send();
                } else {
                    console.error('Erro na requisição: ' + xhr.status);
                }
            }
        };
        xhr.send(formData);
    });
});