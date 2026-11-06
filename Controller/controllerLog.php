
<?php

include_once(__DIR__ . "/../Model/Log.php");
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function registarLog($idUsuario, $nome, $perfil, $accao)
{
    global $conexao;

    $sql = "insert into log (usuario, perfil, accao, dataAccao)
            VALUES ('{$nome}', '{$perfil}', '{$accao}', NOW())";

    $result = mysqli_query($conexao, $sql);

    if (!$result) {
        echo "Ocorreu um erro ao registar o log";
    }
}

function registarAccao($accao)
{
    if (isset($_SESSION['codigo'])) {
        $idUtilizador = $_SESSION['codigo'];
    } else {
        $idUtilizador = 0;
    }

    if (isset($_SESSION['nome'])) {
        $nome = $_SESSION['nome'];
    } else {
        $nome = '';
    }

    if (isset($_SESSION['nivelAcesso'])) {
        $perfil = $_SESSION['nivelAcesso'];
    } else {
        $perfil = '';
    }

    registarLog($idUtilizador, $nome, $perfil, $accao);
}

function listarLogs()
{
    exigirPermissaoAutomatica("logs");
    global $conexao;

    $logs = array();

    $sql = "select * from log ORDER BY dataAccao DESC";
    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $log = new Log(
                $rs['codigoLog'],
                $rs['usuario'],
                $rs['perfil'],
                $rs['accao'],
                $rs['dataAccao']
            );

            array_push($logs, $log);
        }
    }

    return $logs;
}

function pesquisarLogs($campo, $valor)
{
    exigirPermissaoAutomatica("logs");
    global $conexao;

    $logs = array();

    $colunas = array(
        "accao" => "accao",
        "usuario" => "usuario",
        "perfil" => "perfil",
        "dataAccao" => "dataAccao"
    );

    if (!isset($colunas[$campo])) {
        return listarLogs();
    }

    $coluna = $colunas[$campo];

    $sql = "select * from log where $coluna like '%{$valor}%'
            ORDER BY dataAccao DESC";

    $result = mysqli_query($conexao, $sql);

    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $log = new Log(
                $rs['codigoLog'],
                $rs['usuario'],
                $rs['perfil'],
                $rs['accao'],
                $rs['dataAccao']
            );

            array_push($logs, $log);
        }
    }

    return $logs;
}
?>