<?php
//$Id$ 
//gen openMairie le 06/03/2015 12:47

require_once "../core/obj/om_collectivite.class.php";

class om_collectivite extends om_collectivite_core {

    function __construct($id, &$dnu1 = null, $dnu2 = null) {
        $this->constructeur($id);
    }

}

?>
