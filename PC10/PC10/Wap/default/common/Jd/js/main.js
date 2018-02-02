$(function(){
    $(document).on('touchstart', '.tel', function(e){
        e.stopPropagation();
    })
    // $('li[class=adviser]').find('div').each(function(){
        // $(this).on('click',function(){
            // window.location=$(this).parent('li').attr('href');
        // });

    // });
    //异步搜索逻辑
	$(document).keypress(function(e) {//搜索框回车事件
		if (e.which == 13){
		if($('#search').val()!='')  {
			$('#goSearch').click();	//触发搜索按钮点击事件
			}
		}
	});
    $('#hy').change(function(){//行业选择
			$('#goSearch').click();	//触发搜索按钮点击事件
    });
    $('#tags').change(function(){//标签选择
  
			$('#goSearch').click();	//触发搜索按钮点击事件
    });
    $('#cate').change(function(){
			$('#goSearch').click();	//触发搜索按钮点击事件
    });
    $('#goSearch').click(function(){
        //提交数据准备
        var word=$('#search').val();
        var hy=$('#hy').val();
        var tags=$('#tags').val();
        var cate=$('#cate').val();
      
        //if(word=='' && hy=='' && tags=='') return false;
        $.post(searchUrl,{word:word,hy:hy,tag:tags,cate:cate},function(data){
            if(data.status){
                    $('#searchList').html(data.rs);
            }else{
                    $('#searchList').html('<div style="width:100%;text-align:center;">'+data.msg+'</div>');
            }
        },'json');
    });
});

