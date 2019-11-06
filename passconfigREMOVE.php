<?php
/**
 * PassMan - Configuration file.
 * 
 * @author Przemyslaw Mikolajczak <przemek@mylittlepla.net>
 * @license MIT
 */

date_default_timezone_set("Europe/London");

/**
 * Named Constant DB_HOST
 *
 * Database host.
 *
 * @used-by \lib\Core\Model.php
 */
define("DB_HOST", "localhost");

/**
 * Named Constant DB_USER
 *
 * Database username.
 *
 * @used-by \lib\Core\Model.php
 */
define("DB_USER", "db_user");

/**
 * Named Constant DB_PASS
 *
 * Database user's password.
 *
 * @used-by \lib\Core\Model.php
 */
define("DB_PASS", "db_password");

/**
 * Named Constant DB_NAME
 *
 * Database's name.
 *
 * @used-by \lib\Core\Model.php
 */
define("DB_NAME", "db_name");

/**
 * Named Constant ADMIN_EMAIL
 *
 * Email sender and reply-to address.
 *
 * @used-by \lib\Core\Model.php
 */
define("ADMIN_EMAIL", "noreply@mydomain.com");

/**
 * Named Constant APP_DOMAIN
 *
 * Domain application will be setup at.
 *
 * @used-by \lib\Core\Model.php
 */
define("APP_DOMAIN", "passman.mydomain.com");

/**
 * Named Constant SESSION_TIME
 *
 * Session expiry time - gets invalid after SESSION_TIME seconds.
 *
 * @used-by \lib\Core\Model.php
 */
define("SESSION_TIME", 600);