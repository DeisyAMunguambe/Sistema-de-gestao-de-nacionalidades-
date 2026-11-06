<?php
include_once(__DIR__ . "/../Controller/controllerLog.php");
include_once(__DIR__ . "/../Controller/sessao.php");
verificarLogin();
exigirPermissaoAutomatica("logs");
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

$campos = array(
    "accao" => "Ação",
    "usuario" => "Utilizador",
    "perfil" => "Perfil",
    "dataAccao" => "Data de Ação"
);
$campo = isset($_GET['campo']) ? trim($_GET['campo']) : "";
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : "";
if (!array_key_exists($campo, $campos)) {
    $campo = "";
    $valor = "";
}
$logs = ($campo != "" && $valor != "") ? pesquisarLogs($campo, $valor) : listarLogs();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listar Log</title>
    <link rel="stylesheet" href="../assets/estilo.css">
             
</head>
<body>
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br>DE PESSOAS</div>
        </div>
        <nav class="navlinks">
            <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
            <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
            <?php if ($pVer) { ?><a href="Continente.php"><span class="icone"></span>Continente</a><?php } ?>
            <?php if ($pVer) { ?><a href="Pais.php"><span class="icone"></span>Pais</a><?php } ?>
            <?php if ($pVer) { ?><a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a><?php } ?>
             <?php if ($pVer) { ?><a href="Registo.php"><span class="icone"></span>Registo de Nacionalidade</a><?php } ?>
            <?php if ($pUtilizadores) { ?>
                <a href="Cadastro.php"><span class="icone"></span>Cadastro de utilizadores</a>
            <?php } ?>
            <?php if ($pPerfis) { ?><a href="Perfil.php"><span class="icone"></span>Perfil</a><?php } ?>
            <?php if ($pLogs) { ?>
                <a href="log.php" class="on"><span class="icone"></span>Log</a>
            <?php } ?>
            
        </nav>
        <div class="utilizador">
            <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
            <span class="perfil"><?= htmlspecialchars($perfil) ?></span>
        </div>
        <a href="Logout.php" class="sair">Log out</a>
    </div>
    <div class="mae">
        <div class="cabecalho">
            <h1>Registo de Acções</h1>
            <form method="get" action="log.php" class="pesquisa">
                <select name="campo" id="campo" required>
                    <option value="">Pesquisar por...</option>
                    <?php foreach ($campos as $chave => $nome) { ?>
                        <option value="<?= $chave ?>" <?= ($campo == $chave) ? "selected" : "" ?>><?= $nome ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="valor" placeholder="Escreva o que pretende pesquisar" value="<?= htmlspecialchars($valor) ?>">
                
            </form>
        </div>
        <div class="caixa">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Utilizador</th>
                            <th>Perfil</th>
                            <th>Acção</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($logs) == 0) { ?>
                            <tr>
                                <td colspan="5" class="vazio">Nenhum registo encontrado</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($logs as $log) { ?>
                                <tr>
                                    <td><?= $log->getCodigoLog() ?></td>
                                    <td><?= htmlspecialchars($log->getUsuario()) ?></td>
                                    <td><?= htmlspecialchars($log->getPerfil()) ?></td>
                                    <td><?= htmlspecialchars($log->getAccao()) ?></td>
                                    <td><?= htmlspecialchars($log->getDataAccao()) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>