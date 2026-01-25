REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.6.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
1. git clone https://github.com/Viktor07071986/yii2_docker_rabbit_project.git
2. composer install
3. docker compose up -d
4. docker compose exec web php yii migrate
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
url site - http://localhost:8080
url db - http://localhost:6080
url rabbit - http://localhost:15672
~~~

### Install with Docker

Start the container

    docker-compose up -d

You can then access the application through the following URL:

    url site - http://localhost:8080
    url db - http://localhost:6080
    url rabbit - http://localhost:15672

**NOTES:**
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches