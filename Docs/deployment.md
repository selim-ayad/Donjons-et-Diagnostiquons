![CESI École d'Ingénieurs](img/cesi%20alsace.png)

# DONJONS ET DIAGNOSTIQUONS

## GROUPE 1

### PROCESS DE DEPLOIEMENT

![LOGO GROUPE](img/logo_3-removebg-preview.png)

Pour déployer un nouvel élément ou une nouvelle version de code, il faut suivre le processus suivant :

1. Envoyer la version sur git via un commit & push
2. Ouvrir un terminal powershell sur le pc pour se connecter au serveur web
3. Taper `ssh cesi@srvweb-01` puis entrer
4. Entrer le Mot de passe
5. Récupérer la dernière version du code avec un pull sur git
6. Lancer le script pour l’update : `sudo ./update_site.sh`
7. Refaire le même processus sur `cesi@srvweb-02` pour mettre à jour le second serveur
