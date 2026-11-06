<?php
include_once(__DIR__ . "/../Model/Nacionalidade.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listar(){
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $nacionalidades = array();
    $sql = "select n.id_nacionalidade, n.nome,
            p.nomePais, c.nomeContinente
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {

            $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["nomePais"], $rs["nomeContinente"]);
            array_push($nacionalidades, $nacionalidade);
        }
    }
    return $nacionalidades;
}


function criarNacionalidade($nacionalidade){
    exigirPermissaoAutomatica("criar");
    global $conexao;

    $nome = mysqli_real_escape_string($conexao, trim($nacionalidade->getNomeNacionalidade()));
    $idPais =  $nacionalidade->getNomePais();
    try {
        $sql = "select id_nacionalidade from nacionalidade
                where nome='$nome' and id_pais=$idPais";
        $result = mysqli_query($conexao, $sql);
        if (mysqli_num_rows($result) > 0) {
            return "A nacionalidade já existe para este país";
        }
        $sql = "insert into nacionalidade (nome, id_pais)
                values ('$nome', $idPais)";
        mysqli_query($conexao, $sql);
        registarAccao("Registou uma nacionalidade");
        return true;
    } catch (mysqli_sql_exception $e) {
        
        return "Erro na base de dados: " . $e->getMessage();
    }
}

function removerNacionalidade($codigoNacionalidade) {
    exigirPermissaoAutomatica("remover");
    global $conexao;
    $sql = "delete from nacionalidade where id_nacionalidade={$codigoNacionalidade}";
    $result = mysqli_query($conexao, $sql);
    
    if ($result && mysqli_affected_rows($conexao) > 0) {
        registarAccao("Removeu uma nacionalidade");
    }
}

function editarNacionalidade($nacionalidade) {
    exigirPermissaoAutomatica("editar");
    global $conexao;
    $sql = "select * from pais p
            inner join continente c on p.idcontinente = c.codigoContinente
            where p.codigoPais={$nacionalidade->getNomePais()}
            and c.nomeContinente='{$nacionalidade->getNomeContinente()}'";
    $result = mysqli_query($conexao, $sql);
    if (mysqli_num_rows($result) == 0) {
        return "O pais nao pertence ao continente escolhido";
    }

    $sql = "UPDATE nacionalidade SET nome = '{$nacionalidade->getNomeNacionalidade()}',
            id_pais = {$nacionalidade->getNomePais()}
            where id_nacionalidade = '{$nacionalidade->getCodigoNacionalidade()}'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Editou uma nacionalidade");
    } else
        echo "ocorreu um erro ao actualizar os dados";
}

function encontrar($codigoNacionalidade) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $sql = "select n.id_nacionalidade, n.nome,
            p.nomePais, c.nomeContinente
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente
            where n.id_nacionalidade=" . $codigoNacionalidade;
    $result = mysqli_query($conexao, $sql);

    $rs = mysqli_fetch_assoc($result);
    $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["nomePais"], $rs["nomeContinente"]);
    return $nacionalidade;
}

function pesquisar($nome) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $nacionalidades = array();
    $nome = mysqli_real_escape_string($conexao, $nome);
    $sql = "select n.id_nacionalidade, n.nome,
            p.nomePais, c.nomeContinente
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente
            where n.nome like '%$nome%'
            or p.nomePais like '%$nome%'
            or c.nomeContinente like '%$nome%'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["nomePais"], $rs["nomeContinente"]);
            array_push($nacionalidades, $nacionalidade);
        }
    }
    return $nacionalidades;
}

function listarPaisesSelect(){
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $result = mysqli_query($conexao, "select p.codigoPais, p.nomePais, c.nomeContinente
                                      from pais p
                                      inner join continente c on p.idcontinente = c.codigoContinente");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function listarContinentesSelect(){
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $lista = array();
    $result = mysqli_query($conexao, "select codigoContinente, nomeContinente from continente");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}
?>