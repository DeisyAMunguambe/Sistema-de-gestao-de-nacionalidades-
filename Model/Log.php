<?php

class Log {
    private $codigoLog;
    private $usuario;
    private $perfil;
    private $accao;
    private $dataAccao;

    public function __construct($codigoLog, $usuario, $perfil, $accao, $dataAccao){
        $this->codigoLog = $codigoLog;
        $this->usuario = $usuario;
        $this->perfil = $perfil;
        $this->accao = $accao;
        $this->dataAccao = $dataAccao;
    }

    public function getCodigoLog(){ return $this->codigoLog; }
    public function setCodigoLog($codigoLog){ $this->codigoLog = $codigoLog; }
    public function getUsuario(){ return $this->usuario; }
    public function setUsuario($usuario){ $this->usuario = $usuario; }
    public function getPerfil(){ return $this->perfil; }
    public function setPerfil($perfil){ $this->perfil = $perfil; }
    public function getAccao(){ return $this->accao; }
    public function setAccao($accao){ $this->accao = $accao; }
    public function getDataAccao(){ return $this->dataAccao; }
    public function setDataAccao($dataAccao){ $this->dataAccao = $dataAccao; }
}

?>