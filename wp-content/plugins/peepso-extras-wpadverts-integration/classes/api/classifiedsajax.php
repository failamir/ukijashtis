<?php

class PeepSoClassifiedsAjax extends PeepSoAjaxCallback
{

	/**
     * Called from PeepSoAjaxHandler
     * Declare methods that don't need auth to run
     * @return array
     */
    public function ajax_auth_exceptions()
    {
        $list_exception = array();
        $allow_guest_access = PeepSo::get_option('wpadverts_allow_guest_access_to_classifieds', 0);
        if ($allow_guest_access) {
            array_push($list_exception, 'search');
        }

        return $list_exception;
	}
	
	/**
	 * GET
	 * Search for classifieds matching the query.
	 * @param  PeepSoAjaxResponse $resp
	 */
	public function search(PeepSoAjaxResponse $resp)
	{
		$page = $this->_input->int('page', 1);

		$query = stripslashes_deep($this->_input->value('query', '', false));
		$user_id = $this->_input->int('user_id', 0);
		$location = stripslashes_deep($this->_input->value('location', '', false));
		$category = $this->_input->int('category', 0);
		$limit = $this->_input->int('limit', 1);

		$resp->set('page', $page);

		$PeepSoClassifieds = new PeepSoClassifieds();
		$classifieds = $PeepSoClassifieds->get_classifieds($page, $limit, 'unused', 'unused', $query, $user_id, $location, $category);

		if (count($classifieds) > 0 || $page > 1) {

			$resp->success(TRUE);
			$resp->set('classifieds', $classifieds);
		} else {
            $resp->success(FALSE);
            if($user_id) {
                $message = (get_current_user_id() == $user_id) ? __('You have no classifieds yet', 'peepso-wpadverts') : sprintf(__('%s has no classifieds yet', 'peepso-wpadverts'), PeepSoUser::get_instance($user_id)->get_firstname());
                $resp->error(PeepSoTemplate::exec_template('profile', 'no-results-ajax', array('message' => $message), TRUE));
            } else {
                $message = __('No classifieds found', 'peepso-wpadverts');
                $resp->error(PeepSoTemplate::exec_template('general', 'no-results-ajax', array('message' => $message), TRUE));
            }
		}
	}
}
// EOF
