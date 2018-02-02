function showLocation(province , city , town) {
	
	var loc	= new Location();
	var title	= ['请选择省份' , '请选择城市' , '请选择地区'];
	$.each(title , function(k , v) {
		title[k]	= '<option value="">'+v+'</option>';
	})
	
	$('#loc_province').append(title[0]);
	$('#loc_city').append(title[1]);
	$('#loc_town').append(title[2]);
	
	
	$('#loc_province').change(function() {
		$('#loc_city').empty();
		$('#loc_city').append(title[1]);
		loc.fillOption('loc_city' , '0,'+$('#loc_province').val());
		$('#loc_town').empty();
		$('#loc_town').append(title[2]);
		//$('input[@name=location_id]').val($(this).val());
	})
	
	$('#loc_city').change(function() {
		$('#loc_town').empty();
		$('#loc_town').append(title[2]);
		loc.fillOption('loc_town' , '0,' + $('#loc_province').val() + ',' + $('#loc_city').val());
		//$('input[@name=location_id]').val($(this).val());
	})
	
	$('#loc_town').change(function() {
        $('input[name=location_id]').val($(this).val());
        if($(".selectSwrap").length>3){
           var seng=$("#loc_province").find("option:selected").text();
           var si=$("#loc_city").find("option:selected").text();
           var xian=$("#loc_town").find("option:selected").text();
            var furl=url+"&seng="+seng+"&si="+si+"&xian="+xian;
            $.post(furl,function(data){
	    	    if(data.data){
                    $("#isunion").show();
                    var union="<select id='union' class='select'>";
                    $.each(data.data,function(i,o){

                        union+="<option value='' long='"+o.long+"' lat='"+o.lat+"'>"+ o.cname+"</option>";

                    })
                    union+="</select>";
                    $("#isunion").html(union);
                }else{
                    $("#isunion").show();
                    $("#isunion").html("<span style='color:white;text-align: center;line-height: 50px;display: block;'>当前区域没有社区</span>");
                }
            },"json")
        }
    })

	
	if (province) {
		loc.fillOption('loc_province' , '0' , province);
		
		if (city) {
			loc.fillOption('loc_city' , '0,'+province , city);
			
			if (town) {
				loc.fillOption('loc_town' , '0,'+province+','+city , town);
			}
		}
		
	} else {
		loc.fillOption('loc_province' , '0');
	}
		
}