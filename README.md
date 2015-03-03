# Automated Joomla Installations
During PBF (Pizza-Bug-Fun) sessions, many people are struggling with
setting up new Joomla environments, ready for testing. To ease this
process, this project forms an attempt to roll out new fresh Joomla
sites with exactly those requirements needed.

## Current state
None.

## Included steps
* Create a MySQL database
    * Admin-user gets CREATE privileges for `pbf\_%` databases
    * `CREATE DB 'pbf_joomla1'`
    * Create a db-user `pbf_joomla1` with random password
    * Store db-credentials in `build.json`
* Checkout Joomla sources
    * `git clone https://github.com/joomla/joomla-cms.git`
    * `git checkout staging`
    * Select right branch from `requirements.json`
* Install Joomla automatically
    * Select db-credentials from `build.json`
    * Select which demo data from `requirements.json`
* Install additional Joomla extensions
    * Patchtester component
    * Optional: OScontent for dummy content
    * Optional: Language-packs
* Optionally modify other things in Joomla
    * Set lifetime to 180s

## Site structure
```
./cli
    @todo: Build tools
./joomla1
    ./public_html
    ./build.json
    ./requirements.json
./joomla2
    ./public_html
    ./build.json
    ./requirements.json
...
```

## Tools
* PHP-scripts to build site
    * Handling 
* git
* joomla CLI tool from JoomlaTools (?)

## Future steps
* Investigate option for using `composer`
* Simple Bootstrap 3 management interface to ditch and create sites

