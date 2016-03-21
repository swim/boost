# about
Drupal 8 port of the fantastic D7 Boost module.

# installation
Download and install like any D8 module. Once installed visit a page as an anonymous user to
generate a local cache file. Check for the X-Boost response header to confirm it's working. There
are two X-Boost response headers to look for; partial and full. Patial means that PHP is still executing
and that the apache or nginx config has not been applied properly.

# legend
X-Boost-Cache: partial - bad

X-Boost-Cache: full - good

# what happens
1. Page is requested for the first time and is built dynamically by PHP.
2. Page is cached on the local file system; if accessed by an anonymous user.
3. When the route is requested again the page is served from the file system.
4. For this module to do anything worth while the apache or nginx config must be applied.
