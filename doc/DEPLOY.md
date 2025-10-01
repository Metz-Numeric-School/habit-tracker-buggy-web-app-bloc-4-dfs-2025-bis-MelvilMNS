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

## Faire le transfer depuis le repositery cloné sur notre machine en local

Tout d'abord faire un nouveau commit, pour la documentation

Dans ce commit, nous avons des modifications dans le fichier __doc/DEPLOY.md__ ainsi que __CHANGELOG.md__

Les fichiers ont été manuellement créé, mais nous pouvons aussi utiliser l'outil __git cliff__ pour générer / mettre à jour le fichier __CHANGELOG.md__

Dans le CHANGELOG.md nous avons des informations avant le commit sur les changements éffectués pour une version spécifique ici __0.0.1__ (Majeur, moyenne, mineure)

Maintenant pour créer le commit et le pousser sur la branche main
```
git add .
git commit -m "docs: Complétion du doc/DEPLOY.md et CHANGELOG.md"
git push -u origin main
```

Dans VSCode par exemple, à l'endroit ou est le repositery git cloné, nous pouvons créer un nouveau terminal

Maintenant dans ce terminal nous allons créer un tag

```

```