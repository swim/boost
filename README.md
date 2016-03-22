## About
Drupal 8 port of the fantastic D7 Boost module.

## Installation
Download and install like any D8 module. Once installed visit a page as an anonymous user to
generate a local cache file. Check for the X-Boost response header to confirm it's working. There
are two X-Boost response headers to look for; partial and full. Patial means that PHP is still executing
and that the apache or nginx config has not been applied correctly.

## Legend
X-Boost-Cache: partial - bad

X-Boost-Cache: full - good

## Nginx config
The following is a very basic example of an nginx configuration for use with Boost.

```
server {
  server_name mydomain.com;
  access_log /srv/www/mydomain.com/logs/access.log;
  error_log /srv/www/mydomain.com/logs/error.log;
  root /srv/www/mydomain.com/public_html;

  fastcgi_param SCRIPT_NAME $fastcgi_script_name;

  location ~ (^|/)\. {
    return 403;
  }
    
  location / {
    index index.html index.php;
    expires max;

    set $request_url $request_uri;
    if ($request_uri ~ ^/admin/(.*)$) {
      rewrite ^ /index.php;
    }

    location ~* ^(?:.+\.(?:htaccess|make|txt|engine|inc|info|install|module|profile|po|pot|sh|.*sql|test|theme|tpl(?:\.php)?|xtmpl)|code-style\.pl|/Entries.*|/Repository|/Root|/Tag|/Template)$ {
      return 404;
    }

    add_header X-Boost-Cache "full";
    try_files $uri @rewrite;
  }

  location @rewrite {
    gzip_static on;

    if ($request_method = POST) {
      rewrite ^ /index.php;
    }

    set $boost_uri "${request_uri}.html";
    try_files ^ /sites/default/files/boost$boost_uri @drupal;
  }

  location @drupal {
    rewrite ^ /index.php;
  }

  location ~ \.php$ {
    include /etc/nginx/fastcgi_params;
    fastcgi_pass  127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_pass_header Set-Cookie;
    fastcgi_pass_header Cookie;
    fastcgi_ignore_headers Cache-Control Expires Set-Cookie;
    fastcgi_param SCRIPT_FILENAME /srv/www/mydomain.com/public_html$fastcgi_script_name;
  }
}
```

## Apache config
@todo

## The going on's
1. Page is requested for the first time and is built dynamically by PHP.
2. Page is cached on the local file system; if accessed by an anonymous user.
3. When the route is requested again the page is served from the file system.
4. For this module to do anything worth while the apache or nginx config must be applied.

## Roadmap
1. Sub-module Boost crawler; implementing Guzzle.
2. Generate file cache via batch function.
3. Implement cron to invalidate and re-generate cache.