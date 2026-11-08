
// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// VALIDAR NOME DA NACIONALIDADE
function validarNomeNacionalidade(valor) {
    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O nome da nacionalidade não foi preenchido.");
    }

    if (valor.trim().length < 3) {
        throw new ErroValidacao("O nome da nacionalidade deve conter pelo menos 3 caracteres.");
    }

    if (valor.trim().length > 50) {
        throw new ErroValidacao("O nome da nacionalidade não pode ultrapassar 50 caracteres.");
    }

    for (let caractere of valor.trim()) {
        if (!caractere.match(/[a-zA-ZÀ-ÿ\s'-]/)) {
            throw new ErroValidacao("O nome da nacionalidade não pode conter números ou caracteres especiais.");
        }
    }

    return valor.trim();
}

// VALIDAR CONTINENTE
function validarContinenteNacionalidade(valor) {
    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O continente não foi seleccionado.");
    }

    return valor.trim();
}

// VALIDAR PAÍS
function validarPaisNacionalidade(valor) {
    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O país não foi seleccionado.");
    }

    return valor.trim();
}

// PROCESSAR CADASTRO DE NACIONALIDADE
function processarCadastroNacionalidade() {
    let erros = [];

    try {
        let nome;
        let continente;
        let pais;

        // VALIDAR NOME
        try {
            nome = validarNomeNacionalidade(
                document.getElementById("nomeNacionalidade").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR CONTINENTE
        try {
            continente = validarContinenteNacionalidade(
                document.getElementById("continenteNacionalidade").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR PAÍS
        try {
            pais = validarPaisNacionalidade(
                document.getElementById("paisNacionalidade").value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VERIFICAR SE EXISTEM ERROS
        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // ACTUALIZAR VALOR VALIDADO
        document.getElementById("nomeNacionalidade").value = nome;

        document.getElementById("erros-nova-nacionalidade").style.display = "none";

        return true;

    } catch (erro) {
        const elementoErro = document.getElementById("erros-nova-nacionalidade");

        elementoErro.textContent = "Erros encontrados:\n\n" + erro.message;
        elementoErro.style.whiteSpace = "pre-line";
        elementoErro.style.display = "block";

        return false;
    }
}

// PROCESSAR EDIÇÃO DE NACIONALIDADE
function processarEdicaoNacionalidade(id) {
    let erros = [];

    try {
        let nome;
        let continente;
        let pais;

        // VALIDAR NOME
        try {
            nome = validarNomeNacionalidade(
                document.getElementById("nomeEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR CONTINENTE
        try {
            continente = validarContinenteNacionalidade(
                document.getElementById("continenteEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VALIDAR PAÍS
        try {
            pais = validarPaisNacionalidade(
                document.getElementById("paisEditar" + id).value
            );
        } catch (erro) {
            erros.push(erro.message);
        }

        // VERIFICAR SE EXISTEM ERROS
        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // ACTUALIZAR VALOR VALIDADO
        document.getElementById("nomeEditar" + id).value = nome;

        document.getElementById("erros-editar-nacionalidade" + id).style.display = "none";

        return true;

    } catch (erro) {
        const elementoErro = document.getElementById("erros-editar-nacionalidade" + id);

        elementoErro.textContent = "Erros encontrados:\n\n" + erro.message;
        elementoErro.style.whiteSpace = "pre-line";
        elementoErro.style.display = "block";

        return false;
    }
}

// CONTROLAR SUBMISSÃO DO CADASTRO
function validarEnvioCadastroNacionalidade(event) {
    // Permitir a filtragem sem executar a validação
    if (event.submitter && event.submitter.name === "filtrar") {
        return true;
    }

    return processarCadastroNacionalidade();
}

// CONTROLAR SUBMISSÃO DA EDIÇÃO
function validarEnvioEdicaoNacionalidade(event, id) {
    // Permitir a filtragem sem executar a validação
    if (event.submitter && event.submitter.name === "filtrarEditar") {
        return true;
    }

    return processarEdicaoNacionalidade(id);
}
