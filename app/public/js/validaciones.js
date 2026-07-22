function validarRegistro(e) {
    const nombre = document.querySelector('input[name="nombre"]');
    const apellido = document.querySelector('input[name="apellido"]');
    const correo = document.querySelector('input[name="correo"]');
    const documento = document.querySelector('input[name="id_usuario"]');
    const telefono = document.querySelector('input[name="telefono"]');
    const password = document.querySelector('input[name="password"]');
    const mensaje = document.getElementById("mensaje");

    // Limpiar mensajes anteriores
    if (mensaje) {
        mensaje.textContent = "";
        mensaje.style.display = "none";
    }

    const nom = nombre.value.trim();
    const ape = apellido.value.trim();
    const email = correo.value.trim();
    const doc = documento.value.trim();
    const tel = telefono.value.trim();
    const pass = password.value;

    const soloNumeros = /^\d+$/;
    const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    // ==========================
    // VALIDACIONES DE NOMBRE
    // ==========================
    if (!soloLetras.test(nom)) {
        if (e) e.preventDefault();
        mostrarError("❌ El nombre solo puede contener letras.", nombre, mensaje);
        return false;
    }

    // ==========================
    // VALIDACIONES DE APELLIDO
    // ==========================
    if (!soloLetras.test(ape)) {
        if (e) e.preventDefault();
        mostrarError("❌ El apellido solo puede contener letras.", apellido, mensaje);
        return false;
    }

    // ==========================
    // VALIDACIÓN DE CORREO
    // ==========================
    const correoRegex = /^[^\s@]+@[^\s@]+\.com$/i;

    if (!correoRegex.test(email)) {
        if (e) e.preventDefault();
        mostrarError("❌ El correo debe ser válido y terminar en '.com'.", correo, mensaje);
        return false;
    }

    // ==========================
    // VALIDACIONES DE DOCUMENTO
    // ==========================
    if (!soloNumeros.test(doc)) {
        if (e) e.preventDefault();
        mostrarError("❌ El documento debe contener únicamente números.", documento, mensaje);
        return false;
    }

    if (doc.length < 10 || doc.length > 11) {
        if (e) e.preventDefault();
        mostrarError("❌ El documento debe tener exactamente 10 o 11 números.", documento, mensaje);
        return false;
    }

    // ==========================
    // VALIDACIONES DE TELÉFONO
    // ==========================
    if (!soloNumeros.test(tel)) {
        if (e) e.preventDefault();
        mostrarError("❌ El teléfono debe contener únicamente números.", telefono, mensaje);
        return false;
    }

    if (tel.length !== 10) {
        if (e) e.preventDefault();
        mostrarError("❌ El número de teléfono debe tener exactamente 10 dígitos.", telefono, mensaje);
        return false;
    }

    // ==========================
    // VALIDACIÓN DE CONTRASEÑA
    // ==========================
    const passRegex = /^(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

    if (!passRegex.test(pass)) {
        if (e) e.preventDefault();
        mostrarError("❌ La contraseña debe tener mínimo 8 caracteres y un símbolo.", password, mensaje);
        return false;
    }

    return true;
}

function mostrarError(texto, elementoInput, contenedorMensaje) {
    if (contenedorMensaje) {
        contenedorMensaje.textContent = texto;
        contenedorMensaje.style.display = "block";
    } else {
        alert(texto);
    }

    elementoInput.focus();
}

// =========================================
// RESTRINGIR MIENTRAS EL USUARIO ESCRIBE
// =========================================

document.addEventListener("DOMContentLoaded", function () {

    const nombre = document.querySelector('input[name="nombre"]');
    const apellido = document.querySelector('input[name="apellido"]');
    const documento = document.querySelector('input[name="id_usuario"]');
    const telefono = document.querySelector('input[name="telefono"]');

    // Solo letras para nombre
    if (nombre) {
        nombre.addEventListener("input", function () {
            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        });
    }

    // Solo letras para apellido
    if (apellido) {
        apellido.addEventListener("input", function () {
            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, "");
        });
    }

    // Solo números para documento
    if (documento) {
        documento.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 11);
        });
    }

    // Solo números para teléfono
    if (telefono) {
        telefono.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 10);
        });
    }

});