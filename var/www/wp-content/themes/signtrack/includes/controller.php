<?php

class widelyController {

	/**
	 * Whether of not to display the
	 * responsive debug tools.
	 *
	 * @type BOOL
	 */
	private $responsive_debug = FALSE;

	/**
	 * True if you want the standard
	 * banner header. False if you want
	 * the modern sidebar header
	 *
	 *	****************				**********************************
	 * 	*              *				*          *                     *
	 *  *              *				*          *                     *
	 *  ****************				*          *                     *
	 *  *              *				*          *                     *
	 *  *              *      versus    *          *                     *
	 *  *              *				*          *                     *
	 *  *              *				*          *                     *
	 *  *              *				*          *                     *
	 *  *              *				*          *                     *
	 *  ****************				**********************************
	 *
	 * @type BOOL
	 */
	private $header_banner = TRUE;

	/**
	 * True if you want the nav to the right of the header logo,
	 * or false if you want a full nav below header.
	 *
	 *  ************************				**************************
	 *  *                      *				*						 *
	 *  *                      *				*  ***					 *
	 *  *  ***   **  **  ** ** *				*  ***					 *
	 *  *  ***   **  **  ** ** *      versus    *  ***					 *
	 *  *  ***                 *				*						 *
	 *  *                      *				**************************
	 *  *                      *				* **  **  **  **  **  ** *
	 *  ************************				**************************
	 *
	 * @type BOOL
	 */
	private $header_banner_nav = TRUE;


	/**
	 * True if you want site description to display under logo, else False.
	 *
	 * @type BOOl
	 */
	private $show_desc = TRUE;


	/**
	 * True to utilize a fixed nav on scroll, else False.
	 */
	private $fixed_nav = TRUE;


	/**
	 * True to utilize a mobile navigation, else false.
	 */
	private $mobile_nav = TRUE;

	/**
	 * Number of widget areas to include in the footer. Default 3.
	 *
	 * 	Default Layout:
	 *
	 *   	div.widely-footer-container
	 *       ********************************************
	 *       *  div.footer-widget                       *
	 *       *   **********   **********   **********   *
	 *       *   *        *   *        *   *        *   *
	 *       *   *        *   *        *   *        *   *
	 *       *   *        *   *        *   *        *   *
	 *       *   *        *   *        *   *        *   *
	 *       *   **********   **********   **********   *
	 *       *                                          *
	 *       ********************************************
	 *
	 */
	private $footer_widget_num = 3;


	private $font_logo = FALSE;

	private $header_home_hero = FALSE;

	private $home_hero_url = NULL;



	/**
	 *
	 *
	 * SETTERS
	 *
	 *
	 */



	public function set_responsive_debug($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_responsive_debug($bool); $bool must be boolean value.';
		}
		else {
			$this->responsive_debug = $bool;
		}
	}



	public function set_header_banner($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_banner_header($bool); $bool must be boolean value.';
		}
		else {
			$this->header_banner = $bool;
		}
	}



	public function set_header_banner_nav($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_header_banner_nav($bool); $bool must be boolean value.';
		}
		else {
			$this->header_banner_nav = $bool;
		}
	}



	public function set_show_desc($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_show_desc($bool); $bool must be boolean value.';
		}
		else {
			$this->show_desc = $bool;
		}
	}



	public function set_fixed_nav($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_fixed_nav($bool); $bool must be boolean value.';
		}
		else {
			$this->fixed_nav = $bool;
		}
	}


	public function set_mobile_nav($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_mobile_nav($bool); $bool must be boolean value.';
		}
		else {
			$this->mobile_nav = $bool;
		}
	}


	public function set_footer_widget_num($int) {
		if (!is_int($int)) {
			return 'Error: WidelyController::set_footer_widget_num($int); $int must be integer value.';
		}
		else {
			$this->footer_widget_num = $int;
		}
	}


	public function set_font_logo($bool) {
		if (!is_bool($bool)) {
			return 'Error: WidelyController::set_font_logo($bool); $bool must be boolean value.';
		}
		else {
			$this->font_logo = $bool;
		}
	}

	public function set_header_home_hero($bool) {
		if (!is_bool($bool) && $bool != 'parallax') {
			return 'Error: WidelyController::set_header_home_hero($bool); $bool must be boolean value.';
		}
		else {
			$this->header_home_hero = $bool;
		}
	}

	public function set_home_hero_url($string) {
		$this->home_hero_url = $string;
	}


	/**
	*
	*
	* GETTERS
	*
	*
	*/

	public function get_responsive_debug() {
		return $this->responsive_debug;
	}

	public function get_header_banner() {
		return $this->header_banner;
	}

	public function get_header_banner_nav() {
		return $this->header_banner_nav;
	}

	public function get_show_desc() {
			return $this->show_desc;
	}

	public function get_fixed_nav() {
		return $this->fixed_nav;
	}

	public function get_mobile_nav() {
		return $this->mobile_nav;
	}

	public function get_footer_widget_num() {
		return $this->footer_widget_num;
	}

	public function get_font_logo() {
		return $this->font_logo;
	}

	public function get_header_home_hero() {
		return $this->header_home_hero;
	}

	public function get_home_hero_url() {
		return $this->home_hero_url;
	}

}


global $widely_controller;

$widely_controller = new WidelyController();
