#!/bin/sh

# syntax:
# merge_po.sh

pluginroot=`dirname $0`/..
locale_dir=$pluginroot/locale
plugin_language_dir=$pluginroot/languages

for lang in `find $locale_dir -type f -name "*.po" -not -path '*/db_LB/*'`; do
    dir=`dirname $lang`
    stem=`basename $lang .po`
    echo "$stem:"
    msgmerge -o ${dir}/${stem}.po.tmp ${dir}/${stem}.po $plugin_language_dir/${stem}.po
    mv ${dir}/${stem}.po.tmp ${dir}/${stem}.po
done

# update any CHARSETs to UTF-8.
for file in $plugin_language_dir/*.po ; do
    mv $file $file.old
    sed 's/CHARSET/UTF-8/g' $file.old > $file
    rm -f $file.old
done

