#!/bin/bash

#echo "--DEBUG--"
#echo "TRAVIS_REPO_SLUG: $TRAVIS_REPO_SLUG"
#echo "TRAVIS_PHP_VERSION: $TRAVIS_PHP_VERSION"
#echo "TRAVIS_PULL_REQUEST: $TRAVIS_PULL_REQUEST"
#echo "TRAVIS_BRANCH: $TRAVIS_BRANCH"
 
if [ "$TRAVIS_REPO_SLUG" == "lamamos/lamadmin" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "5.5" ]; then
 
  echo -e "Publishing Doxygen and yuidoc...\n"
  ## Copie the generated documentation into the $HOME (doxygen)
  cp -R doc/php/html $HOME/doc-latest-php
  ## Copie the generated documentation into the $HOME (doxygen)
  cp -R doc/js $HOME/doc-latest-js

  cd $HOME
  ## Initialisation and retrieving of the gh-pages of the Git repo
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "travis-ci"
  git clone --quiet --branch=master https://${GH_TOKEN}@github.com/lamamos/lamamos.github.io.git gh-pages > /dev/null

  cd gh-pages
  
  ## Delete the old version
  git rm -rf ./docs/lamadmin/$TRAVIS_BRANCH
 
  ## Create the folders
  mkdir -p ./docs/lamadmin/$TRAVIS_BRANCH/php
  mkdir -p ./docs/lamadmin/$TRAVIS_BRANCH/js

  ## Copie the new version
  cp -Rf $HOME/doc-latest-php/* ./docs/lamadmin/$TRAVIS_BRANCH/php/
  cp -Rf $HOME/doc-latest-js/* ./docs/lamadmin/$TRAVIS_BRANCH/js/

  ## We add everything
  git add -f .
  ## We commit
  #git commit -m "Doxygen and yuidoc (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
  ## We push
  #git push -fq origin master > /dev/null
  ## And it is online !
  echo -e "Published Doxygen and yuidoc to the lamamos website.\n"
 
fi
