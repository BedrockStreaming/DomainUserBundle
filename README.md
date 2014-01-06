# DomainUserBundle

DomainUserBundle provides User authentication by subdomain.

## Installation
Add this line in your composer.json:
```json
{
    "require": {
        "m6web/domain-user-bundle": "~1.0"
    }
}
```

Update your vendors:
```sh
$ composer update m6web/redis-mock
```
Add to your `AppKernel.php`:
```
    new M6Web\Bundle\DomainUserBundle\M6WebDomainUserBundle(),
    new M6Web\Bundle\FirewallBundle\M6WebFirewallBundle(),
```

## Configuration
Modify your routes to add a parameter in your host requirement:
```yaml
routes:
    resource: api_routing.yml
    host:     {client}api.exemple.com
    requirements:
        client: ([a-z0-9]+\.)?
    defaults:
        client ""
```

Add in your `app/config.yml`:
```yaml
m6_web_domain_user:
    default_cache:    300      # Default cache duration
    router_parameter: client   # Parameter in the host requirement
    default_user:     public   # User when the parameter is not found
    users_dir:        %kernel.root_dir%/config/users # Directory containing the user configs
```

Add a user `app/config/users/public.yml`:
```yaml
firewall:
    user_access: # Configure IP restrictions with FirewallBundle
        default_state: false
        lists:
            internal: true

    allow: # Filter routes alllowed to this user
        default: true
        methods:
            delete: false # Override by method
        resources:
            article: false # Override by routes configured with "defaults: {resource: article}"
        route:
            post_comment: false # Override by route name
cache:
    defaults: 60 # Override the default cache duration
    routes:
        hello: 86400 # Override by route name

entities:
    article:
        active: true # Arbitrary flag you can use to filter your entities in your repositories
```

## Tests

```shell
$ ./vendor/bin/atoum
```

## Credits

Developped by the [Cytron Team](http://cytron.fr/) of [M6 Web](http://tech.m6web.fr/).
Tested with [atoum](http://atoum.org).

## License

DomainUserBundle is licensed under the [MIT license](LICENSE).
