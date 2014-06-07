#!/bin/bash

find . \( -iname "*.php" ! -path "./vendor*" \) -exec php -l "{}" \;
phpSyntaxContainsErrors=`echo $?`


if [ $phpSyntaxContainsErrors -eq 0 ]
then
	exit 0
else
	exit $phpSyntaxContainsErrors
fi
