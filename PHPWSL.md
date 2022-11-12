# PHP with WSL

With **Ubuntu 20.04 LTS** as distro. \
The goal is to use PHP from windows under WSL, without installing PHP on the linux distro.

## Using php.exe with WSL

Editing `~/.bash_profile`, add this lines.

```shell
alias php='php.exe'
alias phpdbg='phpdbg.exe'
alias composer='cmd.exe /c composer'
```

## Using PHP_INI_SCAN_DIR

Using directory `C:\phpini` for additionnal ini files, with a php module (PHP ts).

```apache
SetEnv PHP_INI_SCAN_DIR "C:/phpini"
LoadModule php7_module <the_module>
```

With PHP nts, environment variables is required.

- In _Advanced System Setting_ (fr-FR _Paramètres système avancés_), click _Environment Variables..._ (fr-FR _Variables d'environnement..._).
- In _System Variables_, add `PHP_INI_SCAN_DIR`

## Sharing environment

In _Advanced System Setting_, add or extend `WSLENV` with value `PHP_INI_SCAN_DIR/p`

More infirmations here : [share environment vars between wsl and windows][WSLENV]

[WSLENV]: https://devblogs.microsoft.com/commandline/share-environment-vars-between-wsl-and-windows/