#!/bin/bash
 
echo "--DEBUG--"
echo "TRAVIS_REPO_SLUG: $TRAVIS_REPO_SLUG"
echo "TRAVIS_PHP_VERSION: $TRAVIS_PHP_VERSION"
echo "TRAVIS_PULL_REQUEST: $TRAVIS_PULL_REQUEST"
echo "TRAVIS_BRANCH: $TRAVIS_BRANCH"
 
if [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "5.5" ]; then
 
  echo -e "Publishing Doxygen...\n"
  ## Copie the generated documentation into the $HOME
  cp -R doc/html $HOME/doc-latest
 
  cd $HOME
  ## Initialisation and retrieving of the gh-pages of the Git repo
  git config --global user.email "travis@travis-ci.org"
  git config --global user.name "travis-ci"
  git clone --quiet --branch=master https://${GH_TOKEN}@github.com/lamamos/lamamos.github.io.git > /dev/null

  cd gh-pages
  
  ## Delete the old version
  git rm -rf ./docs/$TRAVIS_BRANCH
 
  ## Create the folders
  mkdir docs
  cd docs
  mkdir $TRAVIS_BRANCH
 
  ## Copie the new version
  cp -Rf $HOME/doc-latest/* ./$TRAVIS_BRANCH/
 
  ## We add everything
  git add -f .
  ## We commit
  git commit -m "Doxygen (Travis Build : $TRAVIS_BUILD_NUMBER  - Branch : $TRAVIS_BRANCH)"
  ## We push
  git push -fq origin gh-pages > /dev/null
  ## And it is online !
  echo -e "Published Doxygen to gh-pages.\n"
 
fi
