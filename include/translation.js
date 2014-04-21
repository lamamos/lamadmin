


app.config(function ($translateProvider) {

  $translateProvider.translations('en', {
    USERS: 'Users: ',
    ADD_USER: 'Add user',
    SERVICES: 'Services: ',
    CHOOSE_LANGUAGE: 'Choose your langage: ',
    HOME_PAGE_TITLE: 'Lamadmin part of the Lamamos project',
    HOME_PAGE_DESCRIPTION: 'Description',
    HOME_PAGE_DESCRIPTION_CONTENT: 'The goal of this project if to provide an easy to use interface to manage your own redondant personnal servers.',
    HOME_PAGE_HOW_TO_USE: 'How to use lamadmin',
    HOME_PAGE_HOW_TO_USE_CONTENT: 'To configure a software just click on it\'s name in the left side of the page. Then change the parameter you want to change, save the changes and your servers will be doing the modifications as soon as possible (in less than 30 minutes).',
    HOME_PAGE_FIND_HELP: 'How to get some help',
    HOME_PAGE_FIND_HELP_CONTENT: 'help',
    HOME_PAGE_GET_INVOLVED: 'How to get involved',
    HOME_PAGE_GET_INVOLVED_CONTENT: 'We welkome help of any kind. You don\'t need to know how to code to draw nice interfaces, find problems in the use of the software or translate it in other language. You can get more infos on how to get involved into the project here:',

    REFRESH: 'Refresh'
  });

  $translateProvider.translations('fr', {
    USERS: 'Utilisateurs : ',
    ADD_USER: 'Nouvel utilisateur',
    SERVICES: 'Services : ',
    CHOOSE_LANGUAGE: 'Choisissez votre langue : ',
    HOME_PAGE_TITLE: 'Lamadmin faisant partie du projet Lamamos',
    HOME_PAGE_DESCRIPTION: 'Description',
    HOME_PAGE_DESCRIPTION_CONTENT: 'Le but de ce projet est de fournir une interface façile d\'utilisation pour controler votre propre servers redondants',
    HOME_PAGE_HOW_TO_USE: 'Comment utiliser Lamadmin',
    HOME_PAGE_HOW_TO_USE_CONTENT: 'Pour configurer un logiciel il vous suffit de cliquer sur son nom dans la partie gauche de la page. Puis modifiez le paramètre qui vous intérèsse. Enfin engegistrez. Vos servers appliqueront les modifications aussi tot que possible (dans moins de 30 minutes).',
    HOME_PAGE_FIND_HELP: 'Comment obtenir de l\'aide',
    HOME_PAGE_FIND_HELP_CONTENT: 'aide',
    HOME_PAGE_GET_INVOLVED: 'Comment participer au projet',
    HOME_PAGE_GET_INVOLVED_CONTENT: 'Nous apprécions tout type d\'aides. Vous n\'avez pas besoin de savoir programmer pour dessiner une belle interface, touver des problèmes dans le logiciel ou la traduire dans une autre langue. Vous pouvez obtenir plus d\'informations sur comment participer au projet ici : ',

    REFRESH: 'Rafraichir'
  });
  
  $translateProvider.preferredLanguage('en');
});


