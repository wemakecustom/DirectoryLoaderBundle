# Symfony Directory Loader

Recursively import directories of configuration.

This works for your main configuration files and the router.

See https://github.com/symfony/symfony-standard/issues/599

## Installation

Add the following in your root composer.json file:

```json
{
    "require": {
        "wemakecustom/directory-loader-bundle": "~1.0@dev"
    },
}
```

And modify your `app/AppKernel.php`:
```php
<?php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new WMC\DirectoryLoaderBundle\WMCDirectoryLoaderBundle,
        );
        // ...
    }

    protected function getContainerLoader(ContainerInterface $container)
    {
        $loader = parent::getContainerLoader($container);
        $locator = new FileLocator($this);
     
        // Add additional loader to the resolver
        $resolver = $loader->getResolver();
        $resolver->addLoader(new DirectoryFileLoader($container, $locator));
     
        return $loader;
    }
?>
```

Strictly speaking, registering the Bundle is only necessary if you want the fonctionnality
in the routing files and overloading the ContainerLoader is only necessary for the main
configuration files, but you do what you want.

## Usage

The main goal of this bundle is to drop configuration files in folder without modifying
the main `config.yml`. This way, each file can group all (and only) the configuration
related to a specific bundle.

### Using a directory based on the environment

Instead of using `config_ENV.yml`, one may use a directory for each environment.

For example:

```
└── app
   ├── config
   │  ├── common
   │  │  └── assetic.yml
   │  │  └── framework.yml
   │  │  └── security.yml
   │  ├── dev
   │  │  └── framework.yml
   │  │  └── assetic.yml
   │  ├── prod
   │  │  └── assetic.yml
   │  │  └── google_analytics.yml
   │  ├── config.yml
   │  └── parameters.yml
   └── AppKernel.php
```

```php
<?php
    // app/AppKernel.php
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');
        $loader->load(__DIR__.'/config/' . $this->getEnvironment() . '/');
    }
?>
```

```yaml
# app/config/config.yml
imports:
    - { resource: 'parameters.yml' }
    - { resource: 'common/' }
```

### Using directories for parameters

Parameters can be loaded as directories as well in the same fashion.

Moreover, an interesting concept is to have a folder for core parameters,
global to your application and local parameters, specific to each installation.

For example:

```
└── app
   ├── config
   │  └── …
   ├── parameters
   │  └── common
   │  │  └── core.yml
   │  │  └── i18n.yml
   │  ├── dist
   │  │  └── mailer.yml
   │  ├── local
   │  │  └── .gitignore
   │  └── config.yml
   └── AppKernel.php
```

`app/parameters/local/*.yml` will be ignored and its content will be the files from
`app/parameters/dist`, copied and modified accordingly.

You can then modify your `config.yml`: 
```yaml
# app/config/config.yml
imports:
    - { resource: 'parameters/common/' }
    - { resource: 'parameters/local/' }
    - { resource: 'common/' }
```

### Using a composer script to generate configuration files

To fill the missing files on each composer run, you can replace the native parameters
builder by [wemakecustom/composer-script-utils](https://github.com/wemakecustom/composer-script-utils)
and configure your `composer.json`, replacing `Incenteev\ParameterHandler\ScriptHandler`

```json
{
    "require": {
        # ...
        "wemakecustom/directory-loader-bundle": "1.0.*@dev",
        "wemakecustom/composer-script-utils": ">=0.3"
    },
    "scripts": {
        "post-install-cmd": [
            "WMC\\Composer\\Utils\\ConfigFile\\ConfigDir::updateDirs",
            # remove this: "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters"
            # ...
        ],
        "post-update-cmd": [
            "WMC\\Composer\\Utils\\ConfigFile\\ConfigDir::updateDirs",
            # remove this: "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters"
            # ...
        ]
    },
    "extra": {
        "update-config-dirs": {
            "app/config/parameters/dist": "app/config/parameters/local"
        },
        # # remove this: 
        # "incenteev-parameters": {
        #     "file": "app/config/parameters.yml"
        # },
    }
}
```
