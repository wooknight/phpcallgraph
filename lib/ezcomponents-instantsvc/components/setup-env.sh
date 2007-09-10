#!/bin/bash

cd trunk

echo "Creating autoload environment for 'trunk':";
if test -d autoload; then
	echo "Autoload directory exists."
else
	echo "Creating missing 'autoload' directory."
	mkdir autoload
fi

for i in */src/*autoload.php; do
	p=`echo $i | cut -d / -f 1`;
	r=`echo $i | cut -d / -f 2`;
	b=`echo $i | cut -d / -f 3`;

	if test ! $p == "autoload"; then
		if test ! $r == "releases"; then
			if test -L autoload/$b; then
				echo "Symlink for $b to $i exists."
			else
				echo "Creating symlink from $i to autoload/$b."
				ln -s "../$i" "autoload/$b"
			fi
		fi
	fi
done

cd -

if test -d stable; then
	echo "Setting up environment for 'stable'"

	if ! test -L stable/autoload/base_autoload.php; then
		ln -s ../../trunk/Base/src/base_autoload.php stable/autoload/base_autoload.php
	fi

	if ! test -L stable/autoload/test_autoload.php; then
		ln -s ../../trunk/UnitTest/src/test_autoload.php stable/autoload/test_autoload.php
	fi

	for i in Base UnitTest; do
		if ! test -L stable/$i; then
			ln -s ../trunk/$i stable/$i
		fi
	done

	echo "- Creating autoload files:"

	for i in `cat stable/branch-info`; do
		componentName=`echo $i | cut -d / -f 1`;
		componentVersion=`echo $i | cut -d / -f 2`;
		echo '  - ' $componentName
		for j in stable/$i/src/*_autoload.php; do
			targetFile='stable/autoload/'`echo $j | cut -d / -f 5`;
			cat $j | sed "s/$componentName\//$componentName\/$componentVersion\//g" > $targetFile
		done
	done
fi
