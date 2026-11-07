
<?php
include_once(__DIR__ . "/../Controller/controllerPais.php");
include_once(__DIR__ . "/../Controller/sessao.php");

verificarLogin();

$perfil = $sessao->nivelAcesso;
$nomeCompleto = $sessao->nome . " " . $sessao->apelido;

// PERMISSÕES
$pVer          = $sessao->podeFazer('ver');
$pCriar        = $sessao->podeFazer('criar');
$pEditar       = $sessao->podeFazer('editar');
$pRemover      = $sessao->podeFazer('remover');
$pUtilizadores = $sessao->podeFazer('utilizadores');
$pLogs         = $sessao->podeFazer('logs');
$pPerfis       = $sessao->podeFazer('perfis');

// REMOVER PAÍS
if (isset($_POST['remover']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
    removerPais($_POST['id']);
    header("Location: Pais.php");
    exit;
}

// CADASTRAR PAÍS
if (isset($_POST['gravar']) && isset($_POST['nome']) && isset($_POST['id_continente'])) {
    $nome = trim($_POST['nome']);
    $continente = trim($_POST['id_continente']);

    if ($nome !== "" && $continente !== "") {
        $pais = new Pais(null, $nome, $continente);

        if (criarPais($pais)) {
            header("Location: Pais.php");
            exit;
        }
    }
}

// EDITAR PAÍS
if (isset($_POST['actualizar']) && !empty($_POST['id']) && isset($_POST['nomeEditar']) && isset($_POST['id_continenteEditar'])) {
    $nome = trim($_POST['nomeEditar']);
    $continente = trim($_POST['id_continenteEditar']);

    if ($nome !== "" && $continente !== "") {
        $paisExistente = encontrarPais($_POST['id']);

        if ($paisExistente) {
            $paisExistente->setNomePais($nome);
            $paisExistente->setNomeContinente($continente);
            editarPais($paisExistente);
            header("Location: Pais.php");
            exit;
        }
    }
}

// PESQUISAR PAÍSES
$busca = isset($_GET['pesquisar']) ? trim($_GET['pesquisar']) : null;
$paises = listarPaises($busca);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listar País</title>
    <link rel="stylesheet" href="../assets/estilo.css">
    <script src="../JS/ValPais.js" defer></script>
</head>
<body>
    <!-- BARRA DE NAVEGAÇÃO -->
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br />DE PESSOAS</div>
        </div>
        <nav class="navlinks">
            <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
            <?php if ($pVer) { ?>
                <a href="Pessoa.php"><span class="icone"></span>Pessoa</a>
                <a href="Continente.php"><span class="icone"></span>Continente</a>
                <a href="Pais.php" class="on"><span class="icone"></span>Pais</a>
                <a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a>
                <a href="Registo.php"><span class="icone"></span>Registo de Nacionalidade</a>
            <?php } ?>
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
            <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
            <span class="perfil"><?= htmlspecialchars($perfil) ?></span>
        </div>
        <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
    </div>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="mae">
        <div class="cabecalho">
            <h1>Paises</h1>
            <div class="topo">
                <!-- PESQUISA -->
                <form class="busca" method="get" action="Pais.php">
                    <input type="text" name="pesquisar" placeholder="Pesquisar pais" value="<?= htmlspecialchars($busca ?? '') ?>" />
                    <button type="submit" class="botao-pesquisar"></button>
                </form>
                <?php if ($pCriar) { ?>
                    <a class="botao" href="#novo">Novo Pais</a>
                <?php } ?>
            </div>
        </div>

        <!-- TABELA DE PAÍSES -->
        <div class="caixa">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Nome do Pais</th>
                            <th>Continente</th>
                            <th class="coluna-acao">Accao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($paises) == 0) { ?>
                            <tr>
                                <td colspan="4" class="vazio">
                                    <?= $busca ? "Pais nao encontrado" : "Nenhum pais registado" ?>
                                </td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($paises as $pais) { ?>
                                <tr>
                                    <td><?= $pais->getCodigoPais() ?></td>
                                    <td><?= htmlspecialchars($pais->getNomePais()) ?></td>
                                    <td><?= htmlspecialchars($pais->getNomeContinente()) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <?php if ($pEditar) { ?>
                                                <a href="#editar-<?= $pais->getCodigoPais() ?>" class="botao-atualizar">Editar</a>
                                            <?php } ?>
                                            <?php if ($pRemover) { ?>
                                                <a href="#remover-<?= $pais->getCodigoPais() ?>" class="botao-remover">remover</a>
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

        <!-- MODAL: NOVO PAÍS -->
        <?php if ($pCriar) { ?>
            <div class="modalFundo" id="novo">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Novo Pais</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Pais.php" id="formNovoPais">
                        <input type="hidden" name="gravar" value="1" />
                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Continente</strong></label>
                            </div>
                            <select name="id_continente" class="input" id="continentePais">
                                <option value="" disabled selected>Selecione</option>
                                <?php
                                $rsContinentes = listarContinentes();
                                while ($c = mysqli_fetch_assoc($rsContinentes)) {
                                    $nomeContinente = htmlspecialchars($c['nomeContinente'], ENT_QUOTES, 'UTF-8');
                                    echo "<option value=\"{$nomeContinente}\">{$nomeContinente}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome do pais</strong></label>
                            </div>
                            <input type="text" name="nome" class="input" id="nomePais" />
                            <div id="erros-novo-pais" style="display:none; color:red;"></div>
                        </div>
                        <div class="acao">
                            <button type="button" class="botao" onclick="processarCadastroPais()">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <!-- MODAL: EDITAR PAÍS -->
        <?php if ($pEditar) { ?>
            <?php foreach ($paises as $pais) { ?>
                <?php $idPais = $pais->getCodigoPais(); ?>
                <div class="modalFundo" id="editar-<?= $idPais ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Editar Pais</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post" action="Pais.php" id="formEditarPais<?= $idPais ?>">
                            <input type="hidden" name="id" value="<?= $idPais ?>" />
                            <input type="hidden" name="actualizar" value="1" />
                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do pais</strong></label>
                                </div>
                                <input type="text" name="nomeEditar" class="input" id="nomeEditarPais<?= $idPais ?>" value="<?= htmlspecialchars($pais->getNomePais(), ENT_QUOTES, 'UTF-8') ?>" />
                                <div id="erros-editar-pais<?= $idPais ?>" style="display:none; color:red;"></div>
                            </div>
                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Continente</strong></label>
                                </div>
                                <select name="id_continenteEditar" class="input" id="continenteEditarPais<?= $idPais ?>">
                                    <?php
                                    $rsContinentes = listarContinentes();
                                    while ($c = mysqli_fetch_assoc($rsContinentes)) {
                                        $nomeContinente = htmlspecialchars($c['nomeContinente'], ENT_QUOTES, 'UTF-8');
                                        $selecionado = ($c['nomeContinente'] == $pais->getNomeContinente()) ? 'selected' : '';
                                        echo "<option value=\"{$nomeContinente}\" {$selecionado}>{$nomeContinente}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="acao">
                                <button type="button" class="botao" onclick="processarEdicaoPais(<?= $idPais ?>)">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <!-- MODAL: REMOVER PAÍS -->
        <?php if ($pRemover) { ?>
            <?php foreach ($paises as $pais) { ?>
                <div class="modalFundo" id="remover-<?= $pais->getCodigoPais() ?>">
                    <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                    <div class="modalCaixa">
                        <div class="modalTopo">
                            <h2 class="titulo">Remover Pais</h2>
                            <a href="#" class="modalFechar">x</a>
                        </div>
                        <form method="post" action="Pais.php">
                            <input type="hidden" name="id" value="<?= $pais->getCodigoPais() ?>" />
                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>ID</strong></label>
                                </div>
                                <input type="text" class="input" value="<?= $pais->getCodigoPais() ?>" disabled />
                            </div>
                            <div class="campo">
                                <div class="campoTipo">
                                    <label class="label"><strong>Nome do pais</strong></label>
                                </div>
                                <input type="text" class="input" value="<?= htmlspecialchars($pais->getNomePais(), ENT_QUOTES, 'UTF-8') ?>" disabled />
                            </div>
                            <div class="acao">
                                <button type="submit" class="botao-remover" name="remover">Confirmar Remocao</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>
