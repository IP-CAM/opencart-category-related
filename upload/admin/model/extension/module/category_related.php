<?php
class ModelExtensionModuleCategoryRelated extends Model {
	public function install() {
		// box tables
		$this->db->query("
			CREATE TABLE `" . DB_PREFIX . "category_related` (
				`category_id` INT(11) NOT NULL,
				`product_id` INT(11) NOT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "category_related`");
	}
	
	public function getRelated($category_id) {
		$category_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_related WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_related_data[] = $result['product_id'];
		}

		return $category_related_data;
	}
	
	public function saveRelated($category_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_related WHERE category_id = '" . (int)$category_id . "'");
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_related SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}
	}
	
	public function deleteRelated($category_id = 0, $product_id = 0) {
		if($category_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "category_related WHERE category_id = '" . (int)$category_id . "'");
		}
		if($product_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "category_related WHERE product_id = '" . (int)$product_id . "'");
		}
	}
}