<?php

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\UseCase\User\Login\Command;
use App\UseCase\User\Login\Form;
use Infrastructure\Security\Authenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Form\FormError;

class LoginController extends AbstractController
{
    private Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        if($this->security->isAuth()){
            return $this->redirect('/');
        }

        $command = new Command();
        $form = $this->createForm(Form::class, $command);
        $form->handleRequest();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $this->authenticator->getUser($command);
                $passwordCorrect = $this->authenticator->checkCredentials($command, $user);
                if($passwordCorrect){
                    $this->security->loginAs($user);
                    return $this->redirect('/');
                }
                throw new \DomainException('Invalid password.');
            } catch (\DomainException $e) {
                $form->addError(new FormError('User with such credentials does not exist.'));
            }
        }

        return $this->render('/auth/login', [
            'form' => $form->createView()
        ]);
    }

    public function logout() : ResponseInterface {
        $this->security->logout();
        return $this->redirect('/');
    }

}