// theme.js
document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const toggle = document.getElementById("darkModeBtn");

    // Terapkan mode gelap jika tersimpan
    if (localStorage.getItem("darkMode") === "true") {
        body.classList.add("dark-mode");
        if (toggle) toggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    // Jika tombol ada, pasang event klik
    if (toggle) {
        toggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            const isDark = body.classList.contains("dark-mode");
            localStorage.setItem("darkMode", isDark);
            toggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        });
    }
});
