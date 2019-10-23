<?php
class ModelExtensionModuleCategoryRelated extends Model {
	
	public function getRelated($product_id) {
		$this->load->model('catalog/product');
		$category_related_data = array();
		$categories = $this->db->query('select p.category_id from '.DB_PREFIX."product_to_category p join ".DB_PREFIX."category c on p.category_id = c.category_id where p.product_id = '".(int)$product_id."' and c.status = '1' ORDER BY c.sort_order");
		foreach($categories->rows as $category) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_related WHERE category_id = '" . (int)$category['category_id'] . "'");

			foreach ($query->rows as $result) {
				$category_related_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
			}
		}

		return $category_related_data;
	}
}