<?php

include_once(__DIR__ . "/../Model/CadastroUser.php");
include_once(__DIR__ . "/controllerLog.php");

class Sessao
{
    private $logado = false;
    public $codigo;
    public $email;
    public $nome;
    public $apelido;
    public $username;
    public $nivelAcesso;

    function __construct()
    {
        session_start();
        $this->verificar_login();
    }

    private function verificar_login()
    {
        if (isset($_SESSION['codigo']) && isset($_SESSION['email'])) {
            $this->codigo = $_SESSION['codigo'];
            $this->email = $_SESSION['email'];
            $this->nome = $_SESSION['nome'];
            $this->apelido = $_SESSION['apelido'];
            $this->username = $_SESSION['username'];
            $this->nivelAcesso = $_SESSION['nivelAcesso'];
            $this->logado = true;
        } else {
            unset($this->codigo);
            unset($this->email);
            unset($this->nome);
            unset($this->apelido);
            unset($this->username);
            unset($this->nivelAcesso);

            $this->logado = false;
        }
    }

    public function esta_logado()
    {
        return $this->logado;
    }

    public function login($utilizador)
    {
        if ($utilizador) {
            session_regenerate_id(true);

            $_SESSION['codigo'] = $utilizador['CodigoUtilizador'];
            $this->codigo = $_SESSION['codigo'];

            $_SESSION['email'] = $utilizador['email'];
            $this->email = $_SESSION['email'];
            $_SESSION['nome'] = $utilizador['nome'];
            $this->nome = $_SESSION['nome'];
            $_SESSION['apelido'] = $utilizador['apelido'];
            $this->apelido = $_SESSION['apelido'];
            $_SESSION['username'] = $utilizador['username'];
            $this->username = $_SESSION['username'];
            $_SESSION['nivelAcesso'] = $utilizador['nivelAcesso'];
            $this->nivelAcesso = $_SESSION['nivelAcesso'];
            $this->logado = true;
        }
    }

    public function logout()
    {
        unset($_SESSION['codigo']);
        unset($this->codigo);
        unset($_SESSION['email']);
        unset($this->email);
        unset($_SESSION['nome']);
        unset($this->nome);
        unset($_SESSION['apelido']);
        unset($this->apelido);
        unset($_SESSION['username']);
        unset($this->username);
        unset($_SESSION['nivelAcesso']);
        unset($this->nivelAcesso);
        $this->logado = false;
    }

    public function podeFazer($acao)
    {
        global $conexao;
        if (!$this->esta_logado()) {
            return false;
        }

        $colunas = array(
            'ver' => 'ver',
            'criar' => 'criar',
            'editar' => 'editar',
            'remover' => 'remover',
            'utilizadores' => 'utilizadores',
            'logs' => 'logs',
            'perfis' => 'perfis'
        );

        if (!isset($colunas[$acao])) {
            return false;
        }

        $nivel = mysqli_real_escape_string($conexao, $this->nivelAcesso);
        $coluna = $colunas[$acao];
        $sql = "SELECT $coluna
                FROM perfil
                WHERE nomePerfil = '$nivel'";
        $resultado = mysqli_query($conexao, $sql);
        if (!$resultado || mysqli_num_rows($resultado) == 0) {
            return false;
        }
        $perfil = mysqli_fetch_assoc($resultado);
        return isset($perfil[$coluna])
            && $perfil[$coluna] == 1;
    }

    public function exigirPermissao($acao)
    {
        global $sessao;
        verificarLogin();
        if (!$this->podeFazer($acao)) {
            header("Location: ../View/TelaPrincipal.php?erro=semPermissao");
            exit();
        }
    }
}

$sessao = new Sessao();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");
if (!$conexao) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

function login($identificador, $senha)
{
    global $conexao, $sessao;
    $sql = "SELECT *
            FROM cadastro
            WHERE username='$identificador'
            OR email='$identificador'";
    $result = mysqli_query($conexao, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $utilizador = mysqli_fetch_assoc($result);
        if (password_verify($senha, $utilizador['senha'])) {
            $sessao->login($utilizador);
            registarAccao("Log in");
            header("Location: ../View/TelaPrincipal.php");
            exit();
        } else {
            echo "Senha incorrecta";
        }
    } else {
        echo "Username ou email não encontrado";
    }
}

function verificarLogin()
{
    global $sessao;

    if (!$sessao->esta_logado()) {
        header("Location: ../View/Login.php");
        exit();
    }
}

function exigirPermissaoAutomatica($acao)
{
    global $sessao;

    $sessao->exigirPermissao($acao);
}
?>