<?php
namespace tweeterapp\view;

use tweeterapp\model\User;
use mf\router\Router;



class TweeterView extends \mf\view\AbstractView {
  
    // Constructeur
    public function __construct( $data ){
        parent::__construct($data);
   
    }

	// Header
    public function renderHeader(){

        $router = new Router();

        $renderHeader = "<div id='header'><a href='".$router->urlFor('home')."'><h1 id='h1-header' style='font-family:Calibri'>Mini Tweetr</h1></a></div>";

        // affichage de certains liens uniquement si l'utilisateur est connecté...
        if(isset($_SESSION['user_login'])){
        	$renderHeader .= "	<nav> 
									<a href='".$router->urlFor('home')."'> 
										<img src='../../images/home.png' align='left' > 
									</a> 
									<a href='".$router->urlFor('ViewFollowers')."'> 
										<img src='../../images/followees.png' align='left' > 
									</a>";
			// ... Et s'il a accès au mode admin
			if($_SESSION['access_level']==900){
				$renderHeader .= "	<a href='".$router->urlFor('ViewNumbersOfFollowersOfUsers')."'> 
										<img src='../../images/statistiques.png' align='right' > 
									</a>  ";
			}
			$renderHeader .= "		<a href='".$router->urlFor('logout')."'> 
										<img src='../../images/logout.png' align='center' > 
									</a> 	
								</nav>";
        } else {
        	$renderHeader .= "	<nav> 
									<a href='".$router->urlFor('home')."'> 
										<img src='../../images/home.png' align='left' > 
									</a> 
									<a href='".$router->urlFor('signIn')."'> 
										<img src='../../images/login.png' align='center' > 
									</a>
									<a href='".$router->urlFor('signUp')."'> 
										<img src='../../images/signup.png' align='center' > 
									</a> </br>   
								</nav> 
								<p> Version invité </p>";
        }
        return $renderHeader;
  }
  
    // Footer
    public function renderFooter(){
        return '<div class="tweet-footer"> La super app créée en Licence Pro ©2018 </div>';
    }

	// Home - S'affiche même déconnecté
    public function renderHome(){
        $router = new \mf\router\Router();        
        $res = "";
		// Création d'un tweet uniquement si connecté
		if(!empty($_SESSION)){
			$res .= "<section id='tweet-form'>
						<form action='{$router->urlFor('send')}' method='post'>
							<textarea name='tweet' rows='4' cols='48' placeholder='À quoi pensez-vous ".$_SESSION['user_login']." ?'></textarea>
							<button id='bouton-post' type='submit'> Poster </button>
						</form>
					</section>";
		}
        $res .= "<h2>Latest Tweets</h2>";
        foreach($this->data as $key => $t){
            $user = User::select()->where('id','=',$t->author)->first();
            $res .= "<div class='tweet'>
						<a href='".$router->urlFor('view',['id' => $t->id])."' class='tweet-text'>$t->text</a>
						<a href='".$router->urlFor('profil',['id_user' => $t->author])."' class='tweet-text'> <p class='tweet-author'>$user->username</p> </a>
						<p>$t->created_at</p>";
			if(isset($_SESSION['user_login'])){
				$res .= "	<a href='".$router->urlFor('like',['id' => $t->id])."'>
								<img src='../../images/like.png' align='left' >
							</a>
							<a href='".$router->urlFor('dislike',['id' => $t->id])."'> 
								<img src='../../images/dislike.png' align='left' > 
							</a> &nbsp &nbsp &nbsp &nbsp &nbsp score : $t->score</div>";
			}else{
				$res .= "</div>";
			}
        }
        return $res;
    }

    //affichage du profil utilisateur
    public function renderProfil(){
		$tweets = new \tweeterapp\control\TweeterController();
		$tweets->viewUserTweets();
    }
  
    /* MÃ©thode renderUeserTweets
     *
     * Vue de la fonctionalitÃ© afficher tout les Tweets d'un utilisateur donnÃ© en allant dans le profil de l'utilisateur en cliqaunt sur lire ses tweets 
     * 
     */
     
    public function renderUserTweets(){
		$router = new \mf\router\Router(); 
        $res = "";
        foreach($this->data as $key => $t){
            $res .= "<div class='tweet'>
						<a href='".$router->urlFor('view',['id' => $t->id])."' class='tweet-text'>$t->text</a></br>
						<a href='".$router->urlFor('user',['id_user' => $t->id])."' class='tweet-text'> $t->created_at </a>
					</div>";
                 }
        return $res;
    }
	
    public function renderViewTweet(){
        $router = new \mf\router\Router(); 
         if(!is_null($this->data)){
            $user = User::select()->where('id','=',$this->data->author)->first();
            $res = "<div class='tweet'>
						<a href='".$router->urlFor('view',['id' => $this->data->id])."' class='tweet-text'>".$this->data->text."</a>
						<a href='".$router->urlFor('user',['id' => $user->id])."' class='tweet-author'>".$user->username."</a>
						<p>".$this->data->created_at."</p>
						<div class='tweet-footer'><hr>
							<p class='tweet-score'>".$this->data->score."</p>
						</div>
					</div>";
            return $res;
         }else{
             return "";
         }        
    }

    public function renderfollowingOfAnUser(){
        $router = new \mf\router\Router();  
        $renderfollowingOfAnUser = "";
        $renderfollowingOfAnUser .= "";
        $renderfollowingOfAnUser .= "<h2>Followers of an user</h2>";
        foreach($this->data as $key => $t){
            $user = Follow::select()->where('follower','=',$id);
            $renderfollowingOfAnUser .= "<div class='tweet'>";
            $renderfollowingOfAnUser .= "<div class='tweet-text'>$t->followee</div>";
            $renderfollowingOfAnUser .= "</div>";
        }
        $renderfollowingOfAnUser .= "";
        return $renderfollowingOfAnUser;
        $renderfollowingOfAnUser = "";
    }
	
	
    //affichage du formulaire qui permettra de s'enregistrer en tant que nouvel utilisateur
    private function renderViewFormSignUp(){
    	$router = new Router();
    	$res = "<section id='create'>";
        $html = "	<section id='create'>
						<form action=".$router->urlFor('signup_check')." method='post'>
							<div><label for='fullname'>Fullname</label> <input type='text' name='fullname'></div>
							<div><label for='username'>Username</label> <input type='text' name='username'></div>
							<div><label for='password'>Password</label> <input type='password' name='password'></div>
							<div><label for='password_confirm'>Password Confirmation</label> <input type='password' name='password_confirm'></div>
							<button>Register</button>
						</form>
					</section>";
        return $html;
    }

    //formulaire pour se connecter
    private function renderViewFormSignIn(){
    	$router = new Router();
    	$res = "<form action=".$router->urlFor('login_check')." method='post'>        
					<input class='input' type='text' name='username' id='username' placeholder='username' required /><br>
					<input class='input' type='password' name='password' id='password' placeholder='password' required /><br>
					<button type='submit'>Se connecter</button>
				</form>";

        return $res;
    }

    

    //vue pour voir les relation de following entre utilisateurs
    private function renderViewFollowers(){
        $render = "Personnes qui vous suivent";
        foreach($this->data as $key => $t){
            $user = User::select()->where('id','=',$t->follower)->first();
            $user2 = User::select()->where('id','=',$t->followee)->first();
            $render .= "	<div class='tweet'>
								<div class='tweet-text'>$user->username suit $user2->username</div>
							</div>";
        }
        return $render;
    }


    //vue avec le classement des utilisateur suivant leur nombre de followers
    private function renderViewNumbersOfFollowersOfUsers(){
		if(isset($_SESSION['access_level']) and $_SESSION['access_level']==900){
			$router = new \mf\router\Router();        
			$render = "<div class='classement-follow'><h3>Classement des personnes les plus suivies :</h3>";
			foreach($this->data as $key => $t){
				$render .= "<div class='content-classement'>
								$t->username est suivi(e) par $t->followers personnes
							</div>";
			}
			$render .= "</div>";
			return $render;
		}else{
			return "Accès interdit. Vous n'avez pas l'autorisation d'accéder à ce contenu";
		}
    }


    public function renderBody($selector){
        $http_req = new \mf\utils\HttpRequest();
        $rendu = "<header class='theme-backcolor1'>".$this->renderHeader()."<nav id='nav-menu'></nav></header><section><article class='theme-backcolor2'>";
		//Choix de la fonction à exécuter selon l'URL empruntée
        if($selector == 'home'){
            $rendu .= $this->renderHome();
        }else if($selector == 'userTweets'){
            $rendu .= $this->renderUserTweets();
        }else if($selector == 'viewTweet'){
            $rendu .= $this->renderViewTweet();
        }else if($selector == 'PostTweet'){
            $rendu .= $this->renderPostTweet();
        }else if($selector == 'SendTweet'){
            $rendu .= $this->renderHome();
        }else if($selector == 'viewFormSignIn'){
            $rendu .= $this->renderViewFormSignIn();
        }else if($selector == 'viewFormSignUp'){
            $rendu .= $this->renderViewFormSignUp();
        }else if($selector == 'viewFollowers'){
            $rendu .= $this->renderViewFollowers();
        }else if($selector == 'viewNumbersOfFollowersOfUsers'){
            $rendu .= $this->renderViewNumbersOfFollowersOfUsers();
        }else if($selector == 'profil'){
        	$rendu .= $this->renderProfil();
        }
        $rendu .= "</article>".$this->renderFooter();
        return $rendu;
    }

    //Méthode d'affichage de la page -> Méthode principale
    public function render($selector){
	if(!empty($_SESSION)){
		$title = $_SESSION['user_login']." - Mini Tweetr";
	}else{
		$title = "Mini Tweetr";
	}
	$app_root = (new \mf\utils\HttpRequest())->root;
	$style_sheets ='html/styleTweeter.css';
	$styles ='';
	foreach(self::$style_sheets as $file)
		$styles .= "<link rel='stylesheet' href='".$app_root."/".$file."'>";
        $body = $this->renderBody($selector);
		$html = "
			<html>
				<head>
					<meta charset='utf-8'>
					<title>${title}</title>
					${styles}
				</head>
				<body>
				   ${body}
				</body>
			</html>";
        //Envoi du rendu de la page!!
        print $html;
    }
}
