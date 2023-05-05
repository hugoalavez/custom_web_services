<?php

//namespace local_user_get_users_by_timemodified\external;

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;

class local_custom_web_services_external  extends external_api {   
    
    public static function execute_parameters(): external_function_parameters {
    	return new external_function_parameters([
        'time' => new external_value(PARAM_TEXT, 'Time in seconds')
    	]);
    }
    
    public static function execute($time) {
        global $DB, $USER, $CFG;
        
        require_once("$CFG->dirroot/user/lib.php");
        
        $params = self::validate_parameters(self::execute_parameters(), ['time' => $time]);
        $date = new DateTimeImmutable();
        $limit = $date->getTimestamp() - $time;

        $users_on_time = $DB->get_records_sql("SELECT *  FROM {user} WHERE timemodified >= $limit");

        $returnedusers = array();
        foreach ($users_on_time as $user) {
            $userdetails = user_get_user_details_courses($user);
            $returnedusers[] = $userdetails;
        }
        return array('users' => $returnedusers);        
    }
    
    
    public static function execute_returns() {
        return new external_single_structure(
            array('users' => new external_multiple_structure(
                                self::user_description()
                             )
            )
        );
    }

    /**
     * Create user return value description.
     *
     * @param array $additionalfields some additional field
     * @return single_structure_description
     */
    public static function user_description($additionalfields = array()) {
        $userfields = array(
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'username'    => new external_value(core_user::get_property_type('username'), 'The username', VALUE_OPTIONAL),
            'firstname'   => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user', VALUE_OPTIONAL),
            'lastname'    => new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),
            'fullname'    => new external_value(core_user::get_property_type('firstname'), 'The fullname of the user'),
            'email'       => new external_value(core_user::get_property_type('email'), 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
            'address'     => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            'phone1'      => new external_value(core_user::get_property_type('phone1'), 'Phone 1', VALUE_OPTIONAL),
            'phone2'      => new external_value(core_user::get_property_type('phone2'), 'Phone 2', VALUE_OPTIONAL),
            'department'  => new external_value(core_user::get_property_type('department'), 'department', VALUE_OPTIONAL),
            'institution' => new external_value(core_user::get_property_type('institution'), 'institution', VALUE_OPTIONAL),
            'idnumber'    => new external_value(core_user::get_property_type('idnumber'), 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
            'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
            'firstaccess' => new external_value(core_user::get_property_type('firstaccess'), 'first access to the site (0 if never)', VALUE_OPTIONAL),
            'lastaccess'  => new external_value(core_user::get_property_type('lastaccess'), 'last access to the site (0 if never)', VALUE_OPTIONAL),
            'auth'        => new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, etc', VALUE_OPTIONAL),
            'suspended'   => new external_value(core_user::get_property_type('suspended'), 'Suspend user account, either false to enable user login or true to disable it', VALUE_OPTIONAL),
            'confirmed'   => new external_value(core_user::get_property_type('confirmed'), 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
            'lang'        => new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
            'calendartype' => new external_value(core_user::get_property_type('calendartype'), 'Calendar type such as "gregorian", must exist on server', VALUE_OPTIONAL),
            'theme'       => new external_value(core_user::get_property_type('theme'), 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'timezone'    => new external_value(core_user::get_property_type('timezone'), 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'mailformat'  => new external_value(core_user::get_property_type('mailformat'), 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            'description' => new external_value(core_user::get_property_type('description'), 'User profile description', VALUE_OPTIONAL),
            'descriptionformat' => new external_format_value(core_user::get_property_type('descriptionformat'), VALUE_OPTIONAL),
            'city'        => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country'     => new external_value(core_user::get_property_type('country'), 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
            'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
            'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                        'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                        'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
                    )
                ), 'User custom fields (also known as user profile fields)', VALUE_OPTIONAL),
            'preferences' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name'  => new external_value(PARAM_RAW, 'The name of the preferences'),
                        'value' => new external_value(PARAM_RAW, 'The value of the preference'),
                    )
            ), 'Users preferences', VALUE_OPTIONAL)
        );
        if (!empty($additionalfields)) {
            $userfields = array_merge($userfields, $additionalfields);
        }
        return new external_single_structure($userfields);
    }

}
