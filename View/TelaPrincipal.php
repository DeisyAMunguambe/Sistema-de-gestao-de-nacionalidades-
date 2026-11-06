<?php
    include_once(__DIR__ . "/../Controller/controllerInicio.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
    $perfil = $sessao->nivelAcesso;
    $nomeCompleto = $sessao->nome . " " . $sessao->apelido;
    // permissoes lidas da tabela perfil (funciona tambem com perfis novos)
    $pVer          = $sessao->podeFazer('ver');
    $pCriar        = $sessao->podeFazer('criar');
    $pEditar       = $sessao->podeFazer('editar');
    $pRemover      = $sessao->podeFazer('remover');
    $pUtilizadores = $sessao->podeFazer('utilizadores');
    $pLogs         = $sessao->podeFazer('logs');
    $pPerfis       = $sessao->podeFazer('perfis');

    $totalPessoas = contarPessoas();
    $totalPaises = contarPaises();
    $totalContinentes = contarContinentes();
    $totalNacionalidades = contarNacionalidades();
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Tela Principal</title>
        <link rel="stylesheet" href="../assets/estilo.css" />
    </head>


    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE PESSOAS</div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php" class="on"><span class="icone"></span>Inicio</a>
                <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
                <?php if ($pVer) { ?><a href="Continente.php"><span class="icone"></span>Continente</a><?php } ?>
                <?php if ($pVer) { ?><a href="Pais.php"><span class="icone"></span>Pais</a><?php } ?>
                <?php if ($pVer) { ?><a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a><?php } ?>
                <?php if ($pVer) { ?><a href="Registo.php"><span class="icone"></span>Registo de Nacionalidade</a><?php } ?>
                 <?php if ($pUtilizadores) { ?>
                <a href="Cadastro.php"><span class="icone"></span>Cadastro de utilizadores</a></a>
                <?php } ?>
                <?php if ($pPerfis) { ?>
                <a href="Perfil.php"><span class="icone"></span>Perfil</a></a>
                <?php } ?>
                <?php if ($pLogs) { ?>
                <a href="log.php"><span class="icone"></span>Log</a>
                <?php } ?>
                
            </nav>
            <div class="utilizador">
                <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
                <span class="perfil"><?= htmlspecialchars($perfil) ?></span>
            </div>
            <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
        </div>

        <div class="mae">
            <?php if (isset($_GET['erro']) && $_GET['erro'] == 'semPermissao') { ?>
                <div style="background:#fdecea;color:#b71c1c;padding:12px 16px;border-radius:8px;margin-bottom:16px;">
                    Não tens permissão para aceder a essa tela.
                </div>
            <?php } ?>
            <div class="cabecalho">
                <div>
                    <h1>Bem-vindo</h1>
                    <p class="boasVindas">Resumo geral do sistema de Gestao de Pessoas</p>
                </div>
            </div>

            <div class="cartoes">
                <div class="cartao">
                    <div class="rotulo">Pessoas</div>
                    <div class="numero"><?= $totalPessoas ?></div>
                    <a href="Pessoa.php">Ver todas</a>
                </div>
                <div class="cartao">
                    <div class="rotulo">Paises</div>
                    <div class="numero"><?= $totalPaises ?></div>
                    <a href="Pais.php">Ver todos</a>
                </div>
                <div class="cartao">
                    <div class="rotulo">Continentes</div>
                    <div class="numero"><?= $totalContinentes ?></div>
                    <a href="Continente.php">Ver todos</a>
                </div>
                <div class="cartao">
                    <div class="rotulo">Nacionalidades</div>
                    <div class="numero"><?= $totalNacionalidades ?></div>
                    <a href="Nacionalidade.php">Ver todas</a>
                </div>
            </div>
        </div>
    </body>
</html>