


app.config(function ($translateProvider) {

  $translateProvider.translations('en', {
    USERS: 'Users: ',
    ADD_USER: 'Add user',
    SERVICES: 'Services: ',
    CHOOSE_LANGUAGE: 'Choose your langage: ',
    REFRESH: 'Refresh'
  });

  $translateProvider.translations('fr', {
    USERS: 'Utilisateurs : ',
    ADD_USER: 'Nouvel utilisateur',
    SERVICES: 'Services : ',
    CHOOSE_LANGUAGE: 'Choisissez votre langue : ',
    REFRESH: 'Rafraichir'
  });
  
  $translateProvider.preferredLanguage('en');
});


