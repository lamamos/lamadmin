#!/bin/bash

find . -iname "*.php" -exec php -l "{}" \;
phpSyntaxContainsErrors=`echo $?`


if [ phpSyntaxContainsErrors -eq 0 ]
then
	return 0
else
	return phpSyntaxContainsErrors
fi