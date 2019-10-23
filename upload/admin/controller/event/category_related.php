<?php
class ControllerEventCategoryRelated extends Controller {
	
	public function view(&$view, &$data, &$output) {// triggered before view category form
		// build insert html
		$this->load->model('catalog/product');
		if(isset($this->request->get['category_id']) && $this->request->get['category_id']) {// get related products
			$this->load->model('extension/module/category_related');
			$info = $this->model_extension_module_category_related->getRelated($this->request->get['category_id']);
		} else {
			$info = array();
		}
		if($this->request->server['REQUEST_METHOD'] == 'POST') {
			// override with post
			$info = $this->request->post['product_related'];
		}
		$this->language->load('extension/module/category_related');
		$insert = '<div class="form-group">';
        $insert .= '    <label class="col-sm-2 control-label" for="input-related"><span data-toggle="tooltip" title="'.$this->language->get('help_related').'">'.$this->language->get('entry_related') . '</span></label>';
		$insert .= '	<div class="col-sm-10">';
		$insert .= '	  <input type="text" name="related" value="" placeholder="'.$this->language->get('entry_related') . '" id="input-related" class="form-control" />';
		$insert .= '	  <div id="product-related" class="well well-sm" style="height: 150px; overflow: auto;">';
		foreach($info as $related) {
			$result = $this->model_catalog_product->getProduct($related);
			$insert .= '		<div id="product-related'.$related.'"><i class="fa fa-minus-circle"></i>'.$result[$this->config->get('module_category_related_key')];
			$insert .= '		  <input type="hidden" name="product_related[]" value="'.$related.'" />';
			$insert .= '		</div>';
		}
		$insert .= '		</div>';
		$insert .= '	</div>';
		$insert .= '</div>';
		// javascript
		$insert .= "
<script type='text/javascript'>
$('input[name=\'related\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&user_token=".$this->session->data['user_token']."&filter_".$this->config->get('module_category_related_key')."=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['".$this->config->get('module_category_related_key')."'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'related\']').val('');

		$('#product-related' + item['value']).remove();
";
		$insert .= <<<EOF
		$('#product-related').append('<div id="product-related' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_related[]" value="' + item['value'] + '" /></div>');
	}
});

$('#product-related').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
</script>
EOF;
		
		$this->load->helper('simple_html_dom');
		$html = str_get_html($output);
		if($html === false) {
			$this->log->write('Unable to parse html!');
			return;
		}
		foreach($html->find('#tab-data') as $node) {
			$node->innertext .= $insert;
		}
		
		$output = $html->save();
	}
	
	public function save(&$route, &$data, &$output = null) {
		if((int)$output) {
			$id = $output;
			$temp = $data[0];
		} else {
			$temp = $data[1];
			$id = $data[0];
		}
		$this->load->model('extension/module/category_related');
		
		$this->model_extension_module_category_related->saveRelated($id, $temp);
	}
	
}
