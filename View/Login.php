
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/estilo.css">
    <link rel="stylesheet" href="../assets/login.css">
</head>

<body>
    <main class="pagina min-vh-100">

        <section class="lado d-flex align-items-end p-5">
            <div class="lado-conteudo position-relative">
                <h1 class="lado-titulo">Gestão<br />de Pessoas</h1>
                <p class="lado-texto">Registe pessoas, organize os seus dados e controle quem acede ao sistema.</p>
            </div>
        </section>

        <section class="painel d-flex align-items-center justify-content-center p-4">
            <form class="cartao w-100 shadow-lg" method="post" action="Login.php">
                <h2 class="cartao-titulo mb-1">Entrar</h2>
                <p class="cartao-sub mb-4">Use o seu username ou email e a sua senha.</p>

                <?php if ($erro != "") { ?>
                    <p class="erro"><?= htmlspecialchars($erro) ?></p>
                <?php } ?>

                <div class="mb-3">
                    <label class="form-label" for="username">Username ou email</label>
                    <input type="text" id="username" name="username" class="form-control"
                           autocomplete="username" autofocus
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control"
                           autocomplete="current-password" />
                </div>

                <button type="submit" name="entrar" class="btn botao w-100  rounded-pill mt-2">Entrar</button>
                <p class="cartao-sub  mt-4 mb-0">Alterar senha? <a href="AlterarSenha.php">Clique aqui</a></p>
            </form>
        </section>

    </main>
</body>

</html>