#!/bin/bash
#
# Run this script when setting up a new PBF environment
#

# Get the cloned Yireo version of joomla-console
git clone https://github.com/yireo/joomla-console.git

# Make the script executable
cd joomla-console
chmod 755 bin/joomla

# Run composer to install things
composer install

# Go to the extensions folder
cd ..
mkdir extensions/
cd extensions/

# Download packages to install
wget https://github.com/joomla-extensions/patchtester/releases/download/2.0.0.beta3/com_patchtester.tar.gz
