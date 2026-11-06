<?php
    include_once(__DIR__ . "/../Model/CadastroUser.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerLog.php");
    
    $conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listar(){
    exigirPermissaoAutomatica("utilizadores");
        global $conexao;
        $cadastros = array();
        $sql = "select * from cadastro";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {

                $cadastro = new CadastroUser($rs["CodigoUtilizador"], $rs["nome"], $rs["apelido"], 
                $rs["contacto"], $rs["email"], $rs["username"], $rs["senha"],$rs["nivelAcesso"]);
                array_push($cadastros, $cadastro);
            }
        }
        return $cadastros;
    }
    function criar($cadastro){
    exigirPermissaoAutomatica("utilizadores");
    global $conexao;

    $sql = "select * from cadastro  where username = '{$cadastro->getUserName()}'";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "A pessoa já foi cadastrada ";
    } else {
        
        $senhaHash = password_hash($cadastro->getSenha(), PASSWORD_DEFAULT);

        $sql = "insert into cadastro values( null,'{$cadastro->getNome()}',
                '{$cadastro->getApelido()}','{$cadastro->getContacto()}',
                '{$cadastro->getEmail()}','{$cadastro->getUserName()}',
                '{$senhaHash}','{$cadastro->getNivelAcesso()}')";

        $result = mysqli_query($conexao, $sql);
        if ($result) {
            header('location:Cadastro.php');
             registarAccao("Registou um utilizador");
        } else {
            echo "Ocorreu um erro ao gravar os dados";
        }
    }
}

    function remover($CodigoUtilizador) {
    exigirPermissaoAutomatica("utilizadores");
        global $conexao;
        $sql = "delete from cadastro where CodigoUtilizador ={$CodigoUtilizador}";
        $result = mysqli_query($conexao, $sql);
        
        if ($result && mysqli_affected_rows($conexao) > 0) {
            registarAccao("Removeu um utilizador");
        }
       header('location:Cadastro.php');
    }

    function editar($cadastro) {
    exigirPermissaoAutomatica("utilizadores");
        global $conexao;
        $sql = "UPDATE cadastro SET Nome = '{$cadastro->getNome()}', apelido ='{$cadastro->getApelido()}', 
        contacto ='{$cadastro->getContacto()}', email ='{$cadastro->getEmail()}', 
        username ='{$cadastro->getUserName()}',senha ='{$cadastro->getSenha()}', 
        nivelAcesso ='{$cadastro->getNivelAcesso()}' where CodigoUtilizador ='{$cadastro->getCodigo()}'";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            header('location:Cadastro.php');
             registarAccao("Editou um utilizador");
        }else 
            echo "ocorreu um erro ao actualizar os dados";
    }
    function encontrar($CodigoUtilizador) {
    exigirPermissaoAutomatica("utilizadores");
        global $conexao;
        $sql = "select * from cadastro where CodigoUtilizador=" . $CodigoUtilizador;
        $result = mysqli_query($conexao, $sql);
    
        $rs = mysqli_fetch_assoc($result);
        $cadastro = new CadastroUser($rs['CodigoUtilizador'], $rs['nome'], $rs['apelido'], 
        $rs['contacto'], $rs['email'], $rs['username'], $rs['senha'], $rs['nivelAcesso']);
        return $cadastro;          
    }

    function resetSenha($CodigoUtilizador) {
    exigirPermissaoAutomatica("utilizadores");
    global $conexao;
    // NOVO: a senha padrão também é guardada em hash
    $senhaHash = password_hash('Pessoa2026#', PASSWORD_DEFAULT);
    $sql = "update cadastro 
            set senha = '{$senhaHash}' 
            where CodigoUtilizador = {$CodigoUtilizador}";
    $result = mysqli_query($conexao, $sql);
    header('location:Cadastro.php');
}

    function senhaCorresponde($senhaInformada, $senhaGuardada) {
        if ($senhaInformada === null || $senhaGuardada === null) {
            return false;
        }
        if (password_verify($senhaInformada, $senhaGuardada)) {
            return true;
        }
        return hash_equals((string) $senhaInformada, (string) $senhaGuardada);
    }

        // NOVO: login. Procura por username ou email e confere a senha
    function autenticar($username, $senha) {
        global $conexao;

        // protege contra aspas e caracteres especiais escritos no login
        $username = mysqli_real_escape_string($conexao, $username);

        $sql = "select * from cadastro where username = '{$username}' or email = '{$username}'";
        $result = mysqli_query($conexao, $sql);

        if ($result) {
            $rs = mysqli_fetch_assoc($result);

            if ($rs != null && senhaCorresponde($senha, $rs['senha'])) {
                return new CadastroUser($rs['CodigoUtilizador'], $rs['nome'], $rs['apelido'],
                 $rs['contacto'], $rs['email'], $rs['username'], $rs['senha'], $rs['nivelAcesso']);
            }
        }
        return null;
    }

?>


