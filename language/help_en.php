<?php
//
// kj_x_sendfile, its guide on the help page of the control panel
// English
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
//  - a list is numbered: KJ_X_SENDFILE_HELP_TIP_1, KJ_X_SENDFILE_HELP_TIP_2 ... it ends at the first missing number,
//    so an item can be added or removed without touching the code
//  - KJ_X_SENDFILE_HELP_{LIST}_TITLE is the title of the list, a list without it takes the title that Kleeja gives its type
//  - questions and answers are KJ_X_SENDFILE_HELP_FAQ_Q_1 and KJ_X_SENDFILE_HELP_FAQ_A_1
//  - the examples (_CODE) are shown as they are written, without HTML, and in English for every language
//  - names in <strong> are written as the control panel shows them: the words of Kleeja are in lang/en/,
//    the words of the plugin in kj_x_sendfile_translations() of init.php
//

return [
    'KJ_X_SENDFILE_HELP_TITLE' => 'X-Sendfile Downloads',
    'KJ_X_SENDFILE_HELP_INTRO' =>
        'Hand file transfers over to your web server. Kleeja still checks each request, counts the download and names the file, then Apache (mod_xsendfile) or Nginx (X-Accel-Redirect) sends the file itself, so a long download no longer keeps PHP busy.',

    //
    // what the plugin does
    //
    'KJ_X_SENDFILE_HELP_FEATURE_1' =>
        'Covers every file that Kleeja sends through <code>do.php</code>: downloads, images and thumbnails, linked by ID or by file name, with or without <strong>Mod Rewrite</strong>.',
    'KJ_X_SENDFILE_HELP_FEATURE_2' =>
        'Kleeja keeps its part of every download: it checks the file, counts the download, and sends the file type and the original file name. Only the transfer moves to the web server.',
    'KJ_X_SENDFILE_HELP_FEATURE_3' =>
        'The web server resumes interrupted downloads, sends parts of a file to download managers and video players, and adds caching headers, as it does for any static file.',
    'KJ_X_SENDFILE_HELP_FEATURE_4' =>
        'Works with Kleeja in a subfolder, and with file names that contain spaces, symbols or non-Latin letters.',
    'KJ_X_SENDFILE_HELP_FEATURE_5' =>
        'Some files still go through PHP, as before: the placeholder shown for a missing image, and the downloads that the <strong>Download Speed Limit</strong> plugin slows down for a group.',

    //
    // how to use it
    //
    'KJ_X_SENDFILE_HELP_STEP_TITLE' => 'Set up X-Sendfile',
    'KJ_X_SENDFILE_HELP_STEP_1' => 'Prepare your web server as the Apache or Nginx example below shows.',
    'KJ_X_SENDFILE_HELP_STEP_2' =>
        'In Kleeja, open <strong>Settings</strong>, then <strong>X-Sendfile settings</strong>.',
    'KJ_X_SENDFILE_HELP_STEP_3' =>
        'Choose your <strong>Web server</strong>, set <strong>Serve files through the web server</strong> to <strong>Yes</strong>, and click <strong>Update Settings</strong>.',
    'KJ_X_SENDFILE_HELP_STEP_4' =>
        'In a private window, download a file from its download page, and check that it opens and has its full size. If the file is empty, set <strong>Serve files through the web server</strong> back to <strong>No</strong>, then check the server configuration.',

    //
    // the configuration of each web server
    //
    'KJ_X_SENDFILE_HELP_APACHE_TITLE' => 'Apache example',
    'KJ_X_SENDFILE_HELP_APACHE_CODE' => '# 1. Install and enable mod_xsendfile. On Debian and Ubuntu:
#      sudo apt install libapache2-mod-xsendfile
#      sudo a2enmod xsendfile && sudo systemctl restart apache2
#
# 2. Turn it on in the <VirtualHost> of your site, or in the .htaccess file
#    of the Kleeja folder (which needs AllowOverride FileInfo or All):
XSendFile On',

    'KJ_X_SENDFILE_HELP_NGINX_TITLE' => 'Nginx example',
    'KJ_X_SENDFILE_HELP_NGINX_CODE' => '# X-Accel-Redirect is built into Nginx, there is nothing to install.
# The plugin sends Nginx to the address of each file in the upload folder,
# like /uploads/file.zip, or /kleeja/uploads/file.zip for Kleeja in a subfolder.
# Most setups already serve that folder as plain files. To make sure they do,
# and that nothing in the folder ever runs as PHP, in the server block:
location ^~ /uploads/ {
    try_files $uri =404;
}',

    //
    // common questions
    //
    'KJ_X_SENDFILE_HELP_FAQ_Q_1' => 'Downloads are empty since I turned the plugin on. Why?',
    'KJ_X_SENDFILE_HELP_FAQ_A_1' =>
        'The web server didn\'t act on the header, so nothing sent the file. On Apache, check that mod_xsendfile is loaded and that <code>XSendFile On</code> applies to the Kleeja folder. Check also that <strong>Web server</strong> matches the server that runs your site: Nginx ignores the Apache header. Until it is fixed, set <strong>Serve files through the web server</strong> to <strong>No</strong>, and PHP sends the files again.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_2' => 'Nginx answers 403 Forbidden or 404 Not Found. Why?',
    'KJ_X_SENDFILE_HELP_FAQ_A_2' =>
        'Nginx looks for each file at its address in the upload folder, so that address must lead to the folder. A <code>deny all;</code> rule on the folder blocks the plugin too (403). To hide the folder from visitors and still let the plugin use it, write <code>internal;</code> instead, but only if neither <strong>Files URLs form</strong> nor <strong>Images URLs form</strong> is set to <strong>Direct</strong>, since direct links open the folder itself.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_3' => 'Apache answers 500 Internal Server Error or 404 Not Found. Why?',
    'KJ_X_SENDFILE_HELP_FAQ_A_3' =>
        'A 500 error usually means that <code>XSendFile On</code> is in a <code>.htaccess</code> file where Apache doesn\'t allow it: move the line to the <code>&lt;VirtualHost&gt;</code>, or allow <code>AllowOverride FileInfo</code> for the Kleeja folder. A 404 error means that Apache can\'t find the file at the path that PHP gives it. When PHP runs in another container, Apache must see the Kleeja folder at the same path. When the folder is outside the paths that mod_xsendfile accepts, add <code>XSendFilePath</code> with the full path of the Kleeja folder to the <code>&lt;VirtualHost&gt;</code>.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_4' => 'How can I check that the web server sends the files?',
    'KJ_X_SENDFILE_HELP_FAQ_A_4' =>
        'Open the developer tools of your browser, download a file, and look at its response in the Network tab. A file that the web server sends has an <code>ETag</code> header, a file that PHP sends doesn\'t.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_5' => 'Does the plugin work with the Download Speed Limit plugin?',
    'KJ_X_SENDFILE_HELP_FAQ_A_5' =>
        'Yes. The downloads of a group that has a speed limit go through PHP, which keeps them at that speed. The downloads of the other groups go through the web server.',

    //
    // beside the guide
    //
    'KJ_X_SENDFILE_HELP_TIP_1' =>
        'The plugin helps most on sites with large files or many downloads at once, where each download would keep a PHP process busy until it ends.',
    'KJ_X_SENDFILE_HELP_TIP_2' =>
        'Behind Cloudflare or another proxy, set up the web server that runs Kleeja, not the proxy.',

    'KJ_X_SENDFILE_HELP_WARNING_1' =>
        'Don\'t set <strong>Serve files through the web server</strong> to <strong>Yes</strong> before your web server is ready: visitors would download empty files.',
    'KJ_X_SENDFILE_HELP_WARNING_2' =>
        'When you move your site to another server, check <strong>Web server</strong> and the server configuration again.',

    //
    // in the guide of the settings, whose page has the tab of the plugin
    //
    'KJ_X_SENDFILE_HELP_NOTE' =>
        'The <strong>X-Sendfile settings</strong> tab belongs to the <strong>X-Sendfile Downloads</strong> plugin. <a href="#help-kj_x_sendfile">Read its guide</a>.',
];
