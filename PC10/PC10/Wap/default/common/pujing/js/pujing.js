/* 
* @Author: zhang
* @Date:   2015-04-17 17:25:50
* @Last Modified by:   zhang
* @Last Modified time: 2015-04-17 18:49:24
*/

'use strict';
$(function(){
	$('.jian').click(function(){
		if($('.number').text() > 0){
           $('.number').text(Number($('.number').text()) - 1);
		}else{
		   $('.number').text(0);
		}
	})

    $('.jia').click(function(){
        $('.number').text(Number($('.number').text()) + 1);
	})

})