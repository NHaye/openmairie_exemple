<?php
//$Id: om_etat.class.php 3049 2015-02-13 16:54:11Z fmichon $ 
//gen openMairie le 13/02/2015 17:31

require_once "../core/obj/om_etat.class.php";

class om_etat extends om_etat_core {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

}

?>
