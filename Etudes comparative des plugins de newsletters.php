Le 15/04/16
Sujet : Etude comparative des plugins de newsletters existants sur WP


I) Demande

- Création d'un plugin de newsletter épuré et minimaliste destiné à une certaine catégorie de clients.
- Se servir des plugins existants et en tirer le meilleur -> Proposition d'un plugin se démarquant du choix sur le marché.
- Se différencier de MailChimp.com.


II) Choix des plugins

- Installation des cinq premiers plugins trouvés par rapport au terme de recherche "Newsletter" sur la 'Boutique' de WP.
- Etude des fonctionnalités présentes sur les différents plugins installés.

- ALO EasyMail Newsletter | Version 2.8.2 | Par Alessandro Massasso
- MailPoet Newsletters | Version 2.7.1 | Par MailPoet
- Newsletter | Version 4.1.3 | Par Stefano Lissa, The Newsletter Team
- Newsletters | Version 4.6.1.2 | Par Tribulant Software
- Nifty Newsletters | Version 4.0.13 | Par SolaPlugins


III) Liste des fonctionnalités présentes sur les plugins


	A) Partie édition de la newsletter

	- Titre de la newsletter
	- Contenu de la newsletter -> WYSIWYG
	- Ajout de média
	- Ajout des réseaux sociaux
	- Templates de newsletters
	- Mode "Duplication" pour les newsletters
	- Mode "Brouillon" pour les newsletters
	- Mode "Corbeille" pour les newsletters

	-> Seul les administrateurs du site pourront créer des newsletters.


	B) Partie gestion des listes

	- Liste de diffusion
	- Liste des destinataires
	- Pouvoir éditer des listes de personnes en fontion de leur rôle sur le site

	-> On pourra également trier ces listes.


	C) Partie administration

	- Configuration du serveur SMTP
	- Possibilité d'envoyer des newsletters de test
	- Nombre d'emails envoyés par heure -> Evité d'être blacklisté
	- Configuration d'un CRON à la WP pour l'envoi des emails
	- Liste des erreurs lors des envois de newsletters (logs)


	D) Partie statistiques

	- Statistiques sur les envois de newsletters
	- Statistiques sur les réceptions de newsletters
	- Tracking des clics


	E) Partie utilisateur

	- Notice d'utilisation du plugin
	- Retour d'expérience sur le plugin
	

	F) Autres

	- Possibilité d'ajouter des Widgets

Le 19/04/16
Sujet : Plugin newsletter : demandes plus particulières sur la newsletter en elle-même

Décomposons la newsletter en trois parties selon une page HTML :
- Le header.
- Le body.
- Le footer.

Qu'est-ce qui est modifiable par l'utilisateur ? (CRUD : Create, Read, Update, Delete)
Je crois que l'on avait convenu qu'aucune chose était modifiable par l'utilisateur.

On se retrouverait donc avec :
- Le header :
	-> Logo du client
	-> Nom du client
- Le body : 
	-> Les dix derniers posts pour chaque type de posts (par exemple : événements, posts, etc.)
- Le footer :
	-> Lien pour se désinscrire de la newsletter 

Questions supplémentaires :
- Dans le body : doit-on rajouter une petite phrase d'introduction ? Est-elle personalisable ?
- Dans le footer : doit-on ajouter d'autres choses (statiques) ?
- Y aurait-il d'autres informations (statiques) à ajouter ?
- Charte graphique à respecter pour la newsletter ? Si oui, laquelle ? (récupération des coloris de la charte graphique)

Le 20/04/16
Sujet : Etude sur les limites des serveurs SMTP de principaux acteurs sur le marché



