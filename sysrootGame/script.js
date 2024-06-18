document.addEventListener('DOMContentLoaded', function() {
    const continueButton = document.getElementById('continue-button');
    const redirectUrl = './auth/login.php'; // Altere para a URL de redirecionamento desejada

    // Verifica se o usuário já escolheu continuar
    if (localStorage.getItem('changelogSeen')) {
        window.location.href = redirectUrl;
    }

    continueButton.addEventListener('click', function() {
        // Armazena a escolha do usuário
        localStorage.setItem('changelogSeen', 'true');
        // Redireciona para a página desejada
        window.location.href = redirectUrl;
    });
});
