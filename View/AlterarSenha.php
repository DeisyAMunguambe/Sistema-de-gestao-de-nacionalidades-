<?php

include_once(__DIR__ . "/../Controller/controllerCadastroUser.php");

$mensagem = "";
$username = "";

// Se veio do Login (?id=...), preenche o username automaticamente
if (isset($_GET['id'])) {
    $idLogin = (int) $_GET['id'];
    $res = mysqli_query($conexao, "SELECT username FROM cadastro WHERE CodigoUtilizador = {$idLogin}");
    $linha = mysqli_fetch_assoc($res);
    if ($linha) {
        $username = $linha['username'];
    }
}

if (isset($_POST['alterarSenha'])) {

    $username        = trim($_POST['username']);
    $senhaAntiga     = $_POST['senhaAntiga'];
    $novaSenha       = $_POST['novaSenha'];
    $confirmarSenha  = $_POST['confirmarSenha'];

    // Procurar o utilizador pelo username ou email
    $usernameSql = mysqli_real_escape_string($conexao, $username);
    $sql = "SELECT CodigoUtilizador, senha FROM cadastro
            WHERE username = '{$usernameSql}' OR email = '{$usernameSql}'";
    $resultado = mysqli_query($conexao, $sql);
    $utilizador = mysqli_fetch_assoc($resultado);

    if (!$utilizador) {
        $mensagem = "Utilizador não encontrado.";

    } elseif (!senhaCorresponde($senhaAntiga, $utilizador['senha'])) {
        $mensagem = "A senha actual está incorrecta.";

    } elseif ($novaSenha == "" || $confirmarSenha == "") {
        $mensagem = "Preencha todos os campos.";

    } elseif ($novaSenha != $confirmarSenha) {
        $mensagem = "As novas senhas não coincidem.";

    } elseif ($novaSenha == "Pessoa2026#") {
        $mensagem = "A nova senha deve ser diferente da senha padrão.";

    } elseif ($novaSenha == $senhaAntiga) {
        $mensagem = "A nova senha deve ser diferente da senha actual.";

    } else {
        $senhaNovaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $codigo = (int) $utilizador['CodigoUtilizador'];

        $sql = "UPDATE cadastro SET senha = '{$senhaNovaHash}' WHERE CodigoUtilizador = {$codigo}";

        if (mysqli_query($conexao, $sql) && mysqli_affected_rows($conexao) > 0) {
            header("Location: Login.php");
            exit;
        } else {
            $mensagem = "Erro ao alterar a senha.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Senha</title>
    <link rel="stylesheet" href="../assets/alterar.css">
    <link rel="stylesheet" href="../assets/estilo.css">
</head>

<body>

    <main class="pagina-senha">
        <section class="cartao-senha">
            <div class="cabecalho-senha">    
                <h1 class="titulo-senha">Alterar senha</h1>
                <p class="texto-senha"> Por segurança, confirme a sua senha actual e defina uma nova senha para continuar.</p>
            </div>


            <?php if ($mensagem != "") { ?>
                <div class="erro-senha"><?= htmlspecialchars($mensagem) ?></div>
            <?php } ?>


            <form method="post">
                <div class="campo-senha">
                    <label class="label">Username ou email</label>
                    <input type="text" name="username" class="input-senha" placeholder="Digite o username ou email" value="<?= htmlspecialchars($username) ?>" required>
                </div>
                <div class="campo-senha">
                    <label class="label">Senha actual </label>
                    <input type="password" name="senhaAntiga" class="input-senha" placeholder="Digite a senha actual" required>
                </div>
                <div class="campo-senha">
                    <label class="label"> Nova senha</label>
                    <input type="password" name="novaSenha" class="input-senha" placeholder="Digite a nova senha" required>

                </div>
                <div class="campo-senha">
                    <label class="label">Confirmar nova senha</label>
                    <input type="password" name="confirmarSenha" class="input-senha" placeholder="Digite novamente a nova senha" required>
                </div>

                <button type="submit" name="alterarSenha" class="botao-senha">Alterar senha</button>

            </form>
        </section>
    </main>
</body>

</html>

