<?php
/**
 * Formulaire permettant d'effectuer une demande de reinitialisation
 * de mot de passe.
 *
 * @package openmairie_exemple
 * @version SVN : $Id: password_reset.php 2225 2013-04-02 16:18:34Z fmichon $
 */

require_once "../obj/utils.class.php";
$f = new utils("password_reset", NULL, _("Redefinition du mot de passe"));

$coll = null;
$user_login = null;
$next_action = "display_login_form"; 

if (!empty($_POST)) {
    
    // Si la valeur du champ coll dans le formulaire de login est definie
    if (isset($_POST['coll'])) {
        // On ajoute en variable de session la cle du tableau associatif de
        // configuration de base de donnees a laquelle l'utilisateur
        // souhaite se connecter
        $_SESSION['coll'] = $_POST['coll'];
        // Debug
        $f->addToLog("login(): \$_SESSION['coll']=\"".$_SESSION['coll']."\"", EXTRA_VERBOSE_MODE);
    }
    
    $f->connectDatabase();
    $f->deleteExpiredKey();
}

// traitement de la demande de redefinition
if (isset($_POST['resetpwd_action_sendmail']) && !isset($_GET['key'])) {
    
    $valid_post = true;
    $login = addslashes($_POST['login']);
    
    // validation du login
    if ($login == "") {
        $valid_post = false;
        $f->addToMessage("error", _("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
    }
    
    // traitement ...
    if ($valid_post == true) {
        $mode = $f->retrieveUserAuthenticationMode($login);
        
        // cas : login non trouve en base
        if ($mode == false) {
            $f->addToMessage("error", _("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
        
        // cas : login correct et mode == "db"
        } elseif (strtolower($mode) == "db") {
            
            $sended = false;
            $user_infos = $f->retrieveUserProfile($login);
            
            if (isset($user_infos['email']) and !empty($user_infos['email'])) {
                $hash = $f->genPasswordResetKey();
            
                $key_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://":"http://").$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?key=".$hash."&coll=".(isset($_POST['coll']) ? $_POST['coll'] : "");
    
                // timeout 60 minutes
                $timestamp = time() + 3600; 
                $timeout = date("YmdHis", $timestamp);
                
                // compose mail
                $mail_title  = _("Confirmation de reinitialisation du mot de passe ".$f->config['application']);
                $mail_recipient = $user_infos['email'];
                
                $mail_content = _("Vous avez demande la reinitialisation de votre mot de passe ".$f->config['application']." pour l'identifiant")." : ".$login."<br>";
                $mail_content .= _("Pour finaliser votre demande, veuillez cliquer sur ce lien")." : ";
                $mail_content .= "<br><br><strong><a href=\"".$key_url."\" >".$key_url."</strong></a>";
                $mail_content .= "<br><br>Pour des raisons de securite, le lien ci-dessus expire dans un delai de 1 heure.";
    
                $sended = $f->sendMail($mail_title, $mail_content, $mail_recipient);
            }
            
            if ($sended) {
                $f->addPasswordResetKey($login, $hash, $timeout);
                $f->addToMessage("valid", _("Un message de demande de reinitialisation de mot de passe vous a ete envoye sur votre messagerie."));
                $next_action = null;
            } else {
                $f->addToMessage("error", "Erreur lors de l'envoi par email. Veuillez contacter votre administrateur.");
            }
        
        // cas : login correct et mode != "db"
        } else {
            $f->addToMessage("error", _("Votre identifiant est incorrect, ou ne vous permet pas de redefinir votre mot de passe de cette maniere. Contactez votre administrateur."));
        }
    }
    
} elseif (isset($_POST['resetpwd_action_newpwd']) && !isset($_GET['key'])) {
    
    $user_login = addslashes($_POST['user_login']);
    
    if (empty($_POST['pwd_one']) or empty($_POST['pwd_two'])) {
        
        $f->addToMessage("error", "Veuillez remplir les deux champs mot de passe.");
        $coll = $_POST['coll'];
        $next_action = "display_password_form";
        
    } elseif($_POST['pwd_one'] == $_POST['pwd_two']) {
        $f->changeDatabaseUserPassword(addslashes($_POST['user_login']), $_POST['pwd_one']);
        $f->deletePasswordResetKeys($user_login);
        $f->addToMessage("valid", "Le nouveau mot de passe a bien ete enregistre. Vous pouvez desormais vous connecter avec ce mot de passe.");
        $next_action = null;
    } else {
        $f->addToMessage("error", "Les deux mots de passe ne sont pas identiques.");
        $coll = $_POST['coll'];
        $next_action = "display_password_form";
    }
    
} elseif(isset($_GET['key']) and isset($_GET['coll']) ) {
    
    $_SESSION['coll'] = $_GET['coll'];
    $f->connectDatabase();
    
    $login =  $f->passwordResetKeyExists(addslashes($_GET['key']));
    
    if ($login != false) {
        $coll = $_SESSION['coll'];
        $user_login = $login;
        $next_action = "display_password_form";
    } else {
        $next_action = "display_login_form";
    }
}

$f->displayMessages();

if ($next_action == "display_login_form") { 
    $f->displayPasswordResetLoginForm();
} elseif($next_action == "display_password_form") {
    $f->displayPasswordResetPasswordForm($coll, $user_login);
}

?>

