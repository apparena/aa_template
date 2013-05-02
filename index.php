<?php
include_once("init.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta charset="utf-8">

    <!-- Facebook Meta Data -->
    <meta property="fb:app_id" content="<?php echo $aa['instance']['fb_app_id']?>"/>
    <meta property="og:title" content=""/>
    <meta property="og:type" content="website"/>
    <meta property="og:url"
          content="<?php echo $aa['instance']['fb_page_url'] . "?sk=app_" . $aa['instance']['fb_app_id']?>"/>
    <meta property="og:image" content=""/>
    <meta property="og:site_name" content=""/>
    <meta property="og:description" content=""/>

    <!-- We have no old school title in a facebook app -->
    <title></title>
    <meta name="description" content="">
    <meta name="author" content="iConsultants UG - www.app-arena.com">

    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="<?=$aa['config']['css_bootstrap']['src']?>"/>
    <link rel="stylesheet" href="css/font-awesome.min.css"/>
    <!--[if IE 7]>
    <link rel="stylesheet" href="css/font-awesome-ie7.min.css">
    <![endif]-->
    <link rel="stylesheet" href="<?=$aa['config']['css_app']['src']?>"/>
    <link rel="stylesheet" href="<?=$aa['config']['css_user']['src']?>"/>

    <?php if ($aa['config']['footer_branding']['value'] == 'banner') { ?>
    <!-- Google Publisher -->
    <script type='text/javascript'>
        var googletag = googletag || {};
        googletag.cmd = googletag.cmd || [];
        (function () {
            var gads = document.createElement('script');
            gads.async = true;
            gads.type = 'text/javascript';
            var useSSL = 'https:' == document.location.protocol;
            gads.src = (useSSL ? 'https:' : 'http:') +
                    '//www.googletagservices.com/tag/js/gpt.js';
            var node = document.getElementsByTagName('script')[0];
            node.parentNode.insertBefore(gads, node);
        })();
    </script>
    <script type='text/javascript'>
        googletag.cmd.push(function () {
            googletag.defineSlot('/114327208/<?=$aa['config']['footer_branding_dfp_inv_name']['value']?>', [810, 90], '<?=$aa['config']['footer_branding_dfp_div_id']['value']?>').addService(googletag.pubads());
            googletag.pubads().enableSingleRequest();
            googletag.enableServices();
        });
    </script>
    <?php } ?>
</head>

<body id="<?=$aa['env']['device']['type']?>" class="<?=$aa['env']['device']['type']?>">
<!-- Here starts the header -->
<!-- Prompt IE 6 users to install Chrome Frame. Remove this if you support IE 6.
     chromium.org/developers/how-tos/chrome-frame-getting-started -->
<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a
    different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a>
    to experience this site.</p><![endif]-->

<!-- Show admin panel and admin intro information -->
<?  if ( isset( $aa['fb']['page']['admin'] ) && $aa['fb']['page']['admin'] ) {  ?>
    <div class="admin_div">
        <? require_once dirname(__FILE__) . '/modules/admin_panel/admin_panel.php'; ?>
    </div>
    <div class="modal hide fade" id="admin_modal">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <? __p('admin_intro_header')?>
        </div>
        <div class="modal-body">
            <?php echo $aa['config']["admin_intro"]['value']?>
        </div>
        <div class="modal-footer">
            <label class="checkbox"><input type="checkbox" id="admin-intro"><? __p('do_not_show_any_more') ?></label>
            <a href="#" class="btn" data-dismiss="modal" onclick="setAdminIntroCookie();">
                <i class="icon-remove"></i> <? __p('close') ?>
            </a>
        </div>
    </div>
<?php }?>

<!-- Facebook Fangate -->
<?php if ($aa['fb']['is_fb_user_fan'] == false && $aa['config']['fangate_activated']['value']) {  ?>
    <div id="fangate" class="fangate">
        <div class="img_non_fans">
            <?php if ( $aa['config']['fangate_closable']['value'] ) { ?>
            <a class="btn pull-right" onclick="$('#fangate').hide();">&times;</a>
            <?php } ?>
            <div class="like-button">
                <div class="fb-like" data-href="<?=$aa['instance']['fb_page_url']?>" data-send="false"
                     data-layout="box_count" data-width="200"
                     data-show-faces="false" data-colorscheme="light" data-action="like">
                </div>
            </div>

            <img src="<?php echo $aa['config']['fangate']['value']?>"/>
        </div>
        <div class="backdrop">&nbsp;</div>
    </div>
<?php }?>


<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <ul class="nav">
                <li><a onclick="aa_tmpl_load('index.phtml');"><?php __p("Home");?></a></li>
                <li><a onclick="aa_tmpl_load('localization.phtml');"><?php __p("localization");?></a></li>
                <li><a onclick="aa_tmpl_load('fb-demo.phtml');"><?php __p("FB");?></a></li>
                <li><a onclick="aa_tmpl_load('module_registration.phtml');"><?php __p("Register");?></a></li>
                <li><a onclick="aa_tmpl_load('form_validation.phtml');"><?php __p("Validation");?></a></li>
                <li><a onclick="aa_tmpl_load('db-demo.phtml');"><?php __p("DB");?></a></li>
                <li title="g+, twitter social features"><a onclick="aa_tmpl_load('social_demo.phtml');"><?php __p("social_demo");?></a></li>
                <li id="menu_login"></li>
            </ul>

            <?php if ( is_array( $aa_locales ) && count( $aa_locales ) > 1 ) { ?>
            <ul class="nav pull-right">
                <li id="admin_locale_switch" class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="img/locale/<?=$cur_locale;?>.png"/>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach( $aa_locales as $locale ){ ?>
                        <li>
                            <a href='<?=$aa['instance']['fb_page_url']?>?sk=app_<?=$aa['instance']['fb_app_id']?>&app_data={"locale":"<?=$locale;?>"}' target="_top">
                                <img src="img/locale/<?=$locale;?>.png"/>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            <?php } ?>
        </div>
    </div>
</div>

<!-- this is the div you can append info/alert/error messages to (will be showing between the menu and the content by default) -->
<div id="msg-container"></div>

<div class="custom-header">
    <?php  echo $aa['config']['header_custom']['value'];  ?>
</div>

<div id="main" class="container">
    <!-- the main content is managed by initApp() -->
</div>
<!-- #main -->

<div class="custom-footer">
    <?php  echo $aa['config']['footer_custom']['value'];  ?>
</div>

<footer>
    <?php if ($aa['config']['tac_activated']['value'] == 'apparena') { ?>
    <div class="tac-container">
        <?php
        $terms_and_conditions_link = "<a class='clickable' id='terms-link'>" . __t("terms_and_conditions") . "</a>";
        __p("footer_terms", $terms_and_conditions_link);
        ?>
    </div>
    <?php } ?>

    <?php if ( $aa['config']['footer_branding']['value'] == 'banner' || $aa['config']['footer_branding']['value'] == 'text' ) { ?>
    <div class="banner">
        <div class="tagline pull-left"><?php __p("powered_by"); ?></div>
        <div class="like-button pull-right">
            <div class="fb-like" data-href="<?=$aa['config']['footer_branding_fblike_url']['value']?>" data-send="false"
                 data-layout="button_count" data-width="200" data-show-faces="false"></div>
        </div>
        <?php if ( $aa['config']['footer_branding']['value'] == 'banner' ) { ?>
        <!-- 10000-Template-App-Footer -->
        <div id='<?=$aa['config']['footer_branding_dfp_div_id']['value']?>' style='width:810px; height:90px;'>
            <script type='text/javascript'>
                googletag.cmd.push(function () {
                    googletag.display('<?=$aa['config']['footer_branding_dfp_div_id']['value']?>');
                });
            </script>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</footer>

<!-- Modal container -->
<div id="modal" class="modal hide fade"></div>

<?php
/* Initialize App-Arena variable in js */
$aaForJs = array(
    "t" => $aa['locale'],
    "conf" => $aa['config'],
    "inst" => $aa['instance'],
    "fb" => false
);
if (isset($aa['fb'])) {
    $aaForJs["fb"] = $aa['fb'];
}
// Remove sensitive data from js object
if (isset($aaForJs['inst']['fb_app_secret'])) {
    unset($aaForJs['inst']['fb_app_secret']);
}
if (isset($aaForJs['inst']['aa_app_secret'])) {
    unset($aaForJs['inst']['aa_app_secret']);
}
?>

<script>
    aa = <?php echo json_encode($aaForJs); ?>;
</script>

<!-- Debug mode -->
<?php if (isset($aa['config']['admin_debug_mode']['value']) && $aa['config']['admin_debug_mode']['value']) { ?>
<span class="btn" onclick='jQuery("#_debug").toggle();'>Show debug info</span>
<div id="_debug" style="display:none;">
    <h2>Debug information</h2>
    <h3>$aa['env']</h3>
    <pre><?php var_dump($aa['env']);?></pre>
    <h3>$aa['fb']</h3>
    <pre><?php var_dump($aa['fb']);?></pre>
    <h3>$aa['instance']</h3>
    <pre><?php var_dump($aa['instance']);?></pre>
    <h3>$_COOKIE</h3>
    <pre><?php var_dump($_COOKIE);?></pre>
    <h3>$aa['locale']</h3>
    <pre><?php var_dump($aa['locale']);?></pre>
    <h3>$aa['config']</h3>
    <small>css was removed from config</small>
    <pre><?php 
    			$conf_temp = clone $aa['config'];
    		    unset( $conf_temp['css_bootstrap'] );
    		    unset( $conf_temp['css_app'] );
    		    unset( $conf_temp['css_user'] );
    		    var_dump( $conf_temp );
		  ?></pre>
</div>
<?php } ?>

<!-- Show loading screen -->
<?php require_once(dirname(__FILE__) . '/templates/loading_screen.phtml'); ?>

<?php if ( $aa['config']['ga_activated']['value'] ) { ?>
<!-- google analytics Integration -->
<script>
    var _gaq = _gaq || [];
    var ga_id = '<?php if ( isset( $aa['config']["ga_id"]["value"] ) ) echo $aa['config']["ga_id"]["value"]; ?>';
    _gaq.push(['_setAccount', ga_id]);
    _gaq.push(['_gat._anonymizeIp']);
    _gaq.push(['_trackPageview']);
    _gaq.push(['_setCustomVar', 1, 'aa_inst_id', '<?php if (isset($aa['instance']["aa_inst_id"])) echo $aa['instance']["aa_inst_id"];?>']);
    (function (d, t) {
        var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
        g.async = 1;
        g.src = ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g, s)
    }(document, 'script'));
</script>
<?php } ?>

<?php if ( $aa['config']['admin_feedback_panel_activated']['value'] ) { ?>
<!-- UserVoice JavaScript SDK (only needed once on a page) -->
<script>(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/hGPtoFEKfweXo48qhIgT7g.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})()</script>
<!-- A tab to launch the Classic Widget -->
<script>
    UserVoice = window.UserVoice || [];
    UserVoice.push(['showTab', 'classic_widget', {
        mode: 'feedback',
        primary_color: '#999999',
        link_color: '#6480b8',
        default_mode: 'feedback',
        support_tab_name: 'App-Entwickler kontaktieren',
        feedback_tab_name: 'Verbesserungsvorschläge',
        forum_id: 125827,
        topic_id: 3425,
        tab_label: 'Feedback',
        tab_color: '#999999',
        tab_position: 'bottom-right',
        tab_inverted: false
    }]);
</script>
<?php } ?>

<!-- data-main attribute tells require.js to load scripts/main.js after require.js loads. -->
<!--<script data-main="js/main" src="js/require.js"></script>-->
<script data-main="js/main" src="js/require.dev.js"></script>

</body>
</html>
