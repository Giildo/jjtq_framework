<?php

namespace App\Admin\Controller;

use App\Admin\Model\UserModel;
use Core\Controller\Controller;
use Core\Controller\ControllerInterface;
use Core\Form\BootstrapForm;

class UserController extends Controller implements ControllerInterface
{
    /**
     * @var UserModel
     */
    protected $userModel;

    /**
     * Affiche la page de connexion pour un utilisateur
     *
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function login(): void
    {
        $results = [];

        $results = $this->comparePass($results);

        if (!$this->auth->logged()) {
            $post = $this->createPost();

            $result = $this->addUser($results, $post);

            $results = $result['results'];
            $post = $result['post'];

            $form1 = new BootstrapForm('col-sm-6 loginForm');
            $form1->fieldset('Connectez-vous');
            ($results['c_error']) ?
                $form1->item("<h4 class='error'>Identifiant ou mot de passe incorrect !</h4>") :
                null;
            $form1->input('c_pseudo', 'Pseudo', $post['c_pseudo']);
            $form1->input('c_password', 'Mot de passe', $post['c_password'], 'password');
            $form1 = $form1->submit('Valider');

            $form2 = new BootstrapForm(('col-sm-6 loginForm'));
            $form2->fieldset('Inscrivez-vous');
            ($results['r_error']) ?
                $form2->item("<h4 class='error'>Tous les champs doivent être renseignés !</h4>") :
                null;
            ($results['r_success']) ?
                $form2->item("<h4 class='success'>Utilisateur ajouté avec succès, veuillez vous connecter.</h4>") :
                null;
            $form2->input('r_pseudo', 'Pseudo', $post['r_pseudo']);
            $form2->input('firstName', 'Prénom', $post['firstName']);
            $form2->input('lastName', 'Nom', $post['lastName'], 'text');
            $form2->input('mail', 'Adresse mail', $post['mail'], 'email');
            $form2->input('phone', 'Téléphone', $post['phone'], 'tel');
            $form2->input('r_password', 'Mot de passe', $post['r_password'], 'password', null, 'new-password');
            $form2 = $form2->submit('Valider');

            $this->render('admin/login.twig', compact('form1', 'form2', 'error'));
        } else {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: /admin/post/1');
        }
    }

    /**
     * Détruit la variable de connexion
     *
     * @return void
     */
    public function logout(): void
    {
        unset($_SESSION['confirmConnect']);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /accueil');
    }

    /**
     * Génère un tableau qui va regrouper les éléments passés en post ou non
     *
     * @return string[]
     */
    private function createPost(): array
    {
        $post['c_pseudo'] = (isset($_POST['c_pseudo'])) ? $_POST['c_pseudo'] : '';
        $post['c_password'] = (isset($_POST['c_password'])) ? $_POST['c_password'] : '';
        $post['r_pseudo'] = (isset($_POST['r_pseudo'])) ? $_POST['r_pseudo'] : '';
        $post['firstName'] = (isset($_POST['firstName'])) ? $_POST['firstName'] : '';
        $post['lastName'] = (isset($_POST['lastName'])) ? $_POST['lastName'] : '';
        $post['mail'] = (isset($_POST['mail'])) ? $_POST['mail'] : '';
        $post['phone'] = (isset($_POST['phone'])) ? $_POST['phone'] : '';
        $post['r_password'] = (isset($_POST['r_password'])) ? $_POST['r_password'] : '';

        return $post;
    }

    /**
     * Compare grâce au UserModel si le password en Post est le même que celui en BD
     *
     * @param array $results
     * @return array
     */
    private function comparePass(array $results): array
    {
        if (!empty($_POST) && isset($_POST['c_pseudo']) && isset($_POST['c_password'])) {
            $result = $this->userModel->comparePass($_POST['c_pseudo'], $_POST['c_password']);

            if ($result) {
                $_SESSION['confirmConnect'] = true;
            } else {
                $results['c_error'] = true;
            }
        }

        return $results;
    }

    /**
     * Vérifie si tous les champs son remplis, si c'est le cas ajoute grâce au UserModel le nouvel utilisateur
     *
     * @param array $results
     * @param array $post
     * @return array
     */
    private function addUser(array $results, array $post): array
    {
        if (!empty($_POST) &&
            isset($_POST['r_pseudo']) &&
            isset($_POST['firstName']) &&
            isset($_POST['lastName']) &&
            isset($_POST['mail']) &&
            isset($_POST['phone']) &&
            isset($_POST['r_password'])
        ) {
            if (empty($_POST['r_pseudo']) ||
                empty($_POST['firstName']) ||
                empty($_POST['lastName']) ||
                empty($_POST['mail']) ||
                empty($_POST['phone']) ||
                empty($_POST['r_password'])
            ) {
                $results['r_error'] = true;
            } else {
                $result = $this->userModel->createUser(
                    $_POST['r_pseudo'],
                    $_POST['firstName'],
                    $_POST['lastName'],
                    $_POST['mail'],
                    $_POST['phone'],
                    $_POST['r_password']
                );

                if ($result) {
                    $results['r_success'] = true;

                    $post['c_pseudo'] = $_POST['r_pseudo'];
                    $post['c_password'] = $_POST['r_password'];
                    $post['r_pseudo'] = '';
                    $post['firstName'] = '';
                    $post['lastName'] = '';
                    $post['mail'] = '';
                    $post['phone'] = '';
                    $post['r_password'] = '';
                }
            }
        }

        return compact('post', 'results');
    }
}
