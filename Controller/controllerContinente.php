
<?php
include_once(__DIR__ . "/../Model/Continente.php");
include_once(__DIR__ . "/../Controller/sessao.php");
include_once(__DIR__ . "/../Controller/controllerLog.php");

verificarLogin();

$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listar(){
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $continentes = array();
    $sql = "select * from continente";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $continente = new Continente(
                $rs['codigoContinente'],
                $rs['nomeContinente']
            );
            array_push($continentes, $continente);
        }
    }
    return $continentes;
}

function criar($continente){
    exigirPermissaoAutomatica("criar");
    global $conexao;

    $sql = "select * from continente where nomeContinente='{$continente->getNomeContinente()}'";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "O continente já existe";
    } else {
        $sql = "insert into continente values(null,'{$continente->getNomeContinente()}')";
        $result = mysqli_query($conexao, $sql);

        if ($result) {
            registarAccao("Registou um continente");
        } else {
            echo "Ocorreu um erro ao gravar os dados";
        }
    }
}

function remover($codigoContinente) {
    exigirPermissaoAutomatica("remover");
    global $conexao;
    $sql = "delete from continente where codigoContinente={$codigoContinente}";
    $result = mysqli_query($conexao, $sql);

    if ($result && mysqli_affected_rows($conexao) > 0) {
        registarAccao("Removeu um continente");
    }
}

function editar($continente) {
    exigirPermissaoAutomatica("editar");
    global $conexao;
    $sql = "UPDATE continente SET NomeContinente = '{$continente->getNomeContinente()}'
     where codigoContinente ='{$continente->getCodigoContinente()}'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Editou um continente");
    } else {
        echo "Ocorreu um erro ao actualizar os dados";
    }
}

function encontrar($codigoContinente) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $sql = "select * from continente where codigoContinente=" . $codigoContinente;
    $result = mysqli_query($conexao, $sql);
    $rs = mysqli_fetch_assoc($result);
    $continente = new Continente($rs['codigoContinente'], $rs['nomeContinente']);

    return $continente;
}

function pesquisar($nome) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $continentes = array();
    $sql = "select * from continente where nomeContinente like '%$nome%'";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $continente = new Continente(
                $rs['codigoContinente'],
                $rs['nomeContinente']
            );
            array_push($continentes, $continente);
        }
    }
    return $continentes;
}
?>