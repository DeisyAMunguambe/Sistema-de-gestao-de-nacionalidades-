<?php
    include_once(__DIR__ . "/../Controller/controllerCadastroUser.php");
    // ALTERADO: inclui o controller de perfis para podermos usar listarPerfis()
    include_once(__DIR__ . "/../Controller/controllerPerfil.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
exigirPermissaoAutomatica("utilizadores");
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
    header("Location: Cadastro.php");
    exit;
}
if (isset($_POST['gravar']) && !empty($_POST['nome'])) {
    $utilizador = new CadastroUser(null, $_POST['nome'], $_POST['apelido'], $_POST['contacto'],
     $_POST['email'], $_POST['username'], $_POST['senha'], $_POST['nivelAcesso']);
    if (criar($utilizador)) {
        header("Location: Cadastro.php");
        exit;
    }
}
if (isset($_POST['actualizar']) && !empty($_POST['id']) && !empty($_POST['nomeEditar'])) {
    $userExistente = encontrar($_POST['id']);
    if ($userExistente) {
        $userExistente->setNome($_POST['nomeEditar']);
        $userExistente->setApelido($_POST['apelidoEditar']);
        $userExistente->setContacto($_POST['contactoEditar']);
        $userExistente->setEmail($_POST['emailEditar']);
        $userExistente->setUserName($_POST['usernameEditar']);
        $userExistente->setNivelAcesso($_POST['nivelAcessoEditar']);
        editar($userExistente);
        header("Location: Cadastro.php");
        exit;
    }
}
if (isset($_POST['resetSenha'])) {
    resetSenha($_POST['id']);
}

$utilizadores = listar();
$listaPerfis = listarPerfis();

?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Listar Utilizador</title>
        
        <link rel="stylesheet" href="../assets/estilo.css">
    </head>

    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE Utilizador</div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
                <?php if ($pVer) { ?><a href="Pessoa.php"><span class="icone"></span>Pessoa</a><?php } ?>
                <?php if ($pVer) { ?><a href="Continente.php"><span class="icone"></span>Continente</a><?php } ?>
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
        <h1>Utilizadores</h1>
        <div class="topo">
            <form class="busca" method="get" action="">
                <input type="text" name="q" placeholder="Pesquisar utilizador" />
            </form>
            <a class="botao" href="#novo">Novo Utilizador</a>
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
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Perfil</th>
                        <th class="coluna-acao">Acção</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($utilizadores) == 0) { ?>
            <tr><td colspan="8" class="vazio">Nenhum utilizador registado</td></tr>
            <?php } else { foreach ($utilizadores as $utilizador) { ?>
                    <tr>
                        <td><?= $utilizador->getCodigo() ?></td>
                        <td><?= $utilizador->getNome() ?></td>
                        <td><?= $utilizador->getApelido() ?></td>
                        <td><?= $utilizador->getContacto() ?></td>
                        <td><?= $utilizador->getEmail() ?></td>
                        <td><?= $utilizador->getUserName() ?></td>
                        <td><?= $utilizador->getNivelAcesso() ?></td>
                        <td>
                            <div class="acoes">
                                <a class="botao-atualizar" href="#editar-<?= $utilizador->getCodigo() ?>">Editar</a>
                                <a class="botao-atualizar" href="#ResetSenha<?= $utilizador->getCodigo() ?>">Reset Senha</a>
                                <a class="botao-remover" href="#remover-<?= $utilizador->getCodigo() ?>">Remover</a>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: novo utilizador -->
        <div class="modalFundo" id="novo">
<a href="#" class="modalOverlay" aria-label="Fechar"></a>
<div class="modalCaixa">
    <div class="modalTopo">
        <h2 class="titulo">Novo Utilizador</h2>
        <a href="#" class="modalFechar">x</a>
    </div>
    <form method="post" action="Cadastro.php">
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Nome</strong></label></div>
            <input type="text" name="nome" class="input" />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Apelido</strong></label></div>
            <input type="text" name="apelido" class="input" />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Contacto</strong></label></div>
            <input type="text" name="contacto" class="input" />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Email</strong></label></div>
            <input type="email" name="email" class="input" />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Username</strong></label></div>
            <input type="text" name="username" class="input" />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Senha</strong></label></div>
            <input type="text" name="senha" value="Pessoa2026#" id="senhaNova" class="input" readonly />
        </div>
        <div class="campo">
            <div class="campoTipo"><label class="label"><strong>Perfil</strong></label></div>
            <select name="nivelAcesso" class="input" required>
                <option value="">Seleccione...</option>
                <?php /* ALTERADO: opcoes geradas a partir da tabela perfil (antes eram fixas) */ ?>
                <?php foreach ($listaPerfis as $p) { ?>
                <option value="<?= htmlspecialchars($p->getNomePerfil()) ?>"><?= htmlspecialchars($p->getNomePerfil()) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="acao">
            <button type="submit" name="gravar" class="botao">Guardar</button>
        </div>
        </form>
    </div>
</div>
    

    <!-- MODAL: editar dados do utilizador -->
        <?php foreach ($utilizadores as $utilizador ) { ?>
        
    <div class="modalFundo" id="editar-<?= $utilizador->getCodigo() ?>">
        <a href="#" class="modalOverlay" aria-label="Fechar"></a>
        <div class="modalCaixa">
            <div class="modalTopo">
                <h2 class="titulo">Editar Utilizador</h2>
                <a href="#" class="modalFechar">x</a>
            </div>

            <form method="post" action="Cadastro.php">
                <input type="hidden" name="id" value="<?= $utilizador->getCodigo(); ?>" />
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Nome</strong></label></div>
                    <input type="text" name="nomeEditar" class="input" value="<?= $utilizador->getNome() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Apelido</strong></label></div>
                    <input type="text" name="apelidoEditar" class="input" value="<?= $utilizador->getApelido() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Contacto</strong></label></div>
                    <input type="text" name="contactoEditar" class="input" value="<?= $utilizador->getContacto() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Email</strong></label></div>
                    <input type="email" name="emailEditar" class="input" value="<?= $utilizador->getEmail() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Username</strong></label></div>
                    <input type="text" name="usernameEditar" class="input" value="<?= $utilizador->getUserName() ?>" />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Perfil</strong></label></div>
                    <select name="nivelAcessoEditar" class="input" required>
                         <option value="">Seleccione...</option>
                         <?php /* ALTERADO: opcoes geradas a partir da tabela perfil; "selected" marca o perfil actual do utilizador */ ?>
                         <?php foreach ($listaPerfis as $p) { ?>
                         <option value="<?= htmlspecialchars($p->getNomePerfil()) ?>" <?= ($p->getNomePerfil() == $utilizador->getNivelAcesso()) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p->getNomePerfil()) ?></option>
                         <?php } ?>
                    </select>
                </div>
                <div class="acao">
                    <button type="submit" class="botao" name="actualizar">Guardar</button>
                </div>
            </form>

        </div>
    </div>
    <?php } ?>
    <!-- MODAL: confirmar remocao do utilizador -->
    <?php foreach ($utilizadores as $utilizador ) { ?>
    <div class="modalFundo" id="remover-<?= $utilizador->getCodigo() ?>">
        <a href="#" class="modalOverlay" aria-label="Fechar"></a>
        <div class="modalCaixa">
            <div class="modalTopo">
                <h2 class="titulo">Remover Utilizador</h2>
                <a href="#" class="modalFechar">x</a>
            </div>

            <form method="post" action="Cadastro.php" >

                <input type="hidden" name="id" value="<?= $utilizador->getCodigo() ?>" />
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>ID</strong></label></div>
                    <input type="text" class="input" value="<?= $utilizador->getCodigo() ?>" disabled />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Nome</strong></label></div>
                    <input type="text" class="input" value="<?= $utilizador->getNome() . ' ' . $utilizador->getApelido() ?>" disabled />
                </div>
                <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Username</strong></label></div>
                    <input type="text" class="input" value="<?= $utilizador->getUserName() ?>" disabled />
                </div>
                 <div class="campo">
                    <div class="campoTipo"><label class="label"><strong>Email</strong></label></div>
                    <input type="email" name="emailEditar" class="input" value="<?= $utilizador->getEmail() ?>" disabled/>
                </div>
                <div class="acao">
                    <button type="submit" class="botao-remover" name="remover">Confirmar Remocao</button>
                </div>
            </form>
        </div>
    </div>
    <?php } ?>

    <!-- MODAL: confirmar reset da senha -->
<?php foreach ($utilizadores as $utilizador) { ?>

<div class="modalFundo" id="ResetSenha<?= $utilizador->getCodigo() ?>">

    <a href="#" class="modalOverlay" aria-label="Fechar"></a>

    <div class="modalCaixa">
        <div class="modalTopo">
            <h2 class="titulo">Resetar Senha</h2>
            <a href="#" class="modalFechar">x</a>
        </div>

        <form method="post" action="Cadastro.php">
            <input type="hidden" name="id" value="<?= $utilizador->getCodigo() ?>">
            <div class="campo">
                <div class="campoTipo">
                    <label class="label"> <strong>Utilizador</strong></label>
                </div>
                <input type="text" class="input" value="<?= $utilizador->getNome() . ' ' . $utilizador->getApelido() ?>"disabled>
            </div>
            <div class="campo">
                <div class="campoTipo"> <label class="label"> <strong>Nova senha</strong> </label></div>
                <input type="text"class="input" value="Pessoa2026#"readonly>
            </div>
            <p style="text-align:center;">Tem certeza que deseja resetar a senha?</p>

            <div class="acao">
                <button type="submit" name="resetSenha"class="botao"> Confirmar </button>
            </div>

        </form>

    </div>
</div>

<?php } ?>
</div>     
    </body>
</html>