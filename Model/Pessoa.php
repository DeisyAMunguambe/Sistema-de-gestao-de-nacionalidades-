<?php

class Pessoa {
    private $codigo;
    private $nome;
    private $apelido;
    private $genero;
    private $estadoCivil;
    private $anoNascimento;

    public function __construct($codigo, $nome, $apelido, $genero, $estadoCivil, $anoNascimento){
        $this -> codigo=$codigo;
        $this -> nome=$nome;
        $this -> apelido=$apelido;
        $this -> genero=$genero;
        $this -> estadoCivil=$estadoCivil;
        $this -> anoNascimento=$anoNascimento;
    }

    public function getCodigo(){ return $this->codigo; }
    public function setCodigo($codigo){ $this->codigo = $codigo; }
    public function getNome(){ return $this->nome; }
    public function setNome($nome){ $this->nome = $nome; }
    public function getApelido(){ return $this->apelido; }
    public function setApelido($apelido){ $this->apelido = $apelido; }
    public function getGenero(){ return $this->genero; }
    public function setGenero($genero){ $this->genero = $genero; }
    public function getEstadoCivil(){ return $this->estadoCivil; }
    public function setEstadoCivil($estadoCivil){ $this->estadoCivil = $estadoCivil; }
    public function getAnoNascimento(){ return $this->anoNascimento; }
    public function setAnoNascimento($anoNascimento){ $this->anoNascimento = $anoNascimento; }

    public function getAnoActual(){
        return date('Y');
    }

    public function calcularIdade(){
        return $this->getAnoActual() - $this->anoNascimento;
    }

    public function toString(){
        return "Codigo: {$this->codigo} Nome: {$this->nome} Apelido: {$this->apelido} 
        Genero: {$this->genero} Estado Civil: {$this->estadoCivil} 
        Ano Nascimento: {$this->anoNascimento} Idade: {$this->calcularIdade()}";
    }
}

?>