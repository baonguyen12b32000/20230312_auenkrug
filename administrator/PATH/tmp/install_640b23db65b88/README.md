# Novarain Package Installer #

Novarain Package Installer is a helper Joomla System Plugin that undertakes the task to install the Novarain Framework and all extensions found under the /packages/ directory. 

## Checks ##

In order to procceed with the installation, the environment should pass the checks of the minimum required version of PHP and Joomla. If not all checks pass the installation aborts. Otherwise, the installation proccees with the following order:

* Installs the Novarain Framework only if it's not already installed and the current version is newer.
* Loops through /packages/ directory and installes one-by-one each package
* Updates Update Sites table with the latest update servers URL for each Novarain extension
* Uninstalles itself