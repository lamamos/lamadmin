#!/bin/bash

find . -iname "*.php" -exec php -l "{}" \;
