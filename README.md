# Automated Joomla Installations
During PBF (Pizza-Bug-Fun) sessions, many people are struggling with
setting up new Joomla environments, ready for testing. To ease this
process, this project forms an attempt to roll out new fresh Joomla
sites with exactly those requirements needed.

## Current state
Alpha.

## Requirements
* MySQL user that has privilege to create new databases (DB prefix: `example\_`)
* Composer installed on server (needed to install `joomla-console`)
* Download extra packages to `source/extensions`

## Included steps
* Management interface (`index.php`) to quickly create and dump sites
* Create a new site using `joomla-console`
    * `joomla-console` requires Yireo PR to set custom DB prefix
* Install additional Joomla extensions
    * Drop extension-packages in `source/extensions` folder and thats it
    * Currently included:
        * Patchtester component
    * Optional:
        * OScontent for dummy content
        * Language-packs
* Modify other things using `cli/pbf.php` script
    * Disable debugger plugin
    * Set lifetime to 180s

## Setup
* Copy `config.json.sample` to `config.json`
    * Modify the MySQL data
    * Modify strings if needed
* Go to the `source` folder and run the `setup.sh` script, to install `joomla-console` plus download packages

## Site structure
```
./bin
    ./create.sh - Simple script to install a site using joomla-console
./joomla1
./joomla2
./source
    ./joomla-console - Git clone 
...
```
