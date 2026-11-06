<?php
    include_once(__DIR__ . "/../Model/Perfil.php");
    verificarLogin();
   
    $conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listarPerfis($busca = null) {
        global $conexao;
        $perfis = array();
        $sql = "select * from perfil";
        if (!empty($busca)) {
            $busca = mysqli_real_escape_string($conexao, $busca);
            $sql .= " where nomePerfil like '%{$busca}%'";
        }
        $sql .= " order by codigoPerfil";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
                $perfil = new Perfil($rs['codigoPerfil'], $rs['nomePerfil'], $rs['descricao'],
                                     $rs['ver'], $rs['criar'], $rs['editar'], $rs['remover'],
                                     $rs['utilizadores'], $rs['logs'], $rs['perfis']);
                array_push($perfis, $perfil);
            }
        }
        return $perfis;
    }

    function encontrarPerfil($codigoPerfil) {
        global $conexao;
        $sql = "select * from perfil where codigoPerfil=" . (int)$codigoPerfil;
        $result = mysqli_query($conexao, $sql);
        $rs = mysqli_fetch_assoc($result);
        if ($rs == null) {
            return null;
        }
        return new Perfil($rs['codigoPerfil'], $rs['nomePerfil'], $rs['descricao'],
                          $rs['ver'], $rs['criar'], $rs['editar'], $rs['remover'],
                          $rs['utilizadores'], $rs['logs'], $rs['perfis']);
    }

    function criarPerfil($perfil) {
        exigirPermissaoAutomatica("criar");
        global $conexao;
        $nome      = mysqli_real_escape_string($conexao, strtoupper(trim($perfil->getNomePerfil())));
        $descricao = mysqli_real_escape_string($conexao, trim($perfil->getDescricao()));

        $sql = "select * from perfil where nomePerfil = '{$nome}'";
        $result = mysqli_query($conexao, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "O perfil já existe";
        } else {
            $sql = "insert into perfil values(null, '{$nome}', '{$descricao}',
                    {$perfil->getVer()}, {$perfil->getCriar()}, {$perfil->getEditar()},
                    {$perfil->getRemover()}, {$perfil->getUtilizadores()},
                    {$perfil->getLogs()}, {$perfil->getPerfis()})";
            $result = mysqli_query($conexao, $sql);
            if ($result) {
                header('location:Perfil.php');
            } else {
                echo "Ocorreu um erro ao gravar os dados";
            }
        }
    }
    function editarPerfil($perfil) {
        exigirPermissaoAutomatica("editar");
        global $conexao;
        $descricao = mysqli_real_escape_string($conexao, trim($perfil->getDescricao()));
        $perfis = $perfil->getPerfis();
        if ($perfil->getCodigoPerfil() == 3) {
            $perfis = 1;
        }
        $sql = "update perfil set descricao = '{$descricao}',
                ver = {$perfil->getVer()}, criar = {$perfil->getCriar()},
                editar = {$perfil->getEditar()}, remover = {$perfil->getRemover()},
                utilizadores = {$perfil->getUtilizadores()}, logs = {$perfil->getLogs()},
                perfis = {$perfis}
                where codigoPerfil = " . (int)$perfil->getCodigoPerfil();
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            header('location:Perfil.php');
        } else {
            echo "ocorreu um erro ao actualizar os dados";
        }
    }
    function removerPerfil($codigoPerfil) {
        exigirPermissaoAutomatica("remover");
        global $conexao;
        $codigoPerfil = (int)$codigoPerfil;
        if ($codigoPerfil <= 4) {
            echo "Os perfis base não podem ser removidos";
            return;
        }
        $perfil = encontrarPerfil($codigoPerfil);
        if ($perfil == null) {
            header('location:Perfil.php');
            return; }
        $nome = mysqli_real_escape_string($conexao, $perfil->getNomePerfil());
        $sql = "select * from cadastro where nivelAcesso = '{$nome}'";
        $result = mysqli_query($conexao, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
           
            return;
        }
        $sql = "delete from perfil where codigoPerfil = {$codigoPerfil}";
        mysqli_query($conexao, $sql);
        header('location:Perfil.php');
    }
?>