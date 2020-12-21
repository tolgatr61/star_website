<?php
require_once 'view/View.php';
require_once 'model/star/Star.php';
require_once 'model/star/StarStorage.php';
require_once 'model/account/AccountStorage.php';
require_once 'model/account/AuthenticationManager.php';
require_once 'model/account/AccountBuilder.php';
require_once 'model/image/ImageStorage.php';
require_once 'model/image/ImageBuilder.php';
require_once 'model/cookie/CookieStorage.php';

class Controller {

    private $view;
    private $storage;
    private $accountStorage;
    private $imageStorage;

    public function __construct(View $view, StarStorage $storage, AccountStorage $accountStorage, ImageStorage $imageStorage, CookieStorage $cookieStorage) {
        $this->view = $view;
        $this->storage = $storage;
        $this->accountStorage = $accountStorage;
        $this->imageStorage = $imageStorage;
        $this->cookieStorage = $cookieStorage;
    }

    // Méthode pour afficher une étoile en fonction de son identifiant.
    public function showInformation($id) {
        $star = $this->storage->read($id);
        if ($star !== null) {
            $image = $this->imageStorage->readAll($id);
            if (!empty($image)) {
                $this->view->makeStarPage($star, $image);
            }
            else $this->view->makeStarPage($star, null);
        }
        else {
            $this->view->makeUnknownStarPage();
        }
    }

    // Méthode pour afficher la liste d'étoiles.
    public function showList() {
        $stars = $this->storage->readAll();
        $this->view->makeListPage($stars);
    }

    // Méthode pour créer une nouvelle étoile.
    public function newStar() {
        if (isset($_SESSION['currentNewStar'])) {
            $this->view->makeStarCreationPage($_SESSION['currentNewStar']);
        }
        else {
            $sf = new StarBuilder();
            $this->view->makeStarCreationPage($sf);
        }
    }

    // Méthode pour sauvegarder une étoile si valide.
    public function saveNewStar(array $data, array $data2) {
        $sf = new StarBuilder($data);
		if ($sf->isValid()) {
            unset($_SESSION['currentNewStar']);
			$star = $sf->createStar();
            $starId = $this->storage->create($star);
            $this->imageStorage->checkImage($data2, $starId);
            $this->view->displayStarCreationSuccess($starId);
		} else {
            $_SESSION['currentNewStar'] = $sf;
			$this->view->displayStarCreationFailure();
		}
    }

    // Méthode pour supprimer une étoile.
    public function deleteColor($id) {
        $this->storage->delete($id);
        $this->imageStorage->delete($id);
        $this->view->makeDeletionPage($id);
        $this->view->displayStarDeletedPage();
    }

    // Méthode pour demander la suppression d'étoile.
    public function askDeleteColor($id) {
        $star = $this->storage->read($id);
        if ($star == null) {
            $this->view->makeUnknownStarPage();
            return;
        }
        $this->view->makeAskingDeletionPage($id);
    }

    // Méthode pour montrer la mise à jour d'une étoile.
    public function makeUpdatePage($id) {
        $star = $this->storage->read($id);
        if ($star == null) {
            $this->view->makeUnknownStarPage();
            return;
        }
        else if (isset($_SESSION['currentModifStar'])) {
            $this->view->makeStarUpdatePage($id ,$_SESSION['currentModifStar']);
        }
        else {
        $data = $this->storage->read($id);
        $sf = new StarBuilder($data);
        $this->view->makeStarUpdatePage($id, $sf);
        }
    }

    // Méthode pour mettre à jour.
    public function makeUpdate($id, array $data, array $data2) {
        $sf = new StarBuilder($data);
		if ($sf->isValid()) {
            unset($_SESSION['currentModifStar']);
            $star = $sf->createStar();
            $this->storage->update($id, $star);
            if (!empty($data2)) {
                $this->imageStorage->delete($id);
                $this->imageStorage->checkUpdateImage($data2, $id);
            };
			$this->view->displayStarModificationSuccess($id);
		} else {
            $_SESSION['currentModifStar'] = $sf;
			$this->view->displayStarNotModifiedPage();
		}
    }

    // Méthode pour afficher la page de connexion.
    public function showLogin() {
        $this->view->makeLoginFormPage();
    }

    // Méthode pour connecter.
    public function Login() {
        $account = $this->accountStorage->checkAuth($_POST['id'], $_POST['password']);
        $authenticationManager = new AuthenticationManager($account); // On connecte en fonction de la validité de l'AuthenticationManager.
        if ($authenticationManager->verifyConnection()){ 
            $this->view->displayConnectedPage();
        }
        else {
            $this->view->displayConnectionFailedPage();
        }
    }
    // Méthode pour déconnecter en fonction de l'AuthenticationManager.
    public function disconnect() {
        if (isset($_SESSION['user'])) {
            $authenticationManager = new AuthenticationManager($_SESSION['user']);
            $authenticationManager->disconnectUser();
            $this->view->displayDisconnectedPage();
        }
    }
    // Méthode pour afficher la page à propos.
    public function showAProposPage() {
        $this->view->makeAProposPage();
    }
    // Méthode pour afficher la page d'inscription.
    public function showRegister() {
        if (isset($_SESSION['currentRegister'])) {
            $this->view->makeRegisterFormPage($_SESSION['currentRegister']);
        }
        else {
            $builder = new AccountBuilder(null);
            $this->view->makeRegisterFormPage($builder);
        }
    }
    // Méthode qui inscrit en créant un compte ou non en fonction de la validité des données.
    public function register($data) {
        $accountBuilder = new AccountBuilder($data);
        if ($accountBuilder->isValid()) {
            unset($_SESSION['currentRegister']);
            $account = $accountBuilder->createNewAccount();
            $this->accountStorage->create($account);
            $this->view->displayRegisterValidation();
        }
        else {
            $_SESSION['currentRegister'] = $accountBuilder;
            $this->view->displayRegisterFailed();
        }
    }
    // Méthode qui montre les boutons d'ajout et suppression.
    public function showButtonsOnStarPage($id) {
        $this->view->addButtonOnStarPage($id);
    }
    // Méthode qui demande de se connecter. (lorsque la personne veut check les étoiles sans étant connecter).
    public function makePageAskToLogin() {
        $this->view->displayAskToLogin();
    }

    public function showAdminPage(Account $account) {
        if ($account->getStatut() === 'admin') {
            $this->view->makeAdminPage();
        }
        else {
            $this->view->displayAdminWrongful();
        }
    }

    public function changeLifetime(Account $account, array $data) {
        if ($account->getStatut() === 'admin') {
            $this->cookieStorage->changeLifetime($data['lifetime'], $account);
            $this->view->displayPanelAdminSuccessful();
        }
        else {
            $this->view->displayAdminWrongful();
        }
    }
    
}

?>