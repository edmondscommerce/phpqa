#set -x
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

for i in "${nonstandardVendorPaths[@]}"
do
    echo $i
done

/tmp/host/PhpStorm/bin/inspect.sh\
 $projectRoot\
 $phpstormInspectionProfileConfigPath\
 /tmp/test\
 -v2\
 -d /var/lib/lxc/workshop-badgemaster/rootfs/var/www/vhosts/www.badgemaster.workshop.developmagento.co.uk/app/code \
 -d /var/lib/lxc/workshop-badgemaster/rootfs/var/www/vhosts/www.badgemaster.workshop.developmagento.co.uk/app/design \
 -d /var/lib/lxc/workshop-badgemaster/rootfs/var/www/vhosts/www.badgemaster.workshop.developmagento.co.uk/vendor

set +x

exit 1