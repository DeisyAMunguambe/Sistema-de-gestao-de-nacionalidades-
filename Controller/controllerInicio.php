<?php
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
    $conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function contarPessoas(){
        global $conexao;
        $result = mysqli_query($conexao, "select count(*) as total from pessoa");
        $rs = mysqli_fetch_assoc($result);
        return $rs['total'];
    }

    function contarPaises(){
        global $conexao;
        $result = mysqli_query($conexao, "select count(*) as total from pais");
        $rs = mysqli_fetch_assoc($result);
        return $rs['total'];
    }

    function contarContinentes(){
        global $conexao;
        $result = mysqli_query($conexao, "select count(*) as total from continente");
        $rs = mysqli_fetch_assoc($result);
        return $rs['total'];
    }

    function contarNacionalidades(){
        global $conexao;
        $result = mysqli_query($conexao, "select count(*) as total from nacionalidade");
        $rs = mysqli_fetch_assoc($result);
        return $rs['total'];
    }
?>
