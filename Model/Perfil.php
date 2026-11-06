<?php

class Perfil {
    private $codigoPerfil;
    private $nomePerfil;
    private $descricao;
    private $ver;
    private $criar;
    private $editar;
    private $remover;
    private $utilizadores;
    private $logs;
    private $perfis;

    public function __construct($codigoPerfil, $nomePerfil, $descricao, $ver,
     $criar, $editar, $remover, $utilizadores, $logs, $perfis) {
        $this->codigoPerfil = $codigoPerfil;
        $this->nomePerfil = $nomePerfil;
        $this->descricao  = $descricao;
        $this->ver = $ver;
        $this->criar  = $criar;
        $this->editar = $editar;
        $this->remover  = $remover;
        $this->utilizadores = $utilizadores;
        $this->logs  = $logs;
        $this->perfis   = $perfis;
    }

    public function getCodigoPerfil() { return $this->codigoPerfil; }
    public function setCodigoPerfil($codigoPerfil) { $this->codigoPerfil = $codigoPerfil; }
    public function getNomePerfil() { return $this->nomePerfil; }
    public function setNomePerfil($nomePerfil) { $this->nomePerfil = $nomePerfil; }
    public function getDescricao() { return $this->descricao; }
    public function setDescricao($descricao) { $this->descricao = $descricao; }
    public function getVer() { return $this->ver; }
    public function setVer($ver) { $this->ver = $ver; }
    public function getCriar() { return $this->criar; }
    public function setCriar($criar) { $this->criar = $criar; }
    public function getEditar() { return $this->editar; }
    public function setEditar($editar) { $this->editar = $editar; }
    public function getRemover() { return $this->remover; }
    public function setRemover($remover) { $this->remover = $remover; }
    public function getUtilizadores() { return $this->utilizadores; }
    public function setUtilizadores($utilizadores) { $this->utilizadores = $utilizadores; }
    public function getLogs() { return $this->logs; }
    public function setLogs($logs) { $this->logs = $logs; }
    public function getPerfis() { return $this->perfis; }
    public function setPerfis($perfis) { $this->perfis = $perfis; }
}
?>