## about
Drupal 8 port of the fantastic D7 Boost module.

## installation
Download and install like any D8 module. Once installed visit a page as an anonymous user to
generate a local cache file. Check for the X-Boost response header to confirm it's working. There
are two X-Boost response headers to look for; partial and full. Patial means that PHP is still executing
and that the apache or nginx config has not been applied properly.

## legend
X-Boost-Cache: partial - bad

X-Boost-Cache: full - good

## nginx config
The following is an example of an nginx configuration file for use with Boost.

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

    add_header X-Boost-Cache "full";
    try_files $uri @rewrite;
  }

  location @rewrite {
    set $boost_uri "${request_uri}.html";
    rewrite ^ /sites/default/files/boost$boost_uri;
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

## apache config
@todo

## what happens
1. Page is requested for the first time and is built dynamically by PHP.
2. Page is cached on the local file system; if accessed by an anonymous user.
3. When the route is requested again the page is served from the file system.
4. For this module to do anything worth while the apache or nginx config must be applied.
