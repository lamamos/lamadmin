#!/bin/bash

find . \( -iname "*.php" ! -path "./vendor*" \) -print0 | xargs -0 -n1 php -l
phpSyntaxContainsErrors=`echo $?`


if [ $phpSyntaxContainsErrors -eq 0 ]
then
	exit 0
else
	exit $phpSyntaxContainsErrors
fi
