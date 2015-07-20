# Automated Joomla Installations
During PBF (Pizza-Bug-Fun) sessions, many people are struggling with
setting up new Joomla environments, ready for testing. To ease this
process, this project forms an attempt to roll out new fresh Joomla
sites with exactly those requirements needed.

## Current state
Beta.

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
* Make sure `composer` is installed on the server
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

## Notes: Setting the Joomla version
Normally the config value `joomla_version` (under `server`) is set to `latest`, which simply means the latest available stable Joomla
version is used. You can also install specific versions (`3.4.2`) or a Git branch (`master`, `staging`).

Besides this option, the `batch.php` file also contains a procedure to update the `joomla` command with the latest version. This is
determined via the config value `server.version_refresh`. It makes sure that an existing PBF repository is updating its definition of
the `latest` Joomla version.

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

    "password_reset": 1,

## Tip: Adding extensions by URL
You can automatically install extensions by dumping them in the `source/extensions` folder. You can
also automatically install extensions by defining them in the `config.json` file:

    {
        "extensions": {
            "dutch": "http://joomlacode.org/gf/download/frsrelease/20031/162287/nl-NL_joomla_lang_full_3.4.1v1.zip",
        },
    }

You can check the URL http://update.joomla.org/language/translationlist_3.xml for instance for
translations. Make sure to use the URL pointining to the final ZIP file, not an URL to a XML file.
In effect, this will download the ZIP to the `source/extensions` folder.

## Troubleshooting tips
Make sure the default `php` binary is the CLI binary. If for instance the default `/usr/bin/php` binary is a FastCGI executable, the
CLI tasks of this project will not run. If rearranging the PATH is no option, modify the `config.json` file to point to the right binary.

    {
        "server": {
            "php_binary": "/usr/local/bin/php"
        }
    }

Also make sure the `source/joomla-console/bin/joomla` script uses the right binary:

    #!/usr/local/bin/php

Make sure `display_errors` is `On`. Also make sure the `memory_limit` is large enough. Enable the `log` flag in the configuration if
you want to log the commands to the `logs` folder.

Don't forget to run the command steps in the `source` folder. This also requires `composer` to be already installed on the server.

If in a CGI environment, make sure all files and folders are only writable for the current user:

    find . -type f -exec chmod 644 {} \;
    find . -type d -exec chmod 755 {} \;

## Todo
* Command-line script to destroy or create all sites in batches
* Flexible way to install language packs with URL that is no longer dependant on Joomla version
