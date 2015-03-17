<?php
//============================================================+
// File name   : tcpdf_include.php
// Begin       : 2008-05-14
// Last Update : 2013-05-14
//
// Description : Search and include the TCPDF library.
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Search and include the TCPDF library.
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Include the main class.
 * @author Nicola Asuni
 * @since 2013-05-14
 */

// always load alternative config file for examples
if(file_exists('../dyn/tcpdf.inc.php')) {
    require_once('../dyn/tcpdf.inc.php');
}

// Include the main TCPDF library (search the library on the following directories).
$tcpdf_include_dirs = array(
    realpath('../php/tcpdf/tcpdf.php')
    );
foreach ($tcpdf_include_dirs as $tcpdf_include_path) {
    if (@file_exists($tcpdf_include_path)) {
        require_once($tcpdf_include_path);
        break;
    }
}

//============================================================+
// END OF FILE
//============================================================+

?>
