# Procédure de Déploiement

# Préparer l'environnement local

Tout d'abord cloner ce repositery sur ma machine Windows en local
```
git clone https://github.com/Metz-Numeric-School/habit-tracker-buggy-web-app-bloc-4-dfs-2025-bis-MelvilMNS
```
Installer les dépendances en local
```
composer install
```
Lancer le projet en local
```
php bin/serve
```

# Installation d'AAPanel

Se connecter au VPS en SSH via un autre terminal (CMD)
```
ssh root@172.17.4.3
```
Sur le VPS mettre à jour la liste des paquets:
```
sudo apt update
```
Maintenant lancer l'installation de AAPanel sur le VPS
```
URL=https://www.aapanel.com/script/install_7.0_en.sh && if [ -f /usr/bin/curl ];then curl -ksSO "$URL" ;else wget --no-check-certificate -O install_7.0_en.sh "$URL";fi;bash install_7.0_en.sh aapanel
```
Do you want to install aaPanel to the /www directory now?(y/n):
<br>Choisir y

## Je stocke les identifiants de connexion de AAPanel une fois que l'installation ce soit terminée

```
Congratulations! Installed successfully!
==================================================================
aaPanel Internet Address: https://90.80.241.65:16453/2a6e5f93
aaPanel Internal Address: https://172.17.4.3:16453/2a6e5f93
username: hl2bc5nr
password: 3f6c125b
Warning:
If you cannot access the panel,
release the following port (16453|888|80|443|20|21) in the security group
==================================================================
Time consumed: 3 Minute!
```

## Je peux maintenant me connecter à AAPanel depuis mon naviguateur avec mes identifiants
Il faut autoriser la connexion en HTTP non sécurisé
```
https://172.17.4.3:16453/2a6e5f93
```

## Configuration de AAPanel

J'installe la version LNMP (Recommended)

Je laisse la configuration par défaut proposée (Nginx, MySQL MariaDB, PHP 8.3, Pure-FTPD, phpMyAdmin)

Je garde aussi quick install et clique sur one-click, cela devrait prendre une dizaine de minutes

Après installation de la version LNMP

## Création d'un site via AAPanel

Je vais sur l'onglet Website de AAPanel

Je clique sur le bouton Add Site et complète les informations suivantes:

```
Resolve Domain: Manual Add Record (par défaut)

Domain name: 172.17.4.3 (Adresse IP ou nom de domaine)

Description: 172_17_4_3 (par défaut)

Website Path: /www/wwwroot/172.17.4.3 (par défaut)

FTP: Create et récupérer les informations de connexion ftp_172_17_4_3 / 9d3336aa024cd8

Database: Choisir MySQL et récupérer les informations de connexion sql_172_17_4_3 / 693bbc282064b8

PHP Version: PHP-83 (par défaut)

Site category: Default category (par défaut)

Create html file: activé (par défaut)
```

Cliquer sur Confirm

## Autres configurations du site via AAPanel

Maintenant toujours sur l'onglet Website, au niveau de la ligne du site qui vient d'être créé, on peut cliquer sur __conf__

Une modal apparait, On va sur l'onglet Site directory

Il faudra mettre plus tard le site directory sur : __/public__ une fois que le site sera migré sur le VPS (pas encore le cas)

Il est préférable de désactiver Anti-XSS attack pour éviter des bugs de AAPanel

Configuration terminée, nous pouvons fermer la modal

## Préparer le transfer des fichiers de l'application sur le VPS

Sur le VPS depuis la connexion SSH, il faut créer un dossier git (depot_git) pour recevoir les fichiers:

```
cd /var
mkdir depot_git
cd depot_git
git init --bare
```

## Gérer le versionning

Tout d'abord faire un nouveau commit, pour la documentation

Dans ce commit, nous avons des modifications dans le fichier __doc/DEPLOY.md__ ainsi que __CHANGELOG.md__

Les fichiers ont été manuellement créé, mais nous pouvons aussi utiliser l'outil __git cliff__ pour générer / mettre à jour le fichier __CHANGELOG.md__

Dans le CHANGELOG.md nous avons des informations avant le commit sur les changements éffectués pour une version spécifique ici __0.0.1__ (Majeur, moyenne, mineure)

Maintenant pour créer le commit et le pousser sur la branche main

Dans VSCode par exemple, à l'endroit ou est le repositery git cloné, nous pouvons créer un nouveau terminal
```
git add .
git commit -m "docs: Complétion du doc/DEPLOY.md et CHANGELOG.md"
git push -u origin main
```

## Pour pousser le code sur le VPS (toujours sur VSCode)

Maintenant dans ce terminal nous allons créer un tag par rapport à la dernière version dans le fichier __CHANGELOG.md__
```
git tag 0.0.1 
```

Ensuite il faut ajouter un nouveau remote pour notre VPS
```
git remote add vps root@172.17.4.3:/var/depot_git
```

Maintenant nous pouvons pousser sur le VPS:
```
git push vps 0.0.1
```

Si tout ce passe comme prévu, vous devriez voir ceci
```
To 172.17.4.3:/var/depot_git
 * [new tag]         0.0.1 -> 0.0.1
```

## Déployer le code qui a été poussé sur le VPS

Maintenant sur le terminal du VPS en SSH

Toujours dans __/var/depot_git__ Il faut créer un nouveau fichier nommé deploy.sh
```
touch deploy.sh
```

Maintenant il faut l'ouvrir pour le modifier:
```
nano deploy.sh
```

Maintenant nous pouvons renseigner ce script bash:
```
git --work-tree=/www/wwwroot/172.17.4.3 --git-dir=/var/depot_git checkout -f $1
cd /www/wwwroot/
composer install
```

Ecrire le fichier avec CTRL+0 et Entrer

Et CTRL+X pour quitter l'éditeur de texte nano

Maintenant nous pouvons executer le script via cette commande
```
bash deploy.sh 0.0.1
```
Cela permet de déployer le tag qui a été poussé via git sur le VPS, et placer le code source dans le dossier du site précédemment créé __/www/wwwroot/172.17.4.3__

Voici le résultat attendu:
```
root@debian:/var/depot_git# bash deploy.sh 0.0.1
Note : basculement sur '0.0.1'.

Vous êtes dans l'état « HEAD détachée ». Vous pouvez visiter, faire des modifications
expérimentales et les valider. Il vous suffit de faire un autre basculement pour
abandonner les commits que vous faites dans cet état sans impacter les autres branches

Si vous voulez créer une nouvelle branche pour conserver les commits que vous créez,
il vous suffit d'utiliser l'option -c de la commande switch comme ceci :

  git switch -c <nom-de-la-nouvelle-branche>

Ou annuler cette opération avec :

  git switch -

Désactivez ce conseil en renseignant la variable de configuration advice.detachedHead à false

HEAD est maintenant sur a832bf1 docs: J'ai oublié d'expliquer comment pousser
PHP Fatal error:  Uncaught Error: Call to undefined function Composer\XdebugHandler\putenv() in phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Process.php:93
Stack trace:
#0 phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Status.php(48): Composer\XdebugHandler\Process::setEnv()
#1 phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/XdebugHandler.php(83): Composer\XdebugHandler\Status->__construct()
#2 phar:///usr/bin/composer/bin/composer(16): Composer\XdebugHandler\XdebugHandler->__construct()
#3 /usr/bin/composer(24): require('...')
#4 {main}
  thrown in phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Process.php on line 93

Fatal error: Uncaught Error: Call to undefined function Composer\XdebugHandler\putenv() in phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Process.php:93
Stack trace:
#0 phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Status.php(48): Composer\XdebugHandler\Process::setEnv()
#1 phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/XdebugHandler.php(83): Composer\XdebugHandler\Status->__construct()
#2 phar:///usr/bin/composer/bin/composer(16): Composer\XdebugHandler\XdebugHandler->__construct()
#3 /usr/bin/composer(24): require('...')
#4 {main}
  thrown in phar:///usr/bin/composer/vendor/composer/xdebug-handler/src/Process.php on line 93
root@debian:/var/depot_git#
```

Dans l'onglet Files de AAPanel, si vous naviguez dans le dossier du site __/www/wwwroot/172.17.4.3__ vous devriez retrouver la majeure partie du code source

Etrangement il manque le dossier vendor, aucune idée du pourquoi du comment composer ne l'a pas créé

Pour palier à ce problème, j'ai décidé de me connecter en FTP via le logiciel WinSCP pour le rajouter

Putenv ne marchait toujours pas, du coup en cherchant sur Internet: https://stackoverflow.com/questions/51812996/putenv-has-been-disabled-for-security-reasons-when-executing-composer-commands

Une personne recommande de retirer putenv dans disable_functions depuis le fichier __php.ini__ (que j'ai trouvé dans: __/www/server/php/83/etc/php.ini__)

Maintenant c'est beaucoup mieu en executant le script __deploy.sh__:

```
root@debian:/var/depot_git# bash deploy.sh 0.0.1
HEAD est maintenant sur a832bf1 docs: J'ai oublié d'expliquer comment pousser
Installing dependencies from lock file (including require-dev)
Verifying lock file contents can be installed on current platform.
Nothing to install, update or remove
Generating autoload files
9 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
root@debian:/var/depot_git#
```

## Encore quelques configurations

Retournez dans l'onglet Website de AAPanel, puis dans la partie Conf du site

Allez ensuite dans l'onglet Site directory

Changez le __Running directory__ en /public et cliquez sur __Save__.

Vous pouvez fermer la modal de configuration, et tenter d'accéder au site: 