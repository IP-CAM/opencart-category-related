<?php

class controllerExtensionEventCategoryRelated extends Controller {
    public function productRelated(&$route, &$data = array(), &$output = '') {
        // check if this module is enabled
        if(!$this->config->get('module_category_related_status')) {
            return;
        }
        $this->load->model('extension/module/category_related');
	$category = $this->model_extension_module_category_related->getRelated($data[0]);
	foreach($category as $key => $value) {// loop instead of array_merge to avoid duplicates
		$output[$key] = $value;
	}
	
    }
}
