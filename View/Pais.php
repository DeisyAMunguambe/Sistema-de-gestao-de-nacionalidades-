<?php
    include_once(__DIR__ . "/../Controller/controllerPais.php");
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


    if (isset($_POST['remover']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
        removerPais($_POST['id']);
        header("Location: Pais.php");
        exit;
    }
    if (isset($_POST['gravar']) && !empty($_POST['nome']) && !empty($_POST['id_continente'])) {
        $pais = new Pais(null, $_POST['nome'], $_POST['id_continente']);
        criarPais($pais);
        header("Location: Pais.php");
        exit;
    }
    if (isset($_POST['actualizar']) && !empty($_POST['id']) && !empty($_POST['nomeEditar']) && !empty($_POST['id_continenteEditar'])) {
        $paisExistente = encontrarPais($_POST['id']);
        if ($paisExistente) {
            $paisExistente->setNomePais($_POST['nomeEditar']);
            $paisExistente->setNomeContinente($_POST['id_continenteEditar']);
            editarPais($paisExistente);
            header("Location: Pais.php");
            exit;
        }
    }

    $busca = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : null;
    $paises = listarPaises($busca);
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Listar Pais</title>

        <link rel="stylesheet" href="../assets/estilo.css">
    </head>

    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE PESSOAS</div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
                <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
                <?php if ($pVer) { ?><a href="Continente.php"><span class="icone"></span>Continente</a><?php } ?>
                <?php if ($pVer) { ?><a href="Pais.php" class="on"><span class="icone"></span>Pais</a><?php } ?>
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
            <div class="cabecalho">
                <h1>Paises</h1>
                <div class="topo">
                    <form class="busca" method="get" action="Pais.php">
                        <input type="text" name="pesquisar" placeholder="Pesquisar pais" value="<?= isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '' ?>" />
                        <button type="submit" class="botao-pesquisar"></button>
                    </form>
                    <?php if ($pCriar) { ?><a class="botao" href="#novo">Novo Pais</a><?php } ?>
                </div>
            </div>

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
                            <tr><td colspan="4" class="vazio"><?= $busca ? "Pais nao encontrado" : "Nenhum pais registado" ?></td></tr>
                            <?php } else { foreach ($paises as $pais) { ?>
                            <tr>
                                <td><?= $pais->getCodigoPais() ?></td>
                                <td><?= $pais->getNomePais() ?></td>
                                <td><?= $pais->getNomeContinente() ?></td>
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
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL: novo pais -->
            <div class="modalFundo" id="novo">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Novo Pais</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Pais.php">
                        
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                            <select name="id_continente" class="input">
                                <option value="" disabled selected>Selecione</option>
                                <?php
                                $rsContinentes = listarContinentes();
                                while ($c = mysqli_fetch_assoc($rsContinentes)) {
                                    echo "<option value=\"{$c['nomeContinente']}\">{$c['nomeContinente']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome do pais</strong></label></div>
                            <input type="text" name="nome" class="input" />
                        </div>
                        <div class="acao">
                            <button type="submit" name="gravar" class="botao">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: editar dados do pais -->
            <?php foreach ($paises as $pais) { ?>
            <div class="modalFundo" id="editar-<?= $pais->getCodigoPais() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Pais</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Pais.php">
                        <input type="hidden" name="id" value="<?= $pais->getCodigoPais() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome do pais</strong></label></div>
                            <input type="text" name="nomeEditar" class="input" value="<?= $pais->getNomePais() ?>" />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                            <select name="id_continenteEditar" class="input">
                                <?php
                                $rsContinentes = listarContinentes();
                                while ($c = mysqli_fetch_assoc($rsContinentes)) {
                                    $selecionado = ($c['nomeContinente'] == $pais->getNomeContinente()) ? 'selected' : '';
                                    echo "<option value=\"{$c['nomeContinente']}\" {$selecionado}>{$c['nomeContinente']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao" name="actualizar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>

            <!-- MODAL: confirmar remocao do pais -->
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
                            <div class="campoTipo"><label class="label"><strong>ID</strong></label></div>
                            <input type="text" class="input" value="<?= $pais->getCodigoPais() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome do pais</strong></label></div>
                            <input type="text" class="input" value="<?= $pais->getNomePais() ?>" disabled />
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao-remover" name="remover">Confirmar Remocao</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>

        </div>
    </body>
</html>