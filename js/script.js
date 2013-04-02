/**
 * Author: Guntram Pollock & Sebastian Buckpesch
 */

/**
 * Loads a new template file into the div#main container using jquery animations
 * @param tmpl_filename Filename of the template
 * @param params Additional parameters to control the loading function (data, target, effect)
 *          data GET-Parameters which will be loaded as well.
 *          target css-selector to load the template in
 *          effect transition effect: can be slide, fade or switch
 */
function aa_tmpl_load( tmpl_filename, params ) {

    /* Extract and parse paramters */
    /* Check data param */
    if ( typeof( params ) != 'undefined' ) {
        if ( !params.hasOwnProperty('data') ) {
            data = '';
        } else {
            data = '&' + params['data'];
        }
        /* Loading target parameter */
        if ( !params.hasOwnProperty('target') ) {
            target = '#main';
        }else {
            target = params['target'];
        }
        /* Get effect if not possible */
        if ( !params.hasOwnProperty('effect') ) {
            effect = 'slidedown';
        }else {
            effect = params['effect'];
        }
    } else {
        data = '';
        target = '#main';
        effect = 'slidedown';
    }

    var url = "templates/" + tmpl_filename + "?aa_inst_id=" + aa.inst.aa_inst_id + data;
    if ( effect == 'fade' ) {
        $(target).fadeOut(0, function () {
            $(target).load( url, function () {
                $(target).fadeIn(600, function () {
                    //FB.Canvas.scrollTo(0, 0);
                });
            });
        });
    } else {
        show_loading(); // show the loading screen
        $(target).slideUp(0, function () {
            $(target).load( url, function () {
                $(target).slideDown(600, function () {
                    FB.Canvas.scrollTo(0, 0);
                    hide_loading(); // hide the loading screen
                });
            });
        });
    }
}

function open_popup( url, name ) {
    popup = window.open( url, name, 'target=_blank,width=820,height=800' );
    if ( window.focus ) {
        popup.focus();
    }
    return false;
}

function setAdminIntroCookie() {
    if ($('#admin-intro').is(':checked')) {
        setCookie('admin_intro_' + aa_inst_id, true);
    } else {
        setCookie('admin_intro_' + aa_inst_id, false);
    }
}

function show_admin_info() {
    $('#admin_modal').modal("show");
}

function urlencode(str){str=(str+'').toString();return encodeURIComponent(str).replace(/!/g,'%21').replace(/'/g,'%27').replace(/\(/g,'%28').replace(/\)/g,'%29').replace(/\*/g,'%2A').replace(/%20/g,'+');}

/** cookie functions */
/**
 * set cookie
 *
 * @param integer
 *            days default 1
 */
function setCookie(name, value, days, path, domain) {
    if (exists(days) == false)
        days = 1;

    if (exists(path) == false)
        path = '/';

    if ( typeof( domain ) == 'undefined' ) {

        if ( typeof( aa_domain ) != 'undefined' ) {
            domain = aa_domain;
        } else {
            domain = '.app-arena.com';
        }

    }
    var exp = new Date(); // new Date("December 31, 9998");
    exp.setTime(exp.getTime() + days * 24 * 60 * 60 * 1000);

    document.cookie = name + "=" + escape(value) + ";expires="
        + exp.toGMTString() + ";path=" + path + ";domain=" + domain + ";";
}

/**
 * get cookie ,if not exists , return null
 */
function getCookie(name) {
    var arr = document.cookie
        .match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
    if (arr != null)
        return decodeURI(arr[2]);
    else
        return null;

}
// delete cookie
function clearCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

/**
 * get cookies
 */
function getAllCookie() {
    var result = new Object();
    var strCookie = document.cookie;

    var arrCookie = strCookie.split("; ");
    for (var i = 0; i < arrCookie.length; i++) {
        var arr = arrCookie[i].split("=");

        result[arr[0]] = arr[1];
    }

    return result;
}

/**
 * check if a variables exists
 */
function exists(obj) {
    if (typeof (obj) == 'undefined')
        return false;

    if (obj == null)
        return false;

    if (obj == false)
        return false;

    return true;
}