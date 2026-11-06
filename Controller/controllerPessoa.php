<?php
include_once(__DIR__ . "/../Model/Pessoa.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerLog.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listar(){
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $pessoas = array();
    $sql = "select * from pessoa";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
            array_push($pessoas, $pessoa);
        }
    }
    return $pessoas;
}

function criar($pessoa){
    exigirPermissaoAutomatica("criar");
    global $conexao;

    $sql = "select * from pessoa where nome='{$pessoa->getNome()}' and apelido='{$pessoa->getApelido()}'";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "A pessoa já existe";
    } else {
        $sql = "insert into pessoa values(null,'{$pessoa->getNome()}','{$pessoa->getApelido()}','{$pessoa->getGenero()}',
        '{$pessoa->getEstadoCivil()}','{$pessoa->getAnoNascimento()}')";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            registarAccao("Registou uma pessoa");
           // header('location:index.php');
        } else {
            echo "Ocorreu um erro ao gravar os dados";
        }
    }
}

function remover($codigo) {
    exigirPermissaoAutomatica("remover");
    global $conexao;
    $sql = "delete from pessoa where codigo={$codigo}";
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_affected_rows($conexao) > 0) {
        registarAccao("Removeu uma pessoa");
    }
    //header('location:index.php');
}

function editar($pessoa) {
    exigirPermissaoAutomatica("editar");
    global $conexao;
    $sql = "UPDATE pessoa SET nome = '{$pessoa->getNome()}', apelido = '{$pessoa->getApelido()}', 
    genero = '{$pessoa->getGenero()}', estadoCivil = '{$pessoa->getEstadoCivil()}',
     anoNascimento = '{$pessoa->getAnoNascimento()}' where codigo ='{$pessoa->getCodigo()}'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Editou uma pessoa");
        // header('location:index.php');
       // header('location:index.php');
    } else
        echo "ocorreu um erro ao actualizar os dados";
}

function encontrar($codigo) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $sql = "select * from pessoa where codigo=" . $codigo;
    $result = mysqli_query($conexao, $sql);

    $rs = mysqli_fetch_assoc($result);
    $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
    return $pessoa;
}

function pesquisar($nome) {
    exigirPermissaoAutomatica("ver");
    global $conexao;
    $pessoas = array();
    $sql = "select * from pessoa where nome like '%$nome%' or apelido like '%$nome%'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
            array_push($pessoas, $pessoa);
        }
    }
    return $pessoas;
}
?>