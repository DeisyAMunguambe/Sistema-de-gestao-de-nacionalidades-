<?php
include_once(__DIR__ . "/../Model/Pais.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerContinente.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listarPaises($busca = null){
    exigirPermissaoAutomatica("ver");
        global $conexao;
        $paises = array();
        $sql = "select p.codigoPais, p.nomePais, c.nomeContinente
                from pais p
                inner join continente c on p.idcontinente = c.codigoContinente";
        if (!empty($busca)) {
            $sql .= " where p.nomePais like '%{$busca}%'";
        }
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $pais = new Pais($rs['codigoPais'], $rs['nomePais'], $rs['nomeContinente']);
                array_push($paises, $pais);
            }
        }
        return $paises;
    }

    function criarPais($pais){
    exigirPermissaoAutomatica("criar");
        global $conexao;
        $sql = "select * from pais p
                inner join continente c on p.idcontinente = c.codigoContinente
                where p.nomePais='{$pais->getNomePais()}' and c.nomeContinente='{$pais->getNomeContinente()}'";
        $result = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "O pais já existe";
        } else {
            $sql = "insert into pais values(null,'{$pais->getNomePais()}',
            (select codigoContinente from continente where nomeContinente = '{$pais->getNomeContinente()}'))";
            $result = mysqli_query($conexao, $sql);
            if ($result) {
                registarAccao("Registou um pais");
                header('location:Pais.php');
            } else {
                echo "Ocorreu um erro ao gravar os dados";
            }
        }
    }

    function removerPais($codigoPais) {
    exigirPermissaoAutomatica("remover");
        global $conexao;
        $sql = "delete from pais where codigoPais={$codigoPais}";
        $result = mysqli_query($conexao, $sql);
        if ($result && mysqli_affected_rows($conexao) > 0) {
            registarAccao("Removeu um pais");
        }
        header('location:Pais.php');
    }

    function editarPais($pais) {
    exigirPermissaoAutomatica("editar");
        global $conexao;
        $sql = "UPDATE pais SET nomePais = '{$pais->getNomePais()}',
                idcontinente = (select codigoContinente from continente where nomeContinente = '{$pais->getNomeContinente()}')
                where codigoPais = '{$pais->getCodigoPais()}'";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            registarAccao("Editou um pais");
            header('location:Pais.php');
        } else
            echo "ocorreu um erro ao actualizar os dados";
    }

    function encontrarPais($codigoPais) {
    exigirPermissaoAutomatica("ver");
        global $conexao;
        $sql = "select p.codigoPais, p.nomePais, c.nomeContinente
                from pais p
                inner join continente c on p.idcontinente = c.codigoContinente
                where p.codigoPais=" . $codigoPais;
        $result = mysqli_query($conexao, $sql);

        $rs = mysqli_fetch_assoc($result);
        $pais = new Pais($rs['codigoPais'], $rs['nomePais'], $rs['nomeContinente']);
        return $pais;
    }

    function listarContinentes() {
    exigirPermissaoAutomatica("ver");
        global $conexao;
        $sql = "select * from continente";
        return mysqli_query($conexao, $sql);
    }
?>