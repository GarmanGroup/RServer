RServer
=======

A simple client/server web-application for running R scripts and logging and storing output and produced images

Required software
-----------------
- webserver with PHP
- SQL server (ie MySQL)
- R with packages DBI, RMySQL, Cairo
- The example environment (defined in the .sql file) uses an existing GraphicsMagick installation to resize images (command: gm)


Warning
-------
Do *not* run this on a publically accessible webserver.
The server does not include any authentication methods, so it essentially allows anyone to run any commands on your machine.
Ideally you want to set up your webserver to only serve to localhost, and then use e.g. an SSH-tunnel to access the service.
As an absolute minimum you should set up your webserver to ask for a username and password. You have been warned.
