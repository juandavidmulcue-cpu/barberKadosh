document.addEventListener("DOMContentLoaded", () => {

  const form = document.querySelector("form");
  const documento = document.querySelector('input[name="id_usuario"]');
  const password = document.querySelector('input[name="password"]');
  const mensaje = document.getElementById("mensaje");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // ⛔ BLOQUEO TOTAL (clave)

    mensaje.textContent = "";

    const doc = documento.value.trim();
    const pass = password.value;

    const docRegex = /^\d{1,11}$/;
    const passRegex = /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

    // ❌ Documento inválido
    if (!docRegex.test(doc)) {
      mensaje.textContent = "❌ El documento debe ser solo números (máx 11 dígitos).";
      documento.focus();
      return;
    }

    // ❌ Contraseña inválida
    if (!passRegex.test(pass)) {
      mensaje.textContent = "❌ La contraseña debe tener mínimo 8 caracteres y un símbolo.";
      password.focus();
      return;
    }

    // ✅ TODO BIEN → ENVIAR MANUALMENTE
    form.submit();
  });

});