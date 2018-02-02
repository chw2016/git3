						//角度和脱硫
						function getAngle()
						{
							var angle = $('select#area-1 option:selected').val();												//获得角度									//
							if (angle==2){
									document.getElementById("angle").innerHTML="26~29";											//安徽&脱硫电价
									document.getElementById("tuoliu").innerHTML="0.44";
								
								}else if(angle==19){
									document.getElementById("angle").innerHTML="28~37";	
									document.getElementById("tuoliu").innerHTML="0.44";
								}else if(angle==29){
									document.getElementById("angle").innerHTML="20~21";	
									document.getElementById("tuoliu").innerHTML="0.33";
								}else if(angle==44){
									document.getElementById("angle").innerHTML="19~20";
									document.getElementById("tuoliu").innerHTML="0.52";	
								}else if(angle==66){
									document.getElementById("angle").innerHTML="19~20";	
									document.getElementById("tuoliu").innerHTML="0.48";
								}else if(angle==81){
									document.getElementById("angle").innerHTML="18~20";	
									document.getElementById("tuoliu").innerHTML="0.49";
								}else if(angle==89){
									document.getElementById("angle").innerHTML="19~22";	
									document.getElementById("tuoliu").innerHTML="0.38";
								}else if(angle==99){
									document.getElementById("angle").innerHTML="32~38";	
									document.getElementById("tuoliu").innerHTML="0.43";
								}else if(angle==111){
									document.getElementById("angle").innerHTML="38~40";	
									document.getElementById("tuoliu").innerHTML="0.41";
								}else if(angle==121){
									document.getElementById("angle").innerHTML="28~32";	
									document.getElementById("tuoliu").innerHTML="0.44";
								}else if(angle==40){
									document.getElementById("angle").innerHTML="38~43";	
									document.getElementById("tuoliu").innerHTML="0.4";
								}else if(angle==154){
									document.getElementById("angle").innerHTML="32~36";	
									document.getElementById("tuoliu").innerHTML="0.3";
								}else if(angle==160){
									document.getElementById("angle").innerHTML="23~27";	
									document.getElementById("tuoliu").innerHTML="0.48";
								}else if(angle==178){
									document.getElementById("angle").innerHTML="21~23";	
									document.getElementById("tuoliu").innerHTML="0.5";
								}else if(angle==193){
									document.getElementById("angle").innerHTML="22~25";	
									document.getElementById("tuoliu").innerHTML="0.48";
								}else if(angle==205){
									document.getElementById("angle").innerHTML="26~30";	
									document.getElementById("tuoliu").innerHTML="0.46";
								}else if(angle==219){
									document.getElementById("angle").innerHTML="22~24";	
									document.getElementById("tuoliu").innerHTML="0.49";
								}else if(angle==231){
									document.getElementById("angle").innerHTML="36~39";	
									document.getElementById("tuoliu").innerHTML="0.41";
								}else if(angle==246){
									document.getElementById("angle").innerHTML="36~41";	
									document.getElementById("tuoliu").innerHTML="0.31";
								}else if(angle==259){
									document.getElementById("angle").innerHTML="30~35";	
									document.getElementById("tuoliu").innerHTML="0.34";
								}else if(angle==268){
									document.getElementById("angle").innerHTML="30~34";	
									document.getElementById("tuoliu").innerHTML="0.45";
								}else if(angle==286){
									document.getElementById("angle").innerHTML="31~36";	
									document.getElementById("tuoliu").innerHTML="0.39";
								}else if(angle==298){
									document.getElementById("angle").innerHTML="26~35";	
									document.getElementById("tuoliu").innerHTML="0.4";
								}else if(angle==309){
									document.getElementById("angle").innerHTML="26~30";	
									document.getElementById("tuoliu").innerHTML="0.4";
								}else if(angle==317){
									document.getElementById("angle").innerHTML="22~30";	
									document.getElementById("tuoliu").innerHTML="0.45";
								}else if(angle==339){
									document.getElementById("angle").innerHTML="35~40";	
									document.getElementById("tuoliu").innerHTML="0.26";
								}else if(angle==358){
									document.getElementById("angle").innerHTML="20~25";	
									document.getElementById("tuoliu").innerHTML="0.36";
								}else if(angle==375){
									document.getElementById("angle").innerHTML="36~37";	
									document.getElementById("tuoliu").innerHTML="0.4";
								}else if(angle==377){
									document.getElementById("angle").innerHTML="25~26";	
									document.getElementById("tuoliu").innerHTML="0.48";
								}else if(angle==379){
									document.getElementById("angle").innerHTML="35~36";	
									document.getElementById("tuoliu").innerHTML="0.41";
								}else if(angle==381){
									document.getElementById("angle").innerHTML="22~24";	
									document.getElementById("tuoliu").innerHTML="0.45";
								}
								
						}
