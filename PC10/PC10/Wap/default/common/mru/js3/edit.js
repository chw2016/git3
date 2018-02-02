$(function(){
	$.fn.extend({
		editable: function(callback){
			var _private_class = '__editable_class_private__';
			$(this).click(function(){
				var $this = $(this)
				if ($this.find('input').length > 0) {
					return false;
				};
				var sKey = $this.data('key');
				var $Input = $('<input />');
				$Input.data('key', sKey)
				var sVal = $.trim($this.html());
				$this.empty();
				$Input.addClass(_private_class).val(sVal)
				$Input.appendTo($this);
				$Input.focus();
			});
			$(document).on('blur', '.'+_private_class, function(){
				$this = $(this)
				var sKey = $this.data('key');
				if (typeof(callback) == 'function') {
					if(false !== callback(sKey, $this.val())){
						$this.replaceWith($this.val());
					}
				};
			})
		}
	});
})