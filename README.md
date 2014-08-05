craftingstore.net
=================

The sources for the abandon craftingstore.net. It is a almost fully functional shop management system for Bukkit based Minecraft game servers.


Development began in summer 2011 and january 2014. 

It's based on the [Smarty Template Engine](http://www.smarty.net/) and a custom MVC inspired framework.

Because it began as a student project it had some structural developmental bootlenecks. Yet it has several optimations and runs pretty smooth on an apache webserver with memcache etc.

If someone wants to pick it up, feel free to message us, until that it is avaiable under Creative Commons license BY-NC-SA:

[![Creative Commons BY-NC-SA](https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png)](http://creativecommons.org/licenses/by-nc-sa/4.0/)



How to use it
=================

* Get Apache/NGINX with PHP 5.3+ integration
* create a MySQL database and execute the SQL/create_structure.sql to create the structure. 
* load some default data with SQL/load_languages_and_templates.sql
* edit each file that contains the phrase 'edited for github', for example config/config.inc.php
* create a wildcard subdomain (in our case: *.craftingstore.net) leading to the root directory of the web application
* (create secure.yourdomain.com leading to the root of the web app) - probably not necessary due to the last step

PHP.ini Settings:
=================
    safe_mode off
    open_basedir {Application root}/:/tmp/
    file_uploads on
    magic_quotes_gpc off
    log_errors on
    display_errors off
    
    
it is possible that there are more little tweaks, e.g. in the apache configuration. Feel free to contact us

