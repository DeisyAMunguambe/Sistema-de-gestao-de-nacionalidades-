<?php
class CadastroUser {
    private $codigo;
    private $nome;
    private $apelido;
    private $contacto;
    private $email;
    private $userName;
    private $nivelAcesso;
    private $senha;

    public function __construct($codigo, $nome, $apelido, $contacto, $email,$userName, $senha, $nivelAcesso){
        $this -> codigo=$codigo;
        $this -> nome=$nome;
        $this -> apelido=$apelido;
        $this -> contacto=$contacto;
        $this -> email=$email;
        $this -> userName=$userName;
        $this -> senha=$senha;
        $this -> nivelAcesso=$nivelAcesso;
    }

    public function getCodigo(){ return $this->codigo; }
    public function setCodigo($codigo){ $this->codigo = $codigo; }
    public function getNome(){ return $this->nome; }
    public function setNome($nome){ $this->nome = $nome; }
    public function getApelido(){ return $this->apelido; }
    public function setApelido($apelido){ $this->apelido = $apelido; }
    public function getContacto(){ return $this->contacto; }
    public function setContacto($contacto){ $this->contacto = $contacto; }
    public function getEmail(){ return $this->email; }
    public function setEmail($email){ $this->email = $email; } 
    public function getUserName(){ return $this->userName; }
    public function setUserName($userName){ $this->userName = $userName; }
     public function getNivelAcesso(){ return $this->nivelAcesso; }
    public function setNivelAcesso($nivelAcesso){ $this->nivelAcesso = $nivelAcesso; }
     public function getSenha(){ return $this->senha; }
    public function setSenha($senha){ $this->senha = $senha; }  
}

?>