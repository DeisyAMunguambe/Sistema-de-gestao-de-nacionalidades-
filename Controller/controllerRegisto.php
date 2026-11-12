
<?php
include_once(__DIR__ . "/../Model/Registo.php");
include_once(__DIR__ . "/../Controller/sessao.php");
verificarLogin();

$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listarRegistos() {
    exigirPermissaoAutomatica("ver");
    global $conexao;

    $registos = array();

    $sql = "select r.id_registo, r.id_continente, r.id_pais,
            r.id_nacionalidade, r.id_pessoa, r.dataRegisto, r.motivo,
            c.nomeContinente, p.nomePais, n.Nome as nomeNacionalidade,
            concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from registo_nacionalidade r
            inner join continente c on r.id_continente = c.codigoContinente
            inner join pais p on r.id_pais = p.codigoPais
            inner join nacionalidade n on r.id_nacionalidade = n.id_nacionalidade
            inner join pessoa pe on r.id_pessoa = pe.codigo
            order by r.id_registo";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $registo = new RegistoNacionalidade( $rs["id_registo"], $rs["id_continente"],$rs["id_pais"],
            $rs["id_nacionalidade"],$rs["id_pessoa"],$rs["dataRegisto"], $rs["motivo"], $rs["nomeContinente"],
            $rs["nomePais"],$rs["nomeNacionalidade"], $rs["nomePessoa"]
            );
            array_push($registos, $registo);
        }
    }
    return $registos;
}

function pesquisarRegistos($texto) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $registos = array();
    $texto = mysqli_real_escape_string($conexao, $texto);
    $sql = "select r.id_registo, r.id_continente, r.id_pais,
            r.id_nacionalidade, r.id_pessoa, r.dataRegisto, r.motivo,
            c.nomeContinente, p.nomePais, n.Nome as nomeNacionalidade,
            concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from registo_nacionalidade r
            inner join continente c on r.id_continente = c.codigoContinente
            inner join pais p on r.id_pais = p.codigoPais
            inner join nacionalidade n on r.id_nacionalidade = n.id_nacionalidade
            inner join pessoa pe on r.id_pessoa = pe.codigo
            where concat(pe.nome, ' ', pe.apelido) like '%$texto%'
            or n.Nome like '%$texto%' or p.nomePais like '%$texto%'
            or c.nomeContinente like '%$texto%' or r.motivo like '%$texto%'
            or r.dataRegisto like '%$texto%' order by r.id_registo";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $registo = new RegistoNacionalidade($rs["id_registo"], $rs["id_continente"],$rs["id_pais"],
            $rs["id_nacionalidade"],$rs["id_pessoa"],$rs["dataRegisto"], $rs["motivo"], $rs["nomeContinente"],
            $rs["nomePais"],$rs["nomeNacionalidade"], $rs["nomePessoa"]
            );
            array_push($registos, $registo);
        }
    }
    return $registos;
}

function encontrarRegisto($codigoRegisto) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
   
    $sql = "select r.id_registo, r.id_continente, r.id_pais,
            r.id_nacionalidade, r.id_pessoa, r.dataRegisto, r.motivo,
            c.nomeContinente, p.nomePais, n.Nome as nomeNacionalidade,
            concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from registo_nacionalidade r
            inner join continente c on r.id_continente = c.codigoContinente
            inner join pais p on r.id_pais = p.codigoPais
            inner join nacionalidade n on r.id_nacionalidade = n.id_nacionalidade
            inner join pessoa pe on r.id_pessoa = pe.codigo
            where r.id_registo = $codigoRegisto";

    $result = mysqli_query($conexao, $sql);
    $rs = mysqli_fetch_assoc($result);
    $registo = new RegistoNacionalidade($rs["id_registo"], $rs["id_continente"],$rs["id_pais"],
            $rs["id_nacionalidade"],$rs["id_pessoa"],$rs["dataRegisto"], $rs["motivo"], $rs["nomeContinente"],
            $rs["nomePais"],$rs["nomeNacionalidade"], $rs["nomePessoa"]
    );
    return $registo;
}


function validarRegisto($registo, $codigoRegisto) {

    global $conexao;

    $idContinente = (int)$registo->getIdContinente();
    $idPais = (int)$registo->getIdPais();
    $idNacionalidade = (int)$registo->getIdNacionalidade();
    $idPessoa = (int)$registo->getIdPessoa();

    $dataRegisto = $registo->getDataRegisto();
    $motivo = $registo->getMotivo();

    // 1. Validar os identificadores.
    if (
        $idContinente <= 0 ||
        $idPais <= 0 ||
        $idNacionalidade <= 0 ||
        $idPessoa <= 0
    ) {
        return "Seleccione correctamente todos os campos obrigatórios.";
    }

    // 2. Validar a data.
    if (
        !is_string($dataRegisto) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataRegisto)
    ) {
        return "Introduza uma data válida.";
    }

    $data = DateTime::createFromFormat('!Y-m-d', $dataRegisto);
    $errosData = DateTime::getLastErrors();

    if (
        !$data ||
        ($errosData !== false &&
        ($errosData['warning_count'] > 0 ||
         $errosData['error_count'] > 0)) ||
        $data->format('Y-m-d') !== $dataRegisto
    ) {
        return "A data introduzida não é válida.";
    }

    // 3. Aceitar exclusivamente a data actual de Maputo.
    $hoje = new DateTime(
        'today',
        new DateTimeZone('Africa/Maputo')
    );

    if ($dataRegisto !== $hoje->format('Y-m-d')) {
        return "A data de registo deve ser exclusivamente a data actual.";
    }

    // 4. Validar o motivo.
    $motivosPermitidos = [
        "Casamento",
        "Naturalidade",
        "Naturalização",
        "Filiação",
        "Residência",
        "Outro"
    ];

    if (!in_array($motivo, $motivosPermitidos, true)) {
        return "Seleccione um motivo válido.";
    }

    // 5. Confirmar a relação continente, país e nacionalidade.
    $sql = "SELECT p.codigoPais
            FROM continente c
            INNER JOIN pais p
                ON p.idcontinente = c.codigoContinente
            INNER JOIN nacionalidade n
                ON n.id_pais = p.codigoPais
            WHERE c.codigoContinente = ?
            AND p.codigoPais = ?
            AND n.id_nacionalidade = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        return "Erro ao validar os dados.";
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $idContinente,
        $idPais,
        $idNacionalidade
    );

    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $valido = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    if (!$valido) {
        return "O país ou a nacionalidade não correspondem ao continente seleccionado.";
    }

    // 6. Confirmar se a pessoa existe.
    $sql = "SELECT codigo FROM pessoa WHERE codigo = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        return "Erro ao verificar a pessoa.";
    }

    mysqli_stmt_bind_param($stmt, "i", $idPessoa);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $existePessoa = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    if (!$existePessoa) {
        return "A pessoa seleccionada não existe.";
    }

    // 7. Impedir duplicação de nacionalidade.
    $codigoRegisto = (int)$codigoRegisto;

    $sql = "SELECT id_registo
            FROM registo_nacionalidade
            WHERE id_pessoa = ?
            AND id_nacionalidade = ?
            AND id_registo <> ?
            LIMIT 1";

    $stmt = mysqli_prepare($conexao, $sql);

    if (!$stmt) {
        return "Erro ao verificar registos duplicados.";
    }

    mysqli_stmt_bind_param(
        $stmt,
        "iii",
        $idPessoa,
        $idNacionalidade,
        $codigoRegisto
    );

    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $duplicado = mysqli_num_rows($resultado) > 0;

    mysqli_stmt_close($stmt);

    if ($duplicado) {
        return "Esta pessoa já tem esta nacionalidade registada.";
    }

    return "";
}



function criarRegisto($registo) {
    exigirPermissaoAutomatica("criar");
    global $conexao;
    $erro = validarRegisto($registo, 0);
    if ($erro != "") {
        return $erro;
    }
    $data = mysqli_real_escape_string($conexao, $registo->getDataRegisto());
    $motivo = mysqli_real_escape_string($conexao, $registo->getMotivo());
    $sql = "insert into registo_nacionalidade (id_continente, id_pais, id_nacionalidade,
            id_pessoa, dataRegisto, motivo)
            values ( " . $registo->getIdContinente() . ", " . $registo->getIdPais() . ",
             " .  $registo->getIdNacionalidade() . ", " .  $registo->getIdPessoa() . ",
             '$data', '$motivo')";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Registou a nacionalidade de uma pessoa");
        return true;
    }
    return "Ocorreu um erro ao gravar os dados";
}

function editarRegisto($registo) {
    exigirPermissaoAutomatica("editar");
    global $conexao;
    $erro = validarRegisto($registo, $registo->getCodigoRegisto());
    if ($erro != "") {
        return $erro;
    }

    $data = mysqli_real_escape_string($conexao, $registo->getDataRegisto());
    $motivo = mysqli_real_escape_string($conexao, $registo->getMotivo());

    $sql = "update registo_nacionalidade set
            id_continente = " . $registo->getIdContinente() . ",
            id_pais = " .  $registo->getIdPais() . ",
            id_nacionalidade = " . $registo->getIdNacionalidade() . ",
            id_pessoa = " .  $registo->getIdPessoa() . ",
            dataRegisto = '$data',
            motivo = '$motivo'
            where id_registo = " .  $registo->getCodigoRegisto();

    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Editou o registo de nacionalidade de uma pessoa");
        return true;
    }
    return "Ocorreu um erro ao actualizar os dados";
}

function removerRegisto($codigoRegisto) {
    exigirPermissaoAutomatica("remover");
    global $conexao;
    $codigoRegisto = (int) $codigoRegisto;
    $result = mysqli_query($conexao,"delete from registo_nacionalidade where id_registo = $codigoRegisto"
    );
    if ($result && mysqli_affected_rows($conexao) > 0) {
        registarAccao("Removeu o registo de nacionalidade de uma pessoa");
        return true;
    }
    return false;
}

// Listas para os dropdowns

function registoListarPessoas() {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $sql = "select codigo, nome, apelido from pessoa order by nome, apelido";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function registoListarContinentes() {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $sql = "select codigoContinente, nomeContinente from continente order by nomeContinente";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function registoListarPaises() {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $sql = "select codigoPais, nomePais, idcontinente from pais order by nomePais";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function registoListarNacionalidades() {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $sql = "select id_nacionalidade, Nome, id_pais
            from nacionalidade
            order by Nome";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}
function listarMotivos() {
    return array("Casamento", "Naturalidade", "Naturalização","Filiação","Residência","Outro"
    );
}

// Funções auxiliares para os filtros

function registoPaisDoContinente($paises, $idPais, $idContinente) {
    if ($idPais == "" || $idContinente == "") {
        return false;
    }
    foreach ($paises as $pais) {
        if ( $pais['codigoPais'] == $idPais &&
            $pais['idcontinente'] == $idContinente
        ) {
            return true;
        }
    }
    return false;
}

function registoNacionalidadeDoPais($nacionalidades, $idNacionalidade, $idPais) {
    if ($idNacionalidade == "" || $idPais == "") {
        return false;
    }
    foreach ($nacionalidades as $nac) {
        if (
            $nac['id_nacionalidade'] == $idNacionalidade &&
            $nac['id_pais'] == $idPais
        ) {
            return true;
        }
    }
    return false;
}
?>
