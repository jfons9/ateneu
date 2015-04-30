/**
 * Javascript for the dokukiwix plugin
 *
 * @author Andreas Gohr <andi@splitbrain.org>
 * @author Yann Hamon <yann.hamon@gmail.com>
 */

/**
 * Class to hold some values
 */
function plugin_dokukiwix_class(){
    this.pages = null;
    this.page = null;
    this.sack = null;
    this.done = 1;
    this.count = 0;
    this.play = 0;
    this.timeoutid = 0;
}
var pl_dokukiwix = new plugin_dokukiwix_class();
pl_dokukiwix.sack = new sack(DOKU_BASE + 'lib/plugins/dokukiwix/ajax.php');
pl_dokukiwix.sack.AjaxFailedAlert = '';
pl_dokukiwix.sack.encodeURIString = false;

/**
 * Display the loading gif
 */
function plugin_dokukiwix_showThrobber(on){
    obj = document.getElementById('pl_dokukiwix_throbber');
    if(on){
        obj.style.visibility='visible';
        obj.style.display='inline';
    }else{
        obj.style.visibility='hidden';
        obj.style.display='none';
    }
}

/**
 * Display the loading gif
 */
function plugin_dokukiwix_showStopButton(on){
    obj = document.getElementById('pl_dokukiwix_stop');
    if(on){
        obj.style.visibility='visible';
    }else{
        obj.style.visibility='hidden';
    }
}


/**
 * Gives textual feedback
 */
function plugin_dokukiwix_status(text){
    obj = document.getElementById('pl_dokukiwix_out');
    obj.innerHTML = text;
}

function plugin_dokukiwix_reinit() {
    window.clearTimeout(pl_dokukiwix.timeoutid);
    plugin_dokukiwix_showThrobber(false);
    pl_dokukiwix.pages = null;
    pl_dokukiwix.page = null;
    pl_dokukiwix.done = 1;
    pl_dokukiwix.count = 0;
    pl_dokukiwix.play = 0;
    pl_dokukiwix.timeoutid = 0;
    plugin_dokukiwix_showStopButton(false);
    plugin_dokukiwix_showThrobber(false);
    obj = document.getElementById('pl_dokukiwix_toggle_startpause');
    obj.src="lib/plugins/dokukiwix/images/play.png";
}

/**
 * Callback. Gets the list of all pages
 */
function plugin_dokukiwix_cb_pages(){
    data = this.response;
    pl_dokukiwix.pages = data.split("\n");
    pl_dokukiwix.count = pl_dokukiwix.pages.length;
    plugin_dokukiwix_status(pl_dokukiwix.pages.length+" pages found");
    plugin_dokukiwix_log('Found '+pl_dokukiwix.pages.length+' pages.');
    pl_dokukiwix.page = pl_dokukiwix.pages.shift();
    pl_dokukiwix.timeoutid = window.setTimeout("plugin_dokukiwix_index()",1000);
}

function plugin_dokukiwix_pause(){
    pl_dokukiwix.play = 0;
    obj = document.getElementById('pl_dokukiwix_toggle_startpause');
    obj.src="lib/plugins/dokukiwix/images/play.png";
    plugin_dokukiwix_showThrobber(false);
    window.clearTimeout(pl_dokukiwix.timeoutid);
    plugin_dokukiwix_log('Generation paused.');
}

function plugin_dokukiwix_start(){
    pl_dokukiwix.play = 1;
    obj = document.getElementById('pl_dokukiwix_toggle_startpause');
    obj.src="lib/plugins/dokukiwix/images/pause.png";
    plugin_dokukiwix_showThrobber(true);

    if (pl_dokukiwix.done > 1) {
      pl_dokukiwix.timeoutid = window.setTimeout("plugin_dokukiwix_index()",1000);
      plugin_dokukiwix_log('Generation resumed.');
    }
    else
      plugin_dokukiwix_go(); // First time call
}

function plugin_dokukiwix_toggle_startpause(){
    if (pl_dokukiwix.play == 0)
        plugin_dokukiwix_start();
    else
        plugin_dokukiwix_pause();
}

/**
 * Stop function. Reinitializes all the variables and deletes the lock file.
 */
function plugin_dokukiwix_stop(){
    plugin_dokukiwix_pause();
    if(confirm("Warning: You won't be able to resume if you stop now. Are you sure you want to stop?")) {
        pl_dokukiwix.sack.onCompletion = ';';
        pl_dokukiwix.sack.URLString = '';
        pl_dokukiwix.sack.runAJAX('call=removeLock&page='+encodeURI(pl_dokukiwix.page));
        plugin_dokukiwix_reinit();
        plugin_dokukiwix_status('Genereration stopped.');
        plugin_dokukiwix_log('Generation stopped.');
    }
    else 
     plugin_dokukiwix_start();
}

/**
 * Callback. Gets the info if building of a page was successful
 *
 * Calls the next index run.
 */
function plugin_dokukiwix_cb_index(){
    ok = this.response;

    if (pl_dokukiwix.play == 1) {
        if(ok == 1){
            plugin_dokukiwix_log('Generated: '+pl_dokukiwix.page);
            pl_dokukiwix.page = pl_dokukiwix.pages.shift();
            pl_dokukiwix.done++;
            // get next one
            pl_dokukiwix.timeoutid  = window.setTimeout("plugin_dokukiwix_index()",500);
        }else{
            plugin_dokukiwix_status(ok);
            // get next one
            pl_dokukiwix.timeoutid = window.setTimeout("plugin_dokukiwix_index()",2000);
        }
    }
}

/**
 * Starts the indexing of a page.
 */
function plugin_dokukiwix_index(){
    if(pl_dokukiwix.page){
        plugin_dokukiwix_status('Generating '+pl_dokukiwix.page+' ('+pl_dokukiwix.done+'/'+pl_dokukiwix.count+')');
        pl_dokukiwix.sack.onCompletion = plugin_dokukiwix_cb_index;
        pl_dokukiwix.sack.URLString = '';
        pl_dokukiwix.sack.runAJAX('call=buildOfflinePage&page='+encodeURI(pl_dokukiwix.page));
    }else{
        plugin_dokukiwix_status('finished');
        plugin_dokukiwix_showThrobber(false);
        pl_dokukiwix.sack.onCompletion = ';';
        pl_dokukiwix.sack.URLString = '';
        pl_dokukiwix.sack.runAJAX('call=removeLock&page='+encodeURI(pl_dokukiwix.page));
        plugin_dokukiwix_log('Task finished.');
    }
}

function plugin_dokukiwix_find_pages(){
    plugin_dokukiwix_showThrobber(true);
    plugin_dokukiwix_showStopButton(true);

    plugin_dokukiwix_status('Finding all pages...');
    pl_dokukiwix.sack.onCompletion = plugin_dokukiwix_cb_pages;
    pl_dokukiwix.sack.URLString = '';
    pl_dokukiwix.sack.runAJAX('call=pagelist');
    plugin_dokukiwix_log('Finding pages...');
}

/**
 * Starts the whole index rebuild process
 */
function plugin_dokukiwix_startup(){
     data = this.response;

    if (data == 1) {
      if (!confirm("Warning: Dokukiwix is locked. This may mean that another instance is already running. Proceed anyway? (this will stop the other instance if any)")) {
        plugin_dokukiwix_reinit();
        plugin_dokukiwix_status('Genereration canceled.');
        return;
      }
    }

    plugin_dokukiwix_status('Initialising...');
    pl_dokukiwix.sack.onCompletion = plugin_dokukiwix_find_pages;
    pl_dokukiwix.sack.URLString = '';
    pl_dokukiwix.sack.runAJAX('call=dokukiwix_start');
    plugin_dokukiwix_log('Plugin initialised.');
}


/**
 * Creates the lock 
 */
function plugin_dokukiwix_go(){
    plugin_dokukiwix_status('Creating lock...');
    pl_dokukiwix.sack.onCompletion = plugin_dokukiwix_startup;
    pl_dokukiwix.sack.URLString = '';
    pl_dokukiwix.sack.runAJAX('call=createLock');
    plugin_dokukiwix_log('Created Lock');
}

/**
 * Log every event
 */
function plugin_dokukiwix_log(logstring) {
  var currentDateTime = new Date();
  var currentHours, currentMinutes, currentSeconds;

  if (currentDateTime.getHours()<10) currentHours = "0"+currentDateTime.getHours() ; else currentHours = currentDateTime.getHours();
  if (currentDateTime.getMinutes()<10) currentMinutes = "0"+currentDateTime.getMinutes() ; else currentMinutes = currentDateTime.getMinutes();
  if (currentDateTime.getSeconds()<10) currentSeconds = "0"+currentDateTime.getSeconds() ; else currentSeconds = currentDateTime.getSeconds();

  document.getElementById("pl_dokukiwix_log").value += currentHours+":"+currentMinutes+":"+currentSeconds+" "+logstring+"\n";
  document.getElementById("pl_dokukiwix_log").scrollTop = document.getElementById("pl_dokukiwix_log").scrollHeight;
}

//Setup VIM: ex: et ts=4 enc=utf-8 :
