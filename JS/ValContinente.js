
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

    try {

        let nome;

        try {
            nome = validarContinente(
                document.getElementById("nome").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // Se não existirem erros, permite o envio
        document.getElementById("formNovo").submit();

    } catch (erro) {

        document.getElementById("erros-novo").innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-novo").style.display = "block";
    }
}

// PROCESSAR EDIÇÃO
function processarEdicao() {

    let erros = [];

    try {

        let nome;

        try {
            nome = validarContinente(
                document.getElementById("nomeEditar").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        document.getElementById("formEditar").submit();

    } catch (erro) {

        document.getElementById("erros-editar").innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-editar").style.display = "block";
    }
}
