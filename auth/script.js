document.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('okButton').addEventListener('click', function(e) {
        e.preventDefault();
        var tooltipText = document.querySelector('.tooltiptext');
        tooltipText.style.display = 'none'; // Esconde o balão de sobreposição
    });

    /*const text = "<?php echo $_SESSION['error']; ?>";
    const container = document.getElementById('typewriter-text');
    function typeWriter(text, index) {
        if (index < text.length) {
            container.innerHTML += text.charAt(index);
            setTimeout(() => typeWriter(text, index + 1), 100);
        }
    }
    typeWriter(text, 0);*/
});