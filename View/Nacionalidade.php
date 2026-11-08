<?php
include_once(__DIR__ . "/../Controller/controllerNacionalidade.php");
include_once(__DIR__ . "/../Controller/sessao.php");
include_once(__DIR__ . "/../Controller/controllerLog.php");

verificarLogin();

$perfil = $sessao->nivelAcesso;
$nomeCompleto = $sessao->nome . " " . $sessao->apelido;

// Permissões lidas da tabela perfil
$pVer = $sessao->podeFazer('ver');
$pCriar = $sessao->podeFazer('criar');
$pEditar = $sessao->podeFazer('editar');
$pRemover = $sessao->podeFazer('remover');
$pUtilizadores = $sessao->podeFazer('utilizadores');
$pLogs = $sessao->podeFazer('logs');
$pPerfis = $sessao->podeFazer('perfis');

$erro = "";

// REMOVER NACIONALIDADE
if (isset($_POST['remover'])) {
    removerNacionalidade($_POST['id']);
    header("Location: Nacionalidade.php#lista");
    exit;
}

// CADASTRAR NACIONALIDADE
if (isset($_POST['gravar'])) {
    if (!empty($_POST['nome']) && !empty($_POST['nome_continente']) && !empty($_POST['idpais'])) {
        $nacionalidade = new Nacionalidade(
            null,
            $_POST['nome'],
            $_POST['idpais'],
            $_POST['nome_continente']
        );

        $resultado = criarNacionalidade($nacionalidade);

        if (is_string($resultado) && $resultado != "") {
            $erro = $resultado;
        } elseif ($resultado) {
            header("Location: Nacionalidade.php#lista");
            exit;
        } else {
            $erro = "Não foi possível registar a nacionalidade.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

// ACTUALIZAR NACIONALIDADE
if (isset($_POST['actualizar'])) {
    if (!empty($_POST['id']) && !empty($_POST['nomeEditar']) && !empty($_POST['idpaisEditar']) && !empty($_POST['nome_continenteEditar'])) {
        $nacionalidadeExistente = encontrar($_POST['id']);

        if ($nacionalidadeExistente) {
            $nacionalidadeExistente->setNomeNacionalidade($_POST['nomeEditar']);
            $nacionalidadeExistente->setNomePais($_POST['idpaisEditar']);
            $nacionalidadeExistente->setNomeContinente($_POST['nome_continenteEditar']);

            editarNacionalidade($nacionalidadeExistente);
            header("Location: Nacionalidade.php#lista");
            exit;
        }
    }
}

// PESQUISAR E LISTAR
$pesquisa = $_GET['pesquisa'] ?? "";
$nacionalidades = pesquisar($pesquisa);
$paises = listarPaisesSelect();
$continentes = listarContinentesSelect();
$novoContinente = $_POST['nome_continente'] ?? "";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Nacionalidades</title>
    <link rel="stylesheet" href="../assets/estilo.css">
    <script src="../JS/ValNacionalidade.js" defer></script>
</head>
<body>
    <!-- MENU DE NAVEGAÇÃO -->
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br>DE PESSOAS</div>
        </div>

        <nav class="navlinks">
            <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
            <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
            <?php if ($pVer) { ?><a href="Continente.php"><span class="icone"></span>Continente</a><?php } ?>
            <?php if ($pVer) { ?><a href="Pais.php"><span class="icone"></span>Pais</a><?php } ?>
            <?php if ($pVer) { ?><a href="Nacionalidade.php" class="on"><span class="icone"></span>Nacionalidade</a><?php } ?>
            <?php if ($pVer) { ?><a href="Registo.php"><span class="icone"></span>Registo de Nacionalidade</a><?php } ?>

            <?php if ($pUtilizadores) { ?>
                <a href="Cadastro.php"><span class="icone"></span>Cadastro de utilizadores</a>
            <?php } ?>

            <?php if ($pPerfis) { ?>
                <a href="Perfil.php"><span class="icone"></span>Perfil</a>
            <?php } ?>

            <?php if ($pLogs) { ?>
                <a href="log.php"><span class="icone"></span>Log</a>
            <?php } ?>
        </nav>

        <div class="utilizador">
            <div class="utilizadorNome"><?= ($nomeCompleto) ?></div>
            <span class="perfil"><?= ($perfil) ?></span>
        </div>

        <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
    </div>

    <div class="mae">
        <!-- CABEÇALHO -->
        <div class="cabecalho">
            <h1>Nacionalidades</h1>

            <div class="topo">
                <?php if ($erro != "") { ?>
                    <p class="erro"><?= ($erro) ?></p>
                <?php } ?>

                <form class="busca" method="get" action="Nacionalidade.php">
                    <input type="text" name="pesquisa" placeholder="Pesquisar nacionalidade" value="<?= ($pesquisa) ?>">
                    <button type="submit" class="botao-pesquisar"></button>
                </form>

                <?php if ($pCriar) { ?>
                    <a class="botao" href="#novo">Nova Nacionalidade</a>
                <?php } ?>
            </div>
        </div>

        <!-- TABELA DE NACIONALIDADES -->
        <div class="caixa" id="lista">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Nacionalidade</th>
                            <th>País</th>
                            <th>Continente</th>
                            <th class="coluna-acao">Acção</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($nacionalidades) == 0) { ?>
                            <tr>
                                <td colspan="5" class="vazio">
                                    <?= $pesquisa != "" ? "Nacionalidade não encontrada" : "Nenhuma nacionalidade registada" ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($nacionalidades as $nacionalidade) { ?>
                                <tr>
                                    <td><?= $nacionalidade->getCodigoNacionalidade() ?></td>
                                    <td><?= ($nacionalidade->getNomeNacionalidade()) ?></td>
                                    <td><?= ($nacionalidade->getNomePais()) ?></td>
                                    <td><?= ($nacionalidade->getNomeContinente()) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <?php if ($pEditar) { ?>
                                                <a href="#editar-<?= $nacionalidade->getCodigoNacionalidade() ?>" class="botao-atualizar">Editar</a>
                                            <?php } ?>
                                            <?php if ($pRemover) { ?>
                                                <a href="#remover-<?= $nacionalidade->getCodigoNacionalidade() ?>" class="botao-remover">Remover</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: NOVA NACIONALIDADE -->
        <div class="modalFundo" id="novo">
            <a href="#" class="modalOverlay" aria-label="Fechar"></a>
            <div class="modalCaixa">
                <div class="modalTopo">
                    <h2 class="titulo">Nova Nacionalidade</h2>
                    <a href="#" class="modalFechar">x</a>
                </div>

                <form id="formNovaNacionalidade"
                      method="post"
                      action="Nacionalidade.php#novo"
                      onsubmit="return validarEnvioCadastroNacionalidade(event)">
                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Continente</strong></label>
                        </div>
                        <select id="continenteNacionalidade" name="nome_continente" class="input" required>
                            <option value="">Selecione um continente</option>
                            <?php foreach ($continentes as $c) { ?>
                                <option value="<?= ($c['nomeContinente']) ?>" <?= $c['nomeContinente'] == $novoContinente ? 'selected' : '' ?>>
                                    <?= ($c['nomeContinente']) ?>
                                </option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="filtrar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>País</strong></label>
                        </div>
                        <select id="paisNacionalidade" name="idpais" class="input" required>
                            <option value="">Selecione um país</option>
                            <?php foreach ($paises as $pais) { ?>
                                <?php if ($pais['nomeContinente'] == $novoContinente) { ?>
                                    <option value="<?= $pais['codigoPais'] ?>"><?= ($pais['nomePais']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="campo">
                        <div class="campoTipo">
                            <label class="label"><strong>Nome da nacionalidade</strong></label>
                        </div>
                        <input type="text" id="nomeNacionalidade" name="nome" class="input" required>
                    </div>

                    <div id="erros-nova-nacionalidade" class="erro" style="display:none;"></div>

                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAIS: EDITAR NACIONALIDADE -->
        <?php foreach ($nacionalidades as $n) {
            $idNacionalidade = $n->getCodigoNacionalidade();
            $editNome = $n->getNomeNacionalidade();
            $editContinente = $n->getNomeContinente();
            $editPais = $n->getNomePais();
            $filtrouEste = isset($_POST['filtrarEditar'], $_POST['id']) && $_POST['id'] == $idNacionalidade;

            if ($filtrouEste) {
                $editNome = $_POST['nomeEditar'];
                $editContinente = $_POST['nome_continenteEditar'];
                $editPais = $_POST['idpaisEditar'];
            }
        ?>
            <div class="modalFundo" id="editar-<?= $idNacionalidade ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>

                    <form method="post"
                          action="Nacionalidade.php#editar-<?= $idNacionalidade ?>"
                          onsubmit="return validarEnvioEdicaoNacionalidade(event, <?= $idNacionalidade ?>)">
                        <input type="hidden" name="id" value="<?= $idNacionalidade ?>">

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome da nacionalidade</strong></label>
                            </div>
                            <input type="text"
                                   id="nomeEditar<?= $idNacionalidade ?>"
                                   name="nomeEditar"
                                   class="input"
                                   value="<?= ($editNome) ?>"
                                   required>
                        </div>

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Continente</strong></label>
                            </div>
                            <select id="continenteEditar<?= $idNacionalidade ?>"
                                    name="nome_continenteEditar"
                                    class="input"
                                    required>
                                <option value="">Selecione um continente</option>
                                <?php foreach ($continentes as $c) { ?>
                                    <option value="<?= ($c['nomeContinente']) ?>" <?= $c['nomeContinente'] == $editContinente ? 'selected' : '' ?>>
                                        <?= ($c['nomeContinente']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="filtrarEditar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                        </div>

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>País</strong></label>
                            </div>
                            <select id="paisEditar<?= $idNacionalidade ?>"
                                    name="idpaisEditar"
                                    class="input"
                                    required>
                                <option value="">Selecione um país</option>
                                <?php foreach ($paises as $pais) { ?>
                                    <?php if ($pais['nomeContinente'] == $editContinente) {
                                        $paisSelecionado = $pais['codigoPais'] == $editPais || $pais['nomePais'] == $editPais;
                                    ?>
                                        <option value="<?= $pais['codigoPais'] ?>" <?= $paisSelecionado ? 'selected' : '' ?>>
                                            <?= ($pais['nomePais']) ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>

                        <div id="erros-editar-nacionalidade<?= $idNacionalidade ?>" class="erro" style="display:none;"></div>

                        <div class="acao">
                            <button type="submit" name="actualizar" class="botao">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <!-- MODAIS: REMOVER NACIONALIDADE -->
        <?php foreach ($nacionalidades as $nacionalidade) { ?>
            <div class="modalFundo" id="remover-<?= $nacionalidade->getCodigoNacionalidade() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>

                    <form method="post" action="Nacionalidade.php">
                        <input type="hidden" name="id" value="<?= $nacionalidade->getCodigoNacionalidade() ?>">

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>ID</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= $nacionalidade->getCodigoNacionalidade() ?>" disabled>
                        </div>

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome da nacionalidade</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= ($nacionalidade->getNomeNacionalidade()) ?>" disabled>
                        </div>

                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>País</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= ($nacionalidade->getNomePais()) ?>" disabled>
                        </div>

                        <div class="acao">
                            <button type="submit" class="botao-remover" name="remover">Confirmar Remoção</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>