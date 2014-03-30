




app.config(function ($translateProvider) {
  $translateProvider.translations('en', {
    USERS: 'Users: ',
    ADD_USER: 'Add user',
    SERVICES: 'Services: ',
    REFRESH: 'Refresh'
  });
  $translateProvider.translations('fr', {
    USERS: 'Utilisateurs : ',
    ADD_USER: 'Nouvel utilisateur',
    SERVICES: 'Services : ',
    REFRESH: 'Rafraichir'
  });
  $translateProvider.preferredLanguage('en');
});


