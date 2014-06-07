#!/bin/bash

find . \( -iname "*.php" ! -path "./vendor*" \) -print0 | xargs -0 -n1 php -l
phpSyntaxContainsErrors=`echo $?`


find . \( -iname "*.js" ! -path "./vendor*" ! -path "./doc*" ! -iname "*.min.js" \) -print0 | xargs -0 -n1 jshint
jsSyntaxContainsErrors=`echo $?`


if [ $phpSyntaxContainsErrors != 0 ]
then
	exit $phpSyntaxContainsErrors
fi

if [ $jsSyntaxContainsErrors != 0 ]
then
	exit $jsSyntaxContainsErrors
fi

exit 0