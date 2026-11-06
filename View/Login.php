
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__ . "/../Controller/sessao.php");
include_once(__DIR__ . "/../Controller/controllerCadastroUser.php");

$erro = "";

if ($sessao->esta_logado()) {
    header("Location: TelaPrincipal.php");
    exit;
}

if (isset($_POST['entrar'])) {
    $username = trim($_POST['username'] ?? "");
    $senha = $_POST['senha'] ?? "";

    if ($username == "" || $senha == "") {
        $erro = "Preencha o username ou email e a senha.";
    } else {
        $utilizador = autenticar($username, $senha);

        if ($utilizador != null) {
            if (senhaCorresponde('Pessoa2026#', $utilizador->getSenha())) {
                header("Location: AlterarSenha.php?id=" . $utilizador->getCodigo());
                exit;
            }

            $dadosUtilizador = array(
                'CodigoUtilizador' => $utilizador->getCodigo(),
                'email' => $utilizador->getEmail(),
                'nome' => $utilizador->getNome(),
                'apelido' => $utilizador->getApelido(),
                'username' => $utilizador->getUserName(),
                'nivelAcesso' => $utilizador->getNivelAcesso()
            );

           $sessao->login($dadosUtilizador);
            registarAccao("Iniciou sessão");
            header("Location: TelaPrincipal.php");
            exit;
        } else {
            $erro = "Username, email ou senha incorretos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Entrar - Gestão de Pessoas</title>
    <link rel="stylesheet" href="../assets/login.css">
</head>

<body>
    <main class="pagina">

        <section class="lado">
            <div class="lado-conteudo">
                <h1 class="lado-titulo">Gestão<br />de Pessoas</h1>
                <p class="lado-texto">Registe pessoas, organize os seus dados e controle quem acede ao sistema.</p>
            </div>
        </section>

        <section class="painel">
            <form class="cartao" method="post" action="Login.php">
                <h2 class="cartao-titulo">Entrar</h2>
                <p class="cartao-sub">Use o seu username ou email e a sua senha.</p>

                <?php if ($erro != "") { ?>
                    <p class="erro"><?= htmlspecialchars($erro) ?></p>
                <?php } ?>

                <div class="campo">
                    <label class="label" for="username">Username ou email</label>
                    <input type="text" id="username" name="username" class="input"
                           autocomplete="username" autofocus
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
                </div>

                <div class="campo">
                    <label class="label" for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="input"
                           autocomplete="current-password" />
                </div>

                <button type="submit" name="entrar" class="botao">Entrar</button>
                <p class="cartao-sub">Alterar senha? <a href="AlterarSenha.php">Clique aqui</a></p>
            </form>
        </section>

    </main>
</body>

</html>