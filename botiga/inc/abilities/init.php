<?php
/**
 * Botiga Abilities API bootstrap.
 *
 * @since 2.4.8
 *
 * @package Botiga
 */

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-customizer-manager.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-field-schema.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-settings-validator.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-settings-updater.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-settings-registry.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-access.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities-admin.php';

require_once get_template_directory() .
	'/inc/abilities/abilities/abstract-class-botiga-ability.php';

require_once get_template_directory() .
	'/inc/abilities/abilities/class-botiga-get-site-capabilities-ability.php';

require_once get_template_directory() .
		'/inc/abilities/abilities/class-botiga-list-sections-ability.php';

require_once get_template_directory() .
	'/inc/abilities/abilities/class-botiga-get-section-settings-ability.php';

require_once get_template_directory() .
	'/inc/abilities/abilities/class-botiga-update-setting-ability.php';

require_once get_template_directory() .
	'/inc/abilities/abilities/class-botiga-update-section-settings-ability.php';

require_once get_template_directory() .
	'/inc/abilities/class-botiga-abilities.php';
