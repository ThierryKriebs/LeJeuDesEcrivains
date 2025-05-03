# Créer des interfaces d’authentification, d'inscription et de gestion des mots de passe oubliés
Symfony permet de créer à l'aide de quelques commandes simples des interfaces complètes:
- D'authentification.
- D'inscription (permettant aux utilisateurs de se créer des comptes utilisateurs).
- De gestion des mots de passe oubliés.

 Ces commandes génèrent ensuite tous les fichiers nécessaires (contrôleurs, formulaires, vues...).

L'interface d'authentification intègre également une gestion de la vérification de l'adresse Email de l'utilisateur, en envoyant un E-mail à l'utilisateur, contenant un lien de vérification. 

Concernant la déconnexion de l'utilisateur (logout), celle-ci est gérée directement par le Firewall de symfony (voir: `config/packages/secury.yaml`)

Sources:
- [Formulaire d'authentification site Officiel](https://symfony.com/doc/current/security.html#authenticating-users) (en français) pour Symfony 7
- [Formulaire d'inscription site Officiel](https://symfony.com/doc/current/security.html#registering-the-user-hashing-passwords) (en français) pour Symfony 7
- [Formulaire de réinitialisation du mot de passe site officiel](https://symfony.com/doc/current/security/passwords.html#reset-password) (en français) pour Symfony 7


## Interface d'authentification:
Pour créer une interface d'authentification, entrer simplement la commande suivante:  `php bin/console make:security:form-login`
Pour fonctionner, une entité pour la gestion des utilisateurs (par défaut User) doit déjà exister.
Cette commande va alors poser quelques questions et:
- Créer un contrôleur par défaut: [SecurityController] avec la route [/login]
- Créer un lien pour le logout (géré lui directement par le Firewall de Symfony, voir: `config/packages/secury.yaml`)
- Créer une vue (template) [login.html.twig]
- Paramétrer le fichier : [config/packages/security.yaml]

L'interface est déjà fonctionnelle! Le contrôleur écoute par défaut la route: [/login].
Il ne reste plus qu'à:
- Faire de petits ajustements optionnels, comme changer le nom des routes à votre convenance ou traduire en français ou allemand les interfaces et leurs messages. 
- Paramétrer le Firewall de Symfony pour appliquer votre stratégie de sécurité (quelle(s) route(s) nécessite(nt) d'être authentifiée(s)...)


## Interface d'inscription:
Pour créer une interface d'inscription permettant aux futurs utilisateurs de se créer un compte, entrer simplement la commande suivante:  `php bin/console make:registration-form`
Pour fonctionner, une entité pour la gestion des utilisateurs (par défaut User) doit déjà exister.
Cette commande: 
- Propose d'envoyer des emails pour vérifier les adresses des utilisateurs. Si on répond oui, cette fonctionnalité sera automatiquement ajoutée. Un champs supplémentaire booléen sera alors ajouté dans l'entité user (`isVerified`), la commande demandera alors également l'adresse Email de l'expéditeur.
- Propose d'authentifier automatiquement l'utilisateur une fois l'inscription effectuée.

Cette commande a:
- Actualisée l'entité de gestion des utilisateurs (par défaut: Entity/User.php)
- Crée le fichier de classe `Security/EmailVerifier.php` qui contient une méthode pour envoyer un email et une autre pour valider l'email d'un utilisateur (passe le champ `isVerified` à true).
- Crée les templates: `templates/registration/register.html.twig` et `templates/registration/confirmation_email.html.twig`.
- Crée le formulaire: `src/Form/RegistrationFormType.php`
- Crée le contrôleur: `src/Controller/RegistrationController.php`

Pour que cette interface fonctionne il faut encore:
- Installer le bundle `symfonycasts/verify-email-bundle` en lançant la commande: `composer require symfonycasts/verify-email-bundle`
- Paramétrer la route route de redirection, une fois que l'email a été vérifié avec succès.
- Pousser les changements de l'entité en charge des utilisateurs dans la base de données: `php bin/console make:migration`, puis `php bin/console doctrine:migration:migrate`
- Ajouter dans le fichier `.env` notre mailer DSN (Adresse de notre serveur de messagerie): `MAILER_DSN=smtp://mail.dfh-ufa.org:25`
- Faire de petits ajustements optionnels, comme changer le nom des routes à votre convenance ou traduire en français ou allemand les interfaces et leurs messages. 

L'interface est à présent fonctionnelle! Les paramètres comme la longueur minimale et maximale des mots de passe se trouvent dans le formulaire: `src/Form/RegistrationFormType.php`

## Interface de gestion des mots de passe oubliés:
Pour créer une interface de réinitialisation du mot de passe, entrer simplement les commandes suivantes:
`composer require symfonycasts/reset-password-bundle`
`php bin/console make:reset-password`

Cette commande: 
- Propose d'entrer le nom de la route de redirection (appelée une fois le mot de passe changé avec succès), par défaut `app_home`.
- Propose d'entrer l'adresse Email de l'expédieur, par défaut mailer@your-domain.com.

Cette commande a:
- Crée le contrôleur: `src/Controller/ResetPasswordController.php`.
- Crée l'entité:  `src/Entity/ResetPasswordRequest.php`. Mais quel est son rôle?
- Crée le repository: `src/Repository/ResetPasswordRequestRepository.php`.
- Mis à jour le fichier de configuration: `config/packages/reset_password.yaml` (Celui-ci ne contient qu'un paramètre indiquant le chemin du repository).
- Crée les formulaires suivants: `src/Form/ResetPasswordRequestFormType.php` (appel du formulaire de changement de mot de passe) et `src/Form/ChangePasswordFormType.php` (formulaire de changement du mot de passe).
- Crée la vue: `templates/reset_password/request.html.twig`      => Vue implémentant le formulaire demandant l'adresse email de l'utilisateur.
- Crée la vue: `templates/reset_password/check_email.html.twig`  => Message informant qu'un email a été envoyé.
- Crée la vue: `templates/reset_password/email.html.twig`        => Vue contenant le lien pour réinitialiser le mot de passe.
- Crée la vue: `templates/reset_password/reset.html.twig`        => Vue contenant le formulaire de motification du mot de passe.

Pour que cette interace fonctionne il faut encore:
- Pousser les changements de l'entité en charge des utilisateurs dans la base de données: `php bin/console make:migration`, puis `php bin/console doctrine:migration:migrate`.
- Ajouter dans le fichier `.env` notre mailer DSN (Adresse de notre serveur de messagerie): `MAILER_DSN=smtp://mail.dfh-ufa.org:25`(Normalement déjà fait lors de la création du formulaire d'inscription).
- Faire de petits ajustements optionnels, comme changer le nom des routes à votre convenance ou traduire en français ou allemand les interfaces et leurs messages.