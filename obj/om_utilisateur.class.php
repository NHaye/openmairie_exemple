<?php
//$Id$ 
//gen openMairie le 06/03/2015 14:03

require_once "../core/obj/om_utilisateur.class.php";

class om_utilisateur extends om_utilisateur_core {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

}

?>
