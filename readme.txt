=== Recently updated posts widget ===
Contributors: luciole135
Tags: widget, post, page, custom post type, update, updated, modified, 
Requires at least: 2.8
Tested up to: 4.2
Stable tag: 1.4
Donate link: additifstabac@free.fr
License: GPLv2

A WordPress widget that displays the last updated posts and pages 

== Description ==
= French =
* Ce widget se présente sous forme d’un plugin qui affiche le widget « Recently updated posts » (ou « Articles récemment mis à jour ») dans la page Apparence->Widgets lorsqu’il est activé.
* Il affiche uniquement les « n » derniers articles et pages récemment mis à jour qui ne sont pas les « n » derniers écrits.
* Affiche les customs post type mis à jour
* compatible avec le plugin tablepress (n'affiche pas les customs post type de ce plugin).
* compatible avec le plugin Premise for WordPress (n'affiche pas les customs post type de ce plugin).
* Il est ainsi un complément au widget « Articles récents » puisqu’il n’affiche pas les mêmes articles (et pages) que ce dernier.
* Affiche aussi les customs post types mis à jour.
* Il met en cache les articles et pages récemment mis à jour dans un transient qui est automatiquement actualisé à chaque édition d’un article (ou d'une page).
* A la désinstallation via le tableau de bord, la base de données est nettoyée et optimisée des options et transient. Les fichiers sont supprimés du dossier /wp-content/plugins à la demande.
* Il est compatible avec WordPress multisite.
* Il est écrit en anglais, et traduit en français.
* Il est prêt à être traduit dans d’autres langues (translation ready).
* La date peut s'afficher ou non selon votre gré dans une infobulle, à la suite ou en dessous du titre ou alors ne pas être affichée du tout.


= English =
* This widget comes in the form of a plugin that displays the widget 'Recently updated posts' in the page Appearance->Widgets when activated.
* It only displays the "n" last posts and pages recently updated that is not the "n" last written.
* Also displays the custom post type updated.
* Compatible with tablepress plugin (does not display the customs post type of this plugin).
* Compatible with Premise for WordPress plugin (does not display the customs post type of this plugin).
* It is thus a complement to the "Recent posts" widget since it does not display the same items as the latter.
* It caches posts and pages recently updated in a transient that is automatically updated with each edition of a post (or of a page).
* Uninstalling via dashboard, the database is cleaned and optimized of options and transient. The files are deleted from the folder/wp-content/plugins on demand.
* It is compatible with WordPress multisite.
* It is written in english and translated into french.
* It is ready to be translated into other languages (translation ready).
* The date can be displayed or not according to your will in a ToolTip, after or below the title or may not be displayed.


== Installation ==

1. Dézippez l'archive et placez là dans le dossier /wp-content/plugins
1. Activez le 'Plugin' depuis le tableau de bord de WordPress

== Changelog ==
= 1.4 =
New SQL query compatible with the Premise for WordPress plugin (does not display the customs post type of this plugin). https://members.getpremise.com/login.aspx?ReturnUrl=%2fDefault.aspx

= 1.3 =
New SQL query compatible with the tablepress plugin (does not display the customs post type of this plugin). 

= 1.2.1 =
Fixed an error on Internet Explorer 11 posted by mepmepmep, the radio button is now initialized to the last registered option.

= 1.2 =
* The date can now be displayed too after the title.

= 1.1 =
* The date can be displayed or not according to your will in a ToolTip, below the title or may not be displayed.

= 1.0.3 =
* input data validation
* Performance improvement by not loading the file style.css

= 1.0.2 =
* New ToolTip that works on all local servers

= 1.0.1 =
* correction of errors in English translation (Thanks to fge, moderator of the french WordPress support forum)
* faster mysql request



== Frequently Asked Questions ==
= French : Comment styliser le widget ? =
* Il est préférable de styliser le plugin via le fichier style.css de votre thème enfant en ajoutant une class CSS : par exemple .recently_updated_posts {text-align: left;}
* Sinon, le style du widget peut être customisé via le fichier style.css du plugin. Dans ce cas, il est nécessaire de décommenter les lignes 190 à 201 du fichier recently-updated-posts.php afin de charger le style personalisé. cette deuxième méthode est moins performante.

= English: How styling the widget? =
* It is better to stylize the plugin via the style.css file of your child theme by adding a class CSS: e.g .recently_updated_posts {text-align: left;}
* Otherwise, the style of the widget can be customized via the style.css of the plugin file. In this case, it is necessary to uncomment lines 190 to 201 of file recently-updated - posts .php in order to load the personalized style. This second method is less efficient.