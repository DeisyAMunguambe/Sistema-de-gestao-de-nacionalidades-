// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// REGRAS DA SENHA (altere aqui se mudar a política)
const SENHA_MINIMO = 8;            // mínimo de caracteres
const SENHA_MAXIMO_BYTES = 72;     // limite do password_hash do PHP (bcrypt)

// VALIDAR USERNAME OU EMAIL
function validarIdentificador(valor) {

    const texto = valor.trim();

    if (texto === "") {
        throw new ErroValidacao("O username ou email não foi preenchido.");
    }

    if (texto.includes("@") && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(texto)) {
        throw new ErroValidacao("O email introduzido não é válido.");
    }

    return texto;
}

// VALIDAR SENHA ACTUAL
function validarSenhaActual(valor) {

    if (valor === "") {
        throw new ErroValidacao("A senha actual não foi preenchida.");
    }

    return valor;
}

// VALIDAR NOVA SENHA
function validarNovaSenha(valor, identificador, senhaActual) {

    if (valor === "") {
        throw new ErroValidacao("A nova senha não foi preenchida.");
    }

    const mensagens = [];
    const falta = [];

    if (valor.length < SENHA_MINIMO) {
        falta.push("pelo menos " + SENHA_MINIMO + " caracteres");
    }

    if (!/\p{Lu}/u.test(valor)) {
        falta.push("uma letra maiúscula");
    }

    if (!/\p{Ll}/u.test(valor)) {
        falta.push("uma letra minúscula");
    }

    if (!/[0-9]/.test(valor)) {
        falta.push("um número");
    }

    if (!/[^\p{L}0-9\s]/u.test(valor)) {
        falta.push("um símbolo (por exemplo # ou !)");
    }

    if (falta.length > 0) {
        mensagens.push("A nova senha precisa de ter " + falta.join(", ") + ".");
    }

    if (/\s/.test(valor)) {
        mensagens.push("A nova senha não pode conter espaços.");
    }

    if (new TextEncoder().encode(valor).length > SENHA_MAXIMO_BYTES) {
        mensagens.push(
            "A nova senha é demasiado longa (máximo " + SENHA_MAXIMO_BYTES + " caracteres)."
        );
    }

    // não pode conter o username (ou a parte do email antes do @)
    const base = identificador.split("@")[0].toLowerCase();

    if (base.length >= 3 && valor.toLowerCase().includes(base)) {
        mensagens.push("A nova senha não pode conter o seu username.");
    }

    if (senhaActual !== "" && valor === senhaActual) {
        mensagens.push("A nova senha deve ser diferente da senha actual.");
    }

    if (mensagens.length > 0) {
        throw new ErroValidacao(mensagens.join("\n"));
    }

    return valor;
}

// VALIDAR CONFIRMAÇÃO DA NOVA SENHA
function validarConfirmacao(novaSenha, confirmacao) {

    if (confirmacao === "") {
        throw new ErroValidacao("A confirmação da nova senha não foi preenchida.");
    }

    if (novaSenha !== confirmacao) {
        throw new ErroValidacao("As novas senhas não coincidem.");
    }

    return confirmacao;
}

// EXECUTAR UMA VALIDAÇÃO E GUARDAR O ERRO, SE HOUVER
function tentar(erros, validacao) {
    try {
        validacao();
    } catch (erro) {
        if (erro instanceof ErroValidacao) {
            erros.push(erro.message);
        } else {
            throw erro;   // erro de programação: não esconder
        }
    }
}

// CAIXA ONDE SE MOSTRAM OS ERROS (criada no topo do formulário)
function obterCaixaErros(formulario) {
    let caixa = formulario.querySelector(".erros-validacao");

    if (!caixa) {
        caixa = document.createElement("div");
        caixa.className = "erro-senha erros-validacao";
        caixa.style.display = "none";
        formulario.insertBefore(caixa, formulario.firstChild);
    }

    return caixa;
}

// MOSTRAR OS ERROS NA CAIXA
function mostrarErros(formulario, mensagens) {
    const caixa = obterCaixaErros(formulario);

    caixa.innerHTML = "";

    const titulo = document.createElement("strong");
    titulo.textContent = "Erros encontrados:";
    caixa.appendChild(titulo);
    caixa.appendChild(document.createElement("br"));

    mensagens.forEach(function (mensagem) {
        caixa.appendChild(document.createElement("br"));
        caixa.appendChild(document.createTextNode(mensagem));
    });

    caixa.style.display = "block";
}

// ESCONDER A CAIXA DE ERROS
function esconderErros(formulario) {
    const caixa = formulario.querySelector(".erros-validacao");

    if (caixa) {
        caixa.style.display = "none";
    }
}

// PROCESSAR A ALTERAÇÃO DE SENHA
function processarAlteracaoSenha(formulario) {

    let erros = [];

    try {

        const identificador = formulario.elements["username"].value;
        const senhaActual   = formulario.elements["senhaAntiga"].value;
        const novaSenha     = formulario.elements["novaSenha"].value;
        const confirmacao   = formulario.elements["confirmarSenha"].value;

        // VALIDAR USERNAME OU EMAIL
        tentar(erros, function () {
            validarIdentificador(identificador);
        });

        // VALIDAR SENHA ACTUAL
        tentar(erros, function () {
            validarSenhaActual(senhaActual);
        });

        // VALIDAR NOVA SENHA
        tentar(erros, function () {
            validarNovaSenha(novaSenha, identificador.trim(), senhaActual);
        });

        // VALIDAR CONFIRMAÇÃO
        tentar(erros, function () {
            validarConfirmacao(novaSenha, confirmacao);
        });

        // VERIFICAR SE EXISTEM ERROS
        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        esconderErros(formulario);

        return true;

    } catch (erro) {

        if (!(erro instanceof ErroValidacao)) {
            throw erro;
        }

        mostrarErros(formulario, erro.message.split("\n"));

        return false;
    }
}

// LIGAR A VALIDAÇÃO AO FORMULÁRIO
document.addEventListener("DOMContentLoaded", function () {

    const botao = document.querySelector('[name="alterarSenha"]');

    if (!botao || !botao.form) {
        return;
    }

    const formulario = botao.form;

    // desliga as mensagens do navegador (do "required"), para mostrar só as nossas
    formulario.noValidate = true;

    formulario.addEventListener("submit", function (evento) {
        if (!processarAlteracaoSenha(formulario)) {
            evento.preventDefault();
        }
    });
});