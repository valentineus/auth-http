<?php
/**
 * HTTP Basic Authentication.
 * @package      auth_http
 * @copyright    "Valentin Popov" <info@valentineus.link>
 * @license      MIT License (https://opensource.org/licenses/MIT)
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for no authentication.
 */
class auth_plugin_http extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'http';
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_http() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        global $CFG, $DB;

        if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            return validate_internal_user_password($user, $password);
        }

        return true;
    }

    /**
     * No password updates.
     */
    function user_update_password($user, $newpassword) {
        return false;
    }

    function prevent_local_passwords() {
        // just in case, we do not want to loose the passwords
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     * @return bool
     */
    function is_internal() {
        //we do not know if it was internal or external originally
        return true;
    }

    /**
     * No changing of password.
     */
    function can_change_password() {
        return false;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * No password resetting.
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }

    /**
     * Hook for overriding behaviour before going to the login page.
     */
    function pre_loginpage_hook() {
        $this->loginpage_hook();
    }

    /**
     * Hook for overriding behaviour of login page.
     */
    function loginpage_hook() {
        global $DB;

        if (!isloggedin()) {
            if (isset($_SERVER['PHP_AUTH_USER']) &&
                isset($_SERVER['PHP_AUTH_PW'])) {

                $username = htmlspecialchars($_SERVER['PHP_AUTH_USER']);
                $password = htmlspecialchars($_SERVER['PHP_AUTH_PW']);

                // User existence check
                if ($user = $DB->get_record( 'user', array( 'username' => $username) )) {

                    // Verification of authorization data
                    if (validate_internal_user_password($user, $password)) {
                        complete_user_login($user);
                        $this->redirect_user();
                    } else {
                        // Authentication data verification error
                        $this->authorization_window();
                    }
                } else {
                    // User search failed
                    $this->authorization_window();
                }
            } else {
                // Authorization data is missing
                $this->authorization_window();
            }
        }
    }

    /**
     * Call authorization window.
     */
    function authorization_window() {
        global $SITE;

        header('WWW-Authenticate: Basic realm="'. $SITE->shortname .'"');
        header('HTTP/1.0 401 Unauthorized');
        die(print_string('auth_httperror', 'auth_http'));
    }

    /**
     * Redirect client to the original target.
     */
    function redirect_user() {
        global $CFG, $SESSION;

        if (isset($SESSION->wantsurl)) {
            $redirect = $SESSION->wantsurl;
        } elseif (isset($_GET['wantsurl'])) {
            $redirect = htmlspecialchars($_GET['wantsurl']);
        } else {
            $redirect = $CFG->wwwroot;
        }

        redirect($redirect);
    }
}
