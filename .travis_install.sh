#!/bin/bash

#echo "--DEBUG--"
#echo "TRAVIS_REPO_SLUG: $TRAVIS_REPO_SLUG"
#echo "TRAVIS_PHP_VERSION: $TRAVIS_PHP_VERSION"
#echo "TRAVIS_PULL_REQUEST: $TRAVIS_PULL_REQUEST"
#echo "TRAVIS_BRANCH: $TRAVIS_BRANCH"
 
if [ "$TRAVIS_REPO_SLUG" == "lamamos/lamadmin" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ] && [ "$TRAVIS_PHP_VERSION" == "5.5" ]; then

  #we ar going to generate the documentation only if we are in the main repo and not in a pull request
  #so we don't need to install everything in the other cases

  ##install doxygen
  sudo apt-get install doxygen
  ##install yuidoc
  npm -g install yuidocjs
  ## doxygen : output folder
  mkdir -p doc

 
fi

