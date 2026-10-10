<?php
// Kleeja Plugin
// kj_x_sendfile
// Version: 1.1
// Developer: Kleeja team

// Prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit();
}

// Plugin Basic Information
$kleeja_plugin['kj_x_sendfile']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'X-Sendfile Downloads',
        'ar' => 'التحميل عبر X-Sendfile',
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja.net',
    //settings page, if there is one (what after ? like cp=j_plugins)
    'settings_page' => 'cp=options&smt=kj_x_sendfile',
    // This plugin version
    'plugin_version' => '1.1',
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' =>
            'Let Apache or Nginx send downloads, images and thumbnails with X-Sendfile or X-Accel-Redirect, instead of streaming them through PHP, for faster downloads and a lighter load on your server.',
        'ar' =>
            'تجعل Apache أو Nginx يرسل التحميلات والصور والمصغّرات عبر X-Sendfile أو X-Accel-Redirect بدلًا من تمريرها عبر PHP، لتحميل أسرع وحِمل أخف على خادمك.',
    ],
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '4.0.0',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '4.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 0,
];

//after installation message, you can remove it, it's not requiered
$kleeja_plugin['kj_x_sendfile']['first_run']['ar'] = "
تضيف هذه الإضافة تبويب «إعدادات X-Sendfile» إلى صفحة الإعدادات، وتبقى معطّلة حتى تفعّلها أنت.
جهّز خادم الويب أولًا كما يشرح دليل الإضافة، ثم اختر خادمك واجعل «إرسال الملفات عبر خادم الويب» على «نعم».
<br>
<a href='./index.php?cp=options&amp;smt=kj_x_sendfile'>إعدادات X-Sendfile</a> -
<a href='./index.php?cp=s_help#help-kj_x_sendfile'>دليل الإضافة</a> -
<a href='https://github.com/kleeja/kj_x_sendfile/issues' target='_blank' rel='noopener'>الإبلاغ عن مشكلة</a>
";

$kleeja_plugin['kj_x_sendfile']['first_run']['en'] = "
This plugin adds an X-Sendfile settings tab to the Settings page, and stays off until you turn it on.
Set up your web server first as the plugin guide shows, then choose your server and set Serve files through the web server to Yes.
<br>
<a href='./index.php?cp=options&amp;smt=kj_x_sendfile'>X-Sendfile settings</a> -
<a href='./index.php?cp=s_help#help-kj_x_sendfile'>Plugin guide</a> -
<a href='https://github.com/kleeja/kj_x_sendfile/issues' target='_blank' rel='noopener'>Report a problem</a>
";

// Plugin Installation function
$kleeja_plugin['kj_x_sendfile']['install'] = function ($plg_id) {
    $options = [];

    foreach (kj_x_sendfile_configs() as $name => $setting) {
        $options[$name] = [
            'value' => $setting['value'],
            'html' => $setting['html'],
            'order' => $setting['order'],
            'plg_id' => $plg_id,
            'type' => 'kj_x_sendfile',
        ];
    }

    add_config_r($options);

    foreach (kj_x_sendfile_translations() as $lang_id => $words) {
        add_olang($words, $lang_id, $plg_id);
    }
};

// the words and the fields of the settings are written again, so a new version brings its own (1.1 rewrote all of them),
// with queries of its own: add_olang(), delete_olang(), add_config(), delete_config() and delete_cache() run hooks,
// or need the groups, and the plugins are still loading.
// $kleeja_plugin isn't a global, Kleeja includes this file in a method, so the closure takes the information above with it
$kleeja_plugin['kj_x_sendfile']['update'] = function ($old_version, $new_version) use ($kleeja_plugin) {
    global $SQL, $dbprefix;

    $result = $SQL->build([
        'SELECT' => 'plg_id',
        'FROM' => "{$dbprefix}plugins",
        'WHERE' => 'plg_name = :name',
        'BIND' => ['name' => 'kj_x_sendfile'],
    ]);

    $row = $SQL->fetch_array($result);
    $plg_id = (int) ($row['plg_id'] ?? 0);
    $SQL->freeresult($result);

    //a plg_id of 0 would delete the words of the core and of every plugin
    if ($plg_id > 0) {
        $SQL->build([
            'DELETE' => "{$dbprefix}lang",
            'WHERE' => 'plg_id = :plg_id',
            'BIND' => ['plg_id' => $plg_id],
        ]);

        foreach (kj_x_sendfile_translations() as $lang_id => $words) {
            foreach ($words as $word => $trans) {
                $SQL->build([
                    'INSERT' => 'word, trans, lang_id, plg_id',
                    'INTO' => "{$dbprefix}lang",
                    'VALUES' => ':word, :trans, :lang_id, :plg_id',
                    'BIND' => ['word' => $word, 'trans' => $trans, 'lang_id' => $lang_id, 'plg_id' => $plg_id],
                ]);
            }
        }
    }

    // the saved settings, their values are kept
    $saved = [];

    $result = $SQL->build([
        'SELECT' => 'name',
        'FROM' => "{$dbprefix}config",
        'WHERE' => 'name IN (:names)',
        'BIND' => ['names' => array_keys(kj_x_sendfile_configs())],
    ]);

    while ($row = $SQL->fetch_array($result)) {
        $saved[] = $row['name'];
    }

    $SQL->freeresult($result);

    foreach (kj_x_sendfile_configs() as $name => $setting) {
        if (in_array($name, $saved)) {
            // the field of the setting and its place
            $SQL->build([
                'UPDATE' => "{$dbprefix}config",
                'SET' => '`option` = :option, display_order = :display_order',
                'WHERE' => 'name = :name',
                'BIND' => ['option' => $setting['html'], 'display_order' => $setting['order'], 'name' => $name],
            ]);

            continue;
        }

        $SQL->build([
            'INSERT' => '`name`, `value`, `option`, `display_order`, `type`, `plg_id`, `dynamic`',
            'INTO' => "{$dbprefix}config",
            'VALUES' => ':name, :value, :option, :display_order, :type, :plg_id, :dynamic',
            'BIND' => [
                'name' => $name,
                'value' => $setting['value'],
                'option' => $setting['html'],
                'display_order' => $setting['order'],
                'type' => 'kj_x_sendfile',
                'plg_id' => $plg_id,
                'dynamic' => 0,
            ],
        ]);
    }

    // the developer and the description that the old versions were installed with,
    // the list of plugins shows them while the plugin is disabled
    if (version_compare($old_version, '1.1', '<')) {
        $SQL->build([
            'UPDATE' => "{$dbprefix}plugins",
            'SET' => 'plg_author = :author, plg_dsc = :description',
            'WHERE' => 'plg_name = :name',
            'BIND' => [
                'author' => 'Kleeja.net',
                'description' => kleeja_html_encode(
                    $kleeja_plugin['kj_x_sendfile']['information']['plugin_description']['en'],
                ),
                'name' => 'kj_x_sendfile',
            ],
        ]);
    }

    // the cached words and settings. They are PHP files, and OPcache would keep running the old ones for a while,
    // or until PHP restarts when it doesn't check the files. Before a file is deleted,
    // OPcache finds a relative path only when the file exists
    $cached = array_merge(glob(PATH . 'cache/data_lang*.php') ?: [], [PATH . 'cache/data_config.php']);

    foreach ($cached as $file) {
        if (file_exists($file)) {
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($file, true);
            }

            @unlink($file);
        }
    }
};

// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['kj_x_sendfile']['uninstall'] = function ($plg_id) {
    delete_config(array_keys(kj_x_sendfile_configs()));

    //a plg_id of 0 would delete the words of the core and of every plugin
    if ((int) $plg_id > 0) {
        foreach (array_keys(kj_x_sendfile_translations()) as $lang_id) {
            delete_olang(null, $lang_id, (int) $plg_id);
        }
    }
};

// Plugin functions
$kleeja_plugin['kj_x_sendfile']['functions'] = [
    //the words of the plugin on the settings page, in English for a language that has no translation of them
    'begin_admin_page' => function ($args) {
        $olang = (array) ($args['olang'] ?? []) + kj_x_sendfile_translations()['en'];

        return compact('olang');
    },

    // a file that do.php sends: a download, an image or a thumbnail. Kleeja has checked it, counted the download,
    // and set its type and its name, the web server sends the file itself, resumed downloads included,
    // so do.php stops before it reads the file
    'do_page_headers_set' => function ($args) {
        global $config;

        if (empty($config['kj_x_sendfile_enable'])) {
            return;
        }

        $header = kj_x_sendfile_header($args);

        if ($header === '') {
            return;
        }

        header($header);

        if (is_resource($args['fp'] ?? null)) {
            fclose($args['fp']);
        }

        exit();
    },

    // the guide of the plugin on the help page of the control panel, its words are in language/help_{code}.php
    'admin_help_guides' => function ($args) {
        $help_guides = $args['help_guides'];
        $words = kj_x_sendfile_help_words();

        // the name of the plugin is the key, so Kleeja knows that the plugin has its guide, and shows its icon.
        // The options of the plugin are a tab of the settings page, whose help button opens the guide of Kleeja,
        // so it has no 'page', and the guide of the settings links to it
        $help_guides['kj_x_sendfile'] = [
            'group' => 'plugins',
            'title' => $words['KJ_X_SENDFILE_HELP_TITLE'],
            'intro' => $words['KJ_X_SENDFILE_HELP_INTRO'],
            'link' => './?cp=options&amp;smt=kj_x_sendfile',
            // tips and warnings are shown beside the others on wide screens
            'sections' => [
                kj_x_sendfile_help_section($words, 'features', 'FEATURE'),
                kj_x_sendfile_help_section($words, 'steps', 'STEP'),
                [
                    'type' => 'code',
                    'title' => $words['KJ_X_SENDFILE_HELP_APACHE_TITLE'],
                    'code' => $words['KJ_X_SENDFILE_HELP_APACHE_CODE'],
                ],
                [
                    'type' => 'code',
                    'title' => $words['KJ_X_SENDFILE_HELP_NGINX_TITLE'],
                    'code' => $words['KJ_X_SENDFILE_HELP_NGINX_CODE'],
                ],
                kj_x_sendfile_help_section($words, 'faq', 'FAQ'),
                kj_x_sendfile_help_section($words, 'tips', 'TIP'),
                kj_x_sendfile_help_section($words, 'warnings', 'WARNING'),
            ],
        ];

        if (isset($help_guides['settings'])) {
            $help_guides['settings']['sections'][] = ['type' => 'text', 'text' => $words['KJ_X_SENDFILE_HELP_NOTE']];
        }

        return compact('help_guides');
    },
];

/**
 * special functions
 */

if (!function_exists('kj_x_sendfile_configs')) {
    /**
     * the settings of the plugin, with the values that it is installed with (off, Apache), their order,
     * and their fields on the settings page, which adds the classes of Bootstrap to them
     * @return array
     */
    function kj_x_sendfile_configs()
    {
        $yes_no = '';

        foreach (['1' => 'YES', '0' => 'NO'] as $value => $word) {
            $yes_no .=
                '<label>{lang.' .
                $word .
                '}<input type="radio" id="kj_x_sendfile_enable' .
                ($value ? '' : '_no') .
                '" name="kj_x_sendfile_enable" value="' .
                $value .
                '"<IF NAME="con.kj_x_sendfile_enable==' .
                $value .
                '"> checked="checked"</IF> /></label>';
        }

        // the values of the server, as kj_x_sendfile_header() reads them
        $servers = '';

        foreach (['apache' => 'KJ_X_SENDFILE_APACHE', 'nginx' => 'KJ_X_SENDFILE_NGINX'] as $value => $word) {
            $servers .=
                '<option value="' .
                $value .
                '"<IF NAME="con.kj_x_sendfile_type==' .
                $value .
                '"> selected="selected"</IF>>{olang.' .
                $word .
                '}</option>';
        }

        return [
            'kj_x_sendfile_type' => [
                'value' => 'apache',
                'order' => 1,
                'html' => '<select id="kj_x_sendfile_type" name="kj_x_sendfile_type">' . $servers . '</select>',
            ],
            'kj_x_sendfile_enable' => [
                'value' => '0',
                'order' => 2,
                'html' => $yes_no,
            ],
        ];
    }

    /**
     * the header that hands a file of do.php to the web server, empty for a file that PHP sends itself
     * @param  array  $args the variables of do.php
     * @return string
     */
    function kj_x_sendfile_header($args)
    {
        global $config;

        $path = (string) ($args['path_file'] ?? '');
        $folder = (string) ($args['f'] ?? '');

        // an upload or its thumbnail as do.php found it, ./{folder}/{name} or ./{folder}/thumbs/{name}.
        // The picture that replaces a missing image, and a file that another plugin sends from elsewhere, stay with PHP
        if (empty($args['ii']) || $folder === '' || strpos($path, './' . $folder . '/') !== 0) {
            return '';
        }

        // the speed limit of a group slows the download down while PHP sends it, the web server would send it at full speed
        if (defined('TrottleLimit')) {
            return '';
        }

        // mod_xsendfile and Nginx decode the value of the header, and a file name can have spaces, % or # in it,
        // or letters that aren't ASCII, so each part of the path is encoded
        $encode = function ($path) {
            return implode('/', array_map('rawurlencode', explode('/', str_replace('\\', '/', $path))));
        };

        $file = $encode(substr($path, 2));

        if ($config['kj_x_sendfile_type'] === 'nginx') {
            // an address on this server, in the folder of the script that runs: do.php, or serve.php for pretty URLs,
            // so it works for Kleeja in a subfolder too
            $folder = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/')));
            $folder = $folder === '.' ? '' : trim($folder, '/');

            return 'X-Accel-Redirect: /' . ($folder === '' ? '' : $encode($folder) . '/') . $file;
        }

        // Apache, the full path of the file on the server
        return 'X-Sendfile: ' . $encode(PATH) . $file;
    }

    /**
     * the words of the guide on the help page, in the language of the admin,
     * a word that the translation misses is shown in English
     * @return array
     */
    function kj_x_sendfile_help_words()
    {
        global $config;

        $words = (array) require __DIR__ . '/language/help_en.php';
        $language = preg_replace('/[^a-z0-9_-]/i', '', (string) ($config['language'] ?? ''));
        $translation = __DIR__ . "/language/help_{$language}.php";

        if ($language !== '' && $language !== 'en' && file_exists($translation)) {
            // in its own line, Prettier drops the brackets of (require $translation) + $words
            $translated = require $translation;
            $words = (array) $translated + $words;
        }

        return $words;
    }

    /**
     * a section of the guide from its numbered words, like KJ_X_SENDFILE_HELP_TIP_1, .._TIP_2 ..
     * its title, when it has its own, is KJ_X_SENDFILE_HELP_TIP_TITLE
     * @param  array  $words
     * @param  string $type  how Kleeja shows it: features, steps, tips, warnings or faq
     * @param  string $name  the name of its words, KJ_X_SENDFILE_HELP_{name}_1
     * @return array
     */
    function kj_x_sendfile_help_section($words, $type, $name)
    {
        $prefix = 'KJ_X_SENDFILE_HELP_' . $name;
        $section = ['type' => $type, 'title' => $words[$prefix . '_TITLE'] ?? '', 'items' => []];

        for ($n = 1; isset($words[$prefix . ($type === 'faq' ? '_Q_' : '_') . $n]); $n++) {
            $section['items'][] =
                $type === 'faq'
                    ? ['q' => $words[$prefix . '_Q_' . $n], 'a' => $words[$prefix . '_A_' . $n] ?? '']
                    : $words[$prefix . '_' . $n];
        }

        return $section;
    }

    /**
     * the words of the plugin, they are added to the language table when it is installed or updated,
     * a <small> in the name of a setting is the hint under it
     * @return array
     */
    function kj_x_sendfile_translations()
    {
        return [
            'en' => [
                'CONFIG_KLJ_MENUS_KJ_X_SENDFILE' => 'X-Sendfile settings',
                'KJ_X_SENDFILE_TYPE' =>
                    'Web server <small>The server that runs your site. Apache needs the mod_xsendfile module, Nginx supports X-Accel-Redirect out of the box.</small>',
                'KJ_X_SENDFILE_ENABLE' =>
                    'Serve files through the web server <small>Your web server sends downloads, images and thumbnails instead of PHP. Set up the server first, as the plugin guide shows, or visitors will get empty files.</small>',
                'KJ_X_SENDFILE_APACHE' => 'Apache (X-Sendfile)',
                'KJ_X_SENDFILE_NGINX' => 'Nginx (X-Accel-Redirect)',
            ],
            'ar' => [
                'CONFIG_KLJ_MENUS_KJ_X_SENDFILE' => 'إعدادات X-Sendfile',
                'KJ_X_SENDFILE_TYPE' =>
                    'خادم الويب <small>الخادم الذي يشغّل موقعك. يحتاج Apache إلى الوحدة mod_xsendfile، أما Nginx فيدعم X-Accel-Redirect دون أي إضافة.</small>',
                'KJ_X_SENDFILE_ENABLE' =>
                    'إرسال الملفات عبر خادم الويب <small>يرسل خادم الويب التحميلات والصور والمصغّرات بدلًا من PHP. جهّز الخادم أولًا كما يشرح دليل الإضافة، وإلا فسيحصل الزوار على ملفات فارغة.</small>',
                'KJ_X_SENDFILE_APACHE' => 'Apache عبر X-Sendfile',
                'KJ_X_SENDFILE_NGINX' => 'Nginx عبر X-Accel-Redirect',
            ],
        ];
    }
}
