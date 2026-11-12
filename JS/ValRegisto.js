// CLASSE PARA TRATAR ERROS DE VALIDAÇÃO
class ErroValidacao extends Error {
    constructor(mensagem) {
        super(mensagem);
        this.name = "ErroValidacao";
    }
}

// MOTIVOS PERMITIDOS NO SISTEMA
const MOTIVOS_PERMITIDOS = [
    "Casamento",
    "Naturalidade",
    "Naturalização",
    "Filiação",
    "Residência",
    "Outro"
];

// NOMES DOS CAMPOS NOS FORMULÁRIOS (criar, editar)
// Se na sua view os "name" forem outros, altere só aqui.
const CAMPOS = {
    continente:    ["idcontinente", "idcontinenteEditar"],
    pais:          ["idpais", "idpaisEditar"],
    nacionalidade: ["idnacionalidade", "idnacionalidadeEditar"],
    pessoa:        ["idpessoa", "idpessoaEditar"],
    data:          ["dataRegisto", "dataRegistoEditar"],
    motivo:        ["motivo", "motivoEditar"]
};

// BOTÕES QUE GRAVAM (só os formulários com estes botões são validados)
const BOTOES_GRAVAR = ["gravar", "actualizar"];

// BOTÕES QUE APENAS FILTRAM (não validam)
const BOTOES_FILTRAR = ["filtrar", "filtrarEditar"];

// DATA ACTUAL (YYYY-MM-DD), pela data local do computador
function obterDataActual() {
    const hoje = new Date();
    const ano = hoje.getFullYear();
    const mes = String(hoje.getMonth() + 1).padStart(2, "0");
    const dia = String(hoje.getDate()).padStart(2, "0");

    return `${ano}-${mes}-${dia}`;
}

// PROCURAR UM CAMPO NO FORMULÁRIO (devolve null se não existir ou estiver desactivado)
function obterCampo(formulario, chave) {
    for (const nome of CAMPOS[chave]) {
        const campo = formulario.elements[nome];

        if (campo && !campo.disabled) {
            return campo;
        }
    }

    return null;
}

// VALIDAR SELECÇÃO (continente, país, nacionalidade, pessoa)
function validarSelecao(campo, mensagem) {

    if (
        campo.value.trim() === "" ||
        campo.selectedIndex < 0 ||
        campo.selectedOptions.length === 0 ||
        campo.selectedOptions[0].disabled
    ) {
        throw new ErroValidacao(mensagem);
    }

    return campo.value;
}

// VALIDAR DATA DE REGISTO
function validarDataRegisto(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("A data de registo é obrigatória.");
    }

    // formato AAAA-MM-DD
    if (!/^\d{4}-\d{2}-\d{2}$/.test(valor)) {
        throw new ErroValidacao("Introduza uma data válida.");
    }

    // a data tem de existir realmente (por exemplo, 30 de Fevereiro não existe)
    const partes = valor.split("-").map(Number);
    const data = new Date(partes[0], partes[1] - 1, partes[2]);

    if (
        data.getFullYear() !== partes[0] ||
        data.getMonth() !== partes[1] - 1 ||
        data.getDate() !== partes[2]
    ) {
        throw new ErroValidacao("A data introduzida não é válida.");
    }

    // só se permite a data actual
    const dataHoje = obterDataActual();

    if (valor !== dataHoje) {
        throw new ErroValidacao(
            "A data de registo deve corresponder ao dia actual: " +
            dataHoje.split("-").reverse().join("/")
        );
    }

    return valor;
}

// VALIDAR MOTIVO
function validarMotivo(valor) {

    if (!valor || valor.trim() === "") {
        throw new ErroValidacao("O motivo não foi seleccionado.");
    }

    if (!MOTIVOS_PERMITIDOS.includes(valor)) {
        throw new ErroValidacao("O motivo seleccionado é inválido.");
    }

    return valor;
}

// VALIDAR SE O PAÍS PERTENCE AO CONTINENTE
function validarPaisNoContinente(continente, pais) {

    const opcaoPais = pais.selectedOptions[0];

    if (!opcaoPais || opcaoPais.dataset.continente !== continente.value) {
        throw new ErroValidacao("O país não pertence ao continente seleccionado.");
    }
}

// VALIDAR SE A NACIONALIDADE PERTENCE AO PAÍS
function validarNacionalidadeNoPais(nacionalidade, pais) {

    const opcaoNacionalidade = nacionalidade.selectedOptions[0];

    if (!opcaoNacionalidade || opcaoNacionalidade.dataset.pais !== pais.value) {
        throw new ErroValidacao("A nacionalidade não pertence ao país seleccionado.");
    }
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

// CAIXA ONDE SE MOSTRAM OS ERROS (se não existir na view, é criada)
function obterCaixaErros(formulario) {
    let caixa = formulario.querySelector(".erros-validacao");

    if (!caixa) {
        caixa = document.createElement("div");
        caixa.className = "erro erros-validacao";
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

// PROCESSAR REGISTO E EDIÇÃO (o mesmo formulário serve para os dois)
function processarRegisto(formulario) {

    let erros = [];

    try {

        const continente    = obterCampo(formulario, "continente");
        const pais          = obterCampo(formulario, "pais");
        const nacionalidade = obterCampo(formulario, "nacionalidade");
        const pessoa        = obterCampo(formulario, "pessoa");
        const data          = obterCampo(formulario, "data");
        const motivo        = obterCampo(formulario, "motivo");

        // VALIDAR CONTINENTE
        if (continente) {
            tentar(erros, function () {
                validarSelecao(continente, "Seleccione um continente.");
            });
        }

        // VALIDAR PAÍS
        if (pais) {
            tentar(erros, function () {
                validarSelecao(pais, "Seleccione um país.");
            });
        }

        // VALIDAR NACIONALIDADE
        if (nacionalidade) {
            tentar(erros, function () {
                validarSelecao(nacionalidade, "Seleccione uma nacionalidade.");
            });
        }

        // VALIDAR PESSOA
        if (pessoa) {
            tentar(erros, function () {
                validarSelecao(pessoa, "Seleccione uma pessoa.");
            });
        }

        // VALIDAR DATA DE REGISTO
        if (data) {
            tentar(erros, function () {
                validarDataRegisto(data.value);
            });
        }

        // VALIDAR MOTIVO
        if (motivo) {
            tentar(erros, function () {
                validarMotivo(motivo.value);
            });
        }

        // CONFIRMAR SE O PAÍS PERTENCE AO CONTINENTE
        if (continente && pais && continente.value !== "" && pais.value !== "") {
            tentar(erros, function () {
                validarPaisNoContinente(continente, pais);
            });
        }

        // CONFIRMAR SE A NACIONALIDADE PERTENCE AO PAÍS
        if (nacionalidade && pais && nacionalidade.value !== "" && pais.value !== "") {
            tentar(erros, function () {
                validarNacionalidadeNoPais(nacionalidade, pais);
            });
        }

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

// LIGAR A VALIDAÇÃO AOS FORMULÁRIOS
document.addEventListener("DOMContentLoaded", function () {

    // o campo da data só aceita o dia actual
    const dataHoje = obterDataActual();

    CAMPOS.data.forEach(function (nome) {
        document.querySelectorAll('[name="' + nome + '"]').forEach(function (campo) {
            campo.setAttribute("min", dataHoje);
            campo.setAttribute("max", dataHoje);
        });
    });

    // validação no envio dos formulários
    document.querySelectorAll("form[method='post']").forEach(function (formulario) {

        formulario.addEventListener("submit", function (evento) {

            const botao = evento.submitter;

            // permite filtrar sem executar a validação de gravação
            if (botao && BOTOES_FILTRAR.includes(botao.name)) {
                return;
            }

            // só valida formulários de criação e edição
            const temBotaoGravar = BOTOES_GRAVAR.some(function (nome) {
                return formulario.elements[nome];
            });

            if (!temBotaoGravar) {
                return;
            }

            if (!processarRegisto(formulario)) {
                evento.preventDefault();
            }
        });
    });
});