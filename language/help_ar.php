<?php
//
// kj_x_sendfile, its guide on the help page of the control panel
// Arabic, see help_en.php for how the words are named
//
// A word missing here is shown in English, like the examples of the web servers (_CODE).
// Names in <strong> are written as the control panel shows them in Arabic,
// the words of Kleeja are in lang/ar/, the words of the plugin in kj_x_sendfile_translations() of init.php.
// A <code> that begins or ends with a symbol takes dir="ltr", or the symbol shows on the wrong side.
//

return [
    'KJ_X_SENDFILE_HELP_TITLE' => 'التحميل عبر X-Sendfile',
    'KJ_X_SENDFILE_HELP_INTRO' =>
        'أسند نقل الملفات إلى خادم الويب. تواصل كليجا التحقق من كل طلب وعدّ التحميلات وتسمية الملف، ثم يرسل Apache عبر mod_xsendfile، أو Nginx عبر X-Accel-Redirect، الملف نفسه، فلا يبقى PHP مشغولًا طوال التحميلات الطويلة.',

    //
    // what the plugin does
    //
    'KJ_X_SENDFILE_HELP_FEATURE_1' =>
        'تشمل كل ملف ترسله كليجا عبر <code>do.php</code>: التحميلات والصور والمصغّرات، سواء كان الرابط برقم الملف أو باسمه، ومع <strong>Mod Rewrite</strong> أو دونه.',
    'KJ_X_SENDFILE_HELP_FEATURE_2' =>
        'تؤدي كليجا دورها في كل تحميل كما هو: تتحقق من الملف، وتعدّ التحميل، وترسل نوع الملف واسمه الأصلي. ولا ينتقل إلى خادم الويب إلا نقل الملف نفسه.',
    'KJ_X_SENDFILE_HELP_FEATURE_3' =>
        'يستأنف خادم الويب التحميلات المتقطعة، ويرسل أجزاء الملف إلى برامج التحميل ومشغّلات الفيديو، ويضيف ترويسات التخزين المؤقت، كما يفعل مع أي ملف ثابت.',
    'KJ_X_SENDFILE_HELP_FEATURE_4' =>
        'تعمل حين تكون كليجا في مجلد فرعي، ومع أسماء الملفات التي تحتوي على مسافات أو رموز أو حروف غير لاتينية.',
    'KJ_X_SENDFILE_HELP_FEATURE_5' =>
        'تبقى بعض الملفات تمر عبر PHP كما كانت: الصورة البديلة التي تظهر مكان الصورة المفقودة، والتحميلات التي تبطئها إضافة <strong>تحديد سرعة التحميل</strong> لمجموعة ما.',

    //
    // how to use it
    //
    'KJ_X_SENDFILE_HELP_STEP_TITLE' => 'إعداد X-Sendfile',
    'KJ_X_SENDFILE_HELP_STEP_1' => 'جهّز خادم الويب كما يوضح مثال Apache أو Nginx أدناه.',
    'KJ_X_SENDFILE_HELP_STEP_2' => 'في كليجا، افتح <strong>إعدادات</strong> ثم <strong>إعدادات X-Sendfile</strong>.',
    'KJ_X_SENDFILE_HELP_STEP_3' =>
        'اختر <strong>خادم الويب</strong> الذي يشغّل موقعك، واجعل <strong>إرسال الملفات عبر خادم الويب</strong> على <strong>نعم</strong>، ثم اضغط <strong>تحديث الإعدادات</strong>.',
    'KJ_X_SENDFILE_HELP_STEP_4' =>
        'في نافذة خاصة، حمّل ملفًا من صفحة تحميله، وتأكد من أنه يُفتح وأن حجمه كامل. إذا كان الملف فارغًا، فأعِد <strong>إرسال الملفات عبر خادم الويب</strong> إلى <strong>لا</strong>، ثم راجع إعدادات الخادم.',

    //
    // the configuration of each web server, the examples themselves are in English
    //
    'KJ_X_SENDFILE_HELP_APACHE_TITLE' => 'مثال Apache',
    'KJ_X_SENDFILE_HELP_NGINX_TITLE' => 'مثال Nginx',

    //
    // common questions
    //
    'KJ_X_SENDFILE_HELP_FAQ_Q_1' => 'أصبحت التحميلات فارغة بعد تفعيل الإضافة، لماذا؟',
    'KJ_X_SENDFILE_HELP_FAQ_A_1' =>
        'لم يتعامل خادم الويب مع الترويسة، فلم يُرسَل الملف. في Apache، تأكد من تحميل الوحدة mod_xsendfile ومن أن <code>XSendFile On</code> يشمل مجلد كليجا. وتأكد كذلك من أن <strong>خادم الويب</strong> يطابق الخادم الذي يشغّل موقعك، إذ يتجاهل Nginx ترويسة Apache. وإلى أن تُصلح ذلك، اجعل <strong>إرسال الملفات عبر خادم الويب</strong> على <strong>لا</strong>، فيعود PHP إلى إرسال الملفات.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_2' => 'يردّ Nginx بالخطأ 403 أو 404، لماذا؟',
    'KJ_X_SENDFILE_HELP_FAQ_A_2' =>
        'يبحث Nginx عن كل ملف بعنوانه داخل مجلد الرفع، فلا بد أن يقود هذا العنوان إلى المجلد. وقاعدة <code dir="ltr">deny all;</code> على المجلد تحجب الإضافة أيضًا (403). لإخفاء المجلد عن الزوار مع إبقائه متاحًا للإضافة، اكتب <code dir="ltr">internal;</code> بدلًا منها، بشرط ألا يكون <strong>شكل روابط الملفات</strong> ولا <strong>شكل روابط الصور</strong> على <strong>رابط مباشر</strong>، لأن الروابط المباشرة تفتح المجلد نفسه.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_3' => 'يردّ Apache بالخطأ 500 أو 404، لماذا؟',
    'KJ_X_SENDFILE_HELP_FAQ_A_3' =>
        'يعني الخطأ 500 غالبًا أن <code>XSendFile On</code> موجود في ملف <code dir="ltr">.htaccess</code> لا يسمح Apache بهذا الأمر فيه: انقل السطر إلى <code dir="ltr">&lt;VirtualHost&gt;</code>، أو اسمح بـ <code>AllowOverride FileInfo</code> لمجلد كليجا. أما الخطأ 404 فيعني أن Apache لا يجد الملف في المسار الذي يعطيه إياه PHP. فإذا كان PHP يعمل في حاوية أخرى، فيجب أن يرى Apache مجلد كليجا في المسار نفسه. وإذا كان المجلد خارج المسارات التي تقبلها mod_xsendfile، فأضف <code>XSendFilePath</code> مع المسار الكامل لمجلد كليجا إلى <code dir="ltr">&lt;VirtualHost&gt;</code>.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_4' => 'كيف أتأكد من أن خادم الويب هو الذي يرسل الملفات؟',
    'KJ_X_SENDFILE_HELP_FAQ_A_4' =>
        'افتح أدوات المطوّر في متصفحك، وحمّل ملفًا، ثم انظر إلى استجابته في تبويب Network. الملف الذي يرسله خادم الويب فيه ترويسة <code>ETag</code>، أما الملف الذي يرسله PHP فليس فيه.',
    'KJ_X_SENDFILE_HELP_FAQ_Q_5' => 'هل تعمل الإضافة مع إضافة تحديد سرعة التحميل؟',
    'KJ_X_SENDFILE_HELP_FAQ_A_5' =>
        'نعم. تمر تحميلات المجموعة التي لها حد للسرعة عبر PHP، فتبقى عند تلك السرعة، وتمر تحميلات المجموعات الأخرى عبر خادم الويب.',

    //
    // beside the guide
    //
    'KJ_X_SENDFILE_HELP_TIP_1' =>
        'تفيد الإضافة أكثر ما تفيد في المواقع ذات الملفات الكبيرة أو التحميلات الكثيرة في وقت واحد، إذ يُبقي كل تحميل فيها عملية PHP مشغولة حتى ينتهي.',
    'KJ_X_SENDFILE_HELP_TIP_2' =>
        'إذا كان موقعك خلف Cloudflare أو أي وسيط آخر، فجهّز خادم الويب الذي يشغّل كليجا، لا الوسيط.',

    'KJ_X_SENDFILE_HELP_WARNING_1' =>
        'لا تجعل <strong>إرسال الملفات عبر خادم الويب</strong> على <strong>نعم</strong> قبل أن يصبح خادم الويب جاهزًا، وإلا فسيحمّل الزوار ملفات فارغة.',
    'KJ_X_SENDFILE_HELP_WARNING_2' =>
        'عند نقل موقعك إلى خادم آخر، راجع <strong>خادم الويب</strong> وإعدادات الخادم من جديد.',

    //
    // in the guide of the settings, whose page has the tab of the plugin
    //
    'KJ_X_SENDFILE_HELP_NOTE' =>
        'تبويب <strong>إعدادات X-Sendfile</strong> تابع لإضافة <strong>التحميل عبر X-Sendfile</strong>. <a href="#help-kj_x_sendfile">اقرأ دليلها</a>.',
];
