<?php
require_once 'model/star/Star.php';
require_once 'model/star/StarBuilder.php';
require_once 'model/account/AccountBuilder.php';
require_once 'Router.php';

class View {

    protected $title;
    protected $content;
    protected $router;
    protected $menu;
    protected $feedback;

    public function __construct(Router $router, $feedback) {
        $this->router = $router;
        $this->menu = $this->getMenu();
        $this->feedback = $feedback;
    }
    // On inclut notre squelette. Qui s'affichera en fonction des attributs ($this->title, $this->menu, $this->content et $this->feedback)
    public function render() {
        include_once 'SqueletteRender.php';
    }
    // Page d'étoile.
    public function makeStarPage(Star $star, $imageArray = null) {
        $this->title = $star->getName();
        $this->content = '<p> Bayer : '.$star->getBayer(). ' Type spectral : '. $star->getSpectreType(). ' Magnitude :'. $star->getMagnitude(). ' Distance : '. $star->getDistance(). ' al. </p>';
        
        if ($imageArray !== null) {
            foreach ($imageArray as $image) {
                foreach ($image as $obj) {
                    $this->content .= '<img style="border-radius: 50%; width 100%; height: 150px;" src="'.$obj->getNameURL().'" alt="test" >';
                }
            }
        }
    }
    // Afficher les boutons sur la page d'étoile.
    public function addButtonOnStarPage($id) {
        $this->content .= '<form action="'.$this->router->getStarMakeUpdateURL($id).'" method="GET">'."\n";
        $this->content .= "<p><button class='button'>Update</button> </form></p>";
        $this->content .= '<form action="'.$this->router->getStarAskDeletionURL($id).'" method="GET">'."\n";
        $this->content .= "<p><button class='button'>Supprimer</button> </form></p>";
    }
    // Formulaire pour créer une étoile.
    public function makeStarCreationPage(StarBuilder $starBuilder) {
        $this->title = "Ajouter votre étoile.";
        $s = '<form action="'.$this->router->getStarSaveURL().'" method="POST" enctype="multipart/form-data">'."\n";
        $s .= self::getFormFields($starBuilder);
        $s .= "<button class='button'>Créer</button>\n";
        $s .= "</form>\n";
        $this->content = $s;
    }
    // Page d'accueil.
    public function makeIndexPage() {
        $this->title = 'Page d\'accueil';
        $this->content .= '<p> Bienvenue sur notre site de référencement d\'étoiles. </p>';
        $this->content .= '<img style="border-radius: 50%; width 100%; height: 150px;" src="./img/etoile1.png" alt="test" >';
    }
    // On définit notre menu
    public function getMenu() {
        return array(
            "Accueil" => $this->router->homePage(),
            "Etoile" => $this->router->getAllStars(),
            "Connexion" => $this->router->getLoginPageURL(),
            "Inscription" => $this->router->getRegisterPageURL(),
            "A propos" => $this->router->getAProposURL(),
        );
    }
    // Affichage de la gallerie d'étoiles.
    protected function galleryStar($id, $c) {
		$cclass = "Star".$id;
		$res = '<li><a href="'.$this->router->starPage($id).'">';
		$res .= '<h3>'.self::htmlesc($c->getName()).'</h3>';
		$res .= '<div class="sample '.$cclass.'"></div>';
		$res .= '</a></li>'."\n";
		return $res;
	}
    // Liste d'étoiles. + affichage de la gallerie
    public function makeListPage($starsTab) {
        $this->title = "Toutes les étoiles";
        $this->content = "<p>Cliquer sur une étoile pour voir des détails.</p>\n";
        $this->content .= "<ul class=\"gallery\">\n";
		foreach ($starsTab as $tab) {
            foreach ($tab as $key=>$value)
			$this->content .= $this->galleryStar($key, $value);
		}
		$this->content .= "</ul>\n";
    }
    // Page inconnu
    public function makeUnknownStarPage() {
        $this->title = 'Inconnu';
        $this->content = 'Erreur cette étoile n\'est pas connu';
    }
    // Page inattendu
    public function makeUnexpectedErrorPage($e) {
        $this->title = 'Erreur inconnu';
        $this->content = 'Une erreur inconnue s\'est produite : ' . $e->getMessage();
    }
    // htmlesc pour filter les caractères spéciaux de texte.
    public static function htmlesc($str) {
		return htmlspecialchars($str,
			/* on échappe guillemets _et_ apostrophes : */
			ENT_QUOTES
			/* les séquences UTF-8 invalides sont
			* remplacées par le caractère �
			* au lieu de renvoyer la chaîne vide…) */
			| ENT_SUBSTITUTE
			/* on utilise les entités HTML5 (en particulier &apos;) */
			| ENT_HTML5,
			'UTF-8');
    }
    // Formulaire pour ajouter une étoile.
    protected function getFormFields(StarBuilder $builder) {
		$nameRef = $builder->getNameRef();
		$s = "";

		$s .= '<p><label>Nom de l\'étoile: <input class="input" type="text" name="'.$nameRef.'" value="';
		$s .= self::htmlesc($builder->getData($nameRef));
		$s .= "\" />";
		$err = $builder->getErrors($nameRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
		$s .="</label></p>\n";

		$bayerRef = $builder->getBayerRef();
		$s .= '<p><label>Bayer : <input class="input" type="text" name="'.$bayerRef.'" value="';
		$s .= self::htmlesc($builder->getData($bayerRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($bayerRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";
        
        $spectralRef = $builder->getSpectralRef();
        $s .= '<p><label>Type spectral : <input class="input" type="text" name="'.$spectralRef.'" value="';
		$s .= self::htmlesc($builder->getData($spectralRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($spectralRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";

        $magnitudeRef = $builder->getMagnitudeRef();
        $s .= '<p><label>Magnitude : <input class="input" type="text" name="'.$magnitudeRef.'" value="';
		$s .= self::htmlesc($builder->getData($magnitudeRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($magnitudeRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";

        $distanceRef = $builder->getDistanceRef();
        $s .= '<p><label>Distance : <input class="input" type="text" name="'.$distanceRef.'" value="';
		$s .= self::htmlesc($builder->getData($distanceRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($distanceRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";

        $s .= '<p><label> Images à upload : <input type="file" name="files[]" multiple > </label></p>';
		return $s;
    }
    // Formulaire pour s'inscrire.
    public function registerGetFormFields(AccountBuilder $builder) {

        $surnameRef = $builder->getSurNameRef();
        $s = "";
        $s .= '<p><label class="register_form" >Nom: <input class="surName input" type="text" minlength="4" name="'.$surnameRef.'" value="';
		$s .= self::htmlesc($builder->getData($surnameRef));
		$s .= "\" />";
		$err = $builder->getErrors($surnameRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .="</label></p>\n";

        $nameRef = $builder->getNameRef();
        $s .= '<p><label class="register_form" >Prénom: <input class="name input" type="text" minlength="4" name="'.$nameRef.'" value="';
		$s .= self::htmlesc($builder->getData($nameRef));
		$s .= "\" />";
		$err = $builder->getErrors($nameRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .="</label></p>\n";
        
        $loginRef = $builder->getLoginRef();
		$s .= '<p><label class="register_form" >Login : <input class="login input" type="text" minlength="4" name="'.$loginRef.'" value="';
		$s .= self::htmlesc($builder->getData($loginRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($loginRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";
        
        $passwordRef = $builder->getPasswordRef();
        $s .= '<p><label class="register_form" >Mot de passe : <input class="password input" type="password" minlength="6" name="'.$passwordRef.'" value="';
		$s .= self::htmlesc($builder->getData($passwordRef));
		$s .= '" ';
		$s .= '	/>';
		$err = $builder->getErrors($passwordRef);
		if ($err !== null)
			$s .= ' <span class="error">'.$err.'</span>';
        $s .= '</label></p>'."\n";

        return $s;
    }

    // Page de debug en fonction d'une variable.
    public function makeDebugPage($variable) {
        $this->title = 'Debug';
        $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
    }
    // Page de suppression.
    public function makeDeletionPage($id) {
        $this->title = 'Etoile supprimé';
        $this->content = 'L\'étoile avec l\'identifiant : '. $id . ' a bien été supprimé.';
    }
    // Page pour demander la suppression.
    public function makeAskingDeletionPage($id) {
        $this->title = 'Suppression de l\'étoile';
        $form = '<form action="'.$this->router->getStarDeletionURL($id).'" method="POST">'."\n";
        $form .= "<label> Voulez vous supprimer l'étoile ? <button class='button'>Supprimer</button> </label>\n";
        $form .= "</form>\n";
        $this->content = $form;
    }
    // Page pour mettre à jour une étoile.
    public function makeStarUpdatePage($id, StarBuilder $starBuilder) {
        $this->title = "Modifier votre étoile.";
        $s = '<form action="'.$this->router->getStarUpdateURL().'" method="POST" enctype="multipart/form-data">'."\n";
        $s .= self::getFormFields($starBuilder);
        $s .= "<button class='button'>Créer</button>\n";
        $s .= "</form>\n";
        $this->content = $s;
    }
    // Page à propos.
    public function makeAProposPage() {
        $this->title = "A Propos";
        $s = '<p> Liste d\'étudiants. </p>';
        $s .= '<ul> <li> Sahin Tolga 21801808 </li> ';
        $s .= '<li> Meril Emmanuel Miangouila 21810784 </li>';
        $s .= '<li> Zhandos Urazov 21801012 </li>';
        $s .= '<li> Patryk Schoebela 22012136 </li> </ul>';
        $s .= '<p> Compléments : Responsive Design (**), Image (***), Rester connecté (***)</p>';
        $s .= '<p> Les principaux choix en design sont de mettre en avant le thème des étoiles tout en gardant un design simpliste
            afin d\'offrir une galerie d\'étoile ergonomique. </p>';
        $s .= '<p> Les choix en tant que modélisation ont été fait tel qu\'afin de s\'inspirer le plus possible de ce que l\'on a vu en TP et de mettre en avant le côté relationnelle d\'une base de donnée avec des objets interpretés sous forme de classe.</p>';
        $s .= '<p> Ainsi, on a utiliser les interfaces de Storage (CRUD) et les Builder afin de proposer une construction plus performante et optimisé la gestion de nos données pour construire nos objets. Le routeur lui a été conçu de tel manière à fonctionner relativement avec $_SERVER avec l\'aide d\'un fichier .htaccess. </p>';
        $s .= '<p> Précision : Lors de la modification du lifetime de notre cookie de connexion. Il peut être nécessaire de vider son cache. </p>';
        $this->content = $s;
    }
    // Redirection avec feedback lors de la création d'une étoile avec succès.
    public function displayStarCreationSuccess($id) {
        $this->router->POSTredirect($this->router->StarPage($id), 'Etoile créer avec succès !');
    }
    // Redirection avec feedback lors de l'échec de la création d'une étoile.
    public function displayStarCreationFailure() {
        $this->router->POSTredirect($this->router->getStarCreationURL(), 'Formulaire invalide !');
    }
    // Redirection avec feedback lors de l'échec de la création d'une étoile.
    public function displayStarModificationSuccess($id) {
		$this->router->POSTredirect('.'.$this->router->starPage($id), "L'étoile a bien été modifiée !");
	}
    // Redirection avec feedback lors de l'échec de la création d'une étoile.
	public function displayStarNotModifiedPage() {
		$this->router->POSTredirect($this->router->getStarErrorUpdateURL(), "Erreurs dans le formulaire.");
    }
    // Redirection avec feedback lors de la suppression d'une étoile.
    public function displayStarDeletedPage() {
        $this->router->POSTredirect('./'.$this->router->getAllStars(), "L'étoile a bien été supprimé.");
    }
    // Redirection avec feedback lors de la connexion réussie.
    public function displayConnectedPage() {
        $this->router->POSTredirect($this->router->homePage(), "Vous êtes bien connecté.");
    }
    // Redirection avec feedback lors de la connexion échoué.
    public function displayConnectionFailedPage() {
        $this->router->POSTredirect($this->router->getLoginPageURL(), "Erreur sur votre identifiant ou mot de passe.");
    }
    // Redirection avec feedback lors de la déconnexion.
    public function displayDisconnectedPage() {
        $this->router->POSTredirect($this->router->homePage(), "Vous êtes bien deconnecté.");
    }
    // Redirection avec feedback lors de l'inscription réussie.
    public function displayRegisterValidation() {
        $this->router->POSTredirect($this->router->homePage(), "Vous vous êtes bien inscrit !");
    }
    // Redirection avec feedback lors de l'inscription échoué.
    public function displayRegisterFailed() {
        $this->router->POSTredirect($this->router->getRegisterPageURL(), "Erreur dans le formulaire");
    }
    // Redirection avec feedback lors de la demande de connexion. (on empêche l'accès aux détails des étoiles)
    public function displayAskToLogin() {
        $this->router->POSTredirect($this->router->getAllStars(), "Veuillez vous connecter pour voir une étoile.");
    }

    public function displayPanelAdminSuccessful() {
        $this->router->POSTredirect($this->router->getAdminURL(), "Lifetime de cookie modifié.");
    }

    public function displayAdminWrongful() {
        $this->router->POSTredirect($this->router->getAdminURL(), "Vous n'avez pas le droit d'être ici.");
    }

    // Formulaire de connexion.
    public function makeLoginFormPage() {
        $this->title = 'Connexion';
        $s = '<form action="'.$this->router->getConnectionURL().'" method="post">';
        $s .= '<p><label> Identifiant :  <input class="input" type="text" name="id" minlength="3" /></label></p>' . "\n";
        $s .= '<p><label> Mot de passe : <input class="input" type="password" name="password" size ="20" minlength="4" /> </label></p>' . "\n";
        $s .= '<p><input type="checkbox" id="stayConnected" name="stayConnected" value="Rester connecté></p>';
        $s .= '<p><label for="stayConnected">Rester connecté ?</label></p>';
        $s .= '<input class="button" type="submit" name="SubmitButton" value="Connexion"/>';
        $this->content = $s;
    }
    // Formulaire d'inscription.
    public function makeRegisterFormPage(AccountBuilder $builder) {
        $this->title = "S'inscrire.";
        $s = '<form action="'.$this->router->getRegisterURL().'" method="POST">'."\n";
        $s .= self::registerGetFormFields($builder);
        $s .= "<button class='button register'>Inscription</button>\n";
        $s .= "</form>\n";
        $this->content = $s;
    }

    public function makeAdminPage() {
        $this->title = 'Panel admin';
        $this->content = '<p> Bienvenue sur le panel admin. Vous pouvez modifiez le lifetime des cookies ici </p>';
        $this->content = '<form action="'.$this->router->getAdminValidationURL().'" method="POST">'."\n";
        $this->content .= '<p><label>Lifetime de cookie: <input class="input" type="text" name="lifetime" /> </p>';
        $this->content .= "<p><button class='button'>Modifier</button> </p> \n";
        $this->content .= "</form>\n";
    }

}
?>