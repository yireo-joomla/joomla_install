#!/bin/bash
#
# Very simple script to create a new Joomla site
#
site=$1
cwd=`pwd`

# Check for arguments
if [ $# -eq 0 ] ; then
    echo "Argument needed"
    exit
fi

# Create the site
#./bin/joomla site:create \
#    --www=$cwd \
#    --mysql=pbf:vNeqWZUuVN5KzvGW \
#    --mysql_db_prefix=pbf_ \
#    --sample-data=testing \
#    $site

# Install any extension in the extensions folder
for extension in source/extensions/* ; do
./bin/joomla extension:installfile \
    --www=$cwd \
    --mysql=pbf:vNeqWZUuVN5KzvGW \
    --mysql_db_prefix=pbf_ \
    $site $extension
done

# End
