<?php
include_once(__DIR__ . "/../Controller/controllerRegisto.php");
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

$erroNovo = "";      // erro mostrado dentro do formulario de novo registo
$erroEditar = "";    // erro mostrado dentro do formulario de editar
$idEditado = "";     // qual registo estava a ser editado quando deu erro

// ---------- REMOVER ----------
if (isset($_POST['remover'])) {
    removerRegisto($_POST['id']);
    header("Location: Registo.php#lista");
    exit;
}

// ---------- NOVO REGISTO ----------
if (isset($_POST['gravar'])) {
    if (!empty($_POST['idcontinente']) && !empty($_POST['idpais']) && !empty($_POST['idnacionalidade'])
        && !empty($_POST['idpessoa']) && !empty($_POST['dataRegisto']) && !empty($_POST['motivo'])) {
        $registo = new RegistoNacionalidade(null, $_POST['idcontinente'], $_POST['idpais'], $_POST['idnacionalidade'],
                                            $_POST['idpessoa'], $_POST['dataRegisto'], $_POST['motivo']);
        $resultado = criarRegisto($registo);
        if ($resultado === true) {
            header("Location: Registo.php#lista");
            exit;
        }
        $erroNovo = $resultado;
    } else {
        $erroNovo = "Preencha todos os campos obrigatórios.";
    }
}

// ---------- EDITAR REGISTO ----------
if (isset($_POST['actualizar'])) {
    $idEditado = $_POST['id'] ?? "";
    if (!empty($_POST['id']) && !empty($_POST['idcontinenteEditar']) && !empty($_POST['idpaisEditar'])
        && !empty($_POST['idnacionalidadeEditar']) && !empty($_POST['idpessoaEditar'])
        && !empty($_POST['dataRegistoEditar']) && !empty($_POST['motivoEditar'])) {
        $registo = new RegistoNacionalidade($_POST['id'], $_POST['idcontinenteEditar'], $_POST['idpaisEditar'],
                                            $_POST['idnacionalidadeEditar'], $_POST['idpessoaEditar'],
                                            $_POST['dataRegistoEditar'], $_POST['motivoEditar']);
        $resultado = editarRegisto($registo);
        if ($resultado === true) {
            header("Location: Registo.php#lista");
            exit;
        }
        $erroEditar = $resultado;
    } else {
        $erroEditar = "Preencha todos os campos obrigatórios.";
    }
}

// ---------- PESQUISAR E LISTAR ----------
$pesquisa = $_GET['pesquisa'] ?? "";
$registos = pesquisarRegistos($pesquisa);
$pessoas = registoListarPessoas();
$continentes = registoListarContinentes();
$paises = registoListarPaises();
$nacionalidades = registoListarNacionalidades();
$motivos = listarMotivos();

// valores do formulario "novo": mantem o que a pessoa escolheu quando
// carrega em "Filtrar" ou quando deu erro ao gravar
$mantemNovo = isset($_POST['filtrar']) || $erroNovo != "";
$novoContinente = $mantemNovo ? ($_POST['idcontinente'] ?? "") : "";
$novoPais = $mantemNovo ? ($_POST['idpais'] ?? "") : "";
$novaNacionalidade = $mantemNovo ? ($_POST['idnacionalidade'] ?? "") : "";
$novaPessoa = $mantemNovo ? ($_POST['idpessoa'] ?? "") : "";
$novaData = $mantemNovo ? ($_POST['dataRegisto'] ?? "") : "";
$novoMotivo = $mantemNovo ? ($_POST['motivo'] ?? "") : "";
// o pais so fica escolhido se for do continente, e a nacionalidade se for do pais
if (!registoPaisDoContinente($paises, $novoPais, $novoContinente)) { $novoPais = ""; }
if (!registoNacionalidadeDoPais($nacionalidades, $novaNacionalidade, $novoPais)) { $novaNacionalidade = ""; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registo de Nacionalidade</title>
    <link rel="stylesheet" href="../assets/estilo.css">
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
            <?php if ($pVer) { ?><a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a><?php } ?>
            <?php if ($pVer) { ?><a href="Registo.php" class="on"><span class="icone"></span>Registo de Nacionalidade</a><?php } ?>
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
            <h1>Registo de Nacionalidade</h1>
            <div class="topo">
                <form class="busca" method="get" action="Registo.php">
                    <input type="text" name="pesquisa" placeholder="Pesquisar registo" value="<?= ($pesquisa) ?>">
                    <button type="submit" class="botao-pesquisar"></button>
                </form>
                <?php if ($pCriar) { ?><a class="botao" href="#novo">Novo Registo</a><?php } ?>
            </div>
        </div>
        <!-- TABELA DE REGISTOS -->
        <div class="caixa" id="lista">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Pessoa</th>
                            <th>Continente</th>
                            <th>País</th>
                            <th>Nacionalidade</th>
                            <th>Motivo</th>
                            <th>Data de Registo</th>
                            <th class="coluna-acao">Acção</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($registos) == 0) { ?>
                            <tr><td colspan="8" class="vazio"><?= $pesquisa != "" ? "Registo não encontrado" : "Nenhum registo de nacionalidade" ?></td></tr>
                        <?php } else { ?>
                            <?php foreach ($registos as $registo) { ?>
                                <tr>
                                    <td><?= $registo->getCodigoRegisto() ?></td>
                                    <td><?= ($registo->getNomePessoa()) ?></td>
                                    <td><?= ($registo->getNomeContinente()) ?></td>
                                    <td><?= ($registo->getNomePais()) ?></td>
                                    <td><?= ($registo->getNomeNacionalidade()) ?></td>
                                    <td><?= ($registo->getMotivo()) ?></td>
                                    <td><?= ($registo->getDataRegisto()) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <?php if ($pEditar) { ?>
                                                <a href="#editar-<?= $registo->getCodigoRegisto() ?>" class="botao-atualizar">Editar</a>
                                            <?php } ?>
                                            <?php if ($pRemover) { ?>
                                                <a href="#remover-<?= $registo->getCodigoRegisto() ?>" class="botao-remover">Remover</a>
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

        <!-- MODAL: NOVO REGISTO -->
        <!-- os botoes "Filtrar" recarregam a pagina e o PHP mostra so os paises/nacionalidades certos -->
        <div class="modalFundo" id="novo">
            <a href="#" class="modalOverlay" aria-label="Fechar"></a>
            <div class="modalCaixa">
                <div class="modalTopo">
                    <h2 class="titulo">Novo Registo de Nacionalidade</h2>
                    <a href="#" class="modalFechar">x</a>
                </div>
                <form method="post" action="Registo.php#novo">
                    <?php if ($erroNovo != "") { ?>
                        <p class="erro"><?= ($erroNovo) ?></p>
                    <?php } ?>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                        <select name="idcontinente" class="input" required>
                            <option value="">Selecione um continente</option>
                            <?php foreach ($continentes as $c) { ?>
                                <option value="<?= $c['codigoContinente'] ?>" <?= $c['codigoContinente'] == $novoContinente ? 'selected' : '' ?>><?= ($c['nomeContinente']) ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="filtrar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>País</strong></label></div>
                        <select name="idpais" class="input" required>
                            <option value="">Selecione um país</option>
                            <?php foreach ($paises as $pais) { ?>
                                <?php if ($pais['idcontinente'] == $novoContinente) { ?>
                                    <option value="<?= $pais['codigoPais'] ?>" <?= $pais['codigoPais'] == $novoPais ? 'selected' : '' ?>><?= ($pais['nomePais']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                        <button type="submit" name="filtrar" class="botao-atualizar" formnovalidate>Filtrar nacionalidades</button>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Nacionalidade</strong></label></div>
                       <select name="idnacionalidade" class="input" required <?= $novoPais == "" ? 'disabled' : '' ?>>
                            <option value="">Selecione uma nacionalidade</option>
                            <?php foreach ($nacionalidades as $nac) { ?>
                                <?php if ($nac['id_pais'] == $novoPais) { ?>
                                    <option value="<?= $nac['id_nacionalidade'] ?>" <?= $nac['id_nacionalidade'] == $novaNacionalidade ? 'selected' : '' ?>><?= ($nac['Nome']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                        <select name="idpessoa" class="input" required>
                            <option value="">Selecione uma pessoa</option>
                            <?php foreach ($pessoas as $pessoa) { ?>
                                <option value="<?= $pessoa['codigo'] ?>" <?= $pessoa['codigo'] == $novaPessoa ? 'selected' : '' ?>><?= ($pessoa['nome'] . " " . $pessoa['apelido']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                        <input type="date" name="dataRegisto" class="input" value="<?= ($novaData) ?>" required>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Motivo</strong></label></div>
                        <select name="motivo" class="input" required>
                            <option value="">Selecione o motivo</option>
                            <?php foreach ($motivos as $m) { ?>
                                <option value="<?= ($m) ?>" <?= $m == $novoMotivo ? 'selected' : '' ?>><?= ($m) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="acao"><button type="submit" name="gravar" class="botao">Guardar</button></div>
                </form>
            </div>
        </div>

        <!-- MODAIS: EDITAR REGISTO -->
        <?php foreach ($registos as $r) {
            // valores que aparecem no formulario: os da base de dados, ou os que a
            // pessoa escolheu se carregou em "Filtrar" ou se este formulario deu erro
            $deuErroAqui = $erroEditar != "" && $idEditado == $r->getCodigoRegisto();
            $filtrouAqui = isset($_POST['filtrarEditar']) && ($_POST['id'] ?? "") == $r->getCodigoRegisto();
            $mantem = $deuErroAqui || $filtrouAqui;
            $editContinente = $mantem ? ($_POST['idcontinenteEditar'] ?? "") : $r->getIdContinente();
            $editPais = $mantem ? ($_POST['idpaisEditar'] ?? "") : $r->getIdPais();
            $editNacionalidade = $mantem ? ($_POST['idnacionalidadeEditar'] ?? "") : $r->getIdNacionalidade();
            $editPessoa = $mantem ? ($_POST['idpessoaEditar'] ?? "") : $r->getIdPessoa();
            $editData = $mantem ? ($_POST['dataRegistoEditar'] ?? "") : $r->getDataRegisto();
            $editMotivo = $mantem ? ($_POST['motivoEditar'] ?? "") : $r->getMotivo();
            if (!registoPaisDoContinente($paises, $editPais, $editContinente)) { $editPais = ""; }
            if (!registoNacionalidadeDoPais($nacionalidades, $editNacionalidade, $editPais)) { $editNacionalidade = ""; }
        ?>
            <div class="modalFundo" id="editar-<?= $r->getCodigoRegisto() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Registo</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Registo.php#editar-<?= $r->getCodigoRegisto() ?>">
                        <input type="hidden" name="id" value="<?= $r->getCodigoRegisto() ?>">
                        <?php if ($deuErroAqui) { ?>
                            <p class="erro"><?= ($erroEditar) ?></p>
                        <?php } ?>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                            <select name="idcontinenteEditar" class="input" required>
                                <option value="">Selecione um continente</option>
                                <?php foreach ($continentes as $c) { ?>
                                    <option value="<?= $c['codigoContinente'] ?>" <?= $c['codigoContinente'] == $editContinente ? 'selected' : '' ?>><?= ($c['nomeContinente']) ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="filtrarEditar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>País</strong></label></div>
                            <select name="idpaisEditar" class="input" required>
                                <option value="">Selecione um país</option>
                                <?php foreach ($paises as $pais) { ?>
                                    <?php if ($pais['idcontinente'] == $editContinente) { ?>
                                        <option value="<?= $pais['codigoPais'] ?>" <?= $pais['codigoPais'] == $editPais ? 'selected' : '' ?>><?= ($pais['nomePais']) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <button type="submit" name="filtrarEditar" class="botao-atualizar" formnovalidate>Filtrar nacionalidades</button>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nacionalidade</strong></label></div>
                            <select name="idnacionalidadeEditar" class="input" required>
                                <option value="">Selecione uma nacionalidade</option>
                                <?php foreach ($nacionalidades as $nac) { ?>
                                    <?php if ($nac['id_pais'] == $editPais) { ?>
                                        <option value="<?= $nac['id_nacionalidade'] ?>" <?= $nac['id_nacionalidade'] == $editNacionalidade ? 'selected' : '' ?>><?= ($nac['Nome']) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <select name="idpessoaEditar" class="input" required>
                                <option value="">Selecione uma pessoa</option>
                                <?php foreach ($pessoas as $pessoa) { ?>
                                    <option value="<?= $pessoa['codigo'] ?>" <?= $pessoa['codigo'] == $editPessoa ? 'selected' : '' ?>><?= ($pessoa['nome'] . " " . $pessoa['apelido']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                            <input type="date" name="dataRegistoEditar" class="input" value="<?= ($editData) ?>" required>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Motivo</strong></label></div>
                            <select name="motivoEditar" class="input" required>
                                <option value="">Selecione o motivo</option>
                                <?php foreach ($motivos as $m) { ?>
                                    <option value="<?= ($m) ?>" <?= $m == $editMotivo ? 'selected' : '' ?>><?= ($m) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="acao"><button type="submit" name="actualizar" class="botao">Guardar</button></div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <!-- MODAIS: REMOVER REGISTO -->
        <?php foreach ($registos as $registo) { ?>
            <div class="modalFundo" id="remover-<?= $registo->getCodigoRegisto() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Registo</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Registo.php">
                        <input type="hidden" name="id" value="<?= $registo->getCodigoRegisto() ?>">
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <input type="text" class="input" value="<?= ($registo->getNomePessoa()) ?>" disabled>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nacionalidade</strong></label></div>
                            <input type="text" class="input" value="<?= ($registo->getNomeNacionalidade() . " (" . $registo->getNomePais() . ")") ?>" disabled>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Motivo</strong></label></div>
                            <input type="text" class="input" value="<?= ($registo->getMotivo()) ?>" disabled>
                        </div>
                        <div class="acao"><button type="submit" class="botao-remover" name="remover">Confirmar Remoção</button></div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>

</body>
</html>