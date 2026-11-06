<?php
    include_once(__DIR__ . "/../Controller/controllerContinente.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerLog.php");
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


    if (isset($_POST['remover'])) {
        remover($_POST['id']);
    }
    if (isset($_POST['gravar']) && !empty($_POST['nome'])) {
        $continente = new Continente(null, $_POST['nome']);
        if (criar($continente)) {
            header("Location: Continente.php");
            exit;
        }
    }
    if (isset($_POST['actualizar']) && !empty($_POST['id']) && !empty($_POST['nomeEditar'])) {
    $continenteExistente = encontrar($_POST['id']);
    if ($continenteExistente) {
        $continenteExistente->setNomeContinente($_POST['nomeEditar']);
        editar($continenteExistente);    
     }
    }

    $pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : "";
    $continentes = pesquisar($pesquisa);
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Listar Continente</title>
        
        <link rel="stylesheet" href="../assets/estilo.css">
    </head>

    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE PESSOAS </div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
                <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
                <?php if ($pVer) { ?><a href="Continente.php" class="on"><span class="icone"></span>Continente</a><?php } ?>
                <?php if ($pVer) { ?><a href="Pais.php" ><span class="icone"></span>Pais</a><?php } ?>
                <?php if ($pVer) { ?><a href="Nacionalidade.php" ><span class="icone"></span>Nacionalidade</a><?php } ?>
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
                <h1>Continentes</h1>
                <div class="topo">
                    <form class="busca" method="get" action="">
                        <input type="text" name="pesquisa" placeholder="Pesquisar continente" value="<?= $pesquisa ?>"/>
                    </form>
                    <?php if ($pCriar) { ?><a class="botao" href="#novo">Novo Continente</a><?php } ?>
                </div>
            </div>

            <div class="caixa">
                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th class="coluna-id">ID</th>
                                <th>Nome Continente</th>
                                <th class="coluna-acao">Accao</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($continentes) == 0) { ?>
                    <tr><td colspan="3" class="vazio">Nenhum continente registado</td></tr>
                    <?php } else { foreach ($continentes as $continente) { ?>
                            <tr>
                                <td><?= $continente->getCodigoContinente() ?></td>
                                <td><?= $continente->getNomeContinente() ?></td>
                                <td>
                                <div class="acoes">
                                     <?php if ($pEditar) { ?>
                                                    <a href="#editar-<?= $continente->getCodigoContinente() ?>" class="botao-atualizar">Editar</a>
                                                    <?php } ?>
<?php if ($pRemover) { ?>
                                                    <a href="#remover-<?= $continente->getCodigoContinente() ?>" class="botao-remover">remover</a>
                                     <?php } ?>    
                                    </div>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL: novo continente -->
             <div class="modalFundo" id="novo" >
        <a href="#" class="modalOverlay" aria-label="Fechar"></a>
        <div class="modalCaixa">
            <div class="modalTopo">
                <h2 class="titulo">Novo Continente</h2>
                <a href="#" class="modalFechar">x</a>
            </div>
            <form method="post" action="Continente.php">
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Nome do continente</strong></label></div>
                    <input type="text" name="nome" class="input" />
                </div>
                <div class="acao">
                    <button type="submit" name="gravar" class="botao">Guardar</button>
                </div>
              </form>
            </div>
        </div>
            

            <!-- MODAL: editar dados do continente -->
             <?php foreach ($continentes as $continente ) { ?>
                
           <div class="modalFundo" id="editar-<?= $continente->getCodigoContinente() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Continente</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>

                    <form method="post" action="Continente.php">
                        <input type="hidden" name="id" value="<?= $continente->getCodigoContinente(); ?>" />
                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome do continente</strong></label>
                            </div>
                            <input type="text" name="nomeEditar" class="input" value="<?= ($continente->getNomeContinente()) ?>" />
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao" name="actualizar">Guardar</button>
                        </div>
                    </form>

                </div>
            </div>
            <?php } ?>
            <!-- MODAL: confirmar remocao do continente -->
            <?php foreach ($continentes as $continente ) { ?>
            <div class="modalFundo" id="remover-<?= $continente->getCodigoContinente() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Continente</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>

                    <form method="post" action="Continente.php"> >

                        <input type="hidden" name="id" value="<?= $continente->getCodigoContinente() ?>" />
                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>ID</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= $continente->getCodigoContinente() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo">
                                <label class="label"><strong>Nome do continente</strong></label>
                            </div>
                            <input type="text" class="input" value="<?= $continente->getNomeContinente() ?>" disabled />
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