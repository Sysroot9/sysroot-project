document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('forgotPassCode').addEventListener('click', function(e) {
        e.preventDefault();
        var form = document.getElementById('forgotPassword');
        var actionInput = document.getElementById('actionInput');
        actionInput.value = 'esqueci'; // Define o valor do input oculto
        form.submit(); // Envia o formulário
    });

    const errorText = "<?php echo $_SESSION['error']; ?>";
    const successText = "<?php echo $_SESSION['success']; ?>";
    const errorContainer = document.getElementById('typewriter-error');
    const successContainer = document.getElementById('typewriter-success');
    function typeWriter(text, container, index) {
        if (index < text.length) {
            container.innerHTML += text.charAt(index);
            setTimeout(() => typeWriter(text, container, index + 1), 100);
        }
    }
    typeWriter(errorText, errorContainer, 0);
    typeWriter(successText, successContainer, 0);
});