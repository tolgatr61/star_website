<?php
require_once 'model/account/Account.php';

class PrivateView extends View {
// Vue privé qui hérite de la Vue -> la vue des personnes connectés.
    protected $title;
    protected $content;
    protected $router;
    protected $menu;
    protected $feedback;
    private $account;

    public function __construct(Router $router, $feedback, Account $account) {
        $this->router = $router;
        $this->account = $account;
        $this->menu = $this->getMenu();
        $this->feedback = $feedback;
    }
    // On modifie le menu, et les redirige vers les URL qui seront désormais accessible.
    public function getMenu() {
        if ($this->account->getStatut() === 'user') {
            return array(
                "Accueil" => $this->router->homePage(),
                "Etoile" => $this->router->getAllStars(),
                "Nouvelle etoile" => $this->router->getStarCreationURL(),
                "A propos" => $this->router->getAProposURL(),
                "Disconnect" => $this->router->getDisconnectionURL(),
            );
        }
        else if ($this->account->getStatut() === 'admin') {
            return array(
                "Accueil" => $this->router->homePage(),
                "Etoile" => $this->router->getAllStars(),
                "Nouvelle etoile" => $this->router->getStarCreationURL(),
                "A propos" => $this->router->getAProposURL(),
                "Admin" => $this->router->getAdminURL(),
                "Disconnect" => $this->router->getDisconnectionURL(),
            );
        }
    }
    // Nouvelle page d'index dans laquelle on affichera son nom.
    public function makeIndexPage() {
        $this->title = 'Page d\'accueil';
        $this->content = '<p> Bienvenue sur notre site de référencement d\'étoiles ' . $this->account->getSurName() ." " .$this->account->getName().'. </p>';
        $this->content .= '<img style="border-radius: 50%; width 100%; height: 150px;" src="./img/etoile1.png" alt="test" >';
    }

}

?>