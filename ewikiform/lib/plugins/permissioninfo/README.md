# Dokuwiki Permission Info Plugin

This admin plugin shows which groups exist, what their namespace and page permissions are and which users are in which groups. Additionally, it shows the namespace and page permissions for each user - derived from the groups the user is in. 


## Installation

Download and extract the permissioninfo folder into your plugins folder or use the plugin manager. 

# Known Issues

* I have only tested this plugin with the file-based ACL. 
* Doesn't work correctly with LDAP permissions.
* Doesn't show anything when no user- or group specific rule was set (standard DokuWiki installation)

## History

 * 2007-02-07 - Initial release
 * 2007-10-10 - Some error corrections
 * 2013-06-11 - New version for current Dokuwiki (>= Adora Belle). Uses jQuery UI and modern CSS features
