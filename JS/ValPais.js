
// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// VALIDAR NOME DO PAÍS
function validarPais(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao(
            "O nome do país não foi preenchido."
        );
    }

    if (valor.trim().length < 3) {
        throw new ErroValidacao(
            "O nome do país deve conter pelo menos 3 caracteres."
        );
    }

    if (valor.trim().length > 50) {
        throw new ErroValidacao(
            "O nome do país não pode ultrapassar 50 caracteres."
        );
    }

    for (let caractere of valor.trim()) {

        if (!caractere.match(/[a-zA-ZÀ-ÿ\s'-]/)) {
            throw new ErroValidacao(
                "O nome do país não pode conter números ou caracteres especiais."
            );
        }
    }

    return valor.trim();
}

// VALIDAR CONTINENTE
function validarContinentePais(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao(
            "O continente não foi seleccionado."
        );
    }

    return valor.trim();
}

// PROCESSAR CADASTRO
function processarCadastroPais() {

    let erros = [];

    try {

        let nome;
        let continente;

        try {
            nome = validarPais(
                document.getElementById("nomePais").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        try {
            continente = validarContinentePais(
                document.getElementById("continentePais").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // SE NÃO EXISTIREM ERROS, PERMITE O ENVIO
        document.getElementById("formNovoPais").submit();

    } catch (erro) {

        document.getElementById("erros-novo-pais").innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-novo-pais").style.display = "block";
    }
}

// PROCESSAR EDIÇÃO
function processarEdicaoPais() {

    let erros = [];

    try {

        let nome;
        let continente;

        try {
            nome = validarPais(
                document.getElementById("nomeEditarPais").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        try {
            continente = validarContinentePais(
                document.getElementById("continenteEditarPais").value
            );

        } catch (erro) {
            erros.push(erro.message);
        }

        if (erros.length > 0) {
            throw new ErroValidacao(erros.join("\n"));
        }

        // SE NÃO EXISTIREM ERROS, PERMITE O ENVIO
        document.getElementById("formEditarPais").submit();

    } catch (erro) {

        document.getElementById("erros-editar-pais").innerHTML =
            "<strong>Erros encontrados:</strong><br>" +
            erro.message.replace(/\n/g, "<br>");

        document.getElementById("erros-editar-pais").style.display = "block";
    }
}
