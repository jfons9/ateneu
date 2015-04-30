FlashPlayer plugin for DokuWiki 2008-05-05 or later
Version 2010-10-24

Current version at http://arnowelzel.de/wiki/misc/flashplayer

Uses the JW FLV Player 5.3.1397,
see http://www.jeroenwijering.com/?item=JW_FLV_Player

Based on the work of Sam Hall (http://www.dokuwiki.org/plugin:flashplayer),
extended by Arno Welzel (http://arnowelzel.de).

Licensed under the creative commons license BY-NC-SA
http://creativecommons.org/licenses/by-nc-sa/3.0/deed


Installation
------------

Copy all the files into a directory "flashplayer" within the plugin-directory
if your DokuWiki installation

IMPORTANT: When updating an older version, REMOVE the following line from
the HEAD section of the template:
   
<script type="text/javascript" src="<?php echo DOKU_BASE?>lib/plugins/flashplayer/player/swfobject.js"></script>

This is NO LONGER neccessary since version 2008-10-12!


Usage
-----

To embed a flash video inside a Wiki page, use the following code:

<flashplayer width="width in pixels" height="height in pixels" position="0|1|2">flash vars</flashplayer>

With "position" you can (optionally) define the alignment:

0 - left
1 - centered
2 - right

It is not possible yet to let text flow around the video.

Example - display a video "/demo.flv" with 480*380 pixels, use "/demo.jpg"
for the preview image:

<flashplayer width="480" height="380">file=/demo.flv&image=/demo.jpg</flashplayer>

The flash vars tell the player, which file to play and which image to display
for the static preview. You can also just provide a video without preview image:

<flashplayer width="480" height="380">file=/demo.flv</flashplayer>

A detailed description of all possible flash vars can be found at
http://code.jeroenwijering.com/trac/wiki/FlashVars.


Adding additional languages
---------------------------

Currently, the flashplayer plugin only supports English and German.

If you want to add additional languages, just have a look in the "lang"
directory. Just add the language you need by copying an existing language
(according the conventions in "/inc/lang" of DokuWiki) and translate the
appropriate file "lang.php" - be careful, as this is PHP code and has to
be saved as UTF-8!


Acknowledgments
---------------

Jeroen Wijering for his JW Player
Sam Hall for the first version of this plugin


History
-------

2008-09-21  - Modified plugin by Sam Hall to generate valid XHTML and
              using SWFObjects.

2008-10-12  - Moved SWFObjects to plugin script file, so it has not to
              be included manually in the DokuWiki template.

2009-01-17  - Updated JW FLV Player to version 4.3.132.

2009-06-14  - Added "position" parameter.

2009-12-25  - Updated JW FLV to Version 5.0.753.

2010-02-26  - Removed SWFObjects and changed back to static output

2010-02-26a - Changed OBJECT element, so it works with WebKit too (Chrome, Safari)

2010-05-15  - Updated JW FLV to version 5.1.897.

2010-10-24  - Updated JW FLV to version 5.3.1397
