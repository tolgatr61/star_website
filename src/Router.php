<?php
require_once 'view/View.php';
require_once 'control/Controller.php';
require_once 'model/star/StarStorage.php';
require_once 'model/star/StarBuilder.php';
require_once 'view/PrivateView.php';
require_once 'model/image/ImageStorage.php';
require_once 'model/cookie/CookieStorage.php';

class Router {

    private $db;
    private $db2;
    private $db3;
    private $db4;

    public function __construct(StarStorage $db, AccountStorage $db2, ImageStorage $db3, CookieStorage $db4) {
        $this->db = $db;
        $this->db2 = $db2;
        $this->db3 = $db3;
        $this->db4 = $db4;
    }

    public function main() {
        // On set avant la session le lifetime du cookie.
        // Puis on débute la session dès l'appel du main pour pouvoir sauvegarder toutes les sessions de compte et de données.
        $this->db4->setCookie();
        session_start();

        // Si le feedback existe alors on le définit, puis on le vide (pour pouvoir en récuperer des nouveaux).
        $feedback = key_exists('feedback', $_SESSION) ? $_SESSION['feedback'] : '';
        $_SESSION['feedback'] = '';
        // Si une session utilisatrice existe alors on définit la vue Privée sinon on définit la Vue public.
        $view = key_exists('user', $_SESSION) ? new PrivateView($this, $feedback, $_SESSION['user']) : new View($this, $feedback);
        $controller = new Controller($view, $this->db, $this->db2, $this->db3, $this->db4);

        try {
            // Si une session d'utilisateur est présente (donc la personne est connecté) et le path est défini.
            if(isset($_SESSION['user']) && isset($_SERVER['PATH_INFO'])) {
                $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
                // On sépare les data du path afin de récuperer chaque champ de celui-ci.
                // La logique ici est que si on a un chemin tel que /id/askDelete on délimite avec le / et on a une array parts avec id et askDelete.
                if ($parts[0] === '') {
                    $view->makeIndexPage();
                }
                // Dans le cas où le premier champ est différent de cela, et qu'il y'a qu'un seul champ alors on affiche forcément une étoile en récuperant l'identifiant dans le champ.
                else if ($parts[0] !== 'liste' && $parts[0] !== 'nouveau' && $parts[0] !== 'sauverNouveau' && $parts[0] !== 'disconnect' && $parts[0] !== 'info' && $parts[0] !== 'admin' && $parts[0] !== 'adminValidation' && count($parts) == 1) {
                    $starId = count($parts) >= 1 ? $parts[0] : null;
                    $controller->showInformation($starId);
                    if ($this->db->verifyAccountId($parts[0]) || $this->db2->read($_SESSION['accountId'])->getStatut() == 'admin') {
                        $controller->showButtonsOnStarPage($starId);
                    }
                } // Si le premier champ est liste, alors on appelle la méthode affichant la liste.
                else if($parts[0] == 'liste') {
                    $controller->showList();
                } // Si le premier champ est disconnect, alors on appelle la méthode de déconnection.
                else if ($parts[0] == 'disconnect') {
                    $controller->disconnect();
                } // Si le premier champ est nouveau
                else if ($parts[0] == 'nouveau') {
                    // On distingue le retour de page par sauverNouveau, en vérifiant qu'il n'y ai aucune données POST dans celui-ci.
                    if ($_SERVER['REQUEST_METHOD'] !='POST') {
                        $controller->newStar();
                    }
                } // Si le premier champ est sauverNouveau.
                else if ($parts[0] == 'sauverNouveau') {
                    // Il faut impérativement une requête en POST pour pouvoir sauvegarder une étoile.
                    if ($_SERVER['REQUEST_METHOD']=='POST') {
                        $controller->saveNewStar($_POST, $_FILES['files']);
                    }
                }
                // Si le premier champ est info on affiche la page à propos.
                else if ($parts[0] == 'info') {
                    $controller->showAProposPage();
                }
                else if ($parts[0] == 'admin') {
                    $controller->showAdminPage($_SESSION['user']);
                }
                else if ($parts[0] == 'adminValidation') {
                    $controller->changeLifetime($_SESSION['user'], $_POST);
                }
                // Si le second champ est askDelete et que le compte correspond à l'étoile en cours ou est un compte disposant du statut admin.
                else if ($parts[1] == 'askDelete' && ($this->db->verifyAccountId($parts[0]) || $this->db2->read($_SESSION['accountId'])->getStatut() == 'admin')) {
                    $starId = $parts[0]; // Si le second champ est askDelete, alors le premier est forcément id.
                    if ($starId === null) { // dans le cas où il n'est pas défini
                        $view->makeUnknownStarPage();
                    } else if ($_SERVER['REQUEST_METHOD'] !='POST') { // Sinon on demande la suppression à l'utilisateur.
                            $controller->askDeleteColor($starId);
                    }
                } // Même logique qu'en haut.
                else if ($parts[1] == 'delete' && ($this->db->verifyAccountId($parts[0]) || $this->db2->read($_SESSION['accountId'])->getStatut() == 'admin')) {
                    $starId = $parts[0];
                    if ($starId === null) {
                        $view->makeUnknownStarPage();
                    } else if ($_SERVER['REQUEST_METHOD'] =='POST'){ // si on a méthode requête POST donc une validation via le bouton alors on peut supprimer la couleur. 
                        $controller->deleteColor($starId);
                    }
                } // Idem qu'en haut sauf qu'ici on le fait pour une mise à jour.
                else if ($parts[1] == 'makeUpdate' && ($this->db->verifyAccountId($parts[0]) || $this->db2->read($_SESSION['accountId'])->getStatut() == 'admin')) {
                    $starId = $parts[0];
                    if ($starId === null) {
                        $view->makeUnknownStarPage();
                    } else if ($_SERVER['REQUEST_METHOD'] !='POST') {
                        $controller->makeUpdatePage($starId);
                    }
                }
                else if ($parts[1] == 'update' && ($this->db->verifyAccountId($parts[0]) || $this->db2->read($_SESSION['accountId'])->getStatut() == 'admin')) {
                    $starId = $parts[0];
                    if ($starId === null) {
                        $view->makeUnknownStarPage();
                    } else if ($_SERVER['REQUEST_METHOD'] =='POST') {
                        $controller->makeUpdate($starId, $_POST, $_FILES['files']);
                    }
                }
            } // Si la session utilisatrice n'est pas définie et qu'on a bien un chemin path.
            else if (!isset($_SESSION['user']) && isset($_SERVER['PATH_INFO'])) {
                $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
                if ($parts[0] === '') {
                    $view->makeIndexPage();
                } // on affichera uniquement les pages vérifiant le premier champ des conditions ci-dessous.
                else if($parts[0] == 'liste') {
                    $controller->showList();
                }
                else if ($parts[0] == 'connexion') {
                    $controller->showLogin();
                }
                else if ($parts[0] == 'connect') {
                    $controller->login();
                }
                else if ($parts[0] == 'info') {
                    $controller->showAProposPage();
                }
                else if ($parts[0] == 'register') {
                    $controller->showRegister();
                }
                else if ($parts[0] == 'registerValidation') {
                    $controller->register($_POST);
                }
                else if (isset($parts[0])) {
                    $controller->makePageAskToLogin();
                }
            }
            else { // dans le cas où on a pas de chemin path, on rammène logiquement vers la page d'accueil.
                $view->makeIndexPage();
            }
        }
        catch (Exception $e) { // Dans le cas d'une exception on affiche une page inattendu.
            $view->makeUnexpectedErrorPage($e);
            }
        $view->render(); // Enfin on appelle le render pour afficher notre Vue dans tous les cas;
    }

    // Méthode pour obtenir l'URL adaptativement en fonction de l'état du PATH.
    public function homePage() {
        // url adaptative en fonction du PATH, si il n'est pas défini alors la page est naturellement derrière.
        if (!isset($_SERVER['PATH_INFO'])) {
            return '.';
        } // idem pour une seule profondeur
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return '.';
        } // 2 retours en arrière seront attendu pour une profondeur de 2.
        else if (count($parts) == 2) {
            return '..';
        }
    }
    // URL d'une étoile.
    public function starPage($id) {
		return "./$id";
    }
    
    // URL adaptative pour créer une étoile.
    public function getStarCreationURL() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./nouveau";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './nouveau';
        }
        else if (count($parts) == 2) {
            return '../nouveau';
        }
    }

    // URL adaptative pour la liste des étoiles.
    public function getAllStars() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./liste";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './liste';
        }
        else if (count($parts) == 2) {
            return '../liste';
        }
    }

    // URL pour sauvegarder une étoile.
    public function getStarSaveURL() {
        return "./sauverNouveau";
    }

    // URL pour demander la suppression d'une étoile.
    public function getStarAskDeletionURL($id) {
        return "$id/askDelete";
    }

    // URL pour supprimer une étoile.
    public function getStarDeletionURL() {
        return "./delete";
    }

    // URL pour la page de maj d'une étoile.
    public function getStarMakeUpdateURL($id) {
        return "$id/makeUpdate";
    }
    // URL pour retourner relativement vers la page de création d'étoile en cas d'erreur.
    public function getStarErrorUpdateURL() {
        return "./makeUpdate";
    }

    // URL qui met à jour l'étoile.
    public function getStarUpdateURL() {
        return "./update";
    }

    // URL pour se connecter.
    public function getLoginPageURL() {
        return './connexion';
    }

    // URL qui valide la connexion.
    public function getConnectionURL() {
        return 'connect';
    }
    // URL pour la page à propos.
    public function getAProposURL() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./info";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './info';
        }
        else if (count($parts) == 2) {
            return '../info';
        }
    }
    // URL pour la page de déconnexion.
    public function getDisconnectionURL() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./disconnect";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './disconnect';
        }
        else if (count($parts) == 2) {
            return '../disconnect';
        }
    }

    public function getAdminURL() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./admin";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './admin';
        }
        else if (count($parts) == 2) {
            return '../admin';
        }
    }

    public function getAdminValidationURL() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./adminValidation";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './adminValidation';
        }
        else if (count($parts) == 2) {
            return '../adminValidation';
        }
    }

    // URL pour la page d'inscription.
    public function getRegisterPageURL() {
        return './register';
    }
    // URL pour s'inscrire.
    public function getRegisterURL() {
        return './registerValidation';
    }
    // URL pour le chemin au fichier CSS.
    public function getCSSPath() {
        if (!isset($_SERVER['PATH_INFO'])) {
            return "./css/style.css";
        }
        $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
        if (count($parts) == 1) {
            return './css/style.css';
        }
        else if (count($parts) == 2) {
            return '../css/style.css';
        }
    }
    // Méthode qui permet de rediriger et de définir un feedback, que l'on affichera sur la page.
    public function POSTredirect($url, $feedback) {
        $_SESSION['feedback'] = $feedback;
        header("Location: ".htmlspecialchars_decode($url), true, 303);
		die;
    }
}

?>