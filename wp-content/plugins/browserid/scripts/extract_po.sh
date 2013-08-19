#!/bin/bash

# Extract strings from the browserid.php file into browserid.pot for l10n

pluginroot=`dirname $0`/..
localeroot=$pluginroot/locale

if [ ! -e $localeroot/browserid.pot ]; then
  touch $localeroot/browserid.pot
fi

xgettext -j -L PHP --keyword="__" --output-dir=$localeroot --output=browserid.pot --package-name="browserid-wordpress" --from-code=utf-8 $pluginroot/browserid.php

