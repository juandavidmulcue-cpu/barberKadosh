function validarRegistro(e) {
    const documento = document.querySelector('input[name="id_usuario"]');
    const telefono = document.querySelector('input[name="telefono"]');
    const password = document.querySelector('input[name="password"]');
    const mensaje = document.getElementById("mensaje");

    // Limpiar mensajes anteriores
    if (mensaje) {
        mensaje.textContent = "";
        mensaje.style.display = "none";
    }

    const doc = documento.value.trim();
    const tel = telefono.value.trim();
    const pass = password.value;
    const soloNumeros = /^\d+$/;

    // ----- VALIDACIONES DE DOCUMENTO -----
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

    // ----- VALIDACIONES DE TELÉFONO -----
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

    // ----- VALIDACIÓN DE CONTRASEÑA -----
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