// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// VALIDAR NOME DO CONTINENTE
function validarContinente(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O nome do continente não foi preenchido.");
    }

    if (valor.trim().length < 3) {
        throw new ErroValidacao(
            "O nome do continente deve conter pelo menos 3 caracteres."
        );
    }

    if (valor.trim().length > 50) {
        throw new ErroValidacao(
            "O nome do continente não pode ultrapassar 50 caracteres."
        );
    }

    for (let caractere of valor.trim()) {
        if (!caractere.match(/[a-zA-ZÀ-ÿ\s'-]/)) {
            throw new ErroValidacao(
                "O nome do continente não pode conter números ou caracteres especiais."
            );
        }
    }

    return valor.trim();
}

// PROCESSAR CADASTRO
function processarCadastro() {

    let erros = [];
    const campoNome = document.getElementById("nome");
    const caixaErros = document.getElementById("erros-novo");

    caixaErros.style.display = "none";
    caixaErros.innerHTML = "";

    try {
        try {
            validarContinente(campoNome.value);
        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        return true;

    } catch (erro) {

        caixaErros.innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        caixaErros.style.display = "block";

        return false;
    }
}

// PROCESSAR EDIÇÃO
function processarEdicao(id) {

    let erros = [];

    const campoNome = document.getElementById("nomeEditar" + id);
    const caixaErros = document.getElementById("erros-editar" + id);

    caixaErros.style.display = "none";
    caixaErros.innerHTML = "";

    try {
        try {
            validarContinente(campoNome.value);
        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        return true;

    } catch (erro) {

        caixaErros.innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        caixaErros.style.display = "block";

        return false;
    }
}