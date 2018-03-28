# Description

This is the WmtsBundle that was originally created for the HRO project. It was adapted to work with Mapbender v3.0.6 (v3.0.7 respectively).

## Installation

**Clean mapbender-starter**

* clone the WmtsBundle into mapbender directory

```bash
cd application/mapbender/src
git clone git@repo.wheregroup.com:ckuntzsch/WmtsBundle.git
```

**Project specific installation**

* clone the WmtsBundle into project directory

```bash
cd application/src/Mapbender
git clone git@repo.wheregroup.com:ckuntzsch/WmtsBundle.git
```

* add WmtsBundle to `routing.yml` and `AppKernel.php`

```bash
cd application/app/config
vi routing.yml
```

```yml
#...
    
mapbender_wmtsbundle:
    resource: "@MapbenderWmtsBundle/Controller/"
    type: annotation

#...
```

```bash
cd application/app
vi AppKernel.php
```

```php
// Mapbender3 bundles
//...
new Mapbender\WmtsBundle\MapbenderWmtsBundle(),
//...
```

* update database schema

```bash
cd application
app/console doctrine:schema:update --force
```


