// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// VALIDAR NOME E APELIDO
function validarTexto(valor, campo) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao(
            "O campo " + campo + " não foi preenchido."
        );
    }

    if (valor.trim().length < 3) {
        throw new ErroValidacao(
            campo + " inválido: precisa de conter pelo menos 3 caracteres."
        );
    }

    if (valor.trim().length > 50) {
        throw new ErroValidacao(
            campo + " não pode ultrapassar 50 caracteres."
        );
    }

    for (let caractere of valor.trim()) {

        if (!caractere.match(/[a-zA-ZÀ-ÿ\s'-]/)) {
            throw new ErroValidacao(
                "O campo " + campo + " não pode conter números ou caracteres especiais."
            );
        }
    }

    return valor.trim();
}

// VALIDAR GÉNERO
function validarGenero(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O género não foi selecionado.");
    }

    if (!["Masculino", "Feminino", "Outro"].includes(valor)) {
        throw new ErroValidacao("O género selecionado é inválido.");
    }

    return valor;
}

// VALIDAR ESTADO CIVIL
function validarEstadoCivil(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O estado civil não foi selecionado.");
    }

    if (!["Solteiro", "Casado", "Divorciado", "Viúvo"].includes(valor)) {
        throw new ErroValidacao("O estado civil selecionado é inválido.");
    }

    return valor;
}

// VALIDAR ANO DE NASCIMENTO
function validarAnoNascimento(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao(
            "O ano de nascimento não foi preenchido."
        );
    }

    if (!/^\d{4}$/.test(valor.trim())) {
        throw new ErroValidacao(
            "O ano de nascimento deve conter exatamente 4 números."
        );
    }

    let ano = Number(valor);

    if (ano < 1900 || ano > new Date().getFullYear()) {
        throw new ErroValidacao(
            "O ano de nascimento deve estar entre 1900 e o ano atual."
        );
    }

    return ano;
}

// PROCESSAR CADASTRO DE PESSOA
function processarCadastro() {

    let erros = [];

    try {

        // VALIDAR NOME
        let nome;

        try {
            nome = validarTexto(
                document.getElementById("nome").value,
                "Nome"
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR APELIDO
        let apelido;

        try {
            apelido = validarTexto(
                document.getElementById("apelido").value,
                "Apelido"
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR GÉNERO
        let genero;

        try {
            genero = validarGenero(
                document.getElementById("genero").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR ESTADO CIVIL
        let estadoCivil;

        try {
            estadoCivil = validarEstadoCivil(
                document.getElementById("estadoCivil").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR ANO DE NASCIMENTO
        let anoNascimento;

        try {
            anoNascimento = validarAnoNascimento(
                document.getElementById("anoNascimento").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VERIFICAR SE EXISTEM ERROS
        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // ATUALIZAR VALORES VALIDADOS
        document.getElementById("nome").value = nome;
        document.getElementById("apelido").value = apelido;
        document.getElementById("anoNascimento").value = anoNascimento;

        return true;

    } catch (erro) {

        document.getElementById("erros-novo").innerHTML =
            "<strong>Erros encontrados:</strong><br><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-novo").style.display = "block";

        return false;
    }
}

// PROCESSAR EDIÇÃO DE PESSOA
function processarEdicao(id) {

    let erros = [];

    try {

        // VALIDAR NOME
        let nome;

        try {
            nome = validarTexto(
                document.getElementById("nomeEditar" + id).value,
                "Nome"
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR APELIDO
        let apelido;

        try {
            apelido = validarTexto(
                document.getElementById("apelidoEditar" + id).value,
                "Apelido"
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR GÉNERO
        let genero;

        try {
            genero = validarGenero(
                document.getElementById("generoEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR ESTADO CIVIL
        let estadoCivil;

        try {
            estadoCivil = validarEstadoCivil(
                document.getElementById("estadoCivilEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR ANO DE NASCIMENTO
        let anoNascimento;

        try {
            anoNascimento = validarAnoNascimento(
                document.getElementById("anoNascimentoEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VERIFICAR SE EXISTEM ERROS
        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // ATUALIZAR VALORES VALIDADOS
        document.getElementById("nomeEditar" + id).value = nome;
        document.getElementById("apelidoEditar" + id).value = apelido;
        document.getElementById("anoNascimentoEditar" + id).value = anoNascimento;

        return true;

    } catch (erro) {

        document.getElementById("erros-editar" + id).innerHTML =
            "<strong>Erros encontrados:</strong><br><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-editar" + id).style.display = "block";

        return false;
    }
}