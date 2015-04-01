# Automated Joomla Installations
During PBF (Pizza-Bug-Fun) sessions, many people are struggling with
setting up new Joomla environments, ready for testing. To ease this
process, this project forms an attempt to roll out new fresh Joomla
sites with exactly those requirements needed.

## Current state
Alpha.

## Requirements
* MySQL user that has privilege to create new databases (DB prefix: `example\_`)
* PHP `exec()` support
* Composer installed on server to install `joomla-console`

## Included steps
* Management interface (`index.php`) to quickly create and dump sites
* Create a new site using `joomla-console`
* Disable debugger plugin (`cli/pbf.php`)
* Install Patchtester component

## Usage
Setup the GUI (see: Setup). Click on "Create Site". Navigate to the Joomla Administrator and login
with username `admin` and your password. Passwords are automatically being generated and visible in the GUI
if you have used the secret-token successfully.

## Setup
* Copy `config.json.sample` to `config.json`
    * Modify the MySQL data
    * Modify strings if needed
* Go to the `source` folder and run the `setup.sh` script, to install `joomla-console` plus download packages
* Add optional packages to `source/extensions`
    * OScontent for dummy content
    * Language-packs

## Notes: Using the secret token
The GUI is accessible for anyone. However, if you want to create or destroy sites, or see the generated passwords,
you need to access the GUI with a token:

    http://YOURPBF/index.php?secret=TOKEN

Once the token is set in the URL, a redirect occurs, and the token is saved in the browser session.

This token is configured in the `config.json` file and should only be available to PBF instructors.

## Notes: Creating a new site
The GUI (`index.php`) allows you to create a new site. This is by using the `joomla-console` CLI tool.
There currently is a PR open for the original JoomlaTools project to include a custom DB prefix.
The `source/setup.sh` script therefore contains the Yireo clone of `joomla-console` which contains this PR.

https://github.com/joomlatools/joomla-console

## Notes: Site structure
```
./bin
    ./create.sh - Simple script to install a site using joomla-console
./joomla1
./joomla2
./source
    ./joomla-console - Git clone 
...
```

## Notes: Setting the password type
In your `config.json` you can set the option `password_type`.
See the `config.json.sample` file for an example.
The `password_type` can be one of the following:
* `default` = The username and password of the Super User will be the same as the site name. For example `joomla1`.
* `admin` = The username and password of the Super User will be `admin`
* `generate` = The username of the Super User will be `admin`. The password is automatically generated.

    ```
    "password_type": "generate",
    ```

Additionally, there is the option `password_reset` with a default value `0`. If this option is set
to `1`, it will require the Super User to change his/her password at first login. (Technically, the
`require_reset` flag in the `com_users` table is set.

    ```
    "password_reset": 1,
    ```

## Todo
* Command-line script to destroy or create all sites in batches
* Add language-packs
