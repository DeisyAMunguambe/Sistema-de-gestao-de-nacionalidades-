<?php
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerPerfil.php");
    verificarLogin();
    // ALTERADO: a pagina de perfis exige a permissao "perfis" (antes "utilizadores")
    exigirPermissaoAutomatica("perfis");
    // CORRIGIDO: nome diferente para não ser sobrescrito mais abaixo
    $perfilSessao = $sessao->nivelAcesso;
    $nomeCompleto = $sessao->nome . " " . $sessao->apelido;

    // NOVO: permissoes lidas da tabela perfil (funciona tambem com perfis novos)
    $pVer          = $sessao->podeFazer('ver');
    $pCriar        = $sessao->podeFazer('criar');
    $pEditar       = $sessao->podeFazer('editar');
    $pRemover      = $sessao->podeFazer('remover');
    $pUtilizadores = $sessao->podeFazer('utilizadores');
    $pLogs         = $sessao->podeFazer('logs');
    $pPerfis       = $sessao->podeFazer('perfis');

    // lista das permissoes: chave => texto que aparece no ecra
    $permissoes = array(
        'ver'          => 'Ver / listar registos',
        'criar'        => 'Criar registos',
        'editar'       => 'Editar registos',
        'remover'      => 'Remover registos',
        'utilizadores' => 'Gerir utilizadores',
        'logs'         => 'Ver logs',
        'perfis'       => 'Gerir perfis'
    );

    // devolve 1 ou 0 conforme a permissao do perfil
    function valorPermissao($perfil, $chave) {
        if ($chave == 'ver')          return $perfil->getVer();
        if ($chave == 'criar')        return $perfil->getCriar();
        if ($chave == 'editar')       return $perfil->getEditar();
        if ($chave == 'remover')      return $perfil->getRemover();
        if ($chave == 'utilizadores') return $perfil->getUtilizadores();
        if ($chave == 'logs')         return $perfil->getLogs();
        if ($chave == 'perfis')       return $perfil->getPerfis();
        return 0;
    }

    // checkbox marcada vem no POST, desmarcada nao vem
    function lerPermissao($chave) {
        return isset($_POST['perm_' . $chave]) ? 1 : 0;
    }

    // desenha as checkboxes (se $perfil for null, vem tudo desmarcado)
    function mostrarCheckboxes($permissoes, $perfil) {
        echo '<div class="permissoes">';
        foreach ($permissoes as $chave => $texto) {
            $marcado = '';
            if ($perfil != null && valorPermissao($perfil, $chave) == 1) {
                $marcado = 'checked';
            }
            echo '<label class="permissao">';
            echo '<input type="checkbox" name="perm_' . $chave . '" value="1" ' . $marcado . ' />';
            echo '<span>' . $texto . '</span>';
            echo '</label>';
        }
        echo '</div>';
    }

    if (isset($_POST['remover']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
        removerPerfil($_POST['id']);
    }
    if (isset($_POST['gravar']) && !empty($_POST['nome'])) {
        $perfil = new Perfil(null, $_POST['nome'], $_POST['descricao'],
                             lerPermissao('ver'), lerPermissao('criar'), lerPermissao('editar'),
                             lerPermissao('remover'), lerPermissao('utilizadores'),
                             lerPermissao('logs'), lerPermissao('perfis'));
        criarPerfil($perfil);
    }
    if (isset($_POST['actualizar']) && !empty($_POST['id']) && is_numeric($_POST['id'])) {
        $perfilExistente = encontrarPerfil($_POST['id']);
        if ($perfilExistente) {
            $perfilExistente->setDescricao($_POST['descricaoEditar']);
            $perfilExistente->setVer(lerPermissao('ver'));
            $perfilExistente->setCriar(lerPermissao('criar'));
            $perfilExistente->setEditar(lerPermissao('editar'));
            $perfilExistente->setRemover(lerPermissao('remover'));
            $perfilExistente->setUtilizadores(lerPermissao('utilizadores'));
            $perfilExistente->setLogs(lerPermissao('logs'));
            $perfilExistente->setPerfis(lerPermissao('perfis'));
            editarPerfil($perfilExistente);
        }
    }

    $busca = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : null;
    $perfis = listarPerfis($busca);
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Gestao de Perfis</title>
        <link rel="stylesheet" href="../assets/estilo.css">
        <style>
            .navlinks a.desabilitado {
                opacity: 0.4;
                cursor: not-allowed;
                pointer-events: none;
            }

            .modalCaixa .permissoes {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-top: 8px;
            }

            .modalCaixa .permissoes .permissao {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 14px;
                background: #fff;
                border: 1px solid #e6d5bf;
                border-radius: 12px;
                cursor: pointer;
                font-size: 14px;
            }

            .modalCaixa .permissoes .permissao:hover {
                background: #f6eee3;
                border-color: #6b4435;
            }

            .modalCaixa .permissoes .permissao input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin: 0;
                flex-shrink: 0;
                accent-color: #6b4435;
                cursor: pointer;
            }

            @media (max-width: 520px) {
                .modalCaixa .permissoes {
                    grid-template-columns: 1fr;
                }
            }
        </style>
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
                <?php if ($pVer) { ?><a href="Pais.php"><span class="icone"></span>Pais</a><?php } ?>
                <?php if ($pVer) { ?><a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a><?php } ?>
                <?php if ($pUtilizadores) { ?><a href="Cadastro.php"><span class="icone"></span>Cadastro de utilizadores</a><?php } ?>
                <?php if ($pLogs) { ?><a href="log.php"><span class="icone"></span>Log</a><?php } ?>
                <a href="Perfil.php" class="on"><span class="icone"></span>Perfil</a>
                <?php if ($pVer) { ?><a href="Registo.php"><span class="icone"></span>Registo de Nacionalidade</a><?php } ?>
            </nav>
              <div class="utilizador">
                <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
                <!-- CORRIGIDO: usa $perfilSessao -->
                <span class="perfil"><?= htmlspecialchars($perfilSessao) ?></span>
            </div>
            <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
        </div>

        <div class="mae">
            <div class="cabecalho">
                <h1>Perfis</h1>
                <div class="topo">
                    <form class="busca" method="get" action="Perfil.php">
                        <input type="text" name="pesquisar" placeholder="Pesquisar perfil" value="<?= isset($_GET['pesquisar']) ? htmlspecialchars($_GET['pesquisar']) : '' ?>" />
                        <button type="submit" class="botao-pesquisar"></button>
                    </form>
                    <a class="botao" href="#novo">Novo Perfil</a>
                </div>
            </div>

            <div class="caixa">
                <div class="tabela">
                    <table>
                        <thead>
                            <tr>
                                <th class="coluna-id">ID</th>
                                <th>Perfil</th>
                                <th>Descricao</th>
                                <?php foreach ($permissoes as $chave => $texto) { ?>
                                <th class="coluna-perm"><?= ucfirst($chave) ?></th>
                                <?php } ?>
                                <th class="coluna-acao coluna-acao-perfil">Acção</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($perfis) == 0) { ?>
                            <tr><td colspan="11" class="vazio"><?= $busca ? "Perfil nao encontrado" : "Nenhum perfil registado" ?></td></tr>
                            <?php } else { foreach ($perfis as $perfil) { ?>
                            <tr>
                                <td><?= $perfil->getCodigoPerfil() ?></td>
                                <td><strong><?= htmlspecialchars($perfil->getNomePerfil()) ?></strong></td>
                                <td><?= htmlspecialchars($perfil->getDescricao()) ?></td>
                                <?php foreach ($permissoes as $chave => $texto) { ?>
                                <td class="coluna-perm">
                                    <?php if (valorPermissao($perfil, $chave) == 1) { ?>
                                        <span class="perm-sim">Sim</span>
                                    <?php } else { ?>
                                        <span class="perm-nao">-</span>
                                    <?php } ?>
                                </td>
                                <?php } ?>
                                <td>
                                    <div class="acoes">
                                        <a class="botao-atualizar" href="#editar-<?= $perfil->getCodigoPerfil() ?>">Editar</a>
                                        <!-- BOTAO REMOVER: agora aparece em todos os perfis (sem a condicao > 4) -->
                                        <a class="botao-remover" href="#remover-<?= $perfil->getCodigoPerfil() ?>">Remover</a>
                                    </div>
                                </td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL: novo perfil -->
            <div class="modalFundo" id="novo">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa larga">
                    <div class="modalTopo">
                        <h2 class="titulo">Novo Perfil</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Perfil.php">
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome do perfil</strong></label></div>
                            <input type="text" name="nome" class="input" required />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Descricao</strong></label></div>
                            <input type="text" name="descricao" class="input" />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>O que este perfil pode fazer</strong></label></div>
                            <?php mostrarCheckboxes($permissoes, null); ?>
                        </div>
                        <div class="acao">
                            <button type="submit" name="gravar" class="botao">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: editar permissoes do perfil -->
            <?php foreach ($perfis as $perfil) { ?>
            <div class="modalFundo" id="editar-<?= $perfil->getCodigoPerfil() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa larga">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Perfil</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Perfil.php">
                        <input type="hidden" name="id" value="<?= $perfil->getCodigoPerfil() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome do perfil</strong></label></div>
                            <input type="text" class="input" value="<?= htmlspecialchars($perfil->getNomePerfil()) ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Descricao</strong></label></div>
                            <input type="text" name="descricaoEditar" class="input" value="<?= htmlspecialchars($perfil->getDescricao()) ?>" />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>O que este perfil pode fazer</strong></label></div>
                            <?php mostrarCheckboxes($permissoes, $perfil); ?>
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao" name="actualizar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>

            <!-- MODAL: confirmar remocao do perfil (agora existe para todos os perfis) -->
            <?php foreach ($perfis as $perfil) { ?>
            <div class="modalFundo" id="remover-<?= $perfil->getCodigoPerfil() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Perfil</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Perfil.php">
                        <input type="hidden" name="id" value="<?= $perfil->getCodigoPerfil() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Perfil</strong></label></div>
                            <input type="text" class="input" value="<?= htmlspecialchars($perfil->getNomePerfil()) ?>" disabled />
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