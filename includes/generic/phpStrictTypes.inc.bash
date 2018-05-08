for d in "${pathsToCheck[@]}"
do
    for f in $(
                find $d \
                    -name '*.php' \
                    -o -name '*.phtml' \
                    -exec grep -L 'strict_types' {} \;
              );
    do
        echo "Found file with no strict types:"
        echo $f;
        echo
        read -p "Would you like to fix? " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]
        then
            sed -i 's/<?php/<\?php declare(strict_types=1);/g' $f;
            echo "fixed $f"
        else
            echo "skipped $f"
        fi
    done
done