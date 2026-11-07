<?php
    include_once(__DIR__ . "/../Controller/controllerPessoa.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
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


    if (isset($_POST['remover'])) {
        remover($_POST['id']);
    }
    if (isset($_POST['gravar']) && !empty($_POST['nome'])) {
        $pessoa = new Pessoa(null, $_POST['nome'], $_POST['apelido'], $_POST['genero'], $_POST['estado_civil'], $_POST['ano_nascimento']);
        if (criar($pessoa)) {
            header("Location: Pessoa.php");
            exit;
        }
    }
    if (isset($_POST['actualizar']) && !empty($_POST['id']) && !empty($_POST['nomeEditar'])) {
        $pessoa = new Pessoa($_POST['id'], $_POST['nomeEditar'], $_POST['apelidoEditar'], $_POST['generoEditar'], $_POST['estado_civilEditar'], $_POST['ano_nascimentoEditar']);
        editar($pessoa);
    }

    $pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : "";
    $pessoas = pesquisar($pesquisa);
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Listar Pessoa</title>

        <link rel="stylesheet" href="../assets/estilo.css">
        <script src="../JS/ValPessoa.js" ></script>
    </head>

    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE PESSOAS</div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
                <?php if ($pVer) { ?><a href="Pessoa.php" class="on"><span class="icone"></span>Pessoa</a><?php } ?>
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
            <div class="cabecalho">
                <h1>Pessoas</h1>
                <div class="topo">
                    <form class="busca" method="get" action="">
                        <input type="text" name="pesquisa" placeholder="Pesquisar pessoa" value="<?= $pesquisa ?>"/>
                    </form>
                    <?php if ($pCriar) { ?><a class="botao" href="#novo">Nova Pessoa</a><?php } ?>
                </div>
            </div>

            <div class="caixa">
                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th class="coluna-id">ID</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>Genero</th>
                                <th>Estado Civil</th>
                                <th>Ano de Nascimento</th>
                                <th>Ano Actual</th>
                                <th>Idade</th>
                                <th class="coluna-acao">Accao</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($pessoas) == 0) { ?>
                            <tr><td colspan="9" class="vazio">Nenhuma pessoa registada</td></tr>
                            <?php } else { foreach ($pessoas as $pessoa) { ?>
                            <tr>
                                <td><?= $pessoa->getCodigo() ?></td>
                                <td><?= $pessoa->getNome() ?></td>
                                <td><?= $pessoa->getApelido() ?></td>
                                <td><?= $pessoa->getGenero() ?></td>
                                <td><?= $pessoa->getEstadoCivil() ?></td>
                                <td><?= $pessoa->getAnoNascimento() ?></td>
                                <td><?= $pessoa->getAnoActual() ?></td>
                                <td><?= $pessoa->calcularIdade() ?></td>
                                <td>
                                    <div class="acoes">
                                    <?php if ($pEditar) { ?>
                                                    <a href="#editar-<?= $pessoa->getCodigo() ?>" class="botao-atualizar">Editar</a>
                                                    <?php } ?>
<?php if ($pRemover) { ?>
                                                    <a href="#remover-<?= $pessoa->getCodigo() ?>" class="botao-remover">remover</a>
                                     <?php } ?>  
                                    </div>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL: nova pessoa -->
            <div class="modalFundo" id="novo">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Nova Pessoa</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                   <form method="post" action="Pessoa.php" onsubmit="return processarCadastro()">

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Nome</strong></label>
                    </div>
                    <input type="text" name="nome" id="nome" class="input" />
                </div>

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Apelido</strong></label>
                    </div>
                    <input type="text" name="apelido" id="apelido" class="input" />
                </div>

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Género</strong></label>
                    </div>
                    <select class="input" id="genero" name="genero">
                        <option value="">Selecione</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Estado Civil</strong></label>
                    </div>
                    <select class="input" id="estadoCivil" name="estado_civil">
                        <option value="">Selecione</option>
                        <option value="Solteiro">Solteiro(a)</option>
                        <option value="Casado">Casado(a)</option>
                        <option value="Divorciado">Divorciado(a)</option>
                        <option value="Viúvo">Viúvo(a)</option>
                    </select>
                </div>

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Ano de Nascimento</strong></label>
                    </div>
                    <input type="text" name="ano_nascimento" id="anoNascimento" class="input" />
                </div>
                <div id="erros-novo"
                    style="display:none; color:red; margin-top:10px;">
                </div>
                <div class="acao">
                    <button type="submit" name="gravar" class="botao">Guardar </button>
                </div>
            </form>
                </div>
            </div>

            <!-- MODAL: editar dados da pessoa -->
           <?php foreach ($pessoas as $pessoa) {  $id = $pessoa->getCodigo();  ?>
            <form method="post" action="Pessoa.php" onsubmit="return processarEdicao(<?= $id ?>)">

                <input type="hidden" name="id" value="<?= $id ?>" />
                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Nome</strong></label>
                    </div>
                    <input type="text" name="nomeEditar" id="nomeEditar<?= $id ?>" class="input" value="<?= htmlspecialchars($pessoa->getNome()) ?>" />
                </div>

                <div class="campo">
                    <div class="campoTipo">
                        <label class="label"><strong>Apelido</strong></label>
                    </div>
                    <input type="text" name="apelidoEditar"  id="apelidoEditar<?= $id ?>" class="input" value="<?= htmlspecialchars($pessoa->getApelido()) ?>" />
                </div>

                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Género</strong></label> </div>
                    <select class="input" name="generoEditar" id="generoEditar<?= $id ?>">
                        <option value="Masculino"<?= $pessoa->getGenero() == "Masculino" ? "selected" : "" ?>> Masculino</option>
                        <option value="Feminino" <?= $pessoa->getGenero() == "Feminino" ? "selected" : "" ?>>Feminino</option>
                        <option value="Outro" <?= $pessoa->getGenero() == "Outro" ? "selected" : "" ?>>Outro </option>
                    </select>
                </div>

                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Estado Civil</strong></label></div>
                    <select class="input" name="estado_civilEditar" id="estadoCivilEditar<?= $id ?>">
                        <option value="Solteiro" <?= $pessoa->getEstadoCivil() == "Solteiro" ? "selected" : "" ?>>Solteiro(a) </option>
                        <option value="Casado" <?= $pessoa->getEstadoCivil() == "Casado" ? "selected" : "" ?>> Casado(a) </option>
                        <option value="Divorciado" <?= $pessoa->getEstadoCivil() == "Divorciado" ? "selected" : "" ?>> Divorciado(a)</option>
                        <option value="Viúvo"<?= $pessoa->getEstadoCivil() == "Viúvo" ? "selected" : "" ?>> Viúvo(a) </option>
                    </select>
                </div>
                <div class="campo">
                    <div class="campoTipo"> <label class="label"><strong>Ano de Nascimento</strong></label></div>
                    <input type="text" name="ano_nascimentoEditar" id="anoNascimentoEditar<?= $id ?>" class="input" value="<?= $pessoa->getAnoNascimento() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"> <label class="label"><strong>Idade</strong></label> </div>
                    <input type="text" class="input" value="<?= $pessoa->calcularIdade() ?>" disabled />
                </div>
                <div id="erros-editar<?= $id ?>"
                    style="display:none; color:red; margin-top:10px;">
                </div>
                <div class="acao">
                    <button type="submit" name="actualizar" class="botao"> Guardar </button>
                </div>
            </form>
            <?php } ?>
            <!-- MODAL: confirmar remocao da pessoa -->
            <?php foreach ($pessoas as $pessoa) { ?>
            <div class="modalFundo" id="remover-<?= $pessoa->getCodigo() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Pessoa</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $pessoa->getCodigo() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>ID</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getCodigo() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getNome() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Apelido</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getApelido() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Genero</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getGenero() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Estado Civil</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getEstadoCivil() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Ano de Nascimento</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->getAnoNascimento() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Idade</strong></label></div>
                            <input type="text" class="input" value="<?= $pessoa->calcularIdade() ?>" disabled />
                        </div>
                        <div class="acao">
                            <button type="submit" name="remover" class="botao-remover">Confirmar Remocao</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>

        </div>
    </body>
</html>