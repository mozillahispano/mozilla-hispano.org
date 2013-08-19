#!/bin/sh

# syntax:
# merge_po.sh

pluginroot=`dirname $0`/..
locale_dir=$pluginroot/locale
plugin_language_dir=$pluginroot/languages

for lang in `find ${locale_dir} -type f -name "*.po" -not -path '*/db_LB/*'`; do
    dir=`dirname ${lang}`
    stem=`basename ${lang} .po`
    echo "${stem}:"
    pocompile -o ${plugin_language_dir}/${stem}.mo -i ${locale_dir}/${stem}.po
    cp ${locale_dir}/${stem}.po ${plugin_language_dir}/${stem}.po
done

