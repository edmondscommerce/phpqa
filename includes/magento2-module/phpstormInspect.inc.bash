#set -x

echo "This file exists as an alternative to phpstan"
echo "It was intended to run more quickly, but because of the overhead of PHPStorm this ended up not being the case"
echo "If there are a lot of paths to scan, this will not run quickly"

set +e

vendorFolders=()
while IFS=  read -r -d $'\0'; do
    vendorFolders+=("$REPLY")
done < <(find $projectRoot/vendor/ -maxdepth 2 -mindepth 2 -type d -printf "vendor/%P\0")


nonstandardVendorPaths=()

for i in "${vendorFolders[@]}"; do
    skip=
    for j in "${pathsToIgnore[@]}"; do
        [[ $i == $j ]] && { skip=1; break; }
    done
    [[ -n $skip ]] || nonstandardVendorPaths+=("$i")
done

scanPaths=()
for p in "${pathsToCheck[@]}"
do
    if [[ "$p" != *vendor ]]
    then
        scanPaths+=("$p")
    fi
done

for i in "${nonstandardVendorPaths[@]}"
do
    scanPaths+=("$i")
done

for scanPath in "${scanPaths[@]}"
do
    echo "Running inspection against $scanPath"
    /opt/PhpStorm/bin/inspect.sh\
 $projectRoot\
 $phpstormInspectionProfileConfigPath\
 /tmp/test\
 -v2\
 -d $scanPath
done




exit 1